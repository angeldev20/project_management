<?php

/**
 * Single Forum
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

		<?php if ( bbp_user_can_view_forum() ) : ?>

			<div id="forum-<?php bbp_forum_id(); ?>" class="bbp-forum-content">

				<?php if ( !bbp_is_forum_category() ) { ?>

				<h1 class="entry-title"><?php bbp_forum_title(); ?></h1>

				<?php } ?>

				<div class="entry-content">

					<?php bbp_get_template_part( 'content', 'single-forum' ); ?>

				</div>
			</div><!-- #forum-<?php bbp_forum_id(); ?> -->

		<?php else : // Forum exists, user no access ?>

			<?php bbp_get_template_part( 'feedback', 'no-access' ); ?>

		<?php endif; ?>

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