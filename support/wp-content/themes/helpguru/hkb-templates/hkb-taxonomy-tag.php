<?php 
/*
*
* Theme template for ht_kb_tag taxonomy
*
*/ 
?>

<?php get_header(); ?>

<?php get_template_part( 'page-header', 'kb' ); ?>

<!-- #primary -->
<div id="primary" class="<?php echo get_theme_mod( 'ht_kb_sidebar', 'sidebar-right' ); ?> clearfix"> 
<div class="ht-container">

<!-- #content -->
<main id="content" role="main" itemscope="itemscope" itemprop="mainContentOfPage">
<!-- #ht-kb -->
<div id="hkb" class="hkb-template-category">

    <?php if ( have_posts() ) : ?>
    
        <?php while ( have_posts() ) : the_post(); ?>

    		<?php hkb_get_template_part('hkb-content-article'); ?>
        
        <?php endwhile; ?>

        <nav class="hkb-pagination">
            <?php hkb_get_template_part('hkb-pagination'); ?>
        </nav>
        
    <?php else : ?>

        <h2><?php _e('Nothing else in this category.', 'helpguru'); ?></h2>
        
    <?php endif; ?>

</div>
<!-- /#ht-kb -->
</main>
<!-- /#content -->

<?php $ht_kb_sidebar = get_theme_mod( 'ht_kb_sidebar', 'sidebar-right' );
if ( $ht_kb_sidebar != 'sidebar-off') {
get_sidebar( 'kb' ); } ?>

</div>
<!-- /.ht-container -->
</div>
<!-- /#primary -->

<?php get_footer(); ?>