<!-- #page-header -->
<section id="page-header" class="clearfix">
<div class="ht-container">
	<h1 id="page-header-title"><?php echo get_theme_mod( 'ht_kb_title', __( 'Knowledge Base', 'helpguru' ) ); ?></h1>
	<?php if ( get_theme_mod( 'ht_kb_tagline' ) && ( is_post_type_archive() || ht_kb_is_ht_kb_front_page() ) ) : ?>
		<h2 id="page-header-tagline"><?php echo get_theme_mod( 'ht_kb_tagline' ); ?></h2>
	<?php endif; ?>
	<?php hkb_get_template_part( 'hkb-searchbox', 'search' ); ?>
</div>
</section>
<!-- /#page-header -->

<!-- #page-header-breadcrumbs -->
<?php if ( !is_post_type_archive() && apply_filters('ht_show_breadcrumbs', true) ): ?>
<section id="page-header-breadcrumbs" class="clearfix">
<div class="ht-container">
		<?php hkb_get_template_part( 'hkb-breadcrumbs' ); ?>
</div>
</section>
<?php endif; ?>
<!-- /#page-header -->