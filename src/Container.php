<?php
/*******************************************************************************
 * Copyright (c) 2020, Code Atlantic LLC.
 ******************************************************************************/

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
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function get( $id ) {
		return $this->offsetGet( $id );
	}

	/**
	 * Set item in container
	 *
	 * @param string $id
	 * @param mixed  $value
	 */
	public function set( $id, $value ) {
		$this->offsetSet( $id, $value );
	}
}