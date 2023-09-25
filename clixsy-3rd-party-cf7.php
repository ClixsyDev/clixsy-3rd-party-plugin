<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://github.com/ClixsyDev
 * @since             1.0.0
 * @package           Clixsy_3rd_party_Cf7
 *
 * @wordpress-plugin
 * Plugin Name:       Clisxsy third party integration for CF7
 * Plugin URI:        https://https://github.com/ClixsyDev
 * Description:       This plugin will help you to set the endpoint and map field from Contact Form 7 to any third-party url you want. Developed by CLIXSY
 * Version:           1.0.5
 * Author:            Bogdan Zakharchyshyn
 * Author URI:        https://https://github.com/ClixsyDev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       clixsy-3rd-party-cf7
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
define( 'CLIXSY_3RD_party_CF7_VERSION', '1.0.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-clixsy-3rd-party-cf7-activator.php
 */
function activate_clixsy_3rd_party_cf7() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clixsy-3rd-party-cf7-activator.php';
	Clixsy_3rd_party_Cf7_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-clixsy-3rd-party-cf7-deactivator.php
 */
function deactivate_clixsy_3rd_party_cf7() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clixsy-3rd-party-cf7-deactivator.php';
	Clixsy_3rd_party_Cf7_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_clixsy_3rd_party_cf7' );
register_deactivation_hook( __FILE__, 'deactivate_clixsy_3rd_party_cf7' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-clixsy-3rd-party-cf7.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_clixsy_3rd_party_cf7() {

	$plugin = new Clixsy_3rd_party_Cf7();
	$plugin->run();

}
run_clixsy_3rd_party_cf7();
