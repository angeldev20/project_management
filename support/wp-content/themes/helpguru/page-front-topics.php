<?php

/**
 * Template Name: bbPress - Topics (Newest)
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); ?>

<?php get_template_part( 'page-header', 'bbpress' ); ?>

<!-- #primary -->
<div id="primary" class="<?php echo get_theme_mod( 'ht_bbpress_sidebar', 'sidebar-right' ); ?> clearfix"> 
<div class="ht-container">

<!-- #content -->
<main id="content" role="main">

	<?php do_action( 'bbp_before_main_content' ); ?>

	<?php do_action( 'bbp_template_notices' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div id="topics-front" class="bbp-topics-front">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content">

				<?php the_content(); ?>

				<?php bbp_get_template_part( 'content', 'archive-topic' ); ?>

			</div>
		</div><!-- #topics-front -->

	<?php endwhile; ?>

	<?php do_action( 'bbp_after_main_content' ); ?>

</main>
<!-- /#content -->

<?php $ht_bbpress_sidebar = get_theme_mod( 'ht_bbpress_sidebar', 'sidebar-right' );
if ( $ht_bbpress_sidebar != 'sidebar-off') {
get_sidebar( 'bbpress' ); } ?>

</div>
</div>
<!-- /#primary -->

<?php get_footer(); ?>