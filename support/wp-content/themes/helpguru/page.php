<?php get_header(); ?>

<?php if ( class_exists('BBPress') && bbp_is_search() ) {
	get_template_part( 'page-header', 'bbpress' );
} else { 
	//get_template_part( 'page-header', 'page' );
}
?>

<!-- #primary -->
<div id="primary" class="<?php ht_get_sidebar_position(); ?> clearfix">
<div class="ht-container">

<!-- #content -->
<main id="content" role="main" itemprop="mainContentOfPage">

    <?php while ( have_posts() ) : the_post(); ?>
        
    	<?php get_template_part( 'content', 'page' ); ?>

		<?php
        // If comments are open or we have at least one comment, load up the comment template
        if ( comments_open() || '0' != get_comments_number() )
        comments_template();
        ?>
    
    <?php endwhile; // end of the loop. ?>

</main>
<!-- #content -->

<?php ht_get_sidebar(); ?>

</div>
</div>
<!-- #primary -->

<?php get_footer(); ?>