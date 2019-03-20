<?php
/*
Template Name: Homepage
*/
?>
<?php get_header(); ?>

<!-- #page-header -->
<section id="page-header" class="clearfix ph-align-center ph-large">
<div class="ht-container">
<?php if (get_theme_mod( 'ht_hp_headline' )) : ?><h2 id="page-header-title"><?php echo get_theme_mod( 'ht_hp_headline', __( 'The self-service support theme', 'helpguru' ) ); ?></h2><?php endif; ?>
<?php if (get_theme_mod( 'ht_hp_tagline' )) : ?><h3 id="page-header-tagline"><?php echo get_theme_mod( 'ht_hp_tagline', __( 'A premium WordPress theme with integrated Knowledge Base, providing 24/7 community based support.', 'helpguru' ) ); ?></h3><?php endif; ?>

<?php if (class_exists( 'HT_Knowledge_Base' )): ?>
	<?php hkb_get_template_part( 'hkb-searchbox', 'search' ); ?>
<?php endif;  ?>

</div>
</section>
<!-- /#page-header -->

<!-- #homepage-features -->
<section id="homepage-features" class="clearfix">
<div class="ht-container">

<?php
// Get index ID
$ht_index_id = get_option('page_for_posts');

// Get post counts
$ht_count_posts = wp_count_posts();
$ht_count_posts = $ht_count_posts->publish;

// Get category number
$ht_count_category = get_terms( 'category');
if ( !is_wp_error( $ht_count_category ) ) {
	$ht_count_category = count($ht_count_category);
} else {
	$ht_count_category = 0;
}

if (class_exists( 'HT_Knowledge_Base' )):
// Get kb post counts
$ht_count_kbposts = wp_count_posts('ht_kb');
$ht_count_kbposts = $ht_count_kbposts->publish;
// Get kb category number
$ht_count_kbcategory = get_terms( 'ht_kb_category');
if ( !is_wp_error( $ht_count_kbcategory ) ) {
	$ht_count_kbcategory = count($ht_count_kbcategory);
} else {
	$ht_count_kbcategory = 0;
}
endif; 

if (class_exists( 'bbPress' )):
// Get forum topcs counts
$ht_count_bbp_topics = wp_count_posts('topic');
$ht_count_bbp_topics = $ht_count_bbp_topics->publish;

// Get forum post counts
$ht_count_bbp_reply = wp_count_posts('reply');
$ht_count_bbp_reply = $ht_count_bbp_reply->publish;
endif; 

// Get number of blocks
$ht_hpf_count = 1;
if (class_exists( 'HT_Knowledge_Base' )):
$ht_hpf_count++;
endif; 
if (class_exists( 'bbPress' )):
$ht_hpf_count++;
endif; 

// Set column variable
if ( $ht_hpf_count == 1) {
	$ht_hpf_col = 'ht-grid-12';
} elseif ( $ht_hpf_count == 2) {
	$ht_hpf_col = 'ht-grid-6';
} else {
	$ht_hpf_col = 'ht-grid-4';
}
?>

<div class="ht-grid ht-grid-gutter-20">

	<?php if (class_exists( 'HT_Knowledge_Base' )): ?>
	<div class="ht-grid-col <?php echo $ht_hpf_col; ?>">
	<a href="<?php echo get_post_type_archive_link( 'ht_kb' ) ?>">
		<div class="hf-block hf-kb-block">
		<i class="fa fa-lightbulb-o"></i>
		<h4><?php _e( 'Knowledge Base', 'helpguru' ); ?></h4>
		<h5><?php echo $ht_count_kbposts; ?> <?php _e( 'Articles', 'helpguru' ); ?>  /  <?php echo $ht_count_kbcategory; ?> <?php _e( 'Categories', 'helpguru' ); ?></h5>
		</div>
	</a>
	</div>
	<?php endif; ?>

	<?php if (class_exists( 'bbPress' )): ?>
	<div class="ht-grid-col <?php echo $ht_hpf_col; ?>">
	<a href="<?php echo get_post_type_archive_link( 'forum' ); ?>">
		<div class="hf-block hf-forum-block">
		<i class="fa fa-comment-o"></i>
		<h4><?php _e( 'Forums', 'helpguru' ); ?></h4>
		<h5><?php echo $ht_count_bbp_topics; ?> <?php _e( 'Topics', 'helpguru' ); ?>  /  <?php echo $ht_count_bbp_reply; ?> <?php _e( 'Posts', 'helpguru' ); ?></h5>
		</div>
	</a>
	</div>
	<?php endif; ?>

	<?php $ht_index_id = get_option('page_for_posts'); ?>
	<div class="ht-grid-col <?php echo $ht_hpf_col; ?>">
	<a href="<?php echo get_permalink( $ht_index_id ); ?>">
		<div class="hf-block hf-posts-block">
		<i class="fa fa-bullhorn"></i>
		<h4><?php echo get_the_title($ht_index_id); ?></h4>
		<h5><?php echo $ht_count_posts; ?> <?php _e( 'Posts', 'helpguru' ); ?>  /  <?php echo $ht_count_category; ?> <?php _e( 'Categories', 'helpguru' ); ?></h5>
		</div>
	</a>
	</div>
</div>

</div>
</section>
<!-- /#homepage-features -->

<!-- #homepage-widgets -->
<section id="homepage-widgets" class="clearfix">
<div class="ht-container">

<?php if ( is_active_sidebar( 'ht_homepage_widgets' ) ) { ?>
<div class="ht-grid ht-grid-gutter-20 ht-grid-gutter-bottom-40">
	<?php dynamic_sidebar( 'ht_homepage_widgets' ); ?>
</div>	
<?php } ?>

</div>
</section>
<!-- /#homepage-widgets -->

<?php get_footer(); ?>