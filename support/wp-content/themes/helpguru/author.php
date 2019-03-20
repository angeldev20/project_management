<?php get_header(); ?>

<!-- #page-header -->
<section id="page-header" class="clearfix">
<div class="ht-container">
<h1 id="page-header-title"><?php printf( __( 'Author %s', 'helpguru' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
</div>
</section>
<!-- /#page-header -->

<!-- #primary -->
<div id="primary" class="<?php ht_get_sidebar_position(); ?> clearfix"> 
<div class="ht-container">

<!-- #content -->
<main id="content" role="main">

<?php if ( have_posts() ) : ?>

<?php
/* Queue the first post, that way we know
* what author we're dealing with (if that is the case).
*
* We reset this later so we can run the loop
* properly with a call to rewind_posts().
*/
the_post();
?>

<?php
/* Since we called the_post() above, we need to
* rewind the loop back to the beginning that way
* we can run the loop properly, in full.
*/
rewind_posts();
?>

<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>

<?php get_template_part( 'content', get_post_format() ); ?>

<?php endwhile; ?>

<?php ht_pagination(); ?>

<?php else : ?>
<?php get_template_part( 'content', 'none' ); ?>
<?php endif; ?>

</main>
<!-- /#content -->

<?php ht_get_sidebar(); ?>

</div>
</div>
<!-- /#primary -->

<?php get_footer(); ?>