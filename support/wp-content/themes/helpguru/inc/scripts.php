<?php
/**
 * Enqueues scripts for front-end.
 */
 
function ht_enqueue_scripts() {
	
	/*
	* Load our main theme JavaScript file
	*/
	wp_enqueue_script('ht_theme_custom', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), false, true);
	
	/*
	* Adds JavaScript to pages with the comment form to support
	* sites with threaded comments (when in use).
	*/
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
	}	
	
}
add_action('wp_enqueue_scripts', 'ht_enqueue_scripts');