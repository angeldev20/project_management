<?php
/**
*	Plugin Name: HelpGuru Knowledge Base
*	Plugin URI:  http://herothemes.com
*	Description: Knowledge Base plugin for WordPress 
*	Author: HeroThemes
*	Version: 3.0.0
*	Author URI: http://herothemes.com/
*	Text Domain: ht-knowledge-base
*/


if( !class_exists( 'HT_Knowledge_Base' ) ){

	//controls the global testing/debug mode
	if(!defined('HKB_TESTING_MODE')){
		define('HKB_TESTING_MODE', false);
	}

	if(!defined('HT_KB_VERSION_NUMBER')){
		define('HT_KB_VERSION_NUMBER', '3.0.0');
	}

	if(!defined('HT_USEFULNESS_KEY')){
		define('HT_USEFULNESS_KEY', '_ht_kb_usefulness');
	}

	//knowledge base cpt slug
	if(!defined('KB_CPT_SLUG')){
		define('KB_CPT_SLUG', 'knowledge-base');
	}

	//knowledge base category slug
	if(!defined('KB_CAT_SLUG')){
		define('KB_CAT_SLUG', 'article-categories');
	}

	//knowlege base tag slug
	if(!defined('KB_TAG_SLUG')){
		define('KB_TAG_SLUG', 'article-tags');
	}

	//required for Redux framework
	if(!defined('SECURE_AUTH_KEY')){
		define('SECURE_AUTH_KEY', 'HtKnOwLEdGEbase');
	}

	//required for updater
	if(!defined('HT_KB_MAIN_PLUGIN_FILE')){
		define('HT_KB_MAIN_PLUGIN_FILE', __FILE__);
	}



	//documentation/support url
	if(!defined('HT_KB_SUPPORT_URL')){
		define('HT_KB_SUPPORT_URL', 'https://herothemes.com/support/knowledge-base/helpguru-documentation/');
	}

	class HT_Knowledge_Base {

		private $temp_query;
		public $is_single, $is_singular_ht_kb, $is_ht_kb_category_tax, $is_ht_kb_tag_tax, $is_ht_kb_archive, $is_ht_kb_search, $ht_kb_is_ht_kb_front_page, $nothing_found, $taxonomy, $term, $theme_template_in_use, $custom_content_compat, $orig_post, $hkb_category, $hkb_master_tax_terms, $hkb_current_term_id, $hkb_current_article_id, $ht_calling_depth;


		//Constructor
		function __construct(){
			
			//register the ht_kb custom post type
			add_action( 'init', array( $this,  'register_ht_knowledge_base_cpt' ) );
			//register the ht_kb_category taxonomy
			add_action( 'init', array( $this,  'register_ht_knowledge_base_category_taxonomy' ) );
			//register the ht_kb_tag taxonomy
			add_action( 'init', array( $this,  'register_ht_knowledge_base_tag_taxonomy' ) );
			//maybe flush rewrite rules, lower priority
			add_action( 'init', array( $this,  'ht_knowledge_base_maybe_flush_rewrite' ), 30 );
			//load plugin textdomain
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			//check helpguru theme (or child theme) activen
			add_action( 'admin_notices', array( $this, 'helpguru_theme_active_check_messages' ) );
			//check hkb database version
			add_action( 'admin_notices', array( $this, 'database_version_messages' ) );
			//display notice if ht voting not installed
			add_action( 'admin_notices', array( $this,  'ht_kb_voting_warning' ), 10 );
			//display notice if analytics module is separate plugin
			add_action( 'admin_notices', array( $this,  'ht_kb_analytics_plugin_warning' ), 10 );
			//dismissal notice for warning
			add_action( 'admin_init', array( $this,  'ht_kb_voting_warning_ignore' ), 10 );
			//dismissal notice for warning
			add_action( 'admin_init', array( $this,  'ht_kb_voting_old_version_check' ), 10 );
			//new plugin activation check
			add_action( 'admin_init', array( $this,  'ht_kb_activation_check' ), 10 );
			//display notice for no category
			add_action( 'admin_notices', array( $this,  'ht_kb_no_category_warning' ), 20 );
			//display notice for no articles
			add_action( 'admin_notices', array( $this,  'ht_kb_no_articles_warning' ), 20 );
			//save post
			add_action( 'save_post_ht_kb', array( $this, 'save_ht_kb_article'), 10, 3 );
			//enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'ht_kb_enqueue_styles' ) );
			//add activation action for upgrades
			add_action( 'ht_kb_activate', array( $this, 'ht_kb_plugin_activation_upgrade_actions' ), 10, 1);
			//custom admin menu order
			add_filter( 'custom_menu_order' , '__return_true');
			add_filter( 'menu_order', array( $this, 'ht_kb_custom_admin_menu_order' ));	

			
			//filter the templates - note this will be overriden if theme uses single-ht_kb or archive-ht_kb
			add_filter( 'template_include', array( $this, 'ht_knowledge_base_custom_template' ) );
			

			//filter for body classes on ht_kb
			add_filter( 'body_class', array( $this, 'ht_knowledge_base_custom_body_classes' ) );
			
			//filter for the title ht_kb (inside the loop)
			add_filter( 'the_title', array( $this, 'ht_knowledge_base_custom_title' ), 10, 2 );

			//filter content for ht_kb
			add_filter( 'the_content', array( $this, 'ht_knowledge_base_custom_content' ) );
			add_filter( 'the_excerpt', array( $this, 'ht_knowledge_base_custom_content' ) );

			//search filter
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_pre_get_posts_filter' ), 10 );

			//knowledge base preview
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_modify_kb_preview_pre_get_posts' ), 20 );

			//sort order
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_modify_sort_order_pre_get_posts' ), 50 );

			//number of posts in taxonomy
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_posts_per_taxonomy' ), 50 );

			//remove dummy post from page editor list
			add_filter( 'pre_get_posts', array( $this, 'ht_kb_remove_kb_dummy_post_from_edit_screen' ), 10 );

			//comments open filter
			add_filter( 'comments_open', array( $this, 'ht_kb_comments_open' ), 10, 2 );

			//comments template filter
			add_filter( 'comments_template', array( $this, 'ht_kb_comments_template' ), 10 );

			//add to menu items
			add_action( 'admin_head-nav-menus.php', array( $this, 'ht_knowledge_base_menu_metabox' ) );
			add_filter( 'wp_get_nav_menu_items', array( $this,'ht_knowledge_base_archive_menu_filter'), 10, 3 );

			//add filter for ht_kb archive title
			add_filter( 'wp_title', array( $this, 'ht_kb_wp_title_filter' ), 10, 3 );
			//add filter for ht_kb archive title (2016+)
			add_filter( 'pre_get_document_title', array( $this, 'ht_kb_wp_title_filter' ), 10, 3 );
			//add filter for ht_kb archive title (WordPress SEO shiv), no longer needed due to ht_kb_wp_seo_fix
			//add_filter( 'wpseo_title', array( $this, 'ht_kb_wp_title_filter' ), 10, 3 );
			//custom title filter
			add_filter( 'ht_kb_wp_title', array( $this, 'ht_kb_wp_title_dummy' ), 10, 2 );

			add_action( 'wpseo_head', array( $this, 'ht_kb_wp_seo_fix' ), 10 );	
			add_action( 'wpseo_head', array( $this, 'ht_kb_wp_seo_fix' ), 10 );			

			//filter for plugin action links		
			add_filter( 'plugin_action_links', array( $this, 'ht_kb_plugin_row_action_links' ), 10, 2 );
			//filter for plugin meta links		
			add_filter( 'plugin_row_meta', array( $this, 'ht_kb_plugin_row_meta_links' ), 10, 2 );

			//add shortcode for ht_kb display
			add_shortcode( 'ht_kb_shortcode_archive', array( $this, 'ht_kb_shortcode_archive_display' ) );

			//custom front page
			add_action( 'pre_get_posts', array( $this, 'ht_knowledge_base_custom_front_page' ), 10 );

			//set posts views and article helpfulness to 0
			add_action('publish_ht_kb', array( $this, 'ht_kb_article_publish' ), 10, 2);

			//set custom order when object terms (ht_kb_categories) change
			add_action('set_object_terms', array( $this, 'ht_kb_set_object_terms' ), 10, 4);

			//add custom css
			add_action( 'wp_head', array( $this, 'ht_kb_custom_css_head' ), 10, 0);

			//add generator tag
			add_action( 'wp_head', array( $this, 'ht_kb_generator_head' ), 10, 0);

			//add custom image size
			add_image_size( 'ht-kb-thumb', 50, 50 );

			//get_pages	filter
			add_filter( 'get_pages', array( $this, 'ht_kb_filter_get_pages' ));

			//categories widget
			include_once('widgets/widget-kb-categories.php');
			//articles widget
			include_once('widgets/widget-kb-articles.php');
			//authors widget
			include_once('widgets/widget-kb-authors.php');
			//search widget
			include_once('widgets/widget-kb-search.php');
			//toc widget
			include_once('widgets/widget-kb-toc.php');
			//common functions, now deprecated
			//include_once('php/ht-knowledge-base-common-display-functions.php');
			//helper functions
			include_once('php/ht-knowledge-base-article-helpers.php');
			include_once('php/ht-knowledge-base-settings-helpers.php');
			include_once('php/ht-knowledge-base-template-helpers.php');
			//meta-boxes
			include_once('php/ht-knowledge-base-meta-boxes.php');
			//category ordering
			include_once('php/ht-knowledge-base-category-ordering.php');
			//article ordering
			include_once('php/ht-knowledge-base-article-ordering.php');
			//category meta
			include_once('php/ht-knowledge-base-category-meta.php');
			//live search
			include_once('php/ht-knowledge-base-live-search.php');
			//welcome page
			include_once('php/ht-knowledge-base-welcome.php');
			//sample installer
			include_once('php/ht-knowledge-base-sample-installer.php');
			//updater
			include_once('php/ht-knowledge-base-updater.php');

			//options new
			require_once('php/ht-knowledge-base-settings.php');
			require_once('php/ht-knowledge-base-settings-upgrade.php');
			//edit columns
			require_once('php/ht-knowledge-base-edit-columns.php');
			//ht-voting
			include_once('voting/ht-voting.php');
			//ht-analytics-core
			include_once('php/ht-knowledge-base-analytics-core.php');
			//view count
			include_once('php/ht-knowledge-base-view-count.php');
			//utility functions
			include_once('php/ht-knowledge-base-utility-functions.php');
			//exits module
			include_once('exits/ht-kb-exits.php');
			//conditionally analytics module
			@include_once('analytics/ht-knowledge-base-analytics.php');

			//dummy data modules
			//view count dummy data, for testing functionality
			//include_once('php/ht-knowledge-base-views-dummy-data-creator.php');


			//activation hook
			register_activation_hook( __FILE__, array( 'HT_Knowledge_Base', 'ht_knowlege_base_plugin_activation_hook' ) );

			//deactivation hook
			register_deactivation_hook( __FILE__, array( 'HT_Knowledge_Base', 'ht_knowlege_base_plugin_deactivation_hook' ) );	
		}


		/**
		* Initial activation to add option flush the rewrite rules
		*/
		static function ht_knowlege_base_plugin_activation_hook( $network_wide = null ){
			//flush the rewrite rules
			add_option('ht_kb_flush_rewrite_required', true);

			//perform upgrade actions, moved to a non-static option check
			//new activation method
			add_option( 'ht_kb_activate', true );

			//set network activation status
			add_option( 'ht_kb_network_activate', $network_wide );

			//set installation transient, used for welcome page
			set_transient('_ht_kb_just_installed', true);

			//add term_order to terms table
			HT_Knowledge_Base::knowledgebase_customtaxorder_activate($network_wide);
		}

		/**
		* Initial activation to add option flush the rewrite rules
		*/
		static function ht_knowlege_base_plugin_deactivation_hook(){
			//remove flush the rewrite rules option
			delete_option('ht_kb_flush_rewrite_required');
			//remove activation option
			delete_option('ht_kb_activate');
		}

		/**
		* Register the ht_kb custom post type
		*/
		function register_ht_knowledge_base_cpt(){
			$singular_item = _x('Article', 'Post Type Singular Name', 'ht-knowledge-base');
			$plural_item = _x('Articles', 'Post Type Plural Name', 'ht-knowledge-base');
			$kb_item = __('Knowledge Base', 'ht-knowledge-base');
			$rewrite = $this->get_cpt_slug();

			$labels = array(
				'name'	      =>  $singular_item,
				'singular_name'      => __('Article', 'ht-knowledge-base'),
				'add_new'            => __('Add New', 'ht-knowledge-base') . ' ' .  $singular_item,
				'add_new_item'       => __('Add New', 'ht-knowledge-base') . ' ' .  $singular_item,
				'edit_item'          => __('Edit', 'ht-knowledge-base') . ' ' .  $singular_item,
				'new_item'           => __('New', 'ht-knowledge-base') . ' ' .  $singular_item,
				'all_items'          => __('All', 'ht-knowledge-base') . ' ' .  $plural_item,
				'view_item'          => __('View', 'ht-knowledge-base') . ' ' .  $singular_item,
				'search_items'       => __('Search', 'ht-knowledge-base') . ' ' .  $plural_item,
				'not_found'          => sprintf( __( 'No %s found', 'ht-knowledge-base' ), $plural_item ),
				'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'ht-knowledge-base' ), $plural_item ),
				'parent_item_colon'  => '',
				'menu_name'          => $kb_item,
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'	 => false,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $rewrite, 'with_front'	=>	false ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_icon'			 => 'dashicons-lightbulb',
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author', 'comments', 'post-formats', 'custom-fields', 'revisions' )
			);

		  register_post_type( 'ht_kb', $args );
		}

		/**
		* Get the slug for the custom post type
		* @return (String) The CPT slug
		*/
		function get_cpt_slug(){
			$default = KB_CPT_SLUG;
			$user_option = hkb_kb_article_slug();
			$slug = ( empty( $user_option ) ) ? $default : $user_option;
			//apply filters
			$slug = apply_filters('ht_kb_cpt_slug', $slug);
			return $slug;
		}

		/**
		* Register ht_kb_category taxonomy
		*/
		function register_ht_knowledge_base_category_taxonomy(){
			$singular_item = __('Knowledge Base', 'ht-knowledge-base');
			$rewrite = $this->get_cat_slug();

			$labels = array(
				'name'                       => _x( 'Article Category', 'Taxonomy General Name', 'ht-knowledge-base' ),
				'singular_name'              => _x( 'Article Category', 'Taxonomy Singular Name', 'ht-knowledge-base' ),
				'menu_name'                  => __( 'Article Categories', 'ht-knowledge-base' ),
				'all_items'                  => __( 'All Article Categories', 'ht-knowledge-base' ),
				'parent_item'                => __( 'Parent Article Category', 'ht-knowledge-base' ),
				'parent_item_colon'          => __( 'Parent Article Category:', 'ht-knowledge-base' ),
				'new_item_name'              => __( 'New Article Category', 'ht-knowledge-base' ),
				'add_new_item'               => __( 'Add New Article Category', 'ht-knowledge-base' ),
				'edit_item'                  => __( 'Edit Article Category', 'ht-knowledge-base' ),
				'update_item'                => __( 'Update Article Category', 'ht-knowledge-base' ),
				'separate_items_with_commas' => __( 'Separate Article Categories with commas', 'ht-knowledge-base' ),
				'search_items'               => __( 'Search Article Categories', 'ht-knowledge-base' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'ht-knowledge-base' ),
				'choose_from_most_used'      => __( 'Choose from the most used categories', 'ht-knowledge-base' ),
			);
			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => true,
				'rewrite'            		 => array( 'slug' => $rewrite, 'with_front'	=>	false ),
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => false,
				'show_tagcloud'              => true,
			);
			register_taxonomy( 'ht_kb_category', 'ht_kb', $args );
		}

		/**
		* Get the slug for the category taxonomy
		* @return (String) The category slug
		*/
		function get_cat_slug(){
			$default = KB_CAT_SLUG;
			$user_option = hkb_kb_category_slug();
			$slug = ( empty( $user_option ) ) ? $default : $user_option;
			//apply filters
			$slug = apply_filters('ht_kb_cat_slug', $slug);
			return $slug;
		}

		/**
		* Register ht_kb_tag taxonomy
		*/
		function register_ht_knowledge_base_tag_taxonomy()  {
			$singular_item = __('Knowledge Base Tag', 'ht-knowledge-base');
			$rewrite = $this->get_tag_slug();

			$labels = array(
				'name'                       => _x( 'Article Tags', 'Taxonomy General Name', 'ht-knowledge-base' ),
				'singular_name'              => _x( 'Article Tag', 'Taxonomy Singular Name', 'ht-knowledge-base' ),
				'menu_name'                  => __( 'Article Tags', 'ht-knowledge-base' ),
				'all_items'                  => __( 'All Tags', 'ht-knowledge-base' ),
				'parent_item'                => __( 'Parent Tag', 'ht-knowledge-base' ),
				'parent_item_colon'          => __( 'Parent Tag:', 'ht-knowledge-base' ),
				'new_item_name'              => __( 'New Tag Name', 'ht-knowledge-base' ),
				'add_new_item'               => __( 'Add New Tag', 'ht-knowledge-base' ),
				'edit_item'                  => __( 'Edit Tag', 'ht-knowledge-base' ),
				'update_item'                => __( 'Update Tag', 'ht-knowledge-base' ),
				'separate_items_with_commas' => __( 'Separate tags with commas', 'ht-knowledge-base' ),
				'search_items'               => __( 'Search tags', 'ht-knowledge-base' ),
				'add_or_remove_items'        => __( 'Add or remove tags', 'ht-knowledge-base' ),
				'choose_from_most_used'      => __( 'Choose from the most used tags', 'ht-knowledge-base' ),
			);

			$rewrite_arr = array(
				'slug'                       => $rewrite,
				'with_front'                 => false,
				'hierarchical'               => false,
			);

			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => false,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
				'query_var'                  => 'article_tag',
				'rewrite'                    => $rewrite_arr,
			);

			register_taxonomy( 'ht_kb_tag', 'ht_kb', $args );
		}

		/**
		* Get the slug for the tag taxonomy
		* @return (String) The tag slug
		*/
		function get_tag_slug(){
			$default = KB_TAG_SLUG;
			$user_option = hkb_kb_tag_slug();
			$slug = ( empty( $user_option ) ) ? $default : $user_option;
			//apply filters
			$slug = apply_filters('ht_kb_tag_slug', $slug);
			return $slug;
		}

		/**
		* Flush rewrite rules if required
		*/
		function ht_knowledge_base_maybe_flush_rewrite(){
			// Check the option we set on activation.
			if (true == get_option('ht_kb_flush_rewrite_required')) {
				flush_rewrite_rules();
				delete_option('ht_kb_flush_rewrite_required');
			}
		}

		/**
		* Load plugin textdomain
		*/
		function load_textdomain(){
			load_plugin_textdomain('ht-knowledge-base', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		* Check HelpGuru theme active
		*/
		function helpguru_theme_active_check_messages(){
			//only proceed if we are admin and can activate plugins
			if(defined('HT_HELPGURU_PARENT_THEME_ACTIVE')){
				return;
			}

				$message = __('HelpGuru is not active, you must use HelpGuru as the active or child theme. ', 'ht-knowledge-base');
				if(current_user_can('switch_themes')){
					$message .= sprintf( __('Please <a href="%s">switch themes</a> now.', 'ht-knowledge-base'), admin_url('themes.php') );
				} else {
					$message .= __('Please inform the website administrator of this issue.', 'ht-knowledge-base');
				}
				echo '<div class="error"><p>' . $message . '</p></div>';
		}


		/**
		* Displays any database version check warnings / errors
		*/
		function database_version_messages(){
			//only proceed if we are admin and can activate plugins
			if(!is_admin() || !current_user_can('activate_plugins')){
				return;
			}

			$database_check = $this->check_database_version();

			if( is_admin() && is_wp_error( $database_check ) ){
				$message = $database_check->get_error_message();
				echo '<div class="error"><p>' . $message . '</p></div>';
			}
		}

		/**
		* Check for correct database version
		*/
		function check_database_version(){
			$ht_knowledge_base_anayltics_db_version_ok = false;
			$db_version = get_option('hkb_analytics_search_atomic_db_version');
			if(FALSE===$db_version){
				//do nothing
			} else {
				$ht_knowledge_base_anayltics_db_version_ok = true;
			}
			if( ! $ht_knowledge_base_anayltics_db_version_ok && ! get_transient('_ht_kb_just_installed') ) {
				return new WP_Error( 'ht-kb-db-upgrade-required', sprintf( __( 'You need upgrade your Knowledge Base database, <a href="%s">Deactivate</a> then re-Activate the HelpGuru Knowledge Base plugin', 'ht-knowledge-base'), admin_url('plugins.php#helpguru-knowledge-base') ) );
			}
			return false;
		}
		
		/**
		* Custom template filter
		* @param (String) $template The template file
		* @param (String) The filtered template file
		*/
		function ht_knowledge_base_custom_template($template) {
			global $wp_query, $post;

			$template_basename = basename($template);

			do_action('ht_knowledge_base_custom_template');
			
			$this->is_single = is_single();
			$this->is_singular_ht_kb = is_singular( 'ht_kb' );
			$this->is_ht_kb_category_tax = is_tax( 'ht_kb_category' );
			$this->is_ht_kb_tag_tax = is_tax( 'ht_kb_tag' );
			$this->is_ht_kb_archive = is_post_type_archive( 'ht_kb' );
			$this->is_ht_kb_search = ( array_key_exists('ht-kb-search', $_REQUEST) ) ? true : false;    
			//set the taxonmy and terms if is_tax (these are used for breadcrumbs)
			if( $this->is_ht_kb_category_tax || $this->is_ht_kb_tag_tax ){
				$this->set_taxonomy_and_terms();
			}	

			//check for search results - load hkb-search-results
			if($this->is_ht_kb_search){
				$search_results_template = hkb_locate_template('hkb-search');
				//only use hkb-search-results if it's a theme template, not plugin template
				if($this->theme_template_in_use){
					return $search_results_template;
				}
			}

			//theme re-routes
			$reroute_template = '';
			//archive, category, tag, single
			if( $this->is_ht_kb_archive || ht_kb_is_ht_kb_front_page() ){
				$reroute_template = hkb_locate_template('hkb-archive');
			} elseif($this->is_ht_kb_category_tax){
				$reroute_template = hkb_locate_template('hkb-taxonomy-category');
			} elseif($this->is_ht_kb_tag_tax){
				$reroute_template = hkb_locate_template('hkb-taxonomy-tag');
			} elseif($this->is_singular_ht_kb){
				$reroute_template = hkb_locate_template('hkb-single');
			}
			
			//only use reroute template if theme template
			if($this->theme_template_in_use){
				return $reroute_template;
			}


			//define compat templates
			$compat_templates = array(
				'page.php',
				'custom-page.php',
				'single.php',
				'index.php',
				'archive.php',
				'search.php'
			);

			//check usage of compat template
			$compat_template_in_use = false;
			foreach ($compat_templates as $key => $compat_template) {
				$templatestringlen = strlen($template);
				$compat_template_len = strlen($compat_template);
				if ($compat_template_len > $templatestringlen) continue;
				if( substr_compare($template, $compat_template, $templatestringlen - $compat_template_len, $compat_template_len) === 0 ){
					//check it's not a regular search
					if( $compat_template=='search.php' && !$this->is_ht_kb_search ){
						$compat_template_in_use = false;
					} else {
						$compat_template_in_use = true;
					}			    	
				}
			}


			//Use the the theme template if one exists
			//This will load archive-ht_kb for example, from the theme
			//@todo - check working with child theme
			//@todo - check theme_template_in_use conflict
			if(file_exists($template) && !$compat_template_in_use){
				$this->theme_template_in_use = true; 
				return $template;
			}

			$post_id = isset($post->ID) ? $post->ID : 0;

			//Dummy post default
			$dummy_post_default  = array(
										'ID'             => $post_id,
										'post_title'     => __('Knowledge Base', 'ht-knowledge-base'),
										'post_author'    => 0,
										'post_date'      => 0,
										'post_content'   => ' ',
										'post_excerpt'   => ' ',
										'post_type'      => 'ht_kb',
										'is_archive'     => true,
										'comment_status' => 'closed'
									);
			//Nothing found
			if (isset($wp_query)){
				$query_vars =  $wp_query->query_vars;
				if( ( array_key_exists('ht_kb_category', $query_vars) || array_key_exists('ht_kb_tag', $query_vars)  || $this->is_ht_kb_search ) &&
					isset($wp_query->post_count) &&
					$wp_query->post_count == 0 ) {
					//nothing found
					$this->is_ht_kb_category_tax = (array_key_exists('ht_kb_category', $query_vars)) ? true : false;
					$this->is_ht_kb_tag_tax = (array_key_exists('ht_kb_tag', $query_vars)) ? true : false;
					$this->nothing_found = true;
					//clone the old query
					$this->temp_query = clone $wp_query;

					// Reset post
					$post =	$this->ht_kb_theme_compat_reset_post( $dummy_post_default );


					return locate_template($compat_templates, false, false);

				}
				

			}
			
			//HT KB Archive / Article
			if (isset($post) && $post->post_type == "ht_kb" && !is_author()){
				//clone the old query
				$this->temp_query = clone $wp_query;

				$queried_object = isset($wp_query->queried_object) ? $wp_query->queried_object : null;


				if(is_a($queried_object, 'WP_Post')){
					//posts/articles
					//set the default items
					$dummy_post_default['post_title'] = $queried_object->post_title;
					$dummy_post_default['post_author'] = $queried_object->post_author;
					$dummy_post_default['post_date'] = $queried_object->post_date;
					//populate the post_excerpt if not empty, fix for SEO plugins
					if(isset($queried_object->post_excerpt) && !empty($queried_object->post_excerpt)){
						$dummy_post_default['post_excerpt'] = $queried_object->post_excerpt;
					}
				} elseif (isset($queried_object->name)) {
					if(is_tax()){
						//for categories
						$dummy_post_default['post_title'] = $queried_object->name;
					}
					//populate the post_excerpt if not empty, fix for SEO plugins
					if(isset($queried_object->description) && !empty($queried_object->description)){
						$dummy_post_default['post_excerpt'] = $queried_object->description;
					}
					//remove edit article link from admin bar menu
					add_action( 'admin_bar_menu', array( $this, 'ht_kb_remove_edit_option_from_admin_bar' ), 999 );
					//re-add the correct edit taxonomy or none
					add_action( 'admin_bar_menu', array( $this, 'ht_kb_add_edit_option_to_admin_bar' ), 1000 );
				} else {
					//knowledge base archive

					//remove edit article link from admin bar menu
					add_action( 'admin_bar_menu', array( $this, 'ht_kb_remove_edit_option_from_admin_bar' ), 999 );
				}

				//Reset post
				$post =	$this->ht_kb_theme_compat_reset_post( $dummy_post_default );

				return locate_template($compat_templates, false, false);
			}

			//Search Results
			if (isset($post) && $this->is_ht_kb_search){

				//clone the old query
				$this->temp_query = clone $wp_query;

				// Reset post
				$post =	$this->ht_kb_theme_compat_reset_post( $dummy_post_default );


				return locate_template($compat_templates, false, false);

			}
			return $template;
		}

		/**
		* Set the tax and terms
		*/
		function set_taxonomy_and_terms(){
			global $wp_query;
			$this->taxonomy = @$wp_query->tax_query->queries[0]['taxonomy'];
			$this->term = @$wp_query->tax_query->queries[0]['terms'][0];
		}

		/**
		* Custom content filter
		* @param (Array) $classes The current classes
		* @return (Array) Filtered classes
		*/
		function ht_knowledge_base_custom_body_classes( $classes = array() ) {
			global $post;
			if( isset( $post ) && $post->post_type == 'ht_kb' ){
				$classes[] = 'ht-kb';
			}
			return $classes;
		}

		/**
		* Custom content filter placeholder
		* @param (String) $title The current title
		* @param (Int) $id Post ID
		* @return (String) Filtered title
		*/
		function ht_knowledge_base_custom_title( $title, $id = null ) {
			global $post;
			if( (isset( $post ) && $post->post_type == 'ht_kb' && !is_author()) && !$this->custom_content_compat && !$this->is_ht_kb_search){
				//return knowledge base as the title tag (entry), may also affect other areas the title appears such as widgets and feeds
				//false to return to original title
				if($this->is_ht_kb_archive && 'ht_kb' == $title){
					$title = __('Knowledge Base', 'ht-knowledge-base');
					return apply_filters('ht_knowledge_base_custom_title', $title);
				} else {
					//check title not empty
					if(''==$title){
						$title = __('Untitled Article', 'ht-knowledge-base');
					}
					return apply_filters('ht_knowledge_base_custom_title', $title);
				}				
			}
			return $title;
		}

		/**
		* Custom content filter
		* @param (String) $content The current content
		* @return (String) Filtered content
		*/
		function ht_knowledge_base_custom_content( $content ){
			global $post, $wp_query, $hkb_current_article_id;

			//don't use in admin
			if(is_admin()){
				return $content;
			}

			//don't use in rss feed
			if(is_feed()){
				return $content;
			}

			//json rest api fix
			if(isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false ){
				return $content;
			}

			//disable content
			if(apply_filters('stop_ht_knowledge_base_custom_content', false)){
				return $content;
			}

			//don't use if current theme template in use
			if( true == $this->theme_template_in_use ){
				$hkb_current_article_id = @$wp_query->post->ID;
				//wp_enqueue_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
				remove_filter( 'the_content', array($this, 'ht_knowledge_base_custom_content') );
				remove_filter( 'the_excerpt', array($this, 'ht_knowledge_base_custom_content') );
				return $content;
			}


			//dont use if ajax search
			if(!empty($_GET['ajax']) ? $_GET['ajax'] : null || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ){
				remove_filter( 'the_content', array($this, 'ht_knowledge_base_custom_content') );
				remove_filter( 'the_excerpt', array($this, 'ht_knowledge_base_custom_content') );
				return $content;
			}

			//start custom content_compat mode
			$this->custom_content_compat = true;

			if( (isset( $post ) && $post->post_type == 'ht_kb' && !is_author()) ){
				ob_start();
				
				//remove filters
				remove_filter( 'the_content', array($this, 'ht_knowledge_base_custom_content') );
				remove_filter( 'the_excerpt', array($this, 'ht_knowledge_base_custom_content') );

				//restore query
				$wp_query = $this->temp_query;

				if( $this->is_single ){
					$hkb_current_article_id = $wp_query->post->ID;
					hkb_get_template_part('hkb-compat-single');
				} else {
					if ( $this->is_ht_kb_category_tax ){
						//category
						hkb_get_template_part('hkb-compat-taxonomy', 'category');
					} elseif ( $this->is_ht_kb_tag_tax ){
						//tag
						hkb_get_template_part('hkb-compat-taxonomy', 'tag');
					} elseif ( $this->is_ht_kb_search ){
						//search results
						//@todo - decide what to do about this functionality
						hkb_get_template_part('hkb-compat-search');
					//else default to cpt archive
					} else {
						//archive
						hkb_get_template_part('hkb-compat-archive');	
					}
				}

				//reset the query if single
				if(!$this->is_single)
					wp_reset_query();

				//skip over first post
				if($this->is_single)
					the_post();

				if($this->is_single){
					$wp_query->posts_per_page = -1;
				}

				//fix for some themes that try to do post navigation after the loop
				$wp_query->max_num_pages = 1;
				//remove pagination
				global $multipage;
				$multipage = false;


				add_filter( 'the_content', array($this, 'ht_knowledge_base_custom_content') );
				add_filter( 'the_excerpt', array($this, 'ht_knowledge_base_custom_content') );
				$output = ob_get_clean();

				//end custom content_compat mode
				$this->custom_content_compat = false;
				
				//return possibly hi-jacked content
				return $output;			
			}
			//base case
			return $content;
		}

		/**
		 * This function fills up some WordPress globals with dummy data to
		 * stop page template from complaining about it missing.
		 * It's based on the bbPress functionality
		 *
		 * @credit bbPress (r3108)
		 * @global WP_Query $wp_query
		 * @global object $post
		 * @param array $args
		 */
		function ht_kb_theme_compat_reset_post( $args = array() ) {
			global $wp_query, $post;

			$post_status = isset( $wp_query->post ) ? $wp_query->post->post_status : 'published';

			//Default arguments
			$defaults = array(
				'ID'                    => -9999,
				'post_status'           => $post_status,
				'post_author'           => 0,
				'post_parent'           => 0,
				'post_type'             => 'page',
				'post_date'             => 0,
				'post_date_gmt'         => 0,
				'post_modified'         => 0,
				'post_modified_gmt'     => 0,
				'post_content'          => '',
				'post_title'            => '',
				'post_category'         => 0,
				'post_excerpt'          => '',
				'post_content_filtered' => '',
				'post_mime_type'        => '',
				'post_password'         => '',
				'post_name'             => '',
				'guid'                  => '',
				'menu_order'            => 0,
				'pinged'                => '',
				'to_ping'               => '',
				'ping_status'           => '',
				'comment_status'        => 'closed',
				'comment_count'         => 0,

				'is_404'          => false,
				'is_page'         => false,
				'is_single'       => false,
				'is_singular'       => true,
				'is_archive'      => false,
				'is_tax'          => false,
			);

			//Switch defaults if post is set
			if ( isset( $wp_query->post ) ) {		  
				$defaults = array(
					'ID'                    => $wp_query->post->ID,
					'post_status'           => $wp_query->post->post_status,
					'post_author'           => $wp_query->post->post_author,
					'post_parent'           => $wp_query->post->post_parent,
					'post_type'             => $wp_query->post->post_type,
					'post_date'             => $wp_query->post->post_date,
					'post_date_gmt'         => $wp_query->post->post_date_gmt,
					'post_modified'         => $wp_query->post->post_modified,
					'post_modified_gmt'     => $wp_query->post->post_modified_gmt,
					'post_content'          => $wp_query->post->post_content,
					'post_title'            => $wp_query->post->post_title,
					'post_category'         => $wp_query->post->post_category,
					'post_excerpt'          => $wp_query->post->post_excerpt,
					'post_content_filtered' => $wp_query->post->post_content_filtered,
					'post_mime_type'        => $wp_query->post->post_mime_type,
					'post_password'         => $wp_query->post->post_password,
					'post_name'             => $wp_query->post->post_name,
					'guid'                  => $wp_query->post->guid,
					'menu_order'            => $wp_query->post->menu_order,
					'pinged'                => $wp_query->post->pinged,
					'to_ping'               => $wp_query->post->to_ping,
					'ping_status'           => $wp_query->post->ping_status,
					'comment_status'        => $wp_query->post->comment_status,
					'comment_count'         => $wp_query->post->comment_count,

					'is_404'          => false,
					'is_page'         => false,
					'is_single'       => false,
					'is_singular'       => true,
					'is_archive'      => false,
					'is_tax'          => false,
				);
			}
			$dummy = wp_parse_args( $args, $defaults );

			//Clear out the post related globals
			unset( $wp_query->posts );
			unset( $wp_query->post );
			unset( $post );

			//Setup the dummy post object
			$wp_query->post                        = new stdClass; 
			$wp_query->post->ID                    = $dummy['ID'];
			$wp_query->post->post_status           = $dummy['post_status'];
			$wp_query->post->post_author           = $dummy['post_author'];
			$wp_query->post->post_parent           = $dummy['post_parent'];
			$wp_query->post->post_type             = $dummy['post_type'];
			$wp_query->post->post_date             = $dummy['post_date'];
			$wp_query->post->post_date_gmt         = $dummy['post_date_gmt'];
			$wp_query->post->post_modified         = $dummy['post_modified'];
			$wp_query->post->post_modified_gmt     = $dummy['post_modified_gmt'];
			$wp_query->post->post_content          = $dummy['post_content'];
			$wp_query->post->post_title            = $dummy['post_title'];
			$wp_query->post->post_category         = $dummy['post_category'];
			$wp_query->post->post_excerpt          = $dummy['post_excerpt'];
			$wp_query->post->post_content_filtered = $dummy['post_content_filtered'];
			$wp_query->post->post_mime_type        = $dummy['post_mime_type'];
			$wp_query->post->post_password         = $dummy['post_password'];
			$wp_query->post->post_name             = $dummy['post_name'];
			$wp_query->post->guid                  = $dummy['guid'];
			$wp_query->post->menu_order            = $dummy['menu_order'];
			$wp_query->post->pinged                = $dummy['pinged'];
			$wp_query->post->to_ping               = $dummy['to_ping'];
			$wp_query->post->ping_status           = $dummy['ping_status'];
			$wp_query->post->comment_status        = $dummy['comment_status'];
			$wp_query->post->comment_count         = $dummy['comment_count'];

			// Set the $post global
			$post = $wp_query->post;

			// Setup the dummy post loop
			$wp_query->posts = array($post);


			// Prevent comments form from appearing
			$wp_query->post_count = 1;
			$wp_query->is_404     = $dummy['is_404'];
			$wp_query->is_page    = $dummy['is_page'];
			$wp_query->is_single  = $dummy['is_single'];
			$wp_query->is_singular = $dummy['is_singular'];
			$wp_query->is_archive = $dummy['is_archive'];
			$wp_query->is_tax     = $dummy['is_tax'];
			
			$wp_query->queried_object  	= $wp_query->post;
			$wp_query->queried_object_id  	= $wp_query->post->ID;

			return $post;
		}

		/**
		* Custom pre get posts filter for knowledge base search and author archive
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_pre_get_posts_filter( $query ) {
			//assign is_ht_kb_search
			$this->is_ht_kb_search = ( array_key_exists('ht-kb-search', $_REQUEST) ) ? true : false;


			//live search 
			if ( !is_preview() && !is_singular() && !is_admin() && $this->is_ht_kb_search ) {

				//configurable post types are now set with hkb_search_post_types filter
				$post_types = apply_filters( 'hkb_search_post_types', array('ht_kb') );

				$existing_post_type = (!empty($query) && isset($query->query_vars) && is_array($query->query_vars) && array_key_exists('post_type', $query->query_vars) ) ? $query->query_vars['post_type'] : null; 
				if ( empty( $existing_post_type ) ) {
					//update post_type variable
					$query->set( 'post_type' , $post_types );
					//supress filters false for wpml compatibility
					//$query->set( 'suppress_filters' , 0 );
				}
			}
			
			//author archive
			if ( !is_preview() && !is_singular() && !is_admin() && $query->is_author ) {
				//can add more post types here, eg forum topics/replies
				$post_types = array('ht_kb', 'post');
				
				$existing_post_type = (!empty($query) && isset($query->query_vars) && is_array($query->query_vars) && array_key_exists('post_type', $query->query_vars) ) ? $query->query_vars['post_type'] : null; 
				if ( empty( $existing_post_type ) ) {
					//update post_type variable
					$query->set( 'post_type' , $post_types );
				}

			}

			//search elsewhere on site, not implemented in this version
			/*
			if ($query->is_search && !$this->is_ht_kb_search && hkb_kb_search_sitewide()) {
				$search_post_types = get_query_var( 'post_type', array() );
				$search_post_types[] = 'ht_kb';
				$query->set('post_type', $search_post_types);
			}
			*/

			return $query;
		}

		/**
		* Comments open filter
		* @param (boolean) $open Unfiltered comments open status
		* @return (boolean) Filtered comments open
		*/
		function ht_kb_comments_open( $open, $post_id ) {

			 $post = get_post( $post_id );
			 
			 //check if post type is knowledge base
			 if($post->post_type == 'ht_kb'){ 
				if( hkb_show_comments_display() ){
					return $open;
				} else {
					return false;
				}
			 }

			 return $open;
		}

		/**
		* Comments template filter
		* @param (String) $comment_template The comment template file
		* @return (String) Filtered comment template file
		*/
		function ht_kb_comments_template( $comment_template ) {
			 global $post;
			 if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
				return $comment_template;
			 }
			 //check if post type is knowledge base
			 if($post->post_type == 'ht_kb'){ 
				if( hkb_show_comments_display() ){
					return apply_filters('hk_kb_comments_template', $comment_template);
				} else {
					//don't return the template if closed
					return apply_filters('hk_kb_comments_template', false);
				}
			 }
			 return $comment_template;

		}

		/**
		* Admin warning message if Voting not Module installed
		*/
		function ht_kb_voting_warning() {
			global $current_user;
			$user_id = $current_user->ID;
			if( !class_exists('HT_Voting')  &&  current_user_can( 'install_plugins' ) && !get_user_meta($user_id, 'ht_voting_warning_ignore')  && current_theme_supports('ht-kb-theme-voting-suggested')   ):		    
			?>
				<div class="update-nag">
					<p><?php  printf(__('The voting module is required to use voting features | <a href="%1$s">Hide Notice</a>', 'ht-knowledge-base'), '?ht_voting_warning_ignore=1'); ?></p>
				</div>
			<?php
			endif; //end class exists
		}

		/**
		* Admin warning message dismissal
		*/		
		function ht_kb_voting_warning_ignore() {
			global $current_user;
			$user_id = $current_user->ID;
			if ( isset($_GET['ht_voting_warning_ignore']) && '1' == $_GET['ht_voting_warning_ignore'] ) {
				 add_user_meta($user_id, 'ht_voting_warning_ignore', 'true', true);
			}
		}

		/**
		* Admin warning message if HKB Analytics Plugin is installed and activated
		*/
		function ht_kb_analytics_plugin_warning() {
			global $current_user;
			$user_id = $current_user->ID;
			if( class_exists('HKB_Analytics')  &&  current_user_can( 'install_plugins' )   ):		    
			?>
				<div class="update-nag">
					<p><?php  printf(__('The Knowledge Base Analytics module is active and is no longer required | Please <a href="%1$s">Deactivate the  Analytics Plugin</a>', 'ht-knowledge-base'), admin_url('plugins.php')); ?></p>
				</div>
			<?php
			endif; //end class exists
		}


		/** 
		* Old voting version check and deactivate
		*/
		function ht_kb_voting_old_version_check() {
			//check for file
			$old_voting_file_path = dirname(dirname( __FILE__ )) . '/ht-voting/ht-voting.php';
			if( file_exists( $old_voting_file_path) ){
				add_action('admin_notices', array($this, 'ht_kb_voting_old_version_check_notice'));
			}

			//deactivate if necessary
			if( is_plugin_active( plugin_basename( $old_voting_file_path ) ) ){
				deactivate_plugins( plugin_basename( $old_voting_file_path ) );
			} 
		}

		/**
		* Admin warning message if old voting module installed
		*/
		function ht_kb_voting_old_version_check_notice() {	    
			?>
				<div class="update-nag">
					<p><?php  _e('An old version of the Voting module was detected and deactivated, please remove the Voting plugin and ht-voting folder', 'ht-knowledge-base'); ?></p>
				</div>
			<?php
		}

		/**
		* Activation check, on action as of 2.0.7
		*/
		function ht_kb_activation_check(){
			if ( is_admin() && true == get_option('ht_kb_activate') ) {
				//network_activate check
				$network_activate = get_option('ht_kb_network_activate');
				//perform the actions
				do_action('ht_kb_activate', $network_activate);
				//delete the option
				delete_option('ht_kb_activate');
				//delete network activate option
				delete_option('ht_kb_network_activate');
			}
		}

		/**
		* Adds the HT Knowledge Base Menu Metabox
		*/
		function ht_knowledge_base_menu_metabox() {
			add_meta_box( 'add_ht_knowledge_base_item', __('HelpGuru Knowledge Base Archive', 'ht-knowledge-base'), array( $this, 'ht_knowledge_base_menu_metabox_content' ), 'nav-menus', 'side', 'default' );
		}

		/**
		* Adds the HT Knowledgebase Metabox Content
		*/
		function ht_knowledge_base_menu_metabox_content() {
			
			//Create menu items and store IDs in array
			$item_ids = array();
			$post_type = 'ht_kb';
			$post_type_obj = get_post_type_object( $post_type );

			if( ! $post_type_obj )
				return;

			//Add menu data
			$menu_item_data = array(
				 'menu-item-title'  => esc_attr( $post_type_obj->labels->menu_name ),
				 'menu-item-type'   => $post_type,
				 'menu-item-object' => esc_attr( $post_type ),
				 'menu-item-url'    => get_post_type_archive_link( $post_type )
			);

			//Add the menu item
			$item_ids[] = wp_update_nav_menu_item( 0, 0, $menu_item_data );

			//Die on error
			is_wp_error( $item_ids ) AND die( '-1' );

			// Set up the menu items
			foreach ( (array) $item_ids as $menu_item_id ) {
				$menu_obj = get_post( $menu_item_id );
				if ( ! empty( $menu_obj->ID ) ) {
					$menu_obj->classes = array();
					$menu_obj->label = __('HelpGuru Knowledge Base Archive', 'ht-knowledge-base');
					$menu_obj->object_id = $menu_obj->ID;
					$menu_obj->object = 'ht-knowledge-base';						
					$menu_items[] = $menu_obj;

				}
			}

			$menus = array_map('wp_setup_nav_menu_item', $menu_items);
			$walker = new Walker_Nav_Menu_Checklist( array() );
	
			echo '<div id="ht-knowledge-base-archive" class="posttypediv">';
			echo '<div id="tabs-panel-ht-knowledge-base-archive" class="tabs-panel tabs-panel-active">';
			echo '<ul id="ctp-archive-checklist" class="categorychecklist form-no-clear">';
			echo walk_nav_menu_tree( $menus, 0, (object) array( 'walker' => $walker) );
			echo '</ul>';
			echo '</div><!-- /.tabs-panel -->';
			echo '</div>';
			echo '<p class="button-controls">';
			echo '<span class="add-to-menu">';
			echo '<input type="submit" class="button-secondary submit-add-to-menu" value="' . __('Add to Menu', 'ht-knowledge-base') . '" name="add-ht-knowledge-base-archive-menu-item" id="submit-ht-knowledge-base-archive" />';
			echo '</span>';
			echo '</p>';
			
		}

		/**
		* Menu filter for HT Knowledge Base Archive
		* @param $items The Items
		* @param $menu Menu
		* @param $args Additional params
		*/
		function ht_knowledge_base_archive_menu_filter( $items, $menu, $args ) {
			foreach( $items as $item ) {
				if( $item->object != 'ht-knowledge-base' ) continue;
				$item->url = get_post_type_archive_link( $item->type );
		  
				if( get_query_var( 'post_type' ) == $item->type ) {
					$item->classes[] = 'current-menu-item';
					$item->current = true;
				}
			}
			
			return $items;
		}

		/**
		* Pre posts query filter for kb custom front page
		* @param (Object) $query The query to modify
		*/
		function ht_knowledge_base_custom_front_page($query) {
			global $wp_the_query;
			global $post;

			if(!is_admin() && 
				$wp_the_query===$query && 
				$query->is_main_query() &&
				'page' == get_option( 'show_on_front' ) &&
				$query->is_page && 
				$query->query_vars['page_id'] == get_option('page_on_front')
				){
						
						//get the dummy page id 
						$dummy_page_id  = $this->get_ht_kb_dummy_page_id();
						
						if(defined('ICL_LANGUAGE_CODE')){
							//translate the dummy page id if necessary
							$dummy_page_id = icl_object_id($dummy_page_id, 'page', false, ICL_LANGUAGE_CODE);
						}
						
						//check the page on front and the kb_archive ID are the same
						if(get_option('page_on_front') == $dummy_page_id){
							//continue
						} else {
							//else return
							return;
						}

						//get the site name
						$site_name = get_bloginfo('name');
						
						//set dummy post
						$post = $this->ht_kb_theme_compat_reset_post( array(
							'ID'             => 0,
							'post_title'	 => $site_name,
							'post_author'    => 0,
							'post_date'      => 0,
							'post_content'   => '',
							'post_type'      => 'ht_kb',
							'is_archive'     => true,
							'comment_status' => 'closed'
						) );	
						
						//set query
						$query->set( 'post_type','ht_kb' );
						$query->set( 'posts_per_page',-1 );
						$query->set( 'page', 0 );
						$query->set( 'post_status', 'public' );
						$query->set( 'page_id', '' );
						
						$this->ht_kb_is_ht_kb_front_page=true;

						//override option to edit article from wp admin bar
						add_action( 'admin_bar_menu', array( $this, 'ht_kb_remove_edit_option_from_admin_bar' ), 999 );
						
						//remove breadcrumbs filter
						add_filter( 'ht_show_breadcrumbs', array( $this, 'ht_remove_breadcrumbs' ), 10 );
						
					}			
		}

		/**
		* Add custom css to head
		*/
		function ht_kb_custom_css_head(){
			if(		hkb_custom_styles_sitewide() ||	
					$this->is_singular_ht_kb ||
					$this->is_ht_kb_category_tax ||
					$this->is_ht_kb_tag_tax ||
					$this->is_ht_kb_archive ||
					$this->is_ht_kb_search ){
						echo hkb_get_custom_styles_css();
			}			
		}

		/**
		* Add generator to head
		*/
		function ht_kb_generator_head(){
			echo apply_filters('ht_kb_generator_head', '<meta name="generator" content="HelpGuru Knowledge Base v' . HT_KB_VERSION_NUMBER. '" />' . "\n" );			
		}

		/**
		* Filter to remove breadcrumbs for all registered hooks
		*/
		function ht_remove_breadcrumbs( $status ){
			return false;
		}

		/**
		* Remove edit option from the admin bar
		* @param (Object) $wp_admin_bar Unfiltered admin bar
		*/
		function ht_kb_remove_edit_option_from_admin_bar( $wp_admin_bar ){
				$wp_admin_bar->remove_node('edit');			
		}

		/**
		* Add edit option to the admin bar if taxonmy term
		* @param (Object) $wp_admin_bar Unfiltered admin bar
		*/
		function ht_kb_add_edit_option_to_admin_bar( $wp_admin_bar ){
			//check tax and term set
			if( isset($this->taxonomy) && isset($this->term) ){
				$taxonomy = get_taxonomy($this->taxonomy);
				$term = get_term_by('slug', $this->term, $this->taxonomy);
				if( isset($taxonomy) && isset($term) ){
					//build the edit term url
					$edit_term_url = 'edit-tags.php?' . 'action=edit&taxonomy=' . $this->taxonomy . '&tag_ID=' . $term->term_id . '&post_type=ht_kb';
					$args = array(
									'id'    => 'edit_ht_kb_term',
									'title' => $taxonomy->labels->edit_item,
									'href'  => admin_url( $edit_term_url ),
									'meta'  => array( 'class' => 'edit-term-item' )
						);
					$wp_admin_bar->add_node( $args );
				}
			}				
		}

		/**
		* Modify <title> for ht_kb archive page
		* Note for users of SEO plugins, hook to this function, response will be the just the main title
		* eg. Article Name, Category Name or Knowledge Base for the archive
		* @param (String) $title Unfiltered page title
		* @return (String) Filtered page title
		*/
		function ht_kb_wp_title_filter( $title, $sep=' ', $seplocation=null ) {
			global $post, $wp_query;

			//define pad string
			$pad_str = " ";

			if(!is_archive() || !is_object($post) ){
				//don't do anything if not an archive or post not object
				return $title;
			} else {
				
				$main_title = __('Knowledge Base', 'ht-knowledge-base');
				$site_name = get_bloginfo('name');
				
				if(isset($wp_query->query_vars['ht_kb']) && !empty($wp_query->query_vars['ht_kb'])){
					//article - post name
					//@todo - identify more efficient method for getting post id here
					$slug = $wp_query->query_vars['ht_kb'];
					$page = get_page_by_path( $slug, 'OBJECT', 'ht_kb' );
					if(isset($page) && is_a($page, 'WP_Post') ){
						//get the article title
						$title_front = get_the_title($page->ID);
					} else {
						//default
						$title_front = __('Article', 'ht-knowledge-base');
					}
				} elseif(isset($this->term) && isset($this->taxonomy)){
					//ht_kb_category or ht_kb_tag
					$term = get_term_by( 'slug', $this->term, $this->taxonomy );
					if($term){
						$title_front = $term->name;
					} else {
						$title_front = $main_title; 
					}
				} elseif(isset($wp_query->query_vars['post_type']) && !empty($wp_query->query_vars['post_type']) && ('ht_kb' == $wp_query->query_vars['post_type'])){
					//archive
					$title_front = $main_title;
				} else {
					//not something we are interested in 
					return $title;
				}				

				//pad separator if required
				if(function_exists('ctype_space')){
					$sep = (ctype_space($sep)) ? $sep : $pad_str . $sep . $pad_str;
				} else {
					$sep = $pad_str . $sep; 
				}				

				//build new title
				$filtered_title = $title_front . $sep;

				//filter types for post types, other posts types can go here
				$types = array(
					array( 
						'post_type' => 'ht_kb', 
						'title' => apply_filters( 'ht_kb_wp_title', $filtered_title . $pad_str, 'ht_kb' )
					)
				);

				$post_type = $post->post_type;

				//iterate over types to filter the the title
				foreach ( $types as $key => $value) {
					if ( in_array($post_type, $types[$key])) {
						return $types[$key]['title'];
					}
				}

				//no match found for this post else just return the title
				return $title;
			}
		}


		/**
		* Dummy function for ht_kb_wp_title filter, utilize this function in a theme's functions.php
		* This way you can add additional suffixes to the title
		* @param (String) $title The unfiltered title
		* @param (String) $post_type The archive post type
		* @return (String) Filtered title
		*/
		function ht_kb_wp_title_dummy($title='', $post_type='ht_kb'){
			return $title;
		}

		/**
		* This function fixes the issues with incorrect WP SEO meta and canonical urls
		*/
		function ht_kb_wp_seo_fix(){
			global $wp_query;

			//we're in an archive or taxonomy and theme template not in use
			if( isset($this->temp_query) && get_query_var('ht_kb') == ''  && !$this->theme_template_in_use  ){
				//reset query
				$wp_query = $this->temp_query;
				//reset postdata
				wp_reset_postdata();
			}
		}

		/**
		* Custom pre get posts filter for knowledge base article previews
		* Fixes a bug where WordPress will query post_type=post instead of post_type=ht_kb on Article preview
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_modify_kb_preview_pre_get_posts( $query ){
			if( get_query_var('ht_kb') !== '' && $query->is_main_query() ){
				$query->set( 'post_type' , 'ht_kb' );
			}

			return $query;					
		}

		/**
		* Custom pre get posts filter for knowledge base article order
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_modify_sort_order_pre_get_posts( $query ){
			global  $ht_kb_display_archive, $ht_kb_display_uncategorized_articles;

			//exit if options not set

			//get the user set sort by and sort order
			$user_sort_by = hkb_archive_sortby();
			$user_sort_order = hkb_archive_sortorder();

			//exit if post type archive and custom sort by, this sorting will be handled by the hkb_get_archive_articles function
			//or false (deliberate placeholder) replaces is_tax, which is no longer required as improvements made to hkb_get_archive articles
			if($user_sort_by=='custom' && ( is_post_type_archive( 'ht_kb' ) || false  ) ){
				return $query; 
			}

			if(!is_preview() && !is_singular() && !is_admin() && 
				( 	$ht_kb_display_archive==true ||
					$ht_kb_display_uncategorized_articles==true ||
					( $query->is_main_query() && is_post_type_archive( 'ht_kb' ) ) || 
					( $query->is_main_query() && is_tax('ht_kb_category') ) || 
					( $query->is_main_query() && is_tax('ht_kb_tag') ) )
					){

					$sort_meta_key = '';

					$valid_sort_orders = array('date', 'title', 'comment_count', 'rand', 'modified', 'popular', 'helpful', 'custom');
					if ( in_array($user_sort_by, $valid_sort_orders) ) {
					  $sort_by = $user_sort_by;
					  $sort_order = ($user_sort_order=='asc') ? 'ASC' : 'DESC';
					} else {
					  // by default, display latest first
					  $sort_by = 'date';
					  $sort_order = 'DESC';
					}

					if($user_sort_by=='popular'){
					  $sort_by = 'meta_value_num';
					  $sort_meta_key = HT_KB_POST_VIEW_COUNT_KEY;
					}

					if($user_sort_by=='helpful'){
					  $sort_by = 'meta_value_num';
					  $sort_meta_key = HT_USEFULNESS_KEY;
					} 

					if($user_sort_by=='custom' && ( is_tax('ht_kb_category') ) ){
						$sort_by = 'meta_value_num date';
						$term = get_queried_object();
						if(property_exists($term, 'term_id')){
							$sort_meta_key = '_ht_article_order_'.$term->term_id;
						}
						
					}        

				   
				  //set query 
				   $query->set( 'orderby' ,  $sort_by );
				   $query->set( 'order' ,  $sort_order );
				   $query->set( 'meta_key' ,  $sort_meta_key );

				   return $query;
			  }   

			 return $query;   	
		}

		/**
		* Custom pre get posts filter for knowledge base taxonomy to set posts_per_page
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_posts_per_taxonomy( $query ){
			if(!is_preview() && !is_singular() && !is_admin() && 
				( 	( $query->is_main_query() && is_tax('ht_kb_category') ) || 
					( $query->is_main_query() && is_tax('ht_kb_tag') ) )
				){	
				   
					//remove child articles
					$tax_obj = $query->get_queried_object();
					$tax_query = array(
									'taxonomy' => $tax_obj->taxonomy,
									'field' => 'slug',
									'terms' => $tax_obj->slug,
									'include_children' => FALSE
							);
					$query->tax_query->queries[] = $tax_query;
					$query->query_vars['tax_query'] = $query->tax_query->queries;

					//get the user set sort by and sort order
					$user_number_posts = hkb_category_articles();

					//set query 
					$query->set( 'posts_per_page' ,  $user_number_posts );
					return $query;	  		      
			   }    

			return $query;  	
		}

		/**
		* Remove the kb dummy post from the page editor screen
		* @param (Object) $query The WordPress query object
		* @return (Object) Filtered WordPress query object
		*/
		function ht_kb_remove_kb_dummy_post_from_edit_screen( $query ){
			if(is_admin() && function_exists('get_current_screen')){	
				$screen = get_current_screen();
				if($screen && 'edit' == $screen->base){
					//remove dummy page from edit list
					$post_exclude_list = array();
					$post_exclude_list[] = $this->get_ht_kb_dummy_page_id();
					$query->query_vars['post__not_in'] = $post_exclude_list;
				}
				
				return $query;	  		      
			}

			return $query;
		}

		/**
		* Post published action hook
		* @param (String) $id The post id
		* @param (Object) $post The WordPress post object
		*/
		function ht_kb_article_publish( $id, $post ){
			//set the initial meta
			$this->ht_kb_set_initial_meta( $post->ID );
			//set the inital order
			$this->ht_kb_set_initial_custom_order_meta( $post->ID );
		}

		/**
		* Get pages filter for the options reading page to add a dummy page for the ht_kb archive
		* @param (Array) $args Pages unfiltered
		* @param (Array) The filtered pages
		*/
		function ht_kb_filter_get_pages($pages){
			if(!is_admin()){
				//return if not admin
				return $pages;
			}
			$screen = null; 
			if(function_exists('get_current_screen')){
				$screen = get_current_screen();
			}			
			if(!empty($screen) && $screen->id=='options-reading'){
				$dummy_post = $this->get_ht_kb_dummy_page();				
				array_push($pages, $dummy_post);
				return $pages;
			}
			return $pages;			
		}

		/**
		* Gets the ht_kb archive dummy page, creates it if one doesn't exist
		*/
		function get_ht_kb_dummy_page(){
			$dummy_page_title = __('Knowledge Base Archive', 'ht-knowledge-base');
			$dummy_page_content = __('Knowledge Base Archive - Used by the HelpGuru Knowledge Base, do not delete', 'ht-knowledge-base');
			
			//try to get the existing page
			$existing_page = get_page_by_title($dummy_page_title);
			if($existing_page==null){
				//create dummy post
				$id = wp_insert_post( array(	'post_title'=> $dummy_page_title, 
										'post_content' => $dummy_page_content,
										'post_type' => 'page',
										'post_status'   => 'draft',
										), true );
			} 
			return get_page_by_title($dummy_page_title);
		}

		/**
		* Gets the ht_kb archive dummy page id
		*/
		function get_ht_kb_dummy_page_id(){
			$ht_kb_archive_dummy_page = $this->get_ht_kb_dummy_page();
			if(isset($ht_kb_archive_dummy_page) && is_a($ht_kb_archive_dummy_page, 'WP_Post')){
				//return the archive ID
				return $ht_kb_archive_dummy_page->ID;
			} else {
				//else return
				return 0;
			}
		}

		/**
		* Save knowledge base article hook
		*/
		function save_ht_kb_article($post_id, $post, $update) {

			//if this is just a auto draft, go no further
			if($post->post_status=='auto-draft'){
				return;
			}

			// If this is a revision, get real post ID
			if ( $parent_id = wp_is_post_revision( $post_id ) ) {
				$post_id = $parent_id;
			}				

			//get category terms
			$ht_kb_categories = wp_get_post_terms( $post_id, 'ht_kb_category' );

			if(is_a($ht_kb_categories, 'WP_Error') || empty($ht_kb_categories) ){
				//no category
				set_transient( '_'.$post_id.'_no_categories', true, 5*60 ); 
			} else {
				//categories set
				delete_transient( '_'.$post_id.'_no_categories' );
			}
		}

		/**
		* Enqueue knowledge base styles
		*/
		function ht_kb_enqueue_styles(){
			//check the we're not in theme mode
			$theme_mode = locate_template('hkb-templates', false, false);
			if($theme_mode){
				//don't load hkb-styles
			} else {
				//load hkb-styles
				//wp_enqueue_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
				wp_enqueue_style( 'hkb-style', plugins_url( 'css/hkb-style.css', __FILE__ ) );
			}
		}

		/**
		* Admin warning if no categories set
		*/
		function ht_kb_no_category_warning() {	 
			global $post;   

			//check a post is set
			if( !is_a( $post, 'WP_Post' ) ){
				return;
			}

			//check we are on the post 
			$screen = get_current_screen();
			if( !$screen || 'post'!=$screen->base ){
				return;
			}

			//get the transient to check whether no categories set during save
			$transient = get_transient( '_'.$post->ID.'_no_categories' ); 

			if( $transient ){
				delete_transient( '_'.$post->ID.'_no_categories' ); 
				?>
					<div class="notice notice-warning is-dismissible">
						<p><?php _e('This article has no knowledge base categories set, it will appear as uncategorized', 'ht-knowledge-base'); ?></p>
					</div>
				<?php
			} //end if transient
		}

		/**
		* Admin warning if no articles
		*/
		function ht_kb_no_articles_warning() {
			//check we are on the post 
			$screen = get_current_screen();
			//only display the warning from the edit kb list or dashboard home
			if( !$screen || ( 'edit-ht_kb'!=$screen->id && 'dashboard'!=$screen->id ) ){
				return;
			}

			//only display if no articles
			$articles = get_posts('post_type=ht_kb&posts_per_page=10');
			$article_count = count($articles);
			if($article_count>0){
				return;
			}

			?>
				<div class="notice notice-warning">
					<p><?php printf( 
								__('It looks like you have no Knowledge Base articles, you can <a href="%s">add new article now</a>, or install the demo content from the <a href="%s">welcome screen</a>.', 'ht-knowledge-base'),
								admin_url('post-new.php?post_type=ht_kb'),
								admin_url('index.php?page=ht-kb-welcome')
							);
					 ?></p>
				</div>
			<?php
		}

		/**
		 * Plugin row action links
		 * @param (Array) $input Already defined action links
		 * @param (String) $file Plugin file path and name being processed
		 * @return (Array) The filtered input
		 */
		function ht_kb_plugin_row_action_links( $input, $file ) {
			if ( plugin_basename(__FILE__) != $file )
				return $input;

			$links = array(
				'<a href="' . admin_url( 'edit.php?post_type=ht_kb&page=ht_knowledge_base_settings_page' ) . '">' . esc_html__( 'Settings', 'ht-knowledge-base' ) . '</a>',
			);

			$output = array_merge( $input, $links );

			return $output;
		}

		/**
		 * Plugin row meta links
		 * @param (Array) $input Already defined meta links
		 * @param (String) $file plugin file path and name being processed
		 * @return (Array) The filtered input
		 */
		function ht_kb_plugin_row_meta_links( $input, $file ) {
			if ( plugin_basename(__FILE__) != $file )
				return $input;

			$links = array(
				'<a href="' . admin_url( 'index.php?page=ht-kb-welcome' ) . '">' . esc_html__( 'Getting Started', 'ht-knowledge-base' ) . '</a>',
				'<a href="' . HT_KB_SUPPORT_URL . '" target="_blank">' . esc_html__( 'Support and Documentation', 'ht-knowledge-base' ) . '</a>',
			);

			$output = array_merge( $input, $links );

			return $output;
		}

		/**
		 * Custom get_template_part function that locates and loads a template
		 * @param (String) $slug Slug
		 * @param (String) $name The filename
		 */
		function hkb_get_template_part( $slug, $name = null ) {
			$template = hkb_locate_template($slug, $name);
			load_template($template, false);
		}

		/**
		 * Locate template
		 * Filterable - use hkb_locate_template
		 * @param (String) $slug Slug
		 * @param (String) $name The filename
		 * @return (String) Located template
		 */
		function hkb_locate_template( $slug, $name = null ) {
			$template = '';
			//build filename
			$file_name = ($name) ? $slug . '-' . $name . '.php' : $slug . '.php';  
			//check the theme does not override template
			$theme_template = locate_template( 'hkb-templates/' . $file_name ) ;
			$plugin_template = plugin_dir_path( __FILE__ ) . 'hkb-templates/' . $file_name;
			//load the template
			if( file_exists($theme_template) && !empty( $theme_template) ){
				$this->theme_template_in_use = true;
				//load the theme template first
				$template = $theme_template;
			} elseif( file_exists( $plugin_template ) && !empty( $plugin_template ) ){
				//load the plugin template second
				$template = $plugin_template;
			} elseif( $name ){
				//recursive call to load base (without $name)
				$template = hkb_locate_template( $slug );
			}

			return apply_filters('hkb_locate_template', $template);
		}

		/**
		* Get the term, if one isn't passed get it from the query
		* @param (Object) $term The default term (not required)
		* @return (Object) The term
		*/
		function hkb_get_term($term=null){
			$term = isset($term) ? $term : get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
			return $term;
		}

		/**
		* Get the term id for a given category
		* @param (Object) $category The category to get the term id for (not required)
		* @return (Int) The term id
		*/
		function hkb_get_term_id($category=null){
			$term_id = 0;
			$term = hkb_get_term( $category );
			if( $term ){
				//term id
				$term_id = $term->term_id;
			}
			return $term_id;
		}

		/**
		* Get subcategories
		* @return (array) An array of categories
		*/
		function hkb_get_subcategories($parent_id=null){
			$parent_id = empty($parent_id) ? hkb_get_current_term_id() : $parent_id; 
			$hkb_master_tax_terms = hkb_get_master_tax_terms();

			$sub_categories = wp_list_filter($hkb_master_tax_terms,array('parent'=>$parent_id));
			return (Array)$sub_categories;
		}

		/**
		* Get post ancestors - used in breadcrumbs etc
		* Note posts can have multiple and divergent ancestory
		* @return (Array) Post ancestors
		*/
		function ht_kb_get_ancestors() {
			global $post, $cat, $hkb_current_article_id;

			$ancestors = array(); 

			//path number
			$i = 0;
			$ancestors[$i] = array();

			if (!ht_kb_is_ht_kb_front_page()) { 
	

				$taxonomy = ht_kb_get_taxonomy();
				$term_string = ht_kb_get_term();
				$visited = array();
							
				if (!empty($taxonomy) && !empty($term_string)) {
					//category terms bread crumb

					//add home link to path
					$ancestors[$i][] = array('label'=>__('Home', 'ht-knowledge-base'), 'link' => home_url(), 'title'=>__('Home', 'ht-knowledge-base'), 'type'=>'blog_home');
					
					//kb archive set as home test
					if(!hkb_is_kb_set_as_front()){
						//add knowledge base to path
						$ancestors[$i][] = array('label'=>__('Knowledge Base', 'ht-knowledge-base'), 'link' => get_post_type_archive_link( 'ht_kb' ), 'title'=>__('Knowledge Base', 'ht-knowledge-base'), 'type'=>'kb_home');					
					}

					$term = get_term_by( 'slug', $term_string, $taxonomy );
					
					if($term==false)
						return;

					if ($term->parent != 0) { 
						//has parents
						$parents =  $this->ht_get_custom_category_ancestors($term->term_id, 'ht_kb_category', true,null, false, $visited, $ancestors, $i );
						//itself
						//$ancestors[$i][] = array('label'=>$term->name, 'link' => esc_attr(get_term_link($term, 'ht_kb_category')), 'title'=>sprintf( __( "View all posts in %s" ), $term->name), 'type'=>'kb_current_page');	
					
					} else {
						//path 
						//add knowledge base to path
						$ancestors[$i][] = array('label'=>$term->name, 'link' => esc_attr(get_term_link($term, 'ht_kb_category')), 'title'=>sprintf( __( "View all posts in %s", 'ht-knowledge-base' ), $term->name), 'type'=>'kb_tax');	
						$visited[] = $term->term_id;
					}
					

				} elseif ( !is_single() && 'ht_kb' == get_post_type() ) {
					//add home link to path
					$ancestors[$i][] = array('label'=>__('Home', 'ht-knowledge-base'), 'link' => home_url(), 'title'=>__('Home', 'ht-knowledge-base'), 'type'=>'blog_home');

					//Archive		
					$ht_kb_data = get_post_type_object('ht_kb');

					//kb archive set as home test
					if(!hkb_is_kb_set_as_front()){
						$ancestors[$i][] = array('label'=>__('Knowledge Base', 'ht-knowledge-base'), 'link' => get_post_type_archive_link( 'ht_kb' ), 'title'=>__('Knowledge Base', 'ht-knowledge-base'), 'type'=>'kb_home');
					}

					//search?
					if(ht_kb_is_ht_kb_search()){
						$ancestors[$i][] = array('label'=>__('Search Results', 'ht-knowledge-base'), 'link' => '', 'title'=>__('Search Results', 'ht-knowledge-base'), 'type'=>'kb_search');
					}

				} elseif ( is_single() && 'ht_kb' == get_post_type() ) {
					//Single post
					$hkb_current_article_id = empty($hkb_current_article_id) ? get_the_ID() : $hkb_current_article_id;
					$terms = wp_get_post_terms( $hkb_current_article_id , 'ht_kb_category');

					if( !empty($terms) ){
						foreach($terms as $term) {

							//add home link to path
							$ancestors[$i][] = array('label'=>__('Home', 'ht-knowledge-base'), 'link' => home_url(), 'title'=>__('Home', 'ht-knowledge-base'), 'type'=>'blog_home');

							//kb archive set as home test
							if(!hkb_is_kb_set_as_front()){
								//add knowledge base to path
								$ancestors[$i][] = array('label'=>__('Knowledge Base', 'ht-knowledge-base'), 'link' => get_post_type_archive_link( 'ht_kb' ), 'title'=>__('Knowledge Base', 'ht-knowledge-base'), 'type'=>'kb_home');					
							}

							if ($term->parent != 0) { 
								$parents =  $this->ht_get_custom_category_ancestors($term->term_id, 'ht_kb_category', true,null, false, $visited, $ancestors, $i );
								//itself
								//$ancestors[$i][] = array('label'=>$term->name, 'link' => esc_attr(get_term_link($term, 'ht_kb_category')), 'title'=>sprintf( __( "Viewing a post in %s" ), $term->name), 'type'=>'kb_current_page');	
					
							} else {
								//add knowledge base to path
								$ancestors[$i][] = array('label'=>$term->name, 'link' => esc_attr(get_term_link($term, 'ht_kb_category')), 'title'=>sprintf( __( "View all posts in %s", 'ht-knowledge-base' ), $term->name), 'type'=>'kb_tax');	
								$visited[] = $term->term_id;
							}
							//itself
							$ancestors[$i][] = array('label'=>get_the_title(), 'link' => get_permalink(), 'title'=>get_the_title(), 'type'=>'kb_current_page');	
					
							//increment the counter
							$i++;

						} // End foreach
					} else {
						//add home link to path
						$ancestors[$i][] = array('label'=>__('Home', 'ht-knowledge-base'), 'link' => home_url(), 'title'=>__('Home', 'ht-knowledge-base'), 'type'=>'blog_home');

						//kb archive set as home test
						if(!hkb_is_kb_set_as_front()){
							//add knowledge base to path
							$ancestors[$i][] = array('label'=>__('Knowledge Base', 'ht-knowledge-base'), 'link' => get_post_type_archive_link( 'ht_kb' ), 'title'=>__('Knowledge Base', 'ht-knowledge-base'), 'type'=>'kb_home');					
						}

						//uncategorized article
						$ancestors[$i][] = array('label'=>get_the_title(), 'link' => get_permalink(), 'title'=>get_the_title(), 'type'=>'kb_current_page');	
						$i++;
					}		
					
				} else {
						//Display the post title.
						$ancestors[$i][] = array('label'=>get_the_title(), 'link' => get_permalink(), 'title'=>get_the_title(), 'type'=>'kb_current_page');	
						$i++;
				}
						
				
			} //is_front_page

			return apply_filters('ht_kb_get_ancestors', $ancestors);
		} //end function

		/**
		* Custom ancestory walker
		* @param (Int) $id Term id
		* @param (Object) $taxonomy Taxonomy object
		* @param (Bool) $link Whether to link the ancestor item
		* @param (String) $seperator @deprecated 
		* @param (Bool) $nicename Use slug or name (default false - name)
		* @param (Object) $visited The visted terms
		* @param (Object) $ancestors Ancestors list
		* @param (Int) $i Counter
		* @return (Array) Post ancestors
		*/
		function ht_get_custom_category_ancestors( $id, $taxonomy = false, $link = false, $separator = '/', $nicename = false, $visited = array(), &$ancestors, $i=0 ) {

			if(!($taxonomy && is_taxonomy_hierarchical( $taxonomy )))
				return;

			$chain = array();
			// $parent = get_category( $id );
			$parent = get_term( $id, $taxonomy);

			if ( is_wp_error( $parent ) ){
				$ancestors[$i][] = array('label'=>$parent->name, 'link' => esc_attr(get_term_link($parent, 'ht_kb_category')), 'title'=>sprintf( __( "View all posts in %s", 'ht-knowledge-base' ), $parent->name), 'type'=>'kb_current_page');	
				return;
			}
				

			if ( $nicename ){
				$name = $parent->slug;
			} else {
				$name = $parent->name;
			}

				
			//reset visited if null
			if(empty($visited)){
				$visited = array();
			}				

			if ( $parent->parent && 
				( $parent->parent != $parent->term_id ) && 
				(!in_array( $parent->parent, $visited ) ) ) {
					$visited[] = $parent->parent;
					 $this->ht_get_custom_category_ancestors( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited, $ancestors, $i );
					 //return;
			}

			if ( $link ) {
				$ancestors[$i][] = array('label'=>$name, 'link' => esc_url( get_term_link( (int) $parent->term_id, $taxonomy ) ), 'title'=>sprintf( __( "View all posts in %s", 'ht-knowledge-base' ), $name), 'type'=>'kb_tax');			
			} else {
				$ancestors[$i][] = array('label'=>get_the_title(), 'link' => get_permalink(), 'title'=>get_the_title(), 'type'=>'kb_current_page');	
			}

			return;
		}

		/**
		* Get top level archive taxonomy terms for the Knowledge Base archive
		* @param (Int) $columns Number of columns
		* @param (Int) $sub_cat_depth Depth of subcategories to display
		* @param (Bool) $display_sub_cat_count Display a count in subcategories
		* @param (Bool) $display_sub_cat_articles Display/list subcategory articles
		* @param (String) $sort_by How to sort the archive
		* @param (String) $sort_order Sort order
		* @param (Bool) $hide_empty_kb_categories Whether to hide empty categories
		* @param (Int) $i Counter
		* @return (Array) Category terms
		*/
		function hkb_get_master_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
			global $ht_kb_display_archive, $hkb_master_tax_terms;

			$columns = hkb_archive_columns();
			$sub_cat_display = hkb_archive_display_subcategories();
			$sub_cat_depth = hkb_archive_subcategory_depth();
			$display_sub_cat_count = hkb_archive_display_subcategory_count();
			$display_sub_cat_articles = hkb_archive_display_subcategory_articles();
			$hide_empty_kb_categories = hkb_archive_hide_empty_categories();


			//list terms in a given taxonomy
			$args = array(
				'orderby'       =>  'term_order',
				'depth'         =>  0,
				'child_of'      => 	0,
				'hide_empty'    =>  $hide_empty_kb_categories,
				'pad_counts'   	=>	true,
			); 
			$hkb_master_tax_terms = get_terms('ht_kb_category', $args);
			
			//limit depth if archive display or front page
			if( $this->is_ht_kb_archive || ht_kb_is_ht_kb_front_page() ){
				$hkb_master_tax_terms = $this->hkb_filter_terms_by_depth($hkb_master_tax_terms, $sub_cat_depth);
			}

			return $hkb_master_tax_terms;
		}

		/**
		* Get top level archive taxonomy terms for the Knowledge Base archive
		* @param (Array) $term_list The unfiltered term list
		* @param (Int) $max_depth The max depth of terms
		* @return (Array) Filtered terms
		*/
		function hkb_filter_terms_by_depth($term_list, $max_depth=100){
			$filtered_terms = array();

			foreach ($term_list as $key => $term) {
				$term_depth = $this->hkb_get_category_depth($term->term_id);
				if($term_depth<=$max_depth){
					//add to filtered terms if less than max depth
					$filtered_terms[] = $term;
				}
			}

			return $filtered_terms;
		}

		/**
		* Get category depth
		* @param (Int) $term_id The term id of the category
		* @return (Int) Term depth
		*/
		function hkb_get_category_depth($term_id){
			//get ancestors
			$ancestors = get_ancestors($term_id, 'ht_kb_category');
			//calculate term_depth
			$term_depth = count($ancestors);
			return (int) $term_depth;
		}

		/**
		* Get top level archive taxonomy terms for the Knowledge Base archive
		* @param (Int) $columns Number of columns
		* @param (Int) $sub_cat_depth Depth of subcategories to display
		* @param (Bool) $display_sub_cat_count Display a count in subcategories
		* @param (Bool) $display_sub_cat_articles Display/list subcategory articles
		* @param (String) $sort_by How to sort the archive
		* @param (String) $sort_order Sort order
		* @param (Bool) $hide_empty_kb_categories Whether to hide empty categories
		* @param (Int) $i Counter
		* @return (Array) Category terms
		*/
		function hkb_get_archive_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
			global $ht_kb_display_archive, $hkb_master_tax_terms;
			
			$hkb_master_tax_terms = hkb_get_master_tax_terms($columns, $sub_cat_depth, $display_sub_cat_count, $display_sub_cat_articles, $sort_by, $sort_order, $hide_empty_kb_categories);
			
			$tax_terms = wp_list_filter($hkb_master_tax_terms,array('parent'=>0));

			$ht_kb_display_archive = false;

			return $tax_terms;
		}

		/**
		* Get articles for a category
		* @param (Object) $category The category
		* @param (String) $sort_by How to sort the archive
		* @param (String) $sort_order Sort order
		* @param (String) $location
		* @return (Array) Posts array
		*/
		function hkb_get_archive_articles($category, $sort_by='date', $sort_order='asc', $location=''){
			global $ht_kb_display_archive, $wp_query;

			$numberposts = 0;
			switch ($location) {
				case 'article_ordering':
					$numberposts = -1;
					break;
				
				default:
					$numberposts = hkb_home_articles();
					break;
			}			

			if( 'kb_home'==$location && 0 == $numberposts){
				//return blank array
				return array();
			}

			$ht_kb_display_archive = true;

			$sort_by = hkb_archive_sortby();
			$sort_order = hkb_archive_sortorder();
			
			//get posts per category
			$args = array( 
				'post_type'  => 'ht_kb',
				'posts_per_page' => $numberposts,
				'order' => $sort_order,
				'orderby' => $sort_by,
				'suppress_filters' => 1,
				'tax_query' => array(
					array(
						'taxonomy' => 'ht_kb_category',
						'field' => 'term_id',
						'include_children' => false,
						'terms' => $category->term_id
					)
				)				
			);		

			//if sort by is custom order //?
			if( 'custom' == $sort_by ){
				$args['orderby'] = 'meta_value_num date';
				$args['meta_key'] = '_ht_article_order_'.$category->term_id;
				//ensure order is asc for custom order
				$args['order'] = 'asc';
			}
			//temporarily store the wp_query
			$temp_query = clone $wp_query;
			//reset the query
			wp_reset_query();
			//create new query object
			$ht_kb_category_articles_query = new WP_Query();
			//populate category posts
			$cat_posts =  $ht_kb_category_articles_query->query($args);
			//restore the wp_query object
			$wp_query = $temp_query;

			//end displaying archive
			$ht_kb_display_archive = false;

			return $cat_posts;
		}

		/**
		* Get articles without a category
		* @return (Array) Posts array
		*/
		function hkb_get_uncategorized_articles(){
			global $ht_kb_display_uncategorized_articles;
			//now getting uncategorized posts
			$ht_kb_display_uncategorized_articles = true;

			
			//set max number of articles to fetch
			$numberposts = 100;
			//$numberposts = (array_key_exists('tax-cat-article-number', $ht_knowledge_base_options)) ? $ht_knowledge_base_options['tax-cat-article-number'] : 10;

			//get the master tax terms
			$args = array(
				'orderby'       =>  'term_order',
				'depth'         =>  0,
				'child_of'      => 	0,
				'hide_empty'    =>  0,
				'pad_counts'   	=>	true
			); 
			$master_tax_terms = get_terms('ht_kb_category', $args);
			//get the top level terms, now unused
			$top_level_tax_terms = wp_list_filter($master_tax_terms,array('parent'=>0));
			$tax_terms_ids = array();
			if( !empty ($master_tax_terms ) && !is_a( $master_tax_terms, 'WP_Error' ) && is_array( $master_tax_terms ) ){
				foreach ( (array)$master_tax_terms as $key => $term ) {
					array_push($tax_terms_ids, $term->term_id);
				}
			}
			$args = array( 
					'numberposts' => $numberposts, 
					'post_type'  => 'ht_kb',
					'orderby' => 'date',
					'suppress_filters' => false,
					'tax_query' => array(
						array(
							'taxonomy' => 'ht_kb_category',
							'field' => 'term_id',
							'include_children' => false,
							'terms' => $tax_terms_ids,
							'operator'  => 'NOT IN'
						)
					)
				);

			$uncategorized_posts = get_posts( $args );  

			$ht_kb_display_uncategorized_articles = false;

			return $uncategorized_posts;
		}

		/**
		* Returns true if there a no public posts
		* @todo - needs testing in context
		* @return (Bool) No public posts
		*/
		function hkb_no_public_posts(){
			global $wp_query;
			if ($wp_query->post_count > 0)
				return false;
			else
				return true;
		}

		/**
		 * Print the post excerpt
		 */
		function hkb_the_excerpt(){
			add_filter( 'excerpt_more', array( $this, 'hkb_remove_more_from_excerpt_more_filter'), 20 );
			add_filter( 'excerpt_length', array( $this, 'hkb_custom_excerpt_length'), 999 );
			echo  hkb_get_the_excerpt() ;
		}

		/**
		 * Custom get_the_excerpt function
		 * @return (String) The modified excerpt
		 */
		function hkb_get_the_excerpt() {

			$post = get_post();
			if ( empty( $post ) ) {
				return '';
			}

			if ( post_password_required() ) {
				return __( 'There is no excerpt because this is a protected post.', 'ht-knowledge-base' );
			}

			$content = apply_filters( 'get_the_excerpt', $post->post_excerpt );
			return apply_filters( 'hkb_the_excerpt', $content );
		}

		/**
		 * Remove any read more links for search
		 */
		function hkb_remove_more_from_excerpt_more_filter($more) {
			return __('...', 'ht-knowledge-base');
		}

		/**
		 * Custom excerpt length
		 */
		function hkb_custom_excerpt_length() {
			return 20;
		}
		
		/**
		* Get tag taxonomies
		* @param (String) $search A search string to match
		* @return (Array) An array of matching tags
		*/
		function get_ht_kb_tags($search=''){
			$taxonomies = array('ht_kb_tag');

			$args = array(
				'orderby'       => 'name', 
				'order'         => 'ASC',
				'hide_empty'    => true, 
				'exclude'       => array(), 
				'exclude_tree'  => array(), 
				'include'       => array(),
				'number'        => '', 
				'fields'        => 'all', 
				'slug'          => '', 
				'parent'         => '',
				'hierarchical'  => true, 
				'child_of'      => 0, 
				'get'           => '', 
				'name__like'    => $search,
				'pad_counts'    => false, 
				'offset'        => '', 
				'search'        => '', 
				'cache_domain'  => 'core'
			);

			$tags = get_terms( $taxonomies, $args );

			return $tags;
		}//end function

		/**
		* Get category taxonomies
		* @param (String) $search A search string to match (unused in this version)
		* @param (String) $orderby How to order results (currently not working/implemented)
		* @return (Array) An array of matching categories
		*/
		function get_ht_kb_categories($search='', $orderby=''){

			//note orderby does not appear to be working, possible WordPress issue
			$orderby = (empty($orderby)) ? 'term_id' : $orderby;

			$taxonomies = array('ht_kb_category');

			$args = array(
				'orderby'       => $orderby, 
				'order'         => 'ASC',
				'hide_empty'    => false, 
				'exclude'       => array(), 
				'exclude_tree'  => array(), 
				'include'       => array(),
				'number'        => '', 
				'fields'        => 'all', 
				'slug'          => '', 
				'parent'         => '',
				'hierarchical'  => true, 
				'child_of'      => 0, 
				'get'           => '', 
				'name__like'    => $search,
				'pad_counts'    => false, 
				'offset'        => '', 
				'search'        => '', 
				'cache_domain'  => 'core'
			);

			$categories = get_terms( $taxonomies, $args );

			return $categories;
		}//end function

		/**
		* Set the current term id
		* @param (Int) Current term id
		*/
		function hkb_set_current_term_id($id=null){
			global $hkb_current_term_id;
			$id = empty($id) ? hkb_get_term_id() : $id;
			$hkb_current_term_id = $id;
		}

		/**
		* Get current term id
		* @return (Int) term id
		*/
		function hkb_get_current_term_id(){
			global $hkb_current_term_id;
			$hkb_current_term_id = empty($hkb_current_term_id) ? hkb_get_term_id() : $hkb_current_term_id;
			return $hkb_current_term_id;
		}

		/**
		* Get the attachments
		* @return (Object) The redux file list
		*/
		function hkb_get_attachments($post_id=null){
			global $post;
			$post_id = empty($post_id) ? $post->ID : $post_id;
			return get_post_meta($post_id, '_ht_knowledge_base_file_advanced', true);
		}

		/**
		* Has attachments
		* @return (Object) The redux file list
		*/
		function hkb_has_attachments($post_id=null){
			global $post;
			$post_id = empty($post_id) ? $post->ID : $post_id;
			$attachments = hkb_get_attachments($post_id);
			$has_attachments = empty($attachments) ? false : true;
			return $has_attachments;
		}

	   /**
		* Has attachments
		* @return (Int) Count attachments
		*/
		function hkb_count_attachments($post_id=null){
			global $post;
			$post_id = empty($post_id) ? $post->ID : $post_id;
			$attachments = hkb_get_attachments($post_id);
			$has_attachments = empty($attachments) ? 0 : count($attachments);
			return $has_attachments;
		}

		/**
		* Whether the kb archive is set as front, this differs from ht_kb_is_ht_kb_front_page, 
		* as is does not check whether the front page is also the current page
		* @return (Bool) Knowledge Base archive set as front page
		*/
		function hkb_is_kb_set_as_front(){
			//get the dummy page id 
			$dummy_page_id  = $this->get_ht_kb_dummy_page_id();
			
			if(defined('ICL_LANGUAGE_CODE')){
				//translate the dummy page id if necessary
				$dummy_page_id = icl_object_id($dummy_page_id, 'page', false, ICL_LANGUAGE_CODE);
			}
			
			return ('page' == get_option( 'show_on_front' ) && get_option('page_on_front')==$dummy_page_id );
		}

		/**
		* Get the custom article order for a given post_id and category term_id
		* @param (Int) $post_id The article post id
		* @param (Int) $term_id The term id for the ht_kb_category
		* @return (Int) The custom article/post order if set
		*/
		function hkb_get_custom_article_order($post_id, $term_id){
			return get_post_meta($post_id, '_ht_article_order_'.$term_id, true);
		}

		/**
		* Set the custom article order for a given post_id and category term_id
		* @param (Int) $post_id The article post id
		* @param (Int) $term_id The term id for the ht_kb_category
		* @param (Int) $order The new 
		* @return (Int) New meta ID
		*/
		function hkb_set_custom_article_order($post_id, $term_id, $order){
			return update_post_meta($post_id,   '_ht_article_order_'.$term_id, $order);
		}
		

		/** UPGRADE **/

		/**
		* Set initial post view count and helpfulness as 0
		* @param (String) $id The post id
		*/
		function ht_kb_set_initial_meta( $id ){
			//set post view count to 0 if none
			$post_view_count =  get_post_meta( $id, HT_KB_POST_VIEW_COUNT_KEY, true );
			if($post_view_count == ''){
				//set view count to 0
				update_post_meta($id, HT_KB_POST_VIEW_COUNT_KEY, 0);
			}
			//set post helpfulness meta to 0 if none
			$helpfulness =  get_post_meta( $id, HT_USEFULNESS_KEY, true );
			if($helpfulness == ''){
				//set helpfulness to 0
				update_post_meta($id, HT_USEFULNESS_KEY, 0);
			}
		}

		function ht_kb_plugin_activation_upgrade_actions(){
			//upgrade - set initial meta if required

			//get all ht_kb articles
			$args = array(
					  'post_type' => 'ht_kb',
					  'posts_per_page' => -1,
					 );
			$ht_kb_posts = get_posts( $args );

			//loop and upgrade
			foreach ( $ht_kb_posts as $post ) {
				//upgrade if required
			   $this->ht_kb_set_initial_meta( $post->ID );
			   $this->ht_kb_upgrade_article_meta_fields( $post->ID );
			}
		}

		/**
		 * Upgrade the meta key values.
		 */
		public function ht_kb_upgrade_article_meta_fields($post_id){
			//keys to be upgraded
			$this->ht_kb_upgrade_meta_field($post_id, 'file_advanced');
			$this->ht_kb_upgrade_meta_field($post_id, 'voting_checkbox');
			$this->ht_kb_upgrade_meta_field($post_id, 'voting_reset');
			$this->ht_kb_upgrade_meta_field($post_id, 'voting_reset_confirm');
			$this->ht_kb_upgrade_view_count_meta($post_id);
			$this->ht_kb_upgrade_custom_order_meta($post_id);
		}

		/**
		 * Upgrade a post meta field.
		 * @param (Int) $post_id The id of the post to upgrade
		 * @param (String) $name The name of the meta field to upgrade
		 */
		function ht_kb_upgrade_meta_field($post_id, $name){
			$old_prefix = 'ht_knowledge_base_';
			$new_prefix = '_ht_knowledge_base_';

			//get the old value
			$old_value = get_post_meta($post_id, $old_prefix . $name, true);
			if(!empty($old_value)){
				//get the new value
				$new_value = get_post_meta($post_id, $new_prefix . $name, true);
				if(empty($new_value)){
					//sync the new value to the old value
					update_post_meta($post_id, $new_prefix . $name, $old_value);
				}
				
			}
			//delete old meta key
			delete_post_meta($post_id, $old_prefix . $name);
		}

		/**
		 * Upgrade a view count meta field
		 * @param (Int) $post_id The id of the post to upgrade
		 */
		function ht_kb_upgrade_view_count_meta($post_id){
			$old_key = 'ht_kb_post_views_count';
			$new_key = HT_KB_POST_VIEW_COUNT_KEY;

			//get the old value
			$old_value = get_post_meta($post_id, $old_key, true);
			if(!empty($old_value)){
				//get the new value
				$new_value = get_post_meta($post_id, $new_key, true);
				//upgrade regardless of whether the new value is empty
				if(true){
					//sync the new value to the old value
					update_post_meta($post_id, $new_key, $old_value);
				}
				
			}
			//delete old meta key
			delete_post_meta($post_id, $old_key);
		}

		/**
		 * Upgrade a view count meta field
		 * @param (Int) $post_id The id of the post to upgrade
		 */
		function ht_kb_upgrade_custom_order_meta($post_id){
			$this->ht_kb_set_initial_custom_order_meta($post_id);
		}

		/**
		 * Set object terms filter
		 * @param (Int) $object_id
		 * @param (Array) $category_term
		 * @param (Array) $tt_ids
		 * @param (Int) $taxonomy
		 */
		function ht_kb_set_object_terms($object_id, $category_terms, $tt_ids, $taxonomy){

			//check 
			if(empty($taxonomy) || 'ht_kb_category' != $taxonomy){
				return;
			}

			//for each of the new terms set the article order
			foreach ($category_terms as $key => $category_term) {
				//get the term if it's not a proper term object, seems wordpress can either pass this as an array of
				//strings(slug) or integers(term_id), depending on context
				if(!isset($category_term->taxonomy)){ 
					if(is_int($category_term)){
						$category_term = get_term_by('id', $category_term, $taxonomy);
					} else {
						$category_term = get_term_by('slug', $category_term, $taxonomy);
					}					
				}

				//check term taxonomy is ht_kb_category
				if($category_term && isset($category_term->taxonomy) && 'ht_kb_category' === $category_term->taxonomy){
					$category_term_id = $category_term->term_id;
					$current_custom_order = hkb_get_custom_article_order($object_id, $category_term_id);
					//upgrade if empty, set custom order initially to post_id
					if(empty($current_custom_order)){
						hkb_set_custom_article_order($object_id, $category_term_id, $object_id);
					}
				}						
			}
		}


		/**
		* Set initial custom order meta
		* @param (Int) $post_id The id of the post to set meta
		*/
		function ht_kb_set_initial_custom_order_meta($post_id){
			//get ht_kb_category terms
			$category_terms = wp_get_post_terms( $post_id, 'ht_kb_category' );
			//loop terms and ensure order is set
			foreach ($category_terms as $key => $category_term) {
				$category_term_id = $category_term->term_id;
				$current_custom_order = hkb_get_custom_article_order($post_id, $category_term_id);
				//upgrade if empty, set custom order initially to post_id
				if(empty($current_custom_order)){
					hkb_set_custom_article_order($post_id, $category_term_id, $post_id);
				}		
			}
		}

		/**
		* Reorder admin menu
		* @param (Int) $order The unfiltered order
		* @return (Int) Filtered order
		*/
		function ht_kb_custom_admin_menu_order($order){
			global $menu;
			global $submenu;

			$settings_menu = null;
			//move settings menu to the end
			foreach ($submenu as $key => $menu_group) {
				if('edit.php?post_type=ht_kb'==$key){
					foreach($menu_group as $subkey => $menu_item){
						if( isset($menu_item[2]) && 'ht_knowledge_base_settings_page' == $menu_item[2] ){
							//temp store
							$settings_menu = $submenu[$key][$subkey];
							//remove submenu item
							unset($submenu[$key][$subkey]);
						}
					} //end foreach menu_group
					//add settings to the end
					$submenu[$key][] = $settings_menu;
				} //end if
			} //end foreach submenu
			return $order;
		}

		/** STATIC FUNCTIONS **/

		/**
		* Loop through each blog and add custom tables where necessary
		* fixes network activate on multisite issues
		*/
		static function knowledgebase_customtaxorder_activate( $network_wide = null ) {
			global $wpdb;

			if ( is_multisite() && $network_wide ) {
				//store current blog
				$current_blog = $wpdb->blogid;

				//loop all blogs in the network and create table
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					HT_Knowledge_Base::knowledgebase_customtaxorder_create_term_order_column();
					restore_current_blog();
				}
			} else {
				HT_Knowledge_Base::knowledgebase_customtaxorder_create_term_order_column();
			}
		}

		/**
		* Add required columns to the WP database terms table
		*/
		static function knowledgebase_customtaxorder_create_term_order_column() {
			global $wpdb;
			$init_query = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
			if ($init_query == 0) {	
				$wpdb->query("ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'"); 
			}
		}

		/**
		* Remove columns from the WP database terms table that were added during installation
		* @todo Implement this function (note this should be on plugin UNINSTALL, not deactivation)
		*/
		static function knowledgebase_customtaxorder_uninstall() {
			global $wpdb;
			$init_query = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
			if ($init_query != 0) {	
				$wpdb->query("ALTER TABLE $wpdb->terms DROP COLUMN `term_order`"); 
			}
		}



	} //end class HT_Knowledge_Base
}//end class exists test


//run the plugin
if( class_exists( 'HT_Knowledge_Base' ) ){
	$ht_knowledge_base_init = new HT_Knowledge_Base();

	function ht_kb_get_taxonomy(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->taxonomy;
	}

	function ht_kb_get_term(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->term;
	}

	function ht_kb_is_ht_kb_search(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->is_ht_kb_search;
	}

	function ht_kb_is_ht_kb_front_page(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->ht_kb_is_ht_kb_front_page;
	}

	function ht_kb_get_cpt_slug(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_cpt_slug();
	}

	function ht_kb_get_cat_slug(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_cat_slug();
	}

	function ht_kb_get_tag_slug(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_tag_slug();
	}

	function ht_kb_view_count($post_id=null){
		global $post;
		//set the post id
		$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
		//get the post usefulness meta
		$post_view_count = get_post_meta( $post_id, HT_KB_POST_VIEW_COUNT_KEY, true );
		//convert to integer
		$post_view_count_int = empty($post_view_count) ? 0 : intval($post_view_count);
		//return as integer
		return $post_view_count_int;
	}

	function get_ht_kb_dummy_page_id(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_ht_kb_dummy_page_id();
	}

	function hkb_get_template_part($slug, $name = null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_template_part($slug, $name);
	}

	function hkb_locate_template($slug, $name = null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_locate_template($slug, $name);
	}

	function hkb_get_term($term=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_term($term);
	}

	function hkb_get_term_id($category=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_term_id($category);
	}

	function hkb_get_subcategories($category=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_subcategories($category);
	}

	function ht_kb_get_ancestors(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->ht_kb_get_ancestors();
	}

	function hkb_get_master_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_master_tax_terms($columns, $sub_cat_depth, $display_sub_cat_count, $display_sub_cat_articles, $sort_by, $sort_order, $hide_empty_kb_categories);		
	}

	function hkb_get_archive_tax_terms($columns=2, $sub_cat_depth=2, $display_sub_cat_count=true, $display_sub_cat_articles=true, $sort_by='date', $sort_order='asc', $hide_empty_kb_categories=false){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_archive_tax_terms($columns, $sub_cat_depth, $display_sub_cat_count, $display_sub_cat_articles, $sort_by, $sort_order, $hide_empty_kb_categories);		
	}

	function hkb_get_archive_articles($category, $sort_by='date', $sort_order='asc', $location=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_archive_articles($category, $sort_by, $sort_order, $location);
	}

	function hkb_get_uncategorized_articles(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_uncategorized_articles();
	}

	function hkb_no_public_posts(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_no_public_posts();
	}

	function hkb_the_excerpt(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_the_excerpt();
	}

	function hkb_get_the_excerpt(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_the_excerpt();
	}

	function get_ht_kb_tags($s=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_ht_kb_tags($s);
	}

	function get_ht_kb_categories($s='', $orderby=''){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->get_ht_kb_categories($s, $orderby);
	}

	function hkb_set_current_term_id($id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_set_current_term_id($id);	
	}

	function hkb_get_current_term_id(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_current_term_id();	
	}

	function hkb_get_attachments($post_id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_attachments($post_id);	
	}

	function hkb_has_attachments($post_id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_has_attachments($post_id);	
	}

	function hkb_count_attachments($post_id=null){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_count_attachments($post_id);	
	}

	function hkb_is_kb_set_as_front(){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_is_kb_set_as_front();
	}

	function hkb_get_custom_article_order($post_id, $term_id){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_get_custom_article_order($post_id, $term_id);	
	}

	function hkb_set_custom_article_order($post_id, $term_id, $order){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->hkb_set_custom_article_order($post_id, $term_id, $order);	
	}

	function ht_kb_upgrade_article_meta_fields($post_id){
		global $ht_knowledge_base_init;
		return $ht_knowledge_base_init->ht_kb_upgrade_article_meta_fields($post_id);
	}

}