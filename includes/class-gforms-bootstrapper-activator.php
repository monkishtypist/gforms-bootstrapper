<?php

/**
 * Fired during plugin activation
 *
 * @link     https://github.com/monkishtypist
 * @since    2.0.0
 *
 * @package    Gforms_Bootstrapper
 * @subpackage Gforms_Bootstrapper/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since    2.0.0
 * @package    Gforms_Bootstrapper
 * @subpackage Gforms_Bootstrapper/includes
 * @author     Tim Spinks <tim@monkishtypist.com>
 */
class Gforms_Bootstrapper_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {

		if( !class_exists( 'GFForms' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Please install and activate Gravity Forms.', 'gforms-api-signer' ), 'Plugin dependency check', array( 'back_link' => true ) );
		}

	}

}
