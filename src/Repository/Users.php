<?php
/**
 * Users repository for WP Users.
 *
 * @package     CodeAtlantic\Plugin
 * @author      Code Atlantic
 * @copyright   Copyright (c) 2021, Code Atlantic LLC
 */

namespace CodeAtlantic\Plugin\Repository;

use CodeAtlantic\Plugin\Model\User;
use InvalidArgumentException;
use WP_User;
use WP_User_Query;

/**
 * Users Repository
 *
 * Interface between WP_User_Query and our data needs. Essentially a query factory.
 */
abstract class Users implements \CodeAtlantic\Plugin\Interfaces\Repository {

	/**
	 * WordPress query object.
	 *
	 * @var \WP_User_Query
	 */
	protected $query;

	/**
	 * Array of hydrated object models.
	 *
	 * @var array
	 */
	protected $cache = array(
		'objects' => array(),
		'queries' => array(),
	);

	/**
	 * @var string
	 */
	protected $model;

	/**
	 * Should return a valid user cap to test against.
	 *
	 * @return string
	 */
	protected function get_required_cap() {
		return '';
	}

	/**
	 * Initialize the repository.
	 */
	protected function init() {
		$this->query = new WP_User_Query;
		$this->reset_strict_query_args();
	}

	public function __construct() {
		$this->init();
	}

	/**
	 * @return array
	 */
	public function default_query_args() {
		return array(
			'count_total' => false, // Defaults to true, but we can save some query overhead unless needed.
		);
	}

	/**
	 * @var array
	 */
	protected $strict_query_args = array();

	/**
	 * Returns an array of default strict query args that can't be over ridden, such as user cap.
	 *
	 * @return array
	 */
	protected function default_strict_query_args() {
		return array();
	}

	/**
	 * Returns an array of enforced query args that can't be over ridden, such as user cap.
	 *
	 * @return array
	 */
	protected function get_strict_query_args() {
		return $this->strict_query_args;
	}

	/**
	 * Sets a specific query arg to a strict value.
	 *
	 * @param      $key
	 * @param null $value
	 */
	protected function set_strict_query_arg( $key, $value = null ) {
		$this->strict_query_args[ $key ] = $value;
	}

	/**
	 * Returns an array of enforced query args that can't be over ridden, such as user cap.
	 *
	 * @return array
	 */
	protected function reset_strict_query_args() {
		$this->strict_query_args = $this->default_strict_query_args();

		return $this->strict_query_args;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	protected function _build_wp_query_args( $args = array() ) {
		$args = wp_parse_args( $args, $this->default_query_args() );

		$args = $this->build_query_args( $args );

		return array_merge( $args, $this->get_strict_query_args() );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	protected function build_query_args( $args = array() ) {
		return $args;
	}

	/**
	 * @param int $id
	 *
	 * @return User|WP_User
	 * @throws \InvalidArgumentException
	 */
	public function get_item( $id ) {
		if ( ! $this->has_item( $id ) ) {
			throw new InvalidArgumentException( sprintf( __( 'No user found with id %d.', 'default' ), $id ) );
		}

		return $this->get_model( get_user_by( 'id', $id ) );
	}

	/**
	 * @param $field
	 * @param $value
	 *
	 * @return User|WP_User
	 */
	public function get_item_by( $field, $value ) {
		$user = get_user_by( $field, $value );

		if ( ! $user || ! $this->has_item( $user->ID ) ) {
			throw new InvalidArgumentException( sprintf( __( 'No user found with %s %s.', 'default' ), $field, $value ) );
		}

		return $this->get_model( $user->ID );
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function has_item( $id ) {
		$required_caps = $this->get_required_cap();

		return get_user_by( 'id', $id ) && ( empty( $required_caps ) || user_can( $id, $required_caps ) );
	}


	/**
	 * @param $args
	 *
	 * @return string
	 */
	protected function get_args_hash( $args ) {
		return md5( serialize( $args ) );
	}

	/**
	 * @param array $args
	 *
	 * @return User[]|WP_User[]
	 */
	public function get_items( $args = array() ) {
		/** Reset default strict query args. */
		$this->reset_strict_query_args();

		$args = $this->_build_wp_query_args( $args );

		$hash = $this->get_args_hash( $args );

		if ( ! isset( $this->cache['queries'][ $hash ] ) ) {
			/**
			 * Initialize a new query and return it.
			 *
			 * This also keeps the query cached for potential later usage via $this->get_last_query();
			 */
			$this->query->prepare_query( $args );
			$this->query->query();

			$this->cache['queries'][ $hash ] = (array) $this->query->get_results();
		}

		/** @var array $users */
		$users = $this->cache['queries'][ $hash ];

		/**
		 * Only convert to models if the model set is valid and not the WP_User default.
		 */
		foreach ( $users as $key => $user ) {
			$users[ $key ] = $this->get_model( $user );
		}

		return $users;
	}

	/**
	 * @param array $args
	 *
	 * @return int
	 */
	public function count_items( $args = array() ) {
		/** Reset default strict query args. */
		$this->reset_strict_query_args();

		/** Set several strict query arg overrides, no matter what args were passed. */
		$this->set_strict_query_arg( 'fields', 'ID' );
		$this->set_strict_query_arg( 'number', 1 );
		$this->set_strict_query_arg( 'count_total', true );

		/** We don't use  $this->query here to avoid returning count queries via $this->>get_last_query(); */
		$query = new WP_User_Query( $this->build_query_args( $args ) );

		return (int) $query->get_total();
	}

	/**
	 * @return \WP_User_Query
	 */
	public function get_last_query() {
		return $this->query;
	}

	/**
	 * Assert that data is valid.
	 *
	 * @param array $data
	 *
	 * @throws InvalidArgumentException
	 *
	 * TODO Add better Exceptions via these guides:
	 * - https://www.brandonsavage.net/using-interfaces-for-exceptions/
	 * - https://www.alainschlesser.com/structuring-php-exceptions/
	 *
	 *  if ( isset( $data['subject'] ) && ! $data['subject'] ) {
	 *        throw new InvalidArgumentException( 'The subject is required.' );
	 *  }
	 */
	abstract protected function assert_data( $data );

	/**
	 * @param $user
	 *
	 * @return string
	 */
	protected function get_object_hash( $user ) {
		return md5( serialize( $user ) );
	}

	/**
	 * @param $user
	 *
	 * @return bool
	 */
	protected function cached_model_exists( $user ) {
		return isset( $this->cache['objects'][ $user->ID ] ) && $this->get_object_hash( $user ) === $this->cache['objects'][ $user->ID ]['hash'];
	}

	/**
	 * @param int|WP_User $id
	 *
	 * @return User|WP_User
	 */
	protected function get_model( $id ) {
		$user = is_a( $id, 'WP_User' ) ? $id : get_user_by( 'id', $id );

		/**
		 * Only convert to models if the model set is valid and not the WP_Post default.
		 */
		$model = $this->model;
		if ( ! $model || 'WP_User' === $model || ! class_exists( $model ) || is_a( $user, $model ) ) {
			return $user;
		}

		if ( ! $this->cached_model_exists( $user ) ) {
			$object = new $model( $user );

			$this->cache['objects'][ $user->ID ] = array(
				'object' => $object,
				'hash'   => $this->get_object_hash( $user ),
			);
		}

		return $this->cache['objects'][ $user->ID ]['object'];
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete_item( $id ) {
		return EMPTY_TRASH_DAYS && (bool) wp_delete_user( $id );
	}

}