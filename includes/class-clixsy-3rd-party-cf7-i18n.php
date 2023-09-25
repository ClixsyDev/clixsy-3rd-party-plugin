<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://github.com/ClixsyDev
 * @since      1.0.0
 *
 * @package    Clixsy_3rd_party_Cf7
 * @subpackage Clixsy_3rd_party_Cf7/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Clixsy_3rd_party_Cf7
 * @subpackage Clixsy_3rd_party_Cf7/includes
 * @author     Bogdan Zakharchyshyn <facepalmua@gmail.com>
 */
class Clixsy_3rd_party_Cf7_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'clixsy-3rd-party-cf7',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
