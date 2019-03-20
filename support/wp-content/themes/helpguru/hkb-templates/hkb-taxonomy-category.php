<?php
/*
*
* Theme template for ht_kb_category
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

                <!-- .ht-category -->
                <div class="hkb-category" data-hkb-cat-icon="<?php echo hkb_has_category_custom_icon( get_queried_object()->term_id ); ?>">

                    <?php hkb_category_thumb_img( get_queried_object()->term_id ); ?>

                    <!-- .ht-category__header -->
                    <div class="hkb-category__header">
                        <h2 class="hkb-category__title">
                            <?php hkb_term_name(); ?>
                        </h2>

                        <?php $ht_term_count = hkb_get_term_count(); ?>

                        <?php if ( hkb_archive_display_subcategory_count() ) : ?>
                            <span class="hkb-category__count"><?php echo sprintf( _n( '1 Article', '%s Articles', $ht_term_count, 'helpguru' ), $ht_term_count ); ?></span>
                        <?php endif; ?>

                        <?php if( hkb_get_term_desc()!='' ): ?>
                            <p class="hkb-category__description"><?php hkb_term_desc(); ?></p>
                        <?php endif; ?>

                    </div>
                    <!-- /.ht-category__header -->

                	<?php hkb_get_template_part('hkb-subcategories'); ?>

                    <?php if ( have_posts() ) : ?>
                    
                        <?php while ( have_posts() ) : the_post(); ?>

                    		<?php hkb_get_template_part('hkb-content-article'); ?>
                        
                        <?php endwhile; ?>

                        <nav class="hkb-pagination">
                            <?php hkb_get_template_part('hkb-pagination'); ?>
                        </nav>
                        
                    <?php else : ?>

                        <?php // don't show this if there are subcategories shown
                        $subcategories = hkb_get_subcategories( get_queried_object()->term_id );
                        if ( !$subcategories ): ?>

                            <h2><?php _e('Nothing else in this category.', 'helpguru'); ?></h2>
                            
                        <?php endif; ?>
                        
                    <?php endif; ?>

                </div>
                <!-- /.ht-category -->

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