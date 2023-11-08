<?php
/**
 * Repository Interface.
 *
 * @package     CodeAtlantic\Plugin
 * @author      Code Atlantic
 * @copyright   Copyright (c) 2021, Code Atlantic LLC
 */

namespace CodeAtlantic\Plugin\Interfaces;

use WP_Post as Model;

/**
 * Repository Interface
 *
 * Interface between WP_Query and our data needs. Essentially a query factory.
 *
 * @package CodeAtlantic\Plugin\Interfaces
 */
interface Repository {

	/**
	 * @param int $id
	 *
	 * @return Model
	 */
	public function get_item( $id );

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function has_item( $id );

	/**
	 * @param $args
	 *
	 * @return Model[]
	 */
	public function get_items( $args );

	/**
	 * @param array $data
	 *
	 * @return Model
	 */
	public function create_item( $data );

	/**
	 * @param int   $id
	 * @param array $data
	 *
	 * @return Model
	 */
	public function update_item( $id, $data );

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function delete_item( $id );

}
