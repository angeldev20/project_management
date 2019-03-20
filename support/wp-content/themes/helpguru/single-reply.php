<?php

/**
 * Single Reply
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

	<?php if ( bbp_user_can_view_forum( array( 'forum_id' => bbp_get_reply_forum_id() ) ) ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<div id="bbp-reply-wrapper-<?php bbp_reply_id(); ?>" class="bbp-reply-wrapper">
				<h1 class="entry-title"><?php bbp_reply_title(); ?></h1>
				<div class="entry-content">

					<?php bbp_get_template_part( 'content', 'single-reply' ); ?>

				</div><!-- .entry-content -->
			</div><!-- #bbp-reply-wrapper-<?php bbp_reply_id(); ?> -->

		<?php endwhile; ?>

	<?php elseif ( bbp_is_forum_private( bbp_get_reply_forum_id(), false ) ) : ?>

		<?php bbp_get_template_part( 'feedback', 'no-access' ); ?>

	<?php endif; ?>

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