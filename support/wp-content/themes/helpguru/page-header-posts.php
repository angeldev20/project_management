<!-- #page-header -->
<?php
$ht_index_id = get_option('page_for_posts');
$ht_blog_tagline = get_post_meta( $ht_index_id, '_ht_page_tagline', true );
?>
<section id="page-header" class="clearfix">
<div class="ht-container">

<?php if ( is_home() ) {  ?>
	<h1 id="page-header-title"><?php echo get_the_title($ht_index_id); ?></h1>
<?php if (get_theme_mod( 'ht_blog_tagline' )) : ?><h2 id="page-header-tagline"><?php echo get_theme_mod( 'ht_blog_tagline', __( 'Important news from the super support team.', 'helpguru' ) ); ?></h2><?php endif; ?>

<?php } else { ?>
	<strong id="page-header-title">
		<?php echo get_the_title($ht_index_id); ?>
		<?php if ( is_category() ) : ?>
		<span><?php single_cat_title(); ?></span>
		<?php elseif ( is_tag() ) : ?>
			<span><?php single_tag_title(); ?></span>
		<?php elseif ( is_author() ) : ?>
			<span><?php printf( __( 'Author: %s', 'helpguru' ), '<span class="vcard">' . get_the_author() . '</span>' ); ?></span>
		<?php elseif ( is_day() ) : ?>
			<span><?php printf( __( 'Day: %s', 'helpguru' ), '<span>' . get_the_date() . '</span>' ); ?></span>
		<?php elseif ( is_month() ) : ?>
			<span><?php printf( __( 'Month: %s', 'helpguru' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'helpguru' ) ) . '</span>' ); ?></span>
		<?php elseif ( is_year() ) : ?>
			<span><?php printf( __( 'Year: %s', 'helpguru' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'helpguru' ) ) . '</span>' ); ?></span>
		<?php endif; ?>
	</strong>
<?php if ( get_post_meta( $ht_index_id, '_ht_page_tagline', true ) && is_home() ) { ?>
	<p id="page-header-tagline"><?php echo get_post_meta( $ht_index_id, '_ht_page_tagline', true ); ?></p>
<?php } ?>
<?php } ?>

</div>
</section>
<!-- /#page-header -->

<!-- #page-header-breadcrumbs -->
<?php if ( !is_home() ) {  ?>
<section id="page-header-breadcrumbs" class="clearfix">
<div class="ht-container">
	<?php ht_breadcrumb_display(); ?>
</div>
</section>
<?php } ?>
<!-- /#page-header-breadcrumbs -->