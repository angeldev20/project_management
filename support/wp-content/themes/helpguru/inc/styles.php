<?php
/**
 * Enqueues styles for front-end.
 */
function ht_theme_styles() {
	
	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'ht-theme-style', get_stylesheet_directory_uri() . '/css/style.css' );


	/*
	 * Load HT KB Stylesheet
	 */
	if( class_exists( 'HT_Knowledge_Base' ) ){
		wp_enqueue_style( 'ht-kb', get_template_directory_uri() . '/css/ht-kb.css', array( 'ht-theme-style' ) );
	}
	
	// Add Google font, used in the main stylesheet.
	wp_enqueue_style( 'ht-google-font', ht_google_font_url(), array(), null );

	// Get theme customizer variables
	$ht_linkcolor = get_theme_mod( 'ht_theme_link', '#32a3cb' );
	$ht_linkcolor_hover = get_theme_mod( 'ht_theme_link_hover', '#32a3cb' );
	$ht_header_bg = get_theme_mod( 'ht_theme_header_bg', '#2e97bd' );
	$ht_header_color = get_theme_mod( 'ht_theme_header_fontcolor', '#ffffff' );
	$ht_pageheader_bg = get_theme_mod( 'ht_theme_pageheader_bg', '#32a3cb' );
	$ht_pageheader_color = get_theme_mod( 'ht_theme_pageheader_fontcolor', '#ffffff' );
	$ht_pageheader_color_rgb = ht_get_rgb_from_hex($ht_pageheader_color);
	$ht_setting_themewidth = get_theme_mod( 'ht_setting_themewidth', '1200' );
	$ht_custom_css = get_theme_mod( 'ht_custom_css' );

	// Add custom styles
	$custom_css = "
	a, 
	a:visited,
	.bbp-author-name {
		color: {$ht_linkcolor};
	}
	a:hover {
		color: {$ht_linkcolor_hover};
	}
	#site-header {
		background: $ht_header_bg;
	}
	@media screen and (max-width: 768px) {
		#nav-primary-menu {
			background: $ht_header_bg;
		}
	}
	#site-header,
	#site-header a,
	#site-header a:visited,
	#site-header a:hover,
	#nav-primary button {
		color:$ht_header_color;
	}
	
	#page-header {
		background: $ht_pageheader_bg;
	}
	#page-header,
	#page-header a,
	#page-header a:visited,
	#page-header a:hover,
	#page-header #page-header-title {
		color:$ht_pageheader_color;
	}
	#page-header #page-header-tagline {
    	color:rgba({$ht_pageheader_color_rgb['red']},{$ht_pageheader_color_rgb['green']},{$ht_pageheader_color_rgb['blue']},0.9);
    }
    #ht-site-container.ht-layout-boxed {
    	max-width: {$ht_setting_themewidth}px;
    	box-shadow: 0 0 55px rgba(0,0,0,0.15);
	}
	#homepage-features .hf-block i {
		color: $ht_linkcolor;
	}
	.hkb_widget_exit__btn {
		background: $ht_linkcolor;
	}

	$ht_custom_css
	";
	$custom_css = trim(preg_replace('/\s\s+/', ' ', $custom_css));
	if (!is_child_theme()) {
		wp_add_inline_style('ht-theme-style',$custom_css);
	} else {
		wp_add_inline_style('ht-childtheme-style',$custom_css);
	}
		
	
}
add_action( 'wp_enqueue_scripts', 'ht_theme_styles' );


/**
 * Register Google font
 */
function ht_google_font_url() {
	$font_url = '';
	/*
	 * Translators: If there are characters in your language that are not supported
	 * by this font, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Google font: on or off', 'helpguru' ) ) {
		$font_url = add_query_arg( 'family', 'Open+Sans:400italic,400,600,700|Nunito:400', "//fonts.googleapis.com/css" );
	}

	return $font_url;
}

/**
 * HT Webfonts - Font Stack
 */
function ht_webfonts_themedefaults($ht_webfonts) {
	$ht_webfonts[] = new HT_Custom_Webfont(
					array(
						'name' => 'Body',
						'selector' => 'body',
						'source' => 'gfonts',
						'family' => 'Open Sans',
						'style' => '400',
						'color' => '#393d40',
						'size'	=> '15',
						'height' => '24',
						'spacing' => '0'
						)
					);
	$ht_webfonts[] = new HT_Custom_Webfont(
					array(
						'name' => 'Headings',
						'selector' => 'h1,h2,h3,h4,h5,h6',
						'source' => 'gfonts',
						'family' => 'Nunito',
						'style' => '400',
						'color' => '#393d40',
						'size'	=> '',
						'height' => '',
						'spacing' => ''
						)
					);
	return $ht_webfonts;
}
//add_filter('ht_webfonts_themefonts', 'ht_webfonts_themedefaults');