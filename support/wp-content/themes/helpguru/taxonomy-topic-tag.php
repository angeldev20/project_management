<?php

/**
 * Topic Tag
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

	<div id="topic-tag" class="bbp-topic-tag">
		<h1 class="entry-title"><?php printf( __( 'Topic Tag: %s', 'helpguru' ), '<span>' . bbp_get_topic_tag_name() . '</span>' ); ?></h1>
		<div class="entry-content">

			<?php bbp_get_template_part( 'content', 'archive-topic' ); ?>

		</div>
	</div><!-- #topic-tag -->

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