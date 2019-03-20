<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemprop="blogPost" itemtype="http://schema.org/BlogPosting" itemscope="itemscope">

<!-- .entry-header -->
<header class="entry-header clearfix">

    <!-- .entry-thumb -->
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
    <!-- /.entry-thumb -->

    <!-- .entry-title -->
    <?php if ( is_single() ) { ?>
        <h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>    
    <?php } else { ?> 
        <h2 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2> 
    <?php }?>
    <!-- /.entry-title -->

    <!-- .entry-meta -->
    <ul class="entry-meta clearfix">
        <li><?php _e( 'Posted', 'helpguru'); ?> <a href="<?php the_permalink(); ?>"><time datetime="<?php echo get_the_date( 'c' ); ?>" itemprop="datePublished"><?php the_time( 'd F Y' ); ?></time></a></li>
        <li><?php _e( 'By', 'helpguru'); ?> <?php the_author_posts_link(); ?></li>
        <?php if ( 'ht_kb' != get_post_type() ) : ?><li><?php _e( 'Under', 'helpguru'); ?> <?php the_category('/ '); ?></li><?php endif; ?>
    </ul>
    <!-- /.entry-meta -->

</header>
<!-- .#entry-header -->

<div class="entry-content clearfix" itemprop="text">
	<?php if ( is_single() ) { ?>
        <?php the_content(); ?>
        <?php numbered_in_page_links( array( 'before' => '<div class="page-links"><strong>' . __( 'Pages:', 'helpguru' ) .'</strong>', 'after' => '</div>' ) ); ?>
        <?php if ( has_tag() ) { ?>
            <div class="tags"><?php the_tags( __( 'Tagged:', 'helpguru' ),'',''); ?></div>
    <?php } ?>
    <?php } else { ?>
        <?php the_excerpt(); ?>
    <?php }?>
</div>

</article>