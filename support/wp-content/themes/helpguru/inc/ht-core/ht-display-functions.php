<?php

// Return logo src
if ( ! function_exists( 'ht_theme_logo' ) ) {
function ht_theme_logo() {

	$ht_theme_logo = get_theme_mod( 'ht_site_logo' );
	
	if ( !empty($ht_theme_logo) ) {
		$ht_theme_logo_src = get_theme_mod( 'ht_site_logo' );
	} else {
		$ht_theme_logo_src = get_template_directory_uri()."/images/logo.png";
	}
	return $ht_theme_logo_src;

}
}

if(!function_exists('ht_breadcrumb_display')){
	/**
	 * Breadcrumbs display
	 * @pluggable
	 */
	function ht_breadcrumb_display( $sep = '<span class="sep">&gt;</span>' ) {
	    global $post;  

	    //show breadcrumbs filter
	    $ht_show_breadcrumbs = apply_filters('ht_show_breadcrumbs', true);

	    if (!is_front_page() && $ht_show_breadcrumbs) {  

	    	$home_link = '<a href="' . home_url() . '" class="ht-breadcrumbs-home">' . __('Home', 'helpguru') . '</a> ' . $sep; 

	    	$posts_page_id = get_option('page_for_posts');

	    	$posts_page_link = '<a href="' . get_permalink( $posts_page_id ) . '" class="ht-breadcrumbs-home">' . get_the_title( $posts_page_id ) . '</a> ' . $sep; 

		

			echo '<div class="ht-breadcrumbs" itemprop="breadcrumb">';
			
			$visited = array();
				
			 				
			if ( !is_single()  ) {
				echo $home_link;
				//echo $home_link.$posts_page_link;

				if (is_category()) {
 
		            $cat = get_category_parents(get_query_var('cat'), true, $sep);

		            // remove last sep
		            echo substr($cat, 0, strlen($cat) - strlen($sep));
		 
		        }
		 
		        if (is_tax()) {
		 
		            $tag = single_tag_title('', false);
		            $tag = get_tag_id($tag);
		            $term = ht_get_term_parents($tag, get_query_var('taxonomy'), true, $sep);
		 
		            // remove last sep
		            echo substr($term, 0, strlen($term) - strlen($sep));
		        } 

			} elseif ( is_single() ) {
				//Single Post	
				
				$terms = wp_get_post_terms( $post->ID , 'category');


				if( !empty($terms) ){
					foreach($terms as $term) {
						echo $home_link;
						//echo $home_link.$posts_page_link;
						if ($term->parent != 0) { 
							$parents =  get_category_parents($term->term_id, true,'' .$sep . '', false, $visited );
							echo $parents;
						} else {
							echo '<a href="' . esc_attr(get_term_link($term, 'category')) . '" title="' . sprintf( __( "View all posts in %s", 'helpguru' ), $term->name ) . '" ' . '>' . $term->name.'</a> ';
							echo $sep;
							$visited[] = $term->term_id;
						}
						echo get_the_title();
						echo '<br/>';

					} // End foreach
				} else {
					//uncategorized post
					echo get_the_title();
					echo '<br/>';
				}		
				
			} else {
					//Display the post title.
					echo get_the_title();
			}
					
			echo '</div>';	
		} //is_front_page
	} //end function
} //end function exists


if(!function_exists('ht_get_term_parents')){
	/**
	 * Get the term parents
	 * @pluggable
	 */
	function ht_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
	  $chain = '';
	  $parent = &get_term( $id, $taxonomy );
	  if ( is_wp_error( $parent ) )
	    return $parent;
	  if ( $nicename )
	    $name = $parent->slug;
	  else
	    $name = $parent->name;
	  if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
	    $visited[] = $parent->parent;
	    $chain .= ht_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
	  }
	  if ( $link )
	    $chain .= '<a href="' . esc_url( get_term_link( intval( $parent->term_id ), $taxonomy ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", 'helpguru' ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
	  else
	    $chain .= $name.$separator;
	  return $chain;
	}//end function
}//end function_exists

if ( ! function_exists( 'ht_pagination' ) ){
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 * Based on paging nav function from Twenty Fourteen
	 */

	function ht_pagination() {
		global $wp_query, $wp_rewrite;
		// Don't print empty markup if there's only one page.
		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = esc_url ( remove_query_arg( array_keys( $query_args ), $pagenum_link ) );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

		// Set up paginated links.
		$links = paginate_links( array(
			'base'     => $pagenum_link,
			'format'   => $format,
			'total'    => $wp_query->max_num_pages,
			'current'  => $paged,
			'mid_size' => 3,
			'add_args' => array_map( 'urlencode', $query_args ),
			'prev_text' => __( 'Previous', 'helpguru' ),
			'next_text' => __( 'Next', 'helpguru' ),
			'type'      => 'list',
		) );

		if ( $links ):

		?>
		<nav class="ht-pagination" role="navigation">
				<?php echo $links; ?>
		</nav><!-- .navigation -->
		<?php
		endif;
	}//emnd function
}//end function_exists


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

/**
 * The formatted output of a list of pages.
 */
add_action( 'numbered_in_page_links', 'numbered_in_page_links', 10, 1 );

if ( ! function_exists( 'numbered_in_page_links' ) ) :
/**
 * Modification of wp_link_pages() with an extra element to highlight the current page.
 *
 * @param  array $args
 * @return void
 */
function numbered_in_page_links( $args = array () )
{
    $defaults = array(
        'before'      => '<p>' . __('Pages:', 'helpguru')
    ,   'after'       => '</p>'
    ,   'link_before' => ''
    ,   'link_after'  => ''
    ,   'pagelink'    => '%'
    ,   'echo'        => 1
        // element for the current page
    ,   'highlight'   => 'span'
    );

    $r = wp_parse_args( $args, $defaults );
    $r = apply_filters( 'wp_link_pages_args', $r );
    extract( $r, EXTR_SKIP );

    global $page, $numpages, $multipage, $more, $pagenow;

    if ( ! $multipage )
    {
        return;
    }

    $output = $before;

    for ( $i = 1; $i < ( $numpages + 1 ); $i++ )
    {
        $j       = str_replace( '%', $i, $pagelink );
        $output .= ' ';

        if ( $i != $page || ( ! $more && 1 == $page ) )
        {
            $output .= _wp_link_page( $i ) . "{$link_before}{$j}{$link_after}</a>";
        }
        else
        {   // highlight the current page
            // not sure if we need $link_before and $link_after
            $output .= "<$highlight>{$link_before}{$j}{$link_after}</$highlight>";
        }
    }

    print $output . $after;
}
endif;