<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://github.com/ClixsyDev
 * @since      1.0.0
 *
 * @package    Clixsy_3rd_party_Cf7
 * @subpackage Clixsy_3rd_party_Cf7/admin/partials
 */
ob_start();
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1>Test</h1>
<div class="wrap">
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php
			// This is how you render ACF fields
			acf_form(array(
        'post_id'       => 'theme-general-settings', // This is the options page
				'post_title'    => false,
				'field_groups' => ['group_1'],
        'submit_value'  => 'Save',
			)); 
			?>
	</div>