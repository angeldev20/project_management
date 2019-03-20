<?php

/**
 * Template Name: bbPress - User Lost Password
 *
 * @package bbPress
 * @subpackage Theme
 */

// No logged in users
bbp_logged_in_redirect();

// Begin Template
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

		<div id="bbp-lost-pass" class="bbp-lost-pass">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content">

				<?php the_content(); ?>

				<div id="bbpress-forums">

					<?php bbp_get_template_part( 'form', 'user-lost-pass' ); ?>

				</div>
			</div>
		</div><!-- #bbp-lost-pass -->

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