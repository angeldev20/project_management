<?php

/**
* Register widgetized area and update sidebar with default widgets
*/
add_action( 'widgets_init', 'ht_register_sidebars' );

function ht_register_sidebars() {
	register_sidebar(array(
		'name' => 'Blog Sidebar',
		'id' => 'ht_primary',
		'description'   => __('The deafult sidebar show on posts and pages', 'helpguru'),
		'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</section>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
		)
	);

	register_sidebar(array(
		'name' => 'BBPress Sidebar',
		'id' => 'ht_sidebar_bbpress',
		'description'   => __('BBPress Sidebar', 'helpguru'),
		'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</section>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
		)
	);

	register_sidebar(array(
		'name' => 'Knowledge Base Sidebar',
		'id' => 'ht_sidebar_kb',
		'description'   => __('Knowledge Base Sidebar', 'helpguru'),
		'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
		'after_widget' => '</section>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
		)
	);
	
	register_sidebar(array(
		'name' => 'Footer Widgets',
		'id' => 'ht_footer_widgets',
		'description'   => __('Widgets shown in the footer of the theme', 'helpguru'),
		'before_widget' => '<section id="%1$s" class="widget %2$s ht-grid-col ht-grid-4">',
		'after_widget' => '</section>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
		)
	);

	register_sidebar(array(
		'name' => 'Homepage Widgets',
		'id' => 'ht_homepage_widgets',
		'description'   => __('Widgets shown on the homepage', 'helpguru'),
		'before_widget' => '<section id="%1$s" class="widget %2$s ht-grid-col ht-grid-4">',
		'after_widget' => '</section>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
		)
	);

}