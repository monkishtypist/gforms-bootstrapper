<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link     https://github.com/monkishtypist
 * @since    2.0.0
 *
 * @package    Gforms_Bootstrapper
 * @subpackage Gforms_Bootstrapper/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since    2.0.0
 * @package    Gforms_Bootstrapper
 * @subpackage Gforms_Bootstrapper/includes
 * @author     Tim Spinks <tim@monkishtypist.com>
 */
class Gforms_Bootstrapper_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gforms-bootstrapper',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
