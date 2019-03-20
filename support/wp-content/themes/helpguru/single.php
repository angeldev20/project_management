<?php get_header(); ?>

<?php get_template_part( 'page-header', 'posts' ); ?>

<!-- #primary -->
<div id="primary" class="<?php echo get_theme_mod( 'ht_blog_sidebar', 'sidebar-right' ); ?> clearfix">
<div class="ht-container">

<!-- #content -->
<main id="content" role="main" itemtype="http://schema.org/Blog" itemscope="itemscope" itemprop="mainContentOfPage">

<?php while ( have_posts() ) : the_post(); ?>

<?php get_template_part( 'content', get_post_format() ); ?>

<?php endwhile; // end of the loop. ?>

<?php if (get_the_author_meta('description') != '') { ?>
<section id="entry-author" class="clearfix">
	<div class="gravatar">
		<?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '70' );   } ?>
	</div>
	<?php if ( !is_author() ) { ?><h3 id="entry-author-title"><?php _e('About The Author', 'helpguru') ?></h3><?php } ?>
	<h4 class="entry-author-name">
		<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_the_author() ?></a>
	</h4>
	<div class="entry-author-desc">
		<?php the_author_meta('description') ?>
	</div>
</section>
<?php } ?>

<?php // If comments are open or we have at least one comment, load up the comment template
		 if ( comments_open() || '0' != get_comments_number() )
					comments_template( '', true ) ?>
</main>
<!-- #content -->

<?php $ht_blog_sidebar = get_theme_mod( 'ht_blog_sidebar', 'sidebar-right' );
if ( $ht_blog_sidebar != 'sidebar-off') {
get_sidebar(); } ?>

</div>
</div>
<!-- /#primary -->

<?php get_footer(); ?>