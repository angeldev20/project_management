<?php
/**
* The template used for displaying page content in page.php
*
*/
?>    
                
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemtype="http://schema.org/Article" itemscope="itemscope">

<h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>  

  <?php if ( has_post_thumbnail() ) { ?>
  <div class="entry-thumb">  
  		<?php ht_responsive_post_thumbnail(); ?>
  </div>
  <?php } ?>

  <div class="entry-content clearfix" itemprop="text">
    <?php the_content(); ?>
    <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'helpguru' ), 'after' => '</div>' ) ); ?>
  </div>
      
</article>