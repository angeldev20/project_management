<?php

/**
 * Single Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums">

	<?php do_action( 'bbp_template_before_single_topic' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php if ( !bbp_show_lead_topic() ) : ?>

		<div class="bbp-topic-controls">
			<?php bbp_topic_favorite_link( array( 'before' => '' ) ); ?>
			<?php bbp_topic_subscription_link( array( 'before' => '' ) ); ?>
		</div>

		<?php endif; ?>

		<?php $args = array(
					'before' => '<div class="bbp-topic-tags">',
					'sep' => '',
					'after' => '</div>'
		); 

		bbp_topic_tag_list( '', $args ); ?>

		<?php if ( bbp_show_lead_topic() ) : ?>

			<?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>

		<?php endif; ?>

		<?php if ( bbp_has_replies() ) : ?>

			<?php bbp_get_template_part( 'loop',       'replies' ); ?>

			<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

		<?php endif; ?>

		<?php bbp_get_template_part( 'form', 'reply' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_topic' ); ?>

</div>
