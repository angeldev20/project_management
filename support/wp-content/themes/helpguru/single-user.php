<?php

/**
 * Single User
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); ?>

<?php get_template_part( 'page-header', 'bbpress' ); ?>

<!-- #primary -->
<div id="primary" class="sidebar-off clearfix"> 
<div class="ht-container">

<!-- #content -->
<main id="content" role="main">

	<?php do_action( 'bbp_before_main_content' ); ?>

	<div id="bbp-user-<?php bbp_current_user_id(); ?>" class="bbp-single-user">
		<div class="entry-content clearfix">

			<?php bbp_get_template_part( 'content', 'single-user' ); ?>

		</div><!-- .entry-content -->
	</div><!-- #bbp-user-<?php bbp_current_user_id(); ?> -->

	<?php do_action( 'bbp_after_main_content' ); ?>

</main>
<!-- /#content -->

</div>
</div>
<!-- /#primary -->

<?php get_footer(); ?>