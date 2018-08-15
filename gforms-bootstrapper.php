<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/monkishtypist
 * @since             2.0.0
 * @package           Gforms_Bootstrapper
 *
 * @wordpress-plugin
 * Plugin Name:       Gravity Forms Bootstrapper
 * Plugin URI:        https://github.com/monkishtypist/gforms-bootstrapper
 * Description:       Add Bootstrap CSS and classes to gravity forms.
 * Version:           2.0.5
 * Author:            Tim Spinks
 * Author URI:        https://github.com/monkishtypist
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gforms-bootstrapper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '2.0.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gforms-bootstrapper-activator.php
 */
function activate_gforms_bootstrapper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gforms-bootstrapper-activator.php';
	Gforms_Bootstrapper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gforms-bootstrapper-deactivator.php
 */
function deactivate_gforms_bootstrapper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gforms-bootstrapper-deactivator.php';
	Gforms_Bootstrapper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gforms_bootstrapper' );
register_deactivation_hook( __FILE__, 'deactivate_gforms_bootstrapper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gforms-bootstrapper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_gforms_bootstrapper() {

	$plugin = new Gforms_Bootstrapper();
	$plugin->run();

}

if( class_exists('GFAddOn') ) :
	run_gforms_bootstrapper();
endif;