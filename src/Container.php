<?php
/**
 * Simple plugin container.
 *
 * @package     CodeAtlantic\Plugin
 * @author      Code Atlantic
 * @copyright   Copyright (c) 2020, Code Atlantic LLC
 */

namespace CodeAtlantic\Plugin;

/**
 * Class Container
 *
 * @package CodeAtlantic\Plugin
 */
class Container extends \Pimple\Container {

	/**
	 * Get item from container
	 *
	 * @param string $id Key for the item.
	 *
	 * @return mixed Current value of the item.
	 */
	public function get( $id ) {
		return $this->offsetGet( $id );
	}

	/**
	 * Set item in container
	 *
	 * @param string $id Key for the item.
	 * @param mixed  $value Value to be set.
	 */
	public function set( $id, $value ) {
		$this->offsetSet( $id, $value );
	}
}
