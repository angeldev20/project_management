<?php
/**
 * The template for displaying 404 pages (Not Found).
**/
get_header(); ?>
 
<!-- #primary -->
<div id="primary" class="sidebar-off clearfix"> 

<!-- #content -->
<main id="content" role="main">

<article id="post-0" class="post error404 not-found">

<header class="entry-header">
<h1 class="entry-title"><?php _e( '404', 'helpguru' ); ?></h1>
<h2 class="entry-tagline"><?php _e( 'Oops! That page can&rsquo;t be found.', 'helpguru' ); ?></h2>
</header>

<div class="entry-content">
<?php get_search_form(); ?>
</div>

</article>

</main>
<!-- /#content -->

</div>
<!-- /#primary -->

<?php get_footer(); ?>