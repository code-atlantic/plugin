<?php
/**
 * Hook & Filter management.
 *
 * @package     CodeAtlantic\Plugin
 * @author      Code Atlantic
 * @copyright   Copyright (c) 2020, Code Atlantic LLC
 */

namespace CodeAtlantic\Plugin;

use CodeAtlantic\Plugin\Interfaces\Action_Hook_Subscriber;
use CodeAtlantic\Plugin\Interfaces\Filter_Hook_Subscriber;

/**
 * WP_Plugin_API_Manager handles registering actions and hooks with the
 * WordPress Plugin API.
 *
 * @package CodeAtlantic\Plugin
 */
class Hooks {

	/**
	 * Registers an object with the WordPress Plugin API.
	 *
	 * @param mixed $object Register action or filter.
	 */
	public function register( $object ) {
		if ( $object instanceof Action_Hook_Subscriber ) {
			$this->register_actions( $object );
		}
		if ( $object instanceof Filter_Hook_Subscriber ) {
			$this->register_filters( $object );
		}
	}

	/**
	 * Register an object with a specific action hook.
	 *
	 * @param Action_Hook_Subscriber $object Action hook object.
	 * @param string                 $name Name of the hook used.
	 * @param mixed                  $parameters Additional arguments.
	 */
	private function register_action( Action_Hook_Subscriber $object, $name, $parameters ) {
		if ( is_string( $parameters ) ) {
			add_action( $name, array( $object, $parameters ) );
		} elseif ( is_array( $parameters ) && isset( $parameters[0] ) ) {
			add_action( $name, array( $object, $parameters[0] ), isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
		}
	}

	/**
	 * Regiters an object with all its action hooks.
	 *
	 * @param Action_Hook_Subscriber $object Object to register.
	 */
	private function register_actions( Action_Hook_Subscriber $object ) {
		foreach ( $object->get_actions() as $name => $parameters ) {
			$this->register_action( $object, $name, $parameters );
		}
	}

	/**
	 * Register an object with a specific filter hook.
	 *
	 * @param Filter_Hook_Subscriber $object Filter hook object.
	 * @param string                 $name Name of the filter used.
	 * @param mixed                  $parameters Additional arguments.
	 */
	private function register_filter( Filter_Hook_Subscriber $object, $name, $parameters ) {
		if ( is_string( $parameters ) ) {
			add_filter( $name, array( $object, $parameters ) );
		} elseif ( is_array( $parameters ) && isset( $parameters[0] ) ) {
			add_filter( $name, array( $object, $parameters[0] ), isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
		}
	}

	/**
	 * Regiters an object with all its filter hooks.
	 *
	 * @param Filter_Hook_Subscriber $object Object to register.
	 */
	private function register_filters( Filter_Hook_Subscriber $object ) {
		foreach ( $object->get_filters() as $name => $parameters ) {
			$this->register_filter( $object, $name, $parameters );
		}
	}
}
