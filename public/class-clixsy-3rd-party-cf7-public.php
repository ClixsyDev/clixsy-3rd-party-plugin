<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://github.com/ClixsyDev
 * @since      1.0.0
 *
 * @package    Clixsy_3rd_party_Cf7
 * @subpackage Clixsy_3rd_party_Cf7/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Clixsy_3rd_party_Cf7
 * @subpackage Clixsy_3rd_party_Cf7/public
 * @author     Bogdan Zakharchyshyn <facepalmua@gmail.com>
 */
class Clixsy_3rd_party_Cf7_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/clixsy-3rd-party-cf7-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/clixsy-3rd-party-cf7-public.js', false, $this->version, false);



		// Get the current page ID
		global $post;
		$page_id = is_object($post) ? $post->ID : null;

		// Localize the script with new data
		$translation_array = array(
			'page_id' => $page_id,
		);
		wp_localize_script($this->plugin_name, 'clixsy_js_data', $translation_array);

		$remove_mask_for_phone = get_field('remove_mask_for_phone', 'clixsy-3rd-party');

		if ($remove_mask_for_phone != 1) {
			wp_enqueue_script($this->plugin_name.'_phone-mask', plugin_dir_url(__FILE__) . 'js/clixsy-3rd-party-cf7-public__phone-mask.js', false, $this->version, false);
		}
	}
}
