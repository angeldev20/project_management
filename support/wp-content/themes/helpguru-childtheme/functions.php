<?php

/* Load child theme stylesheet */
function ht_theme_style() {
	wp_enqueue_style( 'ht-theme-style', get_template_directory_uri() . '/css/style.css' );
	wp_enqueue_style( 'ht-childtheme-style', get_stylesheet_uri(), array('ht-theme-style', 'ht-kb') );
}
add_action( 'wp_enqueue_scripts', 'ht_theme_style' );


/* Insert custom functions below */