<?php

/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="post-<?php bbp_reply_id(); ?>">
	<div <?php bbp_reply_class( $reply_id = 0, $classes = array('clearfix') ); ?>>

		<div class="bbp-reply-author clearfix">

		<span class="bbp-reply-post-date"><a href="<?php bbp_reply_url(); ?>" class="bbp-reply-permalink"><i class="fa fa-link"></i></a> <?php bbp_reply_post_date( $reply_id = 0, $humanize = true, $gmt = false ); ?></span>

			<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>

			<?php bbp_reply_author_link( array( 'sep' => '', 'show_role' => true, 'size'  => '60' ) ); ?>

			<?php if ( bbp_is_user_keymaster() ) : ?>

				<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>

				<div class="bbp-reply-ip"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>

				<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>

			<?php endif; ?>

			<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>

		</div><!-- .bbp-reply-author -->

		<div class="bbp-reply-content clearfix">

			<?php do_action( 'bbp_theme_before_reply_content' ); ?>

			<?php bbp_reply_content(); ?>

			<?php do_action( 'bbp_theme_after_reply_content' ); ?>

		</div><!-- .bbp-reply-content -->

			<div class="bbp-meta">

			<?php if ( bbp_is_single_user_replies() ) : ?>

				<span class="bbp-header">
					<?php _e( 'in reply to: ', 'helpguru' ); ?>
					<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a>
				</span>

			<?php endif; ?>

			<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>

			<?php bbp_reply_admin_links( array (
				'id' => 0,
				'before' => '<span class="bbp-admin-links">',
				'after' => '</span>',
				'sep' => '',
				'links' => array()
				) ); ?>

			<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>

			</div><!-- .bbp-meta -->

	</div><!-- .reply -->
</div><!-- #post-<?php bbp_reply_id(); ?> -->