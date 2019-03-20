<?php
/**
 * The template for displaying Tag pages.
 *
 */

get_header(); ?>

<?php get_template_part( 'page-header', 'posts' ); ?>

<!-- #primary -->
<div id="primary" class="<?php echo get_theme_mod( 'ht_blog_sidebar', 'sidebar-right' ); ?> clearfix"> 
<div class="ht-container">

<!-- #content -->
<main id="content" role="main">

<?php if ( have_posts() ) :  ?>

<?php /* Start the Loop */
			while ( have_posts() ) : the_post(); ?>
                 
                 <?php	get_template_part( 'content', get_post_format() );  ?>

                 <?php endwhile; ?>

                <?php ht_pagination(); ?>
            

		<?php else : ?>
   
			<?php get_template_part( 'content', 'none' ); ?>
            
		<?php endif; ?>
 
</main>
<!-- /#content -->

<?php $ht_blog_sidebar = get_theme_mod( 'ht_blog_sidebar', 'sidebar-right' );
if ( $ht_blog_sidebar != 'sidebar-off') {
get_sidebar(); } ?>

</div>
</div>
<!-- /#primary -->

<?php get_footer(); ?>