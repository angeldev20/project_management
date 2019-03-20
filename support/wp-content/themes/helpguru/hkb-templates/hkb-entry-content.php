<!-- .hkb-article__content -->
<div class="hkb-article__content entry-content" itemprop="text">
    <?php the_content(); ?>
</div>
<!-- /.hkb-article__content -->

<?php wp_link_pages( array( 'before' => '<div class="hkb-article__links">' . __( 'Pages:', 'helpguru' ), 'after' => '</div>' ) ); ?>