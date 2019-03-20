<article id="post-<?php the_ID(); ?>" class="hkb-article" <?php //post_class(); ?>>

    <h3 class="hkb-article__title" itemprop="headline">
        <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
    </h3>

    <?php if( hkb_show_search_excerpt() && function_exists('hkb_the_excerpt') ): ?>
        <span class="hkb-article__excerpt"><?php hkb_the_excerpt(); ?></span>
    <?php endif; ?>

    <?php hkb_get_template_part( 'hkb-article-meta' ); ?>

</article>