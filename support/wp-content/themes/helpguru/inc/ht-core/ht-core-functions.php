<?php
/**
* HT Core Functions
*/

/*
* add ie conditional html5 shim to header
*/
function ht_core_html5_shim () {
    echo '<!--[if lt IE 9]>';
    echo '<script src="'. get_template_directory_uri() .'/inc/ht-core/js/html5.js"></script>';
    echo '<![endif]-->';
}
add_action('wp_head', 'ht_core_html5_shim');	

/*
* add ie 6-8 conditional selectivizr to header
*/
function ht_core_selectivizr () {
    echo '<!--[if (gte IE 6)&(lte IE 8)]>';
    echo '<script src="'. get_template_directory_uri() .'/inc/ht-core/js/selectivizr-min.js"></script>';
    echo '<![endif]-->';
}
add_action('wp_head', 'ht_core_selectivizr');

/*
* add browser body class
*/
add_filter('body_class','ht_browser_body_class');
function ht_browser_body_class($classes) {
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

    if($is_lynx) $classes[] = 'lynx';
    elseif($is_gecko) $classes[] = 'gecko';
    elseif($is_opera) $classes[] = 'opera';
    elseif($is_NS4) $classes[] = 'ns4';
    elseif($is_safari) $classes[] = 'safari';
    elseif($is_chrome) $classes[] = 'chrome';
    elseif($is_IE) $classes[] = 'ie';
    else $classes[] = 'unknown';

    if($is_iphone) $classes[] = 'iphone';
    return $classes;
}

// Allow Shortcodes in widgets
add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

/**
 * Modify Exsisting Widgets
 */
// Change default widget tag cloud settings
 function ht_custom_tag_cloud_widget($args) {
	$args['largest'] = 16;
	$args['smallest'] = 10;
	$args['unit'] = 'px';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'ht_custom_tag_cloud_widget' );

// Remove () from category counts
add_filter('wp_list_categories', 'ht_custom_category_widget');
function ht_custom_category_widget($links) {
$links = str_replace('</a> (', '</a> <span>', $links);
$links = str_replace(')', '</span>', $links);
return $links;
}

/*
* Improve default excerpts
*/

// Increase length
function ht_custom_excerpt_length( $length ) {
    return 50;
}
add_filter( 'excerpt_length', 'ht_custom_excerpt_length' );

/*
* Remove recent comments styling from being in the <head>
*/
function ht_remove_recent_comments_style() {
    global $wp_widget_factory;
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}
add_action('widgets_init', 'ht_remove_recent_comments_style');