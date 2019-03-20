<?php

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
function ht_customizer( $wp_customize ) {

function ht_theme_widthmode() {
    	return;
}


	/**
 	* Homepage Section
 	*/
    $wp_customize->add_section( 'ht_homepage', array(
		'title' => __( 'Homepage', 'helpguru' ),
		'description' => '',
		'priority' => 50,
	));

	// HP - Headline
	$wp_customize->add_setting('ht_hp_headline', array(
		'default'  => __( 'The self-service support theme', 'helpguru' ),
	));

	$wp_customize->add_control('ht_hp_headline',	array(
		'type' => 'text',
		'label' => __( 'Headline', 'helpguru' ),
		'section' => 'ht_homepage',
		'settings'   => 'ht_hp_headline',
	));

	// HP - Tagline
	$wp_customize->add_setting( 'ht_hp_tagline', array(
		'default' => __( 'A premium WordPress theme with integrated Knowledge Base, providing 24/7 community based support.', 'helpguru' ),
	) );
	 
	$wp_customize->add_control('ht_hp_tagline', array(
		'type' => 'textarea',
		'label'   => __( 'Tagline', 'helpguru' ),
		'section' => 'ht_homepage',
		'settings'   => 'ht_hp_tagline',
	) );

	/**
 	* Posts Section
 	*/
    $wp_customize->add_section( 'ht_blog', array(
		'title' => __( 'Blog', 'helpguru' ),
		'description' => '',
		'priority' => 50,
	));

	// Posts - Page Header Tagline
	$wp_customize->add_setting( 'ht_blog_tagline', array(
		'default'        => __( 'Important news from the super support team.', 'helpguru' ),
	) );
	 
	$wp_customize->add_control( 'ht_blog_tagline', array(
		'type' => 'textarea',
		'label'   => __( 'Blog Tagline', 'helpguru' ),
		'section' => 'ht_blog',
		'settings'   => 'ht_blog_tagline',
	) );

	// Posts - Sidebar Position
    $wp_customize->add_setting( 'ht_blog_sidebar', array(
            'default'        => 'sidebar-right',
    ) );

    $wp_customize->add_control( new HT_Layout_Picker_Custom_Control( $wp_customize, 'ht_blog_sidebar', array(
        'label'   => __( 'Sidebar Position', 'helpguru' ),
        'section' => 'ht_blog',
        'settings'   => 'ht_blog_sidebar',
     ) ) );

    $wp_customize->add_setting('logo_placement', array('default' => 'left') );
	 

	/**
 	* KB Section
 	*/
    $wp_customize->add_section( 'ht_kb', array(
		'title' => __( 'Knowledge Base', 'helpguru' ),
		'description' => '',
		'priority' => 50,
	));

	// KB - Page Title
	$wp_customize->add_setting('ht_kb_title', array(
		'default'  => __( 'Knowledge Base', 'helpguru' ),
	));

	$wp_customize->add_control('ht_kb_title',	array(
		'type' => 'text',
		'label' => __( 'KB Title', 'helpguru' ),
		'section' => 'ht_kb',
		'settings'   => 'ht_kb_title',
	));

    // KB - Page Header Tagline
	$wp_customize->add_setting( 'ht_kb_tagline', array(
		'default' => __( 'KB Tagline', 'helpguru' ),
	) );
	 
	$wp_customize->add_control( 'ht_kb_tagline', array(
		'type' => 'textarea',
		'label'   => __( 'KB Tagline', 'helpguru' ),
		'section' => 'ht_kb',
		'settings'   => 'ht_kb_tagline',
	) );

	// KB - Sidebar Position
    $wp_customize->add_setting( 'ht_kb_sidebar', array(
            'default'        => 'sidebar-right',
    ) );
    $wp_customize->add_control( new HT_Layout_Picker_Custom_Control( $wp_customize, 'ht_kb_sidebar', array(
        'label'   => 'Sidebar Position',
        'section' => 'ht_kb',
        'settings'   => 'ht_kb_sidebar',
     ) ) );

	/**
 	* BBPress Section
 	*/
    $wp_customize->add_section( 'ht_bbpress', array(
		'title' => __( 'BBPress', 'helpguru' ),
		'description' => '',
		'priority' => 50,
	));

	// BBPress - Page Title
	$wp_customize->add_setting('ht_bbpress_title', array(
		'default'  => __( 'Community Forums', 'helpguru' ),
	));

	$wp_customize->add_control('ht_bbpress_title',	array(
		'type' => 'text',
		'label' => __( 'Forum Title', 'helpguru' ),
		'section' => 'ht_bbpress',
		'settings'   => 'ht_bbpress_title',
	));

	// BBPress - Page Header Tagline
	$wp_customize->add_setting( 'ht_bbpress_tagline', array(
		'default'  => __( 'Forum tagline', 'helpguru' ),
	) );
	 
	$wp_customize->add_control( 'ht_bbpress_tagline', array(
		'type' => 'textarea',
		'label'   => __( 'BBPress integrated forums, perfect for staff to customer interaction.', 'helpguru' ),
		'section' => 'ht_bbpress',
		'settings'   => 'ht_bbpress_tagline',
	) );

	// BBPress - Sidebar Position
    $wp_customize->add_setting( 'ht_bbpress_sidebar', array(
            'default'        => 'sidebar-right',
    ) );
    $wp_customize->add_control( new HT_Layout_Picker_Custom_Control( $wp_customize, 'ht_bbpress_sidebar', array(
        'label'   => 'Sidebar Position',
        'section' => 'ht_bbpress',
        'settings'   => 'ht_bbpress_sidebar',
     ) ) );


	/**
 	* Header Section
 	*/
	$wp_customize->add_section('ht_header', array(
		'title' => __( 'Header', 'helpguru' ),
		'description' => '',
		'priority' => 30,
	) );
	
	$wp_customize->add_setting( 'blogname', array(
		'default'    => get_option( 'blogname' ),
		'type'       => 'option',
		'capability' => 'manage_options',
	) );

	$wp_customize->add_control( 'blogname', array(
		'label'      => __( 'Site Title', 'helpguru' ),
		'section'    => 'ht_header',
	) );

	$wp_customize->add_setting( 'blogdescription', array(
		'default'    => get_option( 'blogdescription' ),
		'type'       => 'option',
		'capability' => 'manage_options',
	) );

	$wp_customize->add_control( 'blogdescription', array(
		'label'      => __( 'Tagline', 'helpguru' ),
		'section'    => 'ht_header',
	) );
	
	
	// Add logo to Site Title & Tagline Section
	$wp_customize->add_setting( 'ht_site_logo', array('default' => get_template_directory_uri() . '/images/logo.png') );
 
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ht_site_logo', array(
		'label' => __( 'Site Logo', 'helpguru' ),
		'section' => 'ht_header',
		'settings' => 'ht_site_logo')
	) );

	
	/**
 	* Footer Section
 	*/
    $wp_customize->add_section('ht_footer', array(
		'title' => __( 'Footer', 'helpguru' ),
		'description' => '',
		'priority' => 35,
	) );
	
	// site coypright
	$wp_customize->add_setting( 'ht_copyright', array(
		'default'        => __( '&copy; Copyright HelpGuru. Powered by <a href="http://herothemes.com">HeroThemes</a>.', 'helpguru' ),
	) );
	 
	$wp_customize->add_control( 'ht_copyright', array(
		'type' => 'textarea',
		'label'   => __( 'Site Copyright', 'helpguru' ),
		'section' => 'ht_footer',
		'settings'   => 'ht_copyright',
	) );	
	


// Panel: Styling
$wp_customize->add_panel( 'ht_panel_styling', array(
    'priority'       => 10,
    'title'          => __( 'Theme Styling', 'helpguru' ),
) );

// Panel: Styling || Section: Width
$wp_customize->add_section( 'ht_section_width', array(
    'priority'       => 10,
    'title'          => __( 'Theme Width', 'helpguru' ),
    'description'    => '',
    'panel'  => 'ht_panel_styling',
) );

// Panel: Styling || Section: xxx || Setting: xxx
$wp_customize->add_setting('ht_setting_widthmode', array(
	'default'  => 'fullwidth',
));
$wp_customize->add_control('ht_setting_widthmode',	array(
	'section' => 'ht_section_width',
	'type' => 'radio',
	'label' => __( 'Forum Title', 'helpguru' ),
	'choices' => array(
                    'fullwidth' => 'Fullwidth',
                    'boxed' => 'Boxed',
                ),
));

// Panel: Styling || Section: xxx || Setting: xxx
$wp_customize->add_setting( 'ht_setting_themewidth', array( 'default' => 1200 ) );
$wp_customize->add_control( 'ht_setting_themewidth', array(
	'section'		=> 'ht_section_width',
    'type'			=> 'range',
    'priority'		=> 10,
    'label'			=> __( 'Site Max Width (px)', 'helpguru' ),
    'description' 	=> __( 'This is the range control description.', 'helpguru' ),
    'input_attrs' 	=> array(
        'min'   => 920,
        'max'   => 2000,
        'step'  => 1,
    ),
    'active_callback' => 'is_front_page',
) );

// Panel: Styling || Section: Background  
    $wp_customize->add_section('ht_section_bg', array(
        'title' => __( 'Background', 'helpguru' ),
        'description' => __( 'This section only applies when using the boxed layout.', 'helpguru' ),
        'priority' => 10,
        'panel'  => 'ht_panel_styling',
        )
    );

        // Panel: Styling || Section: Background || Setting: Custom Background (Image)
        $wp_customize->add_section( 'background_image', array(
            'title'          => __( 'Background', 'helpguru' ),
            'theme_supports' => 'custom-background',
            'priority'       => 80,
            'panel'  => 'ht_panel_styling',
        ) );

        $wp_customize->add_setting( 'background_image', array(
            'default'        => get_theme_support( 'custom-background', 'default-image' ),
            'theme_supports' => 'custom-background',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_setting( new WP_Customize_Background_Image_Setting( $wp_customize, 'background_image_thumb', array(
            'theme_supports' => 'custom-background',
            'sanitize_callback' => 'sanitize_text_field',
        ) ) );

        $wp_customize->add_control( new WP_Customize_Background_Image_Control( $wp_customize ) );

        $wp_customize->add_setting( 'background_repeat', array(
            'default'        => 'repeat',
            'theme_supports' => 'custom-background',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'background_repeat', array(
            'label'      => __( 'Background Repeat', 'helpguru' ),
            'section'    => 'background_image',
            'type'       => 'radio',
            'choices'    => array(
                'no-repeat'  => __('No Repeat', 'helpguru'),
                'repeat'     => __('Tile', 'helpguru'),
                'repeat-x'   => __('Tile Horizontally', 'helpguru'),
                'repeat-y'   => __('Tile Vertically', 'helpguru'),
            ),
        ) );

        $wp_customize->add_setting( 'background_position_x', array(
            'default'        => 'left',
            'theme_supports' => 'custom-background',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'background_position_x', array(
            'label'      => __( 'Background Position', 'helpguru' ),
            'section'    => 'background_image',
            'type'       => 'radio',
            'choices'    => array(
                'left'       => __('Left', 'helpguru'),
                'center'     => __('Center', 'helpguru'),
                'right'      => __('Right', 'helpguru'),
            ),
        ) );

        $wp_customize->add_setting( 'background_attachment', array(
            'default'        => 'fixed',
            'theme_supports' => 'custom-background',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'background_attachment', array(
            'label'      => __( 'Background Attachment', 'helpguru' ),
            'section'    => 'background_image',
            'type'       => 'radio',
            'choices'    => array(
                'fixed'      => __('Fixed', 'helpguru'),
                'scroll'     => __('Scroll', 'helpguru'),
            ),
        ) );

        // Panel: Styling || Section: Background || Setting: Custom Background (Color)
        $wp_customize->add_setting( 'background_color', array(
            'default' => get_theme_support( 'custom-background', 'default-color' ),
            'theme_supports' => 'custom-background',
            'sanitize_callback' => 'sanitize_hex_color_no_hash',
            'sanitize_js_callback' => 'maybe_hash_hex_color',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
            'label' => __( 'Background Color', 'helpguru' ),
            'section' => 'colors',
            'section' => 'background_image',
        ) ) ); 

// Panel: Styling || Section: Custom  
    $wp_customize->add_section('ht_section_custom', array(
        'title' => __( 'Custom CSS', 'helpguru' ),
        'description' => '',
        'priority' => 100,
        'panel'  => 'ht_panel_styling',
        )
    );

        // Panel: Styling || Section: Custom  || Setting: Custom CSS

        $wp_customize->add_setting('ht_custom_css', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
            )
        );
        $wp_customize->add_control( 'ht_custom_css', array(
            'label' => __( 'Custom CSS', 'helpguru' ),
            'description' => 'Enter any Custom CSS here',
            'type'     => 'textarea',
            'section'  => 'ht_section_custom',
        ) );


	/**
 	* Styling Section
 	*/
	$wp_customize->add_section( 'ht_styling', array(
		'title' => __( 'Theme Colors', 'helpguru' ),
		'description' => '',
		'priority' => 40,
		'panel'  => 'ht_panel_styling',
	));

	// Link Color
	$wp_customize->add_setting( 'ht_theme_link', array('default' => '#32a3cb', 'sanitize_callback' => 'sanitize_hex_color' )	);
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ht_theme_link', array(
		'label'        => __( 'Link Color', 'helpguru' ),
		'section'    => 'ht_styling',
	) ) );

	// Link:Hover Color
	$wp_customize->add_setting('ht_theme_link_hover', array('default' => '#32a3cb','sanitize_callback' => 'sanitize_hex_color',)	);
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ht_theme_link_hover', array(
		'label'        => __( 'Link Color (Hover)', 'helpguru' ),
		'section'    => 'ht_styling',
	) ) );
	
	// Header BG Color
	$wp_customize->add_setting('ht_theme_header_bg', array('default' => '#2e97bd','sanitize_callback' => 'sanitize_hex_color',)	);
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ht_theme_header_bg', array(
		'label'        => __( 'Header BG Color', 'helpguru' ),
		'section'    => 'ht_styling',
	) ) );
	
	// Header Font Color
	$wp_customize->add_setting('ht_theme_header_fontcolor', array('default' => '#ffffff','sanitize_callback' => 'sanitize_hex_color',)	);
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ht_theme_header_fontcolor', array(
		'label'        => __( 'Header Font Color', 'helpguru' ),
		'section'    => 'ht_styling',
	) ) );

	// Page Header BG Color
	$wp_customize->add_setting('ht_theme_pageheader_bg', array('default' => '#32a3cb','sanitize_callback' => 'sanitize_hex_color',)	);
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ht_theme_pageheader_bg', array(
		'label'        => __( 'Page Header BG Color', 'helpguru' ),
		'section'    => 'ht_styling',
	) ) );
	
	// Page Header Font Color
	$wp_customize->add_setting('ht_theme_pageheader_fontcolor', array('default' => '#ffffff','sanitize_callback' => 'sanitize_hex_color',)	);
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ht_theme_pageheader_fontcolor', array(
		'label'        => __( 'Page Header Font Color', 'helpguru' ),
		'section'    => 'ht_styling',
	) ) );
	
}
add_action( 'customize_register', 'ht_customizer' );

function ht_customize_preview_js() {
	wp_enqueue_script( 'ht-theme-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '', true );
}
add_action( 'customize_preview_init', 'ht_customize_preview_js' );