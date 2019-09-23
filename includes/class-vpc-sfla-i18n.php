<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://orionorigin.com
 * @since      1.0.0
 *
 * @package    Vpc_Sfla
 * @subpackage Vpc_Sfla/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Vpc_Sfla
 * @subpackage Vpc_Sfla/includes
 * @author     Orion <help@orionorigin.com>
 */
class Vpc_Sfla_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'vpc-sfla',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
