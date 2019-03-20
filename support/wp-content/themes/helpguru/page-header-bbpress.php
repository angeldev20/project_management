<!-- #page-header -->
<section id="page-header" class="clearfix">
<div class="ht-container">
<h1 id="page-header-title"><?php echo get_theme_mod( 'ht_bbpress_title', __( 'Community Forums', 'helpguru' ) ); ?></h1>
<?php if (get_theme_mod( 'ht_bbpress_tagline' )) : ?><h2 id="page-header-tagline"><?php echo get_theme_mod( 'ht_bbpress_tagline' ); ?></h2><?php endif; ?>
</div>
</section>
<!-- /#page-header -->

<?php if (bbp_is_forum_archive() != '1') : ?>
<!-- #page-header-breadcrumbs -->
<section id="page-header-breadcrumbs" class="clearfix">
<div class="ht-container">
<?php 

	$bbp_breadcrumb_arguments = array(
									'before' => '<div class="ht-breadcrumbs bbp-breadcrumb" itemprop="breadcrumb">',
									'after' => '</div>',
									'sep' => '/',
									'pad_sep' => 0
								);

	//fix for profile page breadcrumbs bug
	if(bbp_is_single_user()){
		$bbp_breadcrumb_arguments = array_merge( array( 'current_text'=> bbp_title('', '', '') ), $bbp_breadcrumb_arguments );
	}

	//display breadcrumbs
	bbp_breadcrumb($bbp_breadcrumb_arguments); 

?>
</div>
</section>
<!-- /#page-header -->
<?php endif; ?>