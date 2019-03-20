<?php
/**
 * The template for displaying a "No posts found" message.
 */
?>      
                
<article id="post-0" class="post no-results not-found">
	<h1 class="entry-title"><?php _e( 'Nothing Found', 'helpguru' ); ?></h1>

	<div class="entry-content">
		<p><?php _e('We apologize for any inconvenience, please ', 'helpguru'); ?><a href="<?php echo home_url(); ?>/" title="<?php bloginfo('description'); ?>"><?php _e('return to the home page', 'helpguru'); ?></a><?php _e(' or use the search form below.', 'helpguru'); ?></p>
		<?php get_search_form(); ?>
	</div>
</article>