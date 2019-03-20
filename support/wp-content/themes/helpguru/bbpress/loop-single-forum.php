<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

	<li class="bbp-forum-info">

		<?php if ( bbp_is_user_home() && bbp_is_subscriptions() ) : ?>

			<span class="bbp-row-actions">

				<?php do_action( 'bbp_theme_before_forum_subscription_action' ); ?>

				<?php bbp_forum_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>

				<?php do_action( 'bbp_theme_after_forum_subscription_action' ); ?>

			</span>

		<?php endif; ?>

		<div class="bbp-forum-header">

			<?php do_action( 'bbp_theme_before_forum_title' ); ?>

			<a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a>

			<?php do_action( 'bbp_theme_after_forum_title' ); ?>

			<?php do_action( 'bbp_theme_before_forum_description' ); ?>

			<div class="bbp-forum-content"><?php bbp_forum_content(); ?></div>

			<?php do_action( 'bbp_theme_after_forum_description' ); ?>

		</div>

		<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>

		<?php //bbp_list_forums(); ?>

			<?php if (is_single()) {
			
			bbp_list_forums();
			
			} else {
			
			ht_bbp_list_forums( array (
			'before'            => '<ul class="bbp-forums-list">',
			'after'             => '</ul>',
			'link_before'       => '<li class="bbp-forum clearfix">',
			'link_after'        => '</li>',
			'count_before'      => '<div class="topic-reply-counts">Topics: ',
			'count_after'       => '</div>',
			'count_sep'         => '<br />Posts: ',
			'separator'         => '<div style="clear:both;"></div>',
			'forum_id'          => '',
			'show_topic_count'  => true,
			'show_reply_count'  => true,
			'show_freshness_link' => true,
			)); 
			
			} ?>

		<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

		<?php bbp_forum_row_actions(); ?>

	</li>

	<?php if (is_single()) { ?>

	<li class="bbp-forum-topic-count"><?php bbp_forum_topic_count(); ?></li>

	<li class="bbp-forum-reply-count"><?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?></li>

	<li class="bbp-forum-freshness">

		<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>

		<?php bbp_forum_freshness_link(); ?>

		<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>

		<p class="bbp-topic-meta">

			<?php do_action( 'bbp_theme_before_topic_author' ); ?>

			<span class="bbp-topic-freshness-author"><?php bbp_author_link( array( 'post_id' => bbp_get_forum_last_active_id(), 'size' => 14 ) ); ?></span>

			<?php do_action( 'bbp_theme_after_topic_author' ); ?>

		</p>
	</li>

	<?php } ?>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->
