<?php

if ( ! function_exists( 'ht_paging_nav' ) ) :
/**
* Display navigation to next/previous set of posts when applicable.
*
* @return void
*/
function ht_paging_nav() {
        // Don't print empty markup if there's only one page.
        if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
                return;
        }
        ?>
        <nav class="paging-navigation clearfix" role="navigation">
                <div class="nav-links">

                        <?php if ( get_next_posts_link() ) : ?>
                        <div class="nav-previous"><?php next_posts_link( __( '<i class="fa fa-angle-left"></i> Older posts', 'helpguru' ) ); ?></div>
                        <?php endif; ?>

                        <?php if ( get_previous_posts_link() ) : ?>
                        <div class="nav-next"><?php previous_posts_link( __( 'Newer posts <i class="fa fa-angle-right"></i>', 'helpguru' ) ); ?></div>
                        <?php endif; ?>

                </div><!-- .nav-links -->
        </nav><!-- .navigation -->
        <?php
}
endif;


if ( ! function_exists( 'ht_entry_date' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 */
function ht_entry_date() {
	$post_date = the_date( 'Y-m-d','','', false );
	$month_ago = date( "Y-m-d", mktime(0,0,0,date("m")-1, date("d"), date("Y")) );
	if ( $post_date > $month_ago ) {
		$post_date = sprintf( _x( '%s ago', '%s = human-readable time difference', 'helpguru' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
	} else {
		$post_date = get_the_date();
	}
	if ( is_single() ) {
	printf( __( 'Posted: <time datetime="%1$s">%2$s</time>', 'helpguru' ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( $post_date )
	);	
		echo _e( ' in ', 'helpguru' );
		echo the_category(' &bull; ');
	} else {
	printf( __( '<time datetime="%1$s">%2$s</time>', 'helpguru' ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( $post_date )
	);	
    }
}
endif;




if ( ! function_exists( 'ht_get_sidebar_position' ) ) :
/**
* Returns the sidebar position class
*
*/
function ht_get_sidebar_position() {

if ( is_home() ) {
	$ht_index_id = get_option('page_for_posts');
	$ht_sidebar_position = get_post_meta( $ht_index_id, '_ht_sidebar_pos', true );
} else {
	$ht_sidebar_position = get_post_meta( get_the_id(), '_ht_sidebar_pos', true );
}

if ($ht_sidebar_position == '') {
	echo 'sidebar-right';
} else {
	echo $ht_sidebar_position;
}
	
}
endif;


if ( ! function_exists( 'ht_get_sidebar' ) ) :
/**
* Returns the sidebar position class
*
*/
function ht_get_sidebar() {

if ( is_home() ) {
	$ht_index_id = get_option('page_for_posts');
	$ht_sidebar_position = get_post_meta( $ht_index_id, '_ht_sidebar_pos', true );
} else {
	$ht_sidebar_position = get_post_meta( get_the_id(), '_ht_sidebar_pos', true );
}

if ( $ht_sidebar_position != 'sidebar-off'	) {
	return get_sidebar();
}
	
}
endif;