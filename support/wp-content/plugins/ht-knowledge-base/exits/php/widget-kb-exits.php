<?php

class HT_KB_Exit_Widget extends WP_Widget {

    private $defaults;

    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
    * Specifies the classname and description, instantiates the widget,
    * loads localization files, and includes necessary stylesheets and JavaScript.
    */
    public function __construct() {

        //update classname and description
        parent::__construct(
            'ht-kb-exit-widget',
            __( 'Knowledge Base Exit Point', 'ht-knowledge-base' ),
            array(
              'classname'   =>  'hkb_widget_exit',
              'description' =>  __( 'A widget for displaying an exit for the knowledge base (such as support ticket system)', 'ht-knowledge-base' )
            )
        );

        $this->defaults = array(
            'title' => __('Not the solution you were looking for?', 'ht-knowledge-base'),
            'text' => __('Click the link below to submit a support ticket', 'ht-knowledge-base'),
            'btn' => __('Submit Ticket', 'ht-knowledge-base'),
            'url' => ''
          );

    } // end constructor

    /*--------------------------------------------------*/
    /* Widget API Functions
    /*--------------------------------------------------*/

    /**
    * Outputs the content of the widget.
    *
    * @param array args The array of form elements
    * @param array instance The current instance of the widget
    */
    public function widget( $args, $instance ) {
        global $ht_kb_exit_tools, $wp_query;

        //if(!is_single())
        //    return;

        extract( $args, EXTR_SKIP );

        $instance = wp_parse_args( $instance, $this->defaults );

        $post = get_post( $wp_query->post->ID );

        $default_url = ht_kb_exit_url_option();
        $new_window = ht_kb_exit_new_window_option() ? 'target="_blank"' : '';

        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $text = isset( $instance['text'] ) ? esc_attr( $instance['text'] ) : '';
        $btn = isset( $instance['btn'] ) ? esc_attr( $instance['btn'] ) : '';
        $url = isset( $instance['url'] ) ? esc_attr( $instance['url'] ) : '';

        //check url not empty
        $url = empty($url) ? $default_url : $url;

        echo $before_widget;


        if ( $title )
            echo $before_title . $title . $after_title;

        $exit_widget = '<div class="hkb_widget_exit__content">' . $text . '</div>';
        $exit_widget .= '<a class="hkb_widget_exit__btn" href="' . apply_filters(HKB_EXITS_URL_FILTER_TAG, $url, 'widget') . '" ' . $new_window . '>' . $btn . '</a>';

        //output widget
        echo $exit_widget;
        
        echo $after_widget;

    } // end widget

    /**
    * Processes the widget's options to be saved.
    *
    * @param array new_instance The previous instance of values before the update.
    * @param array old_instance The new instance of values to be generated via the update.
    */
    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        //update widget's old values with the new, incoming values
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['text'] = strip_tags( $new_instance['text'] );
        $instance['btn'] = strip_tags( $new_instance['btn'] );
        $instance['url'] = strip_tags( $new_instance['url'] );


        return $instance;

    } // end widget

    /**
    * Generates the administration form for the widget.
    *
    * @param array instance The array of keys and values for the widget.
    */
    public function form( $instance ) {

      $instance = wp_parse_args((array) $instance, $this->defaults);

      // Store the values of the widget in their own variable

      $title = strip_tags($instance['title']);
      $text = strip_tags($instance['text']);
      $btn = strip_tags($instance['btn']);
      $url = strip_tags($instance['url']);
      ?>
      <label for="<?php echo $this->get_field_id("title"); ?>">
        <?php _e( 'Title', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
      </label>
      <label for="<?php echo $this->get_field_id("text"); ?>">
        <?php _e( 'Text', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo $this->get_field_id("text"); ?>" name="<?php echo $this->get_field_name("text"); ?>" type="text" value="<?php echo esc_attr($instance["text"]); ?>" />
      </label>
      <label for="<?php echo $this->get_field_id("btn"); ?>">
        <?php _e( 'Button Text', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo $this->get_field_id("btn"); ?>" name="<?php echo $this->get_field_name("btn"); ?>" type="text" value="<?php echo esc_attr($instance["btn"]); ?>" />
      </label>
      <label for="<?php echo $this->get_field_id("url"); ?>">
        <?php _e( 'Link URL (leave blank for default Knowledge Base setting url)', 'ht-knowledge-base' ); ?>
        :
        <input type="text" class="widefat" id="<?php echo $this->get_field_id("url"); ?>" name="<?php echo $this->get_field_name("url"); ?>" type="text" value="<?php echo esc_attr($instance["url"]); ?>" />
      </label>
      </p>
    <?php } // end form





} // end class

//Remember to change 'Widget_Name' to match the class name definition
add_action( 'widgets_init', create_function( '', 'register_widget("HT_KB_Exit_Widget");' ) );