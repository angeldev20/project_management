<?php
/*
*
* The template used for single page, for use by the theme
*
*/
?>
<?php get_header(); ?>

<?php get_template_part( 'page-header', 'kb' ); ?>

<!-- #primary -->
<div id="primary" class="<?php echo get_theme_mod( 'ht_kb_sidebar', 'sidebar-right' ); ?> clearfix"> 
<div class="ht-container">

<!-- #content -->
<main id="content" role="main" itemscope="itemscope" itemprop="mainContentOfPage">
<!-- #ht-kb -->
<div id="hkb" class="hkb-template-single">

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemtype="http://schema.org/CreativeWork" itemscope="itemscope">

		<!-- .entry-header -->
		<header class="entry-header">

		<?php if ( has_post_format( 'video' )) { ?>
			<div class="entry-video clearfix">
				<?php
				// Get post format meta
				$ht_pf_video_picker = get_post_meta( get_the_ID(), '_ht_pf_video_picker', true );
				$ht_pf_video_oembed = get_post_meta( get_the_ID(), '_ht_pf_video_oembed', true );
				$ht_pf_video_upload = get_post_meta( get_the_ID(), '_ht_pf_video_upload', true );

				// Echo video
				if ( $ht_pf_video_picker == 'oembed' ) { ?>
					<div class="embed-container">
						<?php echo wp_oembed_get( $ht_pf_video_oembed ); ?>
					</div>
				<?php } elseif ( $ht_pf_video_picker == 'custom' ) {
					echo do_shortcode('[video src="'. $ht_pf_video_upload .'" width="1920" height="1080"]');
				};
				?>
			</div>
		<?php } // End if has_post_format(video) ?>

			<h1 class="entry-title" itemprop="headline">
				<?php the_title(); ?>
			</h1>

			<ul class="hkb-entry-meta clearfix">

				<li class="hkb-em-date"> 
				    <span><?php _e( 'Created' , 'helpguru' ) ?></span>
				    <a href="<?php the_permalink(); ?>" rel="bookmark" itemprop="url"><time datetime="<?php the_time('Y-m-d')?>" itemprop="datePublished"><?php the_time( get_option('date_format') ); ?></time></a>
				</li>
				<li class="ht-kb-em-author">
					<span><?php _e( 'Author' , 'helpguru' ) ?></span>
					<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr( get_the_author() ); ?>" rel="me" itemprop="author"><?php echo esc_attr( get_the_author() ); ?></a>
				</li>
				<?php if( !is_tax() ) : ?>
					<li class="ht-kb-em-category">
					    <span><?php _e( 'Category' , 'helpguru' ) ?></span>
					    <?php 
						    $terms = get_the_term_list( $post->ID, 'ht_kb_category', ' ', ', ', '' );
						    if(empty($terms)){
						    	_e('Uncategorized', 'helpguru');
						    } else {
						    	echo $terms;
						    }
					    ?>
					</li>
				<?php endif; //is tax ?>
				<?php if ( comments_open() && get_comments_number() > 0 ){ ?>
					<li class="ht-kb-em-comments">
					    <span><?php _e( 'Comments' , 'helpguru' ) ?></span>
						<?php comments_popup_link( __( '0', 'helpguru' ), __( '1', 'helpguru' ), __( '%', 'helpguru' ) ); ?>
					</li>
				<?php } ?>

			</ul>

		<?php if ( has_post_thumbnail() ) { ?>
		<div class="entry-thumb">
			<?php if ( is_single() ) { ?>
	            <?php ht_responsive_post_thumbnail(); ?>
	        <?php } else { ?> 
	            <a href="<?php the_permalink(); ?>" rel="nofollow">
	               <?php ht_responsive_post_thumbnail(); ?>
	            </a>
	        <?php }?> 
		</div>
		<?php } ?>
		    
		</header>
		<!-- /.entry-header --> 

			<div class="hkb-entry-content">

					<?php hkb_get_template_part('hkb-entry-content'); ?>

					<?php hkb_get_template_part('hkb-single-attachments'); ?>

					<?php //hkb_get_template_part('hkb-single-tags'); ?> 

					<?php do_action('ht_kb_end_article'); ?>

			</div>			

		</article>

		<?php //hkb_get_template_part('hkb-single-author'); ?>

		<?php hkb_get_template_part('hkb-single-related'); ?>

		<?php // If comments are open or we have at least one comment, load up the comment template
		 if ( comments_open() || '0' != get_comments_number() )
					comments_template( '', true ) ?>

	<?php endwhile; // end of the loop. ?>

</div><!-- /#ht-kb -->
</main>
<!-- /#content -->

<?php $ht_kb_sidebar = get_theme_mod( 'ht_kb_sidebar', 'sidebar-right' );
if ( $ht_kb_sidebar != 'sidebar-off') {
get_sidebar( 'kb' ); } ?>

</div>
<!-- /.ht-container -->
</div>
<!-- /#primary -->

<?php get_footer(); ?>