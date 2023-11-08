<?php
/**
 * Container configuration presets for WordPress.
 *
 * @package     CodeAtlantic\Plugin\Configs
 * @author      Code Atlantic
 * @copyright   Copyright (c) 2020, Code Atlantic LLC
 */

namespace CodeAtlantic\Plugin\Configs;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use DownShift\WordPress\EventEmitter;
use WP_Query;

/**
 * Class WordPress
 *
 * @package CodeAtlantic\Plugin\Configs
 */
class WordPress implements ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $container A container instance.
	 */
	public function register(  $container ) {
		$container['wp.database'] = function (  $container ) {
			global $wpdb;

			return $wpdb;
		};

		$container['wp.query'] = function (  $container ) {
			return new WP_Query();
		};

		$container['hooks'] = function (  $container ) {
			return new EventEmitter();
		};

		$container['wp.user'] = wp_get_current_user();
	}
}
