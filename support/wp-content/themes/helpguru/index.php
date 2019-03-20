<?php get_header(); ?>
<?php

if( isset( $ht_knowledge_base_options ) && $ht_knowledge_base_options['kb-home'] ):
    //backward compatibility, can be removed for next version
    get_template_part('archive', 'ht_kb');
else:
    //continue index load  
?>


<?php get_template_part( 'page-header', 'posts' ); ?>

<!-- #primary -->
<div id="primary" class="<?php echo get_theme_mod( 'ht_blog_sidebar', 'sidebar-right' ); ?> clearfix"> 
<div class="ht-container">

<!-- #content -->
<main id="content" role="main" itemtype="http://schema.org/Blog" itemscope="itemscope" itemprop="mainContentOfPage">
  
<?php if ( have_posts() ) : ?>

	<?php /* Start the Loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'content', get_post_format() ); ?>

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

<?php endif; //end ht-home ?>