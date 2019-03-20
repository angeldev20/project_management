<?php

class HT_KB_Categories_Widget extends WP_Widget {

	private $defaults;

	/**
	* Widget Constructor
	* Specifies the classname and description, instantiates the widget,
	* loads localization files, and includes necessary stylesheets and JS where necessary
	*/
	public function __construct() {

		//set classname and description
		parent::__construct(
					'ht-kb-categories-widget',
					__( 'Knowledge Base Categories', 'ht-knowledge-base' ),
					array(
					'classname'	=>	'hkb_widget_categories',
					'description'	=>	__( 'A widget for displaying Knowledge Base categories', 'ht-knowledge-base' )
					)
		);

		$default_widget_title = __( 'Knowledge Base Categories', 'ht-knowledge-base' );

		/*  default widget settings. */
		$this->defaults = array(
			'title' => $default_widget_title,
			'num' => '5',
			'depth' => '2',
			'sort_by' => 'name',
			'asc_sort_order' => '', 
			'hide_empty' => '', 
			'only_top_level' => '',
			'disp_article_count' => '',
		);


	} // end constructor

	//Widget API Functions

	/**
	* Outputs the content of the widget.
	* @param array args The array of form elements
	* @param array instance The current instance of the widget
	*/
	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$instance = wp_parse_args( $instance, $this->defaults );
		
		$title = apply_filters('widget_title', $instance['title'] );

		$valid_sort_orders = array('count', 'name', 'id', 'slug' );
		if ( in_array($instance['sort_by'], $valid_sort_orders) ) {
		  $sort_by = $instance['sort_by'];
		  $sort_order = (bool) $instance['asc_sort_order'] ? 'ASC' : 'DESC';
		} else {
		  // by default, display alphabetically
		  $sort_by = 'name';
		  $sort_order = 'DESC';
		}

		$only_top_level = (bool) $instance['only_top_level'] ? 0 : '';
		$hide_empty = (bool) $instance['hide_empty'] ? 1 : 0;
		$disp_article_count = (bool) $instance['disp_article_count'] ? 1 : 0;

		$num = empty($instance['num']) ? 100 : (int) $instance['num'];


		$depth = empty($instance['depth']) ? 2 : (int) $instance['depth'];


		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		$args = array(
			'hide_empty'    => $hide_empty,
			'number'		=> $num,
			'depth'			=> $depth,
			'child_of' 		=> 0,
			'pad_counts' 	=> 1,
			'hierarchical'	=> 1,
			'parent'		=> $only_top_level,
			'orderby' 		=> $sort_by,
			'order' 		=> $sort_order,
			'taxonomy'		=> 'ht_kb_category',
			'title_li' 		=> '',
			'show_count'	=> $disp_article_count

		); 

		if( 'custom' == $instance['sort_by'] ){
			//ok - the get_terms call will be filtered by sort_kb_categories in HT_Knowledge_Base_Custom_Tax_Order
		} else {
			//for anything else we need to return false on the ht_kb_allow_custom_category_sort filter
			add_filter( 'ht_kb_allow_custom_category_sort', array($this, 'ht_kb_do_not_sort_categories_on_custom'), 10, 1);
		}

		echo '<ul class="hkb_category_widget__category_list">';
		wp_list_categories($args);
		/*
		 foreach($categories as $category) {

			echo '<li class="hkb_category_widget__category_item">';
			if($disp_article_count){
				echo '<span class="hkb_category_widget__article_count">'. hkb_get_term_count($category) . '</span>';
			}
			echo '<a href="' . get_term_link( $category ) . '" title="' . sprintf( __( 'View all posts in %s', 'ht-knowledge-base' ), $category->name ) . '" ' . '>' . $category->name.'</a></li> ';
		 } 
		 */
		echo '</ul>';

		//reset/remove filters
		remove_filter( 'ht_kb_allow_custom_category_sort', array($this, 'ht_kb_do_not_sort_categories_on_custom') );


		echo $after_widget;
	}

	/**
	* A dummy filter for ht_kb_allow_custom_category_sort filter
	* @param $var The passed filter value
	* @return always false
	*/
	function ht_kb_do_not_sort_categories_on_custom( $var=false ){
		return false;
	}

	/**
	* Processes the widget's options to be saved.
	* @param array new_instance The previous instance of values before the update.
	* @param array old_instance The new instance of values to be generated via the update.
	*/
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['sort_by'] = $new_instance['sort_by'];
		$instance['asc_sort_order'] = $new_instance['asc_sort_order'] ? 1 : 0;
		$instance['hide_empty'] = $new_instance['hide_empty'] ? 1 : 0;
		$instance['only_top_level'] = $new_instance['only_top_level'] ? 1 : 0;
		$instance['disp_article_count'] = $new_instance['disp_article_count'] ? 1 : 0;
		$instance['num'] = $new_instance['num'];
		$instance['depth'] = $new_instance['depth'];


		return $instance;
	}
		
	/**
	* Generates the administration form for the widget.
	* @param array instance The array of keys and values for the widget.
	*/
	function form( $instance ) {

		
		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>
		
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'ht-knowledge-base') ?></label>
			<input  type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id("num"); ?>">
		  <?php _e( 'Number of categories to show', 'ht-knowledge-base' ); ?>
		  :
		  <input style="text-align: center;" id="<?php echo $this->get_field_id("num"); ?>" name="<?php echo $this->get_field_name("num"); ?>" type="text" value="<?php echo absint($instance["num"]); ?>" size='3' />
		</label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id("depth"); ?>">
		  <?php _e( 'Category depth to show', 'ht-knowledge-base' ); ?>
		  :
		  <input style="text-align: center;" id="<?php echo $this->get_field_id("depth"); ?>" name="<?php echo $this->get_field_name("depth"); ?>" type="text" value="<?php echo absint($instance["depth"]); ?>" size='3' />
		</label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id("sort_by"); ?>">
		  <?php _e( 'Sort by', 'ht-knowledge-base' ); ?>
		  :
		  <select id="<?php echo $this->get_field_id("sort_by"); ?>" name="<?php echo $this->get_field_name("sort_by"); ?>" class="ht-kb-widget-admin-dropdown">
			<option value="name"<?php selected( $instance["sort_by"], "name" ); ?>><?php _e( 'Name', 'ht-knowledge-base' ); ?></option>
			<option value="count"<?php selected( $instance["sort_by"], "count" ); ?>><?php _e( 'Number of articles', 'ht-knowledge-base' ); ?></option>
			<option value="slug"<?php selected( $instance["sort_by"], "slug" ); ?>><?php _e( 'Slug', 'ht-knowledge-base' ); ?></option>
			<option value="id"<?php selected( $instance["sort_by"], "id" ); ?>><?php _e( 'ID', 'ht-knowledge-base' ); ?></option>
			<option value="custom"<?php selected( $instance["sort_by"], "custom" ); ?>><?php _e( 'Custom (TL only)', 'ht-knowledge-base' ); ?></option>
		  </select>
		</label>
	  </p>
	  <p>
		<label for="<?php echo $this->get_field_id("asc_sort_order"); ?>">
		  <input type="checkbox" class="checkbox"
	  id="<?php echo $this->get_field_id("asc_sort_order"); ?>"
	  name="<?php echo $this->get_field_name("asc_sort_order"); ?>"
	  <?php checked( (bool) $instance["asc_sort_order"], true ); ?> />
		  <?php _e( 'Reverse sort order', 'ht-knowledge-base' ); ?>
		</label>
	  </p>
	  <p>
		<label for="<?php echo $this->get_field_id("hide_empty"); ?>">
		  <input type="checkbox" class="checkbox"
	  id="<?php echo $this->get_field_id("hide_empty"); ?>"
	  name="<?php echo $this->get_field_name("hide_empty"); ?>"
	  <?php checked( (bool) $instance["hide_empty"], true ); ?> />
		  <?php _e( 'Hide empty categories', 'ht-knowledge-base' ); ?>
		</label>
	  </p>
	  <p>
		<label for="<?php echo $this->get_field_id("only_top_level"); ?>">
		  <input type="checkbox" class="checkbox"
	  id="<?php echo $this->get_field_id("only_top_level"); ?>"
	  name="<?php echo $this->get_field_name("only_top_level"); ?>"
	  <?php checked( (bool) $instance["only_top_level"], true ); ?> />
		  <?php _e( 'Only top level categories', 'ht-knowledge-base' ); ?>
		</label>
	  </p>
	  <p>
		<label for="<?php echo $this->get_field_id("disp_article_count"); ?>">
		  <input type="checkbox" class="checkbox"
	  id="<?php echo $this->get_field_id("disp_article_count"); ?>"
	  name="<?php echo $this->get_field_name("disp_article_count"); ?>"
	  <?php checked( (bool) $instance["disp_article_count"], true ); ?> />
		  <?php _e( 'Display article count', 'ht-knowledge-base' ); ?>
		</label>
	  </p>
	
		<?php
	}
} //end class


//Action Hook
add_action( 'widgets_init', create_function( '', 'register_widget("HT_KB_Categories_Widget");' ) );