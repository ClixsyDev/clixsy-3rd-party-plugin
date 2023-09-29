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
 * Plugin Name:       Clixsy third party integration for CF7
 * Plugin URI:        https://github.com/ClixsyDev
 * Description:       This plugin will help you to set the endpoint and map field from Contact Form 7 to any third-party url you want. Developed by CLIXSY
 * Version:           1.1.0.7
 * Author:            Bogdan Zakharchyshyn
 * Author URI:        https://github.com/ClixsyDev
 * License:           GPL-2.0+
 * Network:           true
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       clixsy-3rd-party-cf7
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

$github_repo_url = 'https://api.github.com/repos/ClixsyDev/clixsy-3rd-party-plugin/releases/latest';
$plugin_slug = plugin_basename(__FILE__);
$plugin_file = 'clixsy-3rd-party-cf7.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CLIXSY_3RD_party_CF7_VERSION', '1.1.0.7');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-clixsy-3rd-party-cf7-activator.php
 */
function activate_clixsy_3rd_party_cf7() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-clixsy-3rd-party-cf7-activator.php';
	Clixsy_3rd_party_Cf7_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-clixsy-3rd-party-cf7-deactivator.php
 */
function deactivate_clixsy_3rd_party_cf7() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-clixsy-3rd-party-cf7-deactivator.php';
	Clixsy_3rd_party_Cf7_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_clixsy_3rd_party_cf7');
register_deactivation_hook(__FILE__, 'deactivate_clixsy_3rd_party_cf7');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-clixsy-3rd-party-cf7.php';

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


/**
 * Check for updates for autoupdates
 *
 * @since    1.0.0
 */



function check_for_updates($transient) {
	global $github_repo_url, $plugin_slug, $plugin_file;


	if (empty($transient->checked)) {
		return $transient;
	}


	$response = wp_remote_get($github_repo_url);

	if (is_wp_error($response)) {
		return $transient;
	}

	$response = json_decode(wp_remote_retrieve_body($response));

	if (!isset($response->tag_name) || !isset($response->zipball_url)) {
		return $transient;
	}

	if (version_compare(CLIXSY_3RD_party_CF7_VERSION, $response->tag_name, '<')) {
		$transient->response[plugin_basename(__FILE__)] = (object) array(
			'new_version' => $response->tag_name,
			'package'     => $response->zipball_url,
			'slug'        => $plugin_slug,
			'tested'      => '6.3.1',  // latest WordPress version the plugin has been tested with
			'requires'    => '5.0',    // minimum WordPress version required for the plugin
			'last_updated' => date('Y-m-d'), // the date of the last update
			'sections'    => array(
				'description' => '<strong>Description:</strong><br>This plugin allows you to ...',
				'changelog'   => '<strong>1.1.0.7:</strong><br> - Fixed a bug ...'
			)
		);
	}

	return $transient;
}
add_filter('pre_set_site_transient_update_plugins', 'check_for_updates');



add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_clear_transient_link');

function add_clear_transient_link($links) {
	$mylinks = array(
		'<a href="' . admin_url('plugins.php?clear_my_transient=true') . '">Clear Transient</a>',
	);
	return array_merge($links, $mylinks);
}

if (isset($_GET['clear_my_transient']) && $_GET['clear_my_transient'] === 'true') {
	delete_site_transient('update_plugins');
}

function rename_github_zip($source, $remote_source, $upgrader, $hook_extra) {
	global $plugin_slug;

	// Ensure is plugin update and is the plugin in question
	if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $plugin_slug) {
			return $source;
	}

	$desired_directory_name = 'clixsy-3rd-party-plugin';
	$path_parts = explode('/', rtrim($source, '/'));  // Trim trailing slash if present
	$last_path = end($path_parts);
	
	if (empty($last_path)) {
			array_pop($path_parts);
			$last_path = end($path_parts);
	}

	if ($last_path !== $desired_directory_name) {
			$path_parts[count($path_parts) - 1] = $desired_directory_name;
			$new_source = implode('/', $path_parts);
			
			if (!is_dir($source) || is_dir($new_source)) {
					error_log("Either $source doesn't exist or $new_source already exists.");
					return $source;
			}

			if (!rename($source, $new_source)) {
					error_log("Failed to rename $source to $new_source");
					return $source;
			}
			
			return $new_source;
	}

	return $source;
}
add_filter('upgrader_source_selection', 'rename_github_zip', 10, 4);


