<?php

class Clixsy_3rd_Patrty_Cf7_Admin {
	private $plugin_name;
	private $version;
	private $github_repo_url = 'https://github.com/ClixsyDev/clixsy-3rd-party-plugin/releases/latest';
	private $plugin_slug = 'clixsy-3rd-party-cf7';
	private $plugin_file = 'clixsy-3rd-patrty-cf7.php';
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// register ACF fields
		$this->register_acf_fields();

		// Add the redirect action
		add_action('acf/save_post', array($this, 'redirect_after_save'), 1);
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_updates'));

	}

	/**
	 * Check for updates for autoupdates
	 *
	 * @since    1.0.0
	 */
	public function check_for_updates($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $response = wp_remote_get($this->github_repo_url);

    if (is_wp_error($response)) {
        return $transient;
    }

    $response = json_decode(wp_remote_retrieve_body($response));

    if (version_compare($this->version, $response->tag_name, '<')) {
        $transient->response[$this->plugin_slug . '/' . $this->plugin_file] = (object) array(
            'new_version' => $response->tag_name,
            'package'     => $response->zipball_url,
            'slug'        => $this->plugin_slug,
        );
    }

    return $transient;
}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clixsy_3rd_Patrty_Cf7_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clixsy_3rd_Patrty_Cf7_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/clixsy-3rd-patrty-cf7-admin.css', array(), $this->version, 'all');
		acf_enqueue_scripts();
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clixsy_3rd_Patrty_Cf7_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clixsy_3rd_Patrty_Cf7_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/clixsy-3rd-patrty-cf7-admin.js', array(''), $this->version, false);
	}




	public function create_plugin_admin_page() {
		add_menu_page(
			'CLIXSY 3rd party',
			'CLIXSY 3rd party',
			'manage_options',
			'clixsy-3rd-party',
			array($this, 'display_plugin_admin_page')
		);
	}

	public function display_plugin_admin_page() {
		acf_form_head();
		acf_form(array(
			'post_id'       => 'clixsy-3rd-party', // This is the options page
			'post_title'    => false,
			'field_groups' => ['group_1'],
			'submit_value'  => 'Update',
		));
	}

	public function wh_log($log_msg, $log_msg_info = '') {
		$log_filename = WP_CONTENT_DIR . "/litify-log/";
		if (!file_exists($log_filename)) {
			// create directory/folder uploads.
			mkdir($log_filename, 0777, true);
		}
		$log_file_data = $log_filename . 'log_' . date('Y-M-d') . '.log';
		// if you don't add `FILE_APPEND`, the file will be erased each time you add a log
		file_put_contents($log_file_data,  "\r\n" . ' ======================= Start ' . date('Y/m/d H:i:s') . ' ======================' . "\r\n" . $log_msg . "\r\n" . json_encode($log_msg_info) . "\r\n" . "\r\n" . ' ======================= end of log ======================' . "\n" . "\r\n", FILE_APPEND);
	}

	public function stringify($value) {
		if (is_array($value)) {
			return implode(', ', $value);
		} else {
			return (string) $value;
		}
	}

	public function webhook_integration_clixsy_cf7($contact_form) {
		$remove_litify_integration = get_field('remove_litify_integration', 'clixsy-3rd-party');

		if (!$remove_litify_integration) {
			$map_fields_and_forms = get_field('map_fields_and_forms', 'clixsy-3rd-party');
			$form_id = $contact_form->id();
			$submission = WPCF7_Submission::get_instance();
			$posted_data = $submission->get_posted_data();




			foreach ($map_fields_and_forms as $key => $fields_and_forms) {
				if (!$fields_and_forms['disable_single_integration']) {
					foreach ($fields_and_forms['select_form'] as $acf_form_id) {
						if ($acf_form_id == $form_id) {
							$auth_token = $fields_and_forms['authorization'];
							$url = $fields_and_forms['endpoint'];
							$content_type = $fields_and_forms['content_type']['label'];
							$curl = curl_init($url);
							curl_setopt_array($curl, [
								CURLOPT_URL => $url,
								CURLOPT_POST => true,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_VERBOSE => true,
								CURLOPT_HTTPHEADER => [
									$content_type,
									"Authorization: $auth_token"
								],
							]);


							// additional fields to array
							$user_agent = $_SERVER['HTTP_USER_AGENT'];
							$post_id = $posted_data['post_id'];
							$post_url = get_permalink($post_id);
							$post_name = get_post($post_id)->post_name;
							$post_title = get_the_title($post_id);
							$remote_ip = $_SERVER['REMOTE_ADDR'];
							$acf_mapped_fields = $fields_and_forms['fields'];
							$empty_arr = [];

							foreach ($acf_mapped_fields as $f_id => $acf_data) {
								$empty_arr[$acf_data['third_party_field']] = $acf_data['default_value'] ?: '';
							}

							foreach ($posted_data as $p_id => $post_data) {
								foreach ($acf_mapped_fields as $f_id => $acf_data) {
									if ($acf_data['form_submission_field'] == '_post_url') {
										$empty_arr[$acf_data['third_party_field']] = $post_url;
									} else if ($acf_data['form_submission_field'] == '_user_agent') {
										$empty_arr[$acf_data['third_party_field']] = $user_agent;
									} else if ($acf_data['form_submission_field'] == '_post_id') {
										$empty_arr[$acf_data['third_party_field']] = $post_id;
									} else if ($acf_data['form_submission_field'] == '_post_name') {
										$empty_arr[$acf_data['third_party_field']] = $post_name;
									} else if ($acf_data['form_submission_field'] == '_post_title') {
										$empty_arr[$acf_data['third_party_field']] = $post_title;
									} else if ($acf_data['form_submission_field'] == '_remote_ip') {
										$empty_arr[$acf_data['third_party_field']] = $remote_ip;
									} else if ($p_id == $acf_data['form_submission_field']) {
										$preppedned_value = !empty($acf_data['preppended_value']) ? $acf_data['preppended_value'] : '';
										$empty_arr[$acf_data['third_party_field']] = $preppedned_value . $this->stringify($post_data);
										break;
									}
								}
							}

							curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($empty_arr));

							// Uncomment the following lines for debugging only!
							// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
							// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

							$resp = curl_exec($curl);
							$responseInfo = curl_getinfo($curl);
							curl_close($curl);

							// logging everything
							$this->wh_log($resp, $responseInfo);
						}
					}
				}
			}
		}
	}

	// public function webhook_integration_clixsy_ninja($form_data) {
	// 	$remove_litify_integration = get_field('remove_litify_integration', 'clixsy-3rd-party');

	// 	if (!$remove_litify_integration) {
	// 		$map_fields_and_forms = get_field('map_fields_and_forms', 'clixsy-3rd-party');
	// 		$form_id = $form_data['form_id'];
	// 		$field_values = [];

	// 		foreach ($form_data['fields'] as $field) {
	// 			$field_values[$field['key']] = $field['value'];
	// 		}


	// 		foreach ($map_fields_and_forms as $key => $fields_and_forms) {
	// 			if (!$fields_and_forms['disable_single_integration']) {
	// 				foreach ($fields_and_forms['select_form'] as $acf_form_id) {
	// 					if ($acf_form_id == $form_id) {
	// 						$url = $fields_and_forms['endpoint'];
	// 						$content_type = $fields_and_forms['content_type']['label'];
	// 						$curl = curl_init($url);
	// 						curl_setopt_array($curl, [
	// 							CURLOPT_URL => $url,
	// 							CURLOPT_POST => true,
	// 							CURLOPT_RETURNTRANSFER => true,
	// 							CURLOPT_VERBOSE => true,
	// 							CURLOPT_HTTPHEADER => [
	// 								$content_type
	// 							],
	// 						]);


	// 						// additional fields to array
	// 						$user_agent = $_SERVER['HTTP_USER_AGENT'];
	// 						$post_id = $field_values['post_id'];
	// 						$post_url = get_permalink($post_id);
	// 						$post_name = get_post($post_id)->post_name;
	// 						$post_title = get_the_title($post_id);
	// 						$remote_ip = $_SERVER['REMOTE_ADDR'];
	// 						$acf_mapped_fields = $fields_and_forms['fields'];
	// 						$empty_arr = [];

	// 						foreach ($acf_mapped_fields as $f_id => $acf_data) {
	// 							$empty_arr[$acf_data['third_party_field']] = $acf_data['default_value'] ?: '';
	// 						}

	// 						foreach ($field_values as $p_id => $post_data) {
	// 							foreach ($acf_mapped_fields as $f_id => $acf_data) {
	// 								if ($acf_data['form_submission_field'] == '_post_url') {
	// 									$empty_arr[$acf_data['third_party_field']] = $post_url;
	// 								} else if ($acf_data['form_submission_field'] == '_user_agent') {
	// 									$empty_arr[$acf_data['third_party_field']] = $user_agent;
	// 								} else if ($acf_data['form_submission_field'] == '_post_id') {
	// 									$empty_arr[$acf_data['third_party_field']] = $post_id;
	// 								} else if ($acf_data['form_submission_field'] == '_post_name') {
	// 									$empty_arr[$acf_data['third_party_field']] = $post_name;
	// 								} else if ($acf_data['form_submission_field'] == '_post_title') {
	// 									$empty_arr[$acf_data['third_party_field']] = $post_title;
	// 								} else if ($acf_data['form_submission_field'] == '_remote_ip') {
	// 									$empty_arr[$acf_data['third_party_field']] = $remote_ip;
	// 								} else if ($p_id == $acf_data['form_submission_field']) {
	// 									$preppedned_value = !empty($acf_data['preppended_value']) ? $acf_data['preppended_value'] : '';
	// 									$empty_arr[$acf_data['third_party_field']] = $preppedned_value . $this->stringify($post_data);
	// 									break;
	// 								}
	// 							}
	// 						}

	// 						curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($empty_arr));

	// 						// Uncomment the following lines for debugging only!
	// 						// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	// 						// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	// 						$resp = curl_exec($curl);
	// 						$responseInfo = curl_getinfo($curl);
	// 						curl_close($curl);

	// 						// logging everything
	// 						$this->wh_log($resp, $responseInfo);
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	public function register_acf_fields() {
		if (function_exists('acf_add_local_field_group')) {
			acf_add_local_field_group(array(
				'key' => 'group_1',
				'title' => 'My Group',
				'fields' => array(
					array(
						'key' =>  'remove_litify_integration_key_sdklzjdasf12',
						'name' =>  'remove_litify_integration',
						'label' => 'Remove integration?',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
					),
					array(
						'key' => 'field_63811ac830c531',
						'label' => 'Map fields and forms',
						'name' => 'map_fields_and_forms',
						'type' => 'repeater',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'collapsed' => '',
						'min' => 0,
						'max' => 0,
						'layout' => 'block',
						'button_label' => 'Add new endpoint',
						'sub_fields' => array(
							array(
								'key' => 'field_123oierwalkdjfw',
								'name' => 'disable_single_integration',
								'label' => 'Disable integration',
								'type' => 'true_false',
								'instructions' => '',
								'required' => 0,
								'wrapper' => array(
									'width' => '30',
									'class' => '',
									'id' => '',
								),
							),
							array(
								'key' => 'field_adsfkaqoiwe1',
								'name' => 'form_submitssion_name_optional',
								'label' => 'Name',
								'type' => 'text',
								'instructions' => 'Use this field for instructions or additional info.',
								'wrapper' => array(
									'width' => '70',
									'class' => '',
									'id' => '',
								),
							),
							array(
								'key' => 'field_63811aee30c55',
								'label' => 'Endpoint',
								'name' => 'endpoint',
								'type' => 'text',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_6499be4621d72a',
								'label' => 'Content type',
								'name' => 'content_type',
								'type' => 'select',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'json' => 'Content-Type: application/json',
									'urlencoded' => 'Content-Type: application/x-www-form-urlencoded',
								),
								'default_value' => false,
								'allow_null' => 0,
								'multiple' => 0,
								'ui' => 0,
								'return_format' => 'array',
								'ajax' => 0,
								'placeholder' => '',
							),

							array(
								'key' => 'field_63811a8fb1ed25',
								'label' => 'Select form',
								'name' => 'select_form',
								'type' => 'relationship',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'post_type' => array(
									0 => 'wpcf7_contact_form',
								),
								'taxonomy' => 0,
								'allow_null' => 0,
								'multiple' => 0,
								'return_format' => 'id',
								'ui' => 1,
							),
							array(
								'key' => 'field_63823411aee30c55',
								'label' => 'Authorization',
								'name' => 'authorization',
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '50',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_64d67e319ebde8',
								'label' => 'Map fields',
								'name' => '',
								'type' => 'accordion',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'open' => 0,
								'multi_expand' => 0,
								'endpoint' => 0,
							),
							array(
								'key' => 'field_63811adc30c54',
								'label' => 'Fields',
								'name' => 'fields',
								'type' => 'repeater',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'collapsed' => '',
								'min' => 0,
								'max' => 0,
								'layout' => 'table',
								'button_label' => 'Add new field',
								'sub_fields' => array(
									array(
										'key' => 'field_63811b0b30c56',
										'label' => 'Form submission field',
										'name' => 'form_submission_field',
										'type' => 'text',
										'instructions' => '',
										'required' => 0,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
									array(
										'key' => 'field_63811b2a30c57',
										'label' => '3-rd party field',
										'name' => 'third_party_field',
										'type' => 'text',
										'instructions' => '',
										'required' => 0,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
									array(
										'key' => 'default_63811b2a30c57',
										'label' => 'Value',
										'name' => 'default_value',
										'type' => 'text',
										'instructions' => '',
										'required' => 0,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
									array(
										'key' => 'default_6381123121b2a30c57',
										'label' => 'Preppended value',
										'name' => 'preppended_value',
										'type' => 'text',
										'instructions' => '',
										'required' => 0,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
									),
								),
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'clixsy-3rd-party',
						),
					),
				),
			));
		}
	}

	function redirect_after_save($post_id) {
		// Check if it's the options page
		if ($post_id === 'clixsy-3rd-party') {
			echo 'Updating data...';
			// Use JavaScript for redirection.
			echo '<script>setTimeout(function(){ window.location.href="' . admin_url('admin.php?page=clixsy-3rd-party') . '"; }, 1000);</script>';
		}
	}
}
