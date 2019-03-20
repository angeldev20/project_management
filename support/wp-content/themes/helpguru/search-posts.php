<?php
/**
* The template for displaying Search content
*
*/
?>
<!-- #content -->
<main id="content" role="main">

<?php if ( have_posts() ) { ?>

	<?php /* Start the Loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>

	<?php get_template_part( 'content', get_post_format() ); ?>
     			
	<?php endwhile;  ?>

	<?php ht_pagination(); ?>

<?php } else { ?>

	<?php get_template_part( 'content', 'none' ); ?>

<?php } ?>
    
</main>
<!-- /#content -->