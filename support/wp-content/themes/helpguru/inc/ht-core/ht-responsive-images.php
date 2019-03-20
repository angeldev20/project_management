<?php
/**
* HeroThemes - Responsive Image Functions
* by HeroThemes (http://herothemes.com)
*/

/**
 * Enqueues scripts for front-end.
 */
 
function ht_core_responsive_img_scripts() {
		
	// Load jQuery Picture
	wp_enqueue_script('jquery-picture', get_template_directory_uri() . '/inc/ht-core/js/jquery-picture-min.js', array( 'jquery' ), false, true);

}
add_action('wp_enqueue_scripts', 'ht_core_responsive_img_scripts');


if ( ! function_exists( 'ht_get_responsive_post_thumbnail' ) ) :
/**
* Responsive Post Thumbnails
*
*/
function ht_get_responsive_post_thumbnail() {
 
	$ht_post_thumbnail_id = get_post_thumbnail_id( get_the_id() );
	$ht_post_thumbnail_320 = wp_get_attachment_image_src( $ht_post_thumbnail_id, 'width=320&height=0&crop=resize-crop' );
	$ht_post_thumbnail_480 = wp_get_attachment_image_src( $ht_post_thumbnail_id, 'width=480&height=0&crop=resize-crop' );
	$ht_post_thumbnail_600 = wp_get_attachment_image_src( $ht_post_thumbnail_id, 'width=600&height=0&crop=resize-crop' );
	$ht_post_thumbnail_920 = wp_get_attachment_image_src( $ht_post_thumbnail_id, 'width=920&height=0&crop=resize-crop' );
	$ht_post_thumbnail_1200 = wp_get_attachment_image_src( $ht_post_thumbnail_id, 'width=1200&height=0&crop=resize-crop' );
	?>
    <picture>
    <source src="<?php echo $ht_post_thumbnail_320[0]; ?>">
    <source media="(min-width: 320px)" src="<?php echo $ht_post_thumbnail_480[0]; ?>">
    <source media="(min-width: 480px)" src="<?php echo $ht_post_thumbnail_600[0]; ?>">
    <source media="(min-width: 600px)" src="<?php echo $ht_post_thumbnail_920[0]; ?>">
    <source media="(min-width: 920px)" src="<?php echo $ht_post_thumbnail_1200[0]; ?>">
    	<noscript>
    	<img src="<?php echo $ht_post_thumbnail_1200[0] ?>" alt="" />
        </noscript>
  	</picture>
	<?php
	
}
endif;



/**
* Responsive Post Thumbnails
*
*/
add_filter( 'image_size_names_choose', 'ht_core_my_custom_sizes' );
function ht_core_my_custom_sizes( $sizes ) {
	
	return array_merge( $sizes, array(
        'post' => __('Post Large', 'helpguru'),
		'post-mid' => __('Post Mid', 'helpguru'),
		'post-small' => __('Post Small', 'helpguru'),
    ) );
	
}

add_action( 'print_media_templates', 'ht_core_print_media_templates' );
function ht_core_print_media_templates() {
	
?>
<style>
.media-modal .attachment-display-settings .size option[value="thumbnail"],
.media-modal .attachment-display-settings .size option[value="medium"],
.media-modal .attachment-display-settings .size option[value="large"] {
	display:none;	
}
</style>
<?php }


