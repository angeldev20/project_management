<?php
/**
* HeroThemes - Customizer Extended Controls
* by HeroThemes (http://herothemes.com)
*/


if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

/**
 * Class to create a custom layout control
 */
class HT_Layout_Picker_Custom_Control extends WP_Customize_Control
{
    /**
    * Render the content on the theme customizer page
    */
    public function render_content()
       { ?>
                <label>
                <?php if ( ! empty( $this->label ) ) : ?>
                  <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description ; ?></span>
				<?php endif; ?>

<input type="radio" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>[full_width]" class="image-radio" value="sidebar-off" <?php $this->link(); ?> />
<label for="<?php echo $this->id; ?>[full_width]">
<img src="<?php echo get_template_directory_uri() ?>/inc/ht-core/img/1col.png" alt="Full Width" />
</label>

<input type="radio" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>[left_sidebar]" class="image-radio" value="sidebar-left" <?php $this->link(); ?> />
<label for="<?php echo $this->id; ?>[left_sidebar]">
<img src="<?php echo get_template_directory_uri() ?>/inc/ht-core/img/2cl.png" alt="Left Sidebar" />
</label>

<input type="radio" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>[right_sidebar]" class="image-radio" value="sidebar-right" <?php $this->link(); ?> />
<label for="<?php echo $this->id; ?>[right_sidebar]">
<img src="<?php echo get_template_directory_uri() ?>/inc/ht-core/img/2cr.png" alt="Right Sidebar" />
</label>
                </label>

            <?php
       }
	public function enqueue() {
      wp_enqueue_style( 'ht-customizer-controls', get_template_directory_uri() . '/inc/ht-core/css/customizer-controls.css' );
	}
}

