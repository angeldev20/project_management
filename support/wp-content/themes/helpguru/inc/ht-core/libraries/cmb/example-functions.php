<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'cmb_sample_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function cmb_sample_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_cmb_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$meta_boxes['test_metabox'] = array(
		'id'         => 'test_metabox',
		'title'      => __( 'Test Metabox', 'helpguru', 'helpguru' ),
		'pages'      => array( 'page', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
				'name'       => __( 'Test Text', 'helpguru', 'helpguru' ),
				'desc'       => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'         => $prefix . 'test_text',
				'type'       => 'text',
				'show_on_cb' => 'cmb_test_text_show_on_cb', // function should return a bool value
				// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
				// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
				// 'on_front'        => false, // Optionally designate a field to wp-admin only
				// 'repeatable'      => true,
			),
			array(
				'name' => __( 'Test Text Small', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textsmall',
				'type' => 'text_small',
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Test Text Medium', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textmedium',
				'type' => 'text_medium',
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Website URL', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'url',
				'type' => 'text_url',
				// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Test Text Email', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'email',
				'type' => 'text_email',
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Test Time', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_time',
				'type' => 'text_time',
			),
			array(
				'name' => __( 'Time zone', 'helpguru', 'helpguru' ),
				'desc' => __( 'Time zone', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'timezone',
				'type' => 'select_timezone',
			),
			array(
				'name' => __( 'Test Date Picker', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textdate',
				'type' => 'text_date',
			),
			array(
				'name' => __( 'Test Date Picker (UNIX timestamp)', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textdate_timestamp',
				'type' => 'text_date_timestamp',
				// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
			),
			array(
				'name' => __( 'Test Date/Time Picker Combo (UNIX timestamp)', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_datetime_timestamp',
				'type' => 'text_datetime_timestamp',
			),
			// This text_datetime_timestamp_timezone field type
			// is only compatible with PHP versions 5.3 or above.
			// Feel free to uncomment and use if your server meets the requirement
			// array(
			// 	'name' => __( 'Test Date/Time Picker/Time zone Combo (serialized DateTime object)', 'cmb' ),
			// 	'desc' => __( 'field description (optional)', 'cmb' ),
			// 	'id'   => $prefix . 'test_datetime_timestamp_timezone',
			// 	'type' => 'text_datetime_timestamp_timezone',
			// ),
			array(
				'name' => __( 'Test Money', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textmoney',
				'type' => 'text_money',
				// 'before'     => '£', // override '$' symbol if needed
				// 'repeatable' => true,
			),
			array(
				'name'    => __( 'Test Color Picker', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'test_colorpicker',
				'type'    => 'colorpicker',
				'default' => '#ffffff'
			),
			array(
				'name' => __( 'Test Text Area', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textarea',
				'type' => 'textarea',
			),
			array(
				'name' => __( 'Test Text Area Small', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textareasmall',
				'type' => 'textarea_small',
			),
			array(
				'name' => __( 'Test Text Area for Code', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_textarea_code',
				'type' => 'textarea_code',
			),
			array(
				'name' => __( 'Test Title Weeeee', 'helpguru', 'helpguru' ),
				'desc' => __( 'This is a title description', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_title',
				'type' => 'title',
			),
			array(
				'name'    => __( 'Test Select', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'test_select',
				'type'    => 'select',
				'options' => array(
					'standard' => __( 'Option One', 'helpguru', 'helpguru' ),
					'custom'   => __( 'Option Two', 'helpguru', 'helpguru' ),
					'none'     => __( 'Option Three', 'helpguru', 'helpguru' ),
				),
			),
			array(
				'name'    => __( 'Test Radio inline', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'test_radio_inline',
				'type'    => 'radio_inline',
				'options' => array(
					'standard' => __( 'Option One', 'helpguru', 'helpguru' ),
					'custom'   => __( 'Option Two', 'helpguru', 'helpguru' ),
					'none'     => __( 'Option Three', 'helpguru', 'helpguru' ),
				),
			),
			array(
				'name'    => __( 'Test Radio', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'test_radio',
				'type'    => 'radio',
				'options' => array(
					'option1' => __( 'Option One', 'helpguru', 'helpguru' ),
					'option2' => __( 'Option Two', 'helpguru', 'helpguru' ),
					'option3' => __( 'Option Three', 'helpguru', 'helpguru' ),
				),
			),
			array(
				'name'     => __( 'Test Taxonomy Radio', 'helpguru', 'helpguru' ),
				'desc'     => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'       => $prefix . 'text_taxonomy_radio',
				'type'     => 'taxonomy_radio',
				'taxonomy' => 'category', // Taxonomy Slug
				// 'inline'  => true, // Toggles display to inline
			),
			array(
				'name'     => __( 'Test Taxonomy Select', 'helpguru', 'helpguru' ),
				'desc'     => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'       => $prefix . 'text_taxonomy_select',
				'type'     => 'taxonomy_select',
				'taxonomy' => 'category', // Taxonomy Slug
			),
			array(
				'name'     => __( 'Test Taxonomy Multi Checkbox', 'helpguru', 'helpguru' ),
				'desc'     => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'       => $prefix . 'test_multitaxonomy',
				'type'     => 'taxonomy_multicheck',
				'taxonomy' => 'post_tag', // Taxonomy Slug
				// 'inline'  => true, // Toggles display to inline
			),
			array(
				'name' => __( 'Test Checkbox', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_checkbox',
				'type' => 'checkbox',
			),
			array(
				'name'    => __( 'Test Multi Checkbox', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'test_multicheckbox',
				'type'    => 'multicheck',
				'options' => array(
					'check1' => __( 'Check One', 'helpguru', 'helpguru' ),
					'check2' => __( 'Check Two', 'helpguru', 'helpguru' ),
					'check3' => __( 'Check Three', 'helpguru', 'helpguru' ),
				),
				// 'inline'  => true, // Toggles display to inline
			),
			array(
				'name'    => __( 'Test wysiwyg', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'test_wysiwyg',
				'type'    => 'wysiwyg',
				'options' => array( 'textarea_rows' => 5, ),
			),
			array(
				'name' => __( 'Test Image', 'helpguru', 'helpguru' ),
				'desc' => __( 'Upload an image or enter a URL.', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_image',
				'type' => 'file',
			),
			array(
				'name'         => __( 'Multiple Files', 'helpguru', 'helpguru' ),
				'desc'         => __( 'Upload or add multiple images/attachments.', 'helpguru', 'helpguru' ),
				'id'           => $prefix . 'test_file_list',
				'type'         => 'file_list',
				'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
			),
			array(
				'name' => __( 'oEmbed', 'helpguru', 'helpguru' ),
				'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'test_embed',
				'type' => 'oembed',
			),
		),
	);

	/**
	 * Metabox to be displayed on a single page ID
	 */
	$meta_boxes['about_page_metabox'] = array(
		'id'         => 'about_page_metabox',
		'title'      => __( 'About Page Metabox', 'helpguru', 'helpguru' ),
		'pages'      => array( 'page', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'show_on'    => array( 'key' => 'id', 'value' => array( 2, ), ), // Specific post IDs to display this metabox
		'fields'     => array(
			array(
				'name' => __( 'Test Text', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . '_about_test_text',
				'type' => 'text',
			),
		)
	);

	/**
	 * Repeatable Field Groups
	 */
	$meta_boxes['field_group'] = array(
		'id'         => 'field_group',
		'title'      => __( 'Repeating Field Group', 'helpguru', 'helpguru' ),
		'pages'      => array( 'page', ),
		'fields'     => array(
			array(
				'id'          => $prefix . 'repeat_group',
				'type'        => 'group',
				'description' => __( 'Generates reusable form entries', 'helpguru', 'helpguru' ),
				'options'     => array(
					'group_title'   => __( 'Entry {#}', 'helpguru', 'helpguru' ), // {#} gets replaced by row number
					'add_button'    => __( 'Add Another Entry', 'helpguru', 'helpguru' ),
					'remove_button' => __( 'Remove Entry', 'helpguru', 'helpguru' ),
					'sortable'      => true, // beta
				),
				// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
				'fields'      => array(
					array(
						'name' => 'Entry Title',
						'id'   => 'title',
						'type' => 'text',
						// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
					),
					array(
						'name' => 'Description',
						'description' => 'Write a short description for this entry',
						'id'   => 'description',
						'type' => 'textarea_small',
					),
					array(
						'name' => 'Entry Image',
						'id'   => 'image',
						'type' => 'file',
					),
					array(
						'name' => 'Image Caption',
						'id'   => 'image_caption',
						'type' => 'text',
					),
				),
			),
		),
	);

	/**
	 * Metabox for the user profile screen
	 */
	$meta_boxes['user_edit'] = array(
		'id'         => 'user_edit',
		'title'      => __( 'User Profile Metabox', 'helpguru', 'helpguru' ),
		'pages'      => array( 'user' ), // Tells CMB to use user_meta vs post_meta
		'show_names' => true,
		'cmb_styles' => false, // Show cmb bundled styles.. not needed on user profile page
		'fields'     => array(
			array(
				'name'     => __( 'Extra Info', 'helpguru', 'helpguru' ),
				'desc'     => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'       => $prefix . 'exta_info',
				'type'     => 'title',
				'on_front' => false,
			),
			array(
				'name'    => __( 'Avatar', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'avatar',
				'type'    => 'file',
				'save_id' => true,
			),
			array(
				'name' => __( 'Facebook URL', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'facebookurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Twitter URL', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'twitterurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Google+ URL', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'googleplusurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Linkedin URL', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'linkedinurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'User Field', 'helpguru', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'   => $prefix . 'user_text_field',
				'type' => 'text',
			),
		)
	);

	/**
	 * Metabox for an options page. Will not be added automatically, but needs to be called with
	 * the `cmb_metabox_form` helper function. See wiki for more info.
	 */
	$meta_boxes['options_page'] = array(
		'id'      => 'options_page',
		'title'   => __( 'Theme Options Metabox', 'helpguru', 'helpguru' ),
		'show_on' => array( 'key' => 'options-page', 'value' => array( $prefix . 'theme_options', ), ),
		'fields'  => array(
			array(
				'name'    => __( 'Site Background Color', 'helpguru', 'helpguru' ),
				'desc'    => __( 'field description (optional)', 'helpguru', 'helpguru' ),
				'id'      => $prefix . 'bg_color',
				'type'    => 'colorpicker',
				'default' => '#ffffff'
			),
		)
	);

	// Add other metaboxes as needed

	return $meta_boxes;
}

add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}
