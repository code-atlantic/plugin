<?php
/*******************************************************************************
 * Copyright (c) 2020, Code Atlantic LLC.
 ******************************************************************************/

namespace CodeAtlantic\Plugin\Configs\PopupMaker;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class Extension
 *
 * @package CodeAtlantic\Plugin\Configs\PopupMaker
 */
class Extension implements ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Container $container A container instance
	 */
	public function register( Container $container ) {
		// This should mimic the registration process in our PUM_Extension_Activator class.
	}
}