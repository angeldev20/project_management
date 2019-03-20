<?php
/**
* The template for displaying Search Results pages.
*
*/

get_header(); ?>

<?php 
if( function_exists( 'ht_kb_is_ht_kb_search' ) && ht_kb_is_ht_kb_search() ){
	get_template_part( 'page-header', 'kb' );
} else {
	get_template_part( 'page-header', 'posts' );
}
?>

<!-- #primary -->
<div id="primary" class="sidebar-right clearfix"> 
<div class="ht-container">

<?php 
if( function_exists( 'ht_kb_is_ht_kb_search' ) && ht_kb_is_ht_kb_search() ){
	get_template_part( 'search', 'ht_kb' );
} else {
	get_template_part( 'search', 'posts' );
}
?>

<?php ht_get_sidebar(); ?>

</div>
</div>
<!-- /#primary -->

<?php get_footer(); ?>