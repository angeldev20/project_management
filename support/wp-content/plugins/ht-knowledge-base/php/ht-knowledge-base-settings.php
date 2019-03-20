<?php
/**
 * 2.6.5 New Settings Page
 */

if (!class_exists('Knowledge_Base_Settings')) {

    class Knowledge_Base_Settings {

        private $ht_kb_settings;
        private $reserved_terms;
        private $existing_post_names;

        //Constructor
        function __construct(){  
            global $ht_knowledge_base_settings;


            //get option
            $this->ht_kb_settings = get_option( 'ht_knowledge_base_settings' );

            $ht_knowledge_base_settings = $this->ht_kb_settings;

            

            //register settings
            add_action('admin_init', array($this, 'register_settings') );

            //add settings page
            add_action('admin_menu', array($this, 'add_ht_kb_settings_page'), 10 );         

        }

        function register_settings(){
            register_setting( 'ht_knowledge_base_settings', 'ht_knowledge_base_settings', array($this,'ht_knowledge_base_settings_validate') );
        } 


        function add_ht_kb_settings_page(){

            //add the submenu page
            add_submenu_page(
                    'edit.php?post_type=ht_kb',
                    __('HelpGuru Knowledge Base Settings', 'ht-knowledge-base'), 
                    __('Settings', 'ht-knowledge-base'), 
                    'manage_options', 
                    'ht_knowledge_base_settings_page', 
                   array($this, 'ht_kb_settings_display')
                );

            //general section
            add_settings_section('ht_knowledge_base_general_settings', __('General Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_general_section_description'), 'ht_kb_settings_general_section'); 
            //breadcrumbs
            add_settings_field('display-breadcrumbs', __('Breadcrumbs', 'ht-knowledge-base'), array($this, 'general_display_breadcrumbs_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
            $this->set_default('display-breadcrumbs', true);
            //sort by
            add_settings_field('sort-by', __('Sort By', 'ht-knowledge-base'), array($this, 'general_sort_by_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
            $this->set_default('sort-by', 'date');
            //sort order
            //sort by
            add_settings_field('sort-order', __('Sort Order', 'ht-knowledge-base'), array($this, 'general_sort_order_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
            $this->set_default('sort-order', 'asc');
            //number of articles
            add_settings_field('num-articles', __('Number of Articles', 'ht-knowledge-base'), array($this, 'general_num_articles_option_render'), 'ht_kb_settings_general_section', 'ht_knowledge_base_general_settings');
            $this->set_default('num-articles', 5);

            //archive section
            add_settings_section('ht_knowledge_base_archive_settings', __('Archive Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_archive_section_description'), 'ht_kb_settings_archive_section'); 
            //kb columns
            add_settings_field('archive-columns', __('Knowledge Base Columns', 'ht-knowledge-base'), array($this, 'archive_archive_columns_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
            $this->set_default('archive-columns', 2);
            //article count
            add_settings_field('display-article-count', __('Display Category Article Count', 'ht-knowledge-base'), array($this, 'archive_display_article_count_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
            $this->set_default('display-article-count', true);
            //articles in home
            add_settings_field('num-articles-home', __('Number of Articles to Display in Home', 'ht-knowledge-base'), array($this, 'archive_num_articles_home_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
            $this->set_default('num-articles-home', 5);
            //display sub categories
            add_settings_field('display-sub-cats', __('Display Sub Categories', 'ht-knowledge-base'), array($this, 'archive_display_sub_cats_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
            $this->set_default('display-sub-cats', true);
            //sub category depth
            add_settings_field('sub-cat-depth', __('Sub Category Depth', 'ht-knowledge-base'), array($this, 'archive_sub_cat_depth_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
            $this->set_default('sub-cat-depth', 1);
            //display subcategory articles
            add_settings_field('display-sub-cat-articles', __('Display Sub Category Articles', 'ht-knowledge-base'), array($this, 'archive_display_sub_cat_articles_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
            $this->set_default('display-sub-cat-articles', true);
            //hide empty categories
            add_settings_field('hide-empty-cats', __('Hide Empty Categories', 'ht-knowledge-base'), array($this, 'archive_hide_empty_cats_option_render'), 'ht_kb_settings_archive_section', 'ht_knowledge_base_archive_settings');
            $this->set_default('hide-empty-cats', false);

            //article section                         
            add_settings_section('ht_knowledge_base_article_settings', __('Article Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_article_section_description'), 'ht_kb_settings_article_section'); 
            //enable comments
            add_settings_field('enable-article-comments', __('Enable Comments', 'ht-knowledge-base'), array($this, 'article_enable_article_comments_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
            $this->set_default('enable-article-comments', true);
            //display usefulness
            add_settings_field('display-article-usefulness', __('Display Usefulness', 'ht-knowledge-base'), array($this, 'article_display_article_usefulness_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
            $this->set_default('display-article-usefulness', true);
            //display views
            add_settings_field('display-article-views-count', __('Display Views', 'ht-knowledge-base'), array($this, 'article_display_article_views_count_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
            $this->set_default('display-article-views-count', true);
            //comment count
            add_settings_field('display-article-comment-count', __('Display Comment Count', 'ht-knowledge-base'), array($this, 'article_display_article_comment_count_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
            $this->set_default('display-article-comment-count', true);
            //related articles
            add_settings_field('display-related-articles', __('Display Related Articles', 'ht-knowledge-base'), array($this, 'article_display_related_articles_option_render'), 'ht_kb_settings_article_section', 'ht_knowledge_base_article_settings');
            $this->set_default('display-related-articles', true);

            //search section
            add_settings_section('ht_knowledge_base_search_settings', __('Search Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_search_section_description'), 'ht_kb_settings_search_section'); 
            //live search
            add_settings_field('display-live-search', __('Live Search', 'ht-knowledge-base'), array($this, 'search_display_live_search_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
            $this->set_default('display-live-search', true);
            //search focus
            add_settings_field('focus-live-search', __('Search Focus', 'ht-knowledge-base'), array($this, 'search_focus_live_search_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
            $this->set_default('focus-live-search', true);
            //search placeholder      
            add_settings_field('search-placeholder-text', __('Search Placeholder', 'ht-knowledge-base'), array($this, 'search_search_placeholder_text_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
            $this->set_default('search-placeholder-text', __('Search the Knowledge Base', 'ht-knowledge-base'));
            //search result excerpt
            add_settings_field('display-search-result-excerpt', __('Search Result Excerpt', 'ht-knowledge-base'), array($this, 'search_display_search_result_excerpt_option_render'), 'ht_kb_settings_search_section', 'ht_knowledge_base_search_settings');
            $this->set_default('display-search-result-excerpt', true);


            //slugs section - note the defaults are no longer translated
            add_settings_section('ht_knowledge_base_slugs_settings', __('Slugs Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_slugs_section_description'), 'ht_kb_settings_slugs_section'); 
            //article slug
            add_settings_field('kb-article-slug', __('Knowledge Base Article Slug', 'ht-knowledge-base'), array($this, 'slugs_kb_article_slug_option_render'), 'ht_kb_settings_slugs_section', 'ht_knowledge_base_slugs_settings');
            $this->set_default('kb-article-slug', 'knowledge-base');
            //category slug
            add_settings_field('kb-category-slug', __('Knowledge Base Category Slug', 'ht-knowledge-base'), array($this, 'slugs_kb_category_slug_option_render'), 'ht_kb_settings_slugs_section', 'ht_knowledge_base_slugs_settings');
            $this->set_default('kb-category-slug', 'article-categories');
            //tag slug
            add_settings_field('kb-tag-slug', __('Knowledge Base Tag Slug', 'ht-knowledge-base'), array($this, 'slugs_kb_tag_slug_option_render'), 'ht_kb_settings_slugs_section', 'ht_knowledge_base_slugs_settings');
            $this->set_default('kb-tag-slug', 'article-tags');


            //custom styles
            add_settings_section('ht_knowledge_base_customstyles_settings', __('Custom Styles Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_customstyles_section_description'), 'ht_kb_settings_customstyles_section'); 
            add_settings_field('custom-kb-styling-content', __('Custom Styles', 'ht-knowledge-base'), array($this, 'customstyles_custom_kb_styline_content_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
            $this->set_default('custom-kb-styling-content', '');
            add_settings_field('enable-kb-styling-sitewide', __('Site-Wide Custom Styles', 'ht-knowledge-base'), array($this, 'customstyles_enable_kb_styling_sitewide_option_render'), 'ht_kb_settings_customstyles_section', 'ht_knowledge_base_customstyles_settings');
            $this->set_default('enable-kb-styling-sitewide', true);


            //article feedback
            add_settings_section('ht_knowledge_base_articlefeedback_settings', __('Article Feedback Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_articlefeedback_section_description'), 'ht_kb_settings_articlefeedback_section'); 
            add_settings_field('enable-article-feedback', __('Enable Feedback', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
            $this->set_default('enable-article-feedback', true);
            add_settings_field('enable-anon-article-feedback', __('Enable Anonymous', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_anon_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
            $this->set_default('enable-anon-article-feedback', true);
            add_settings_field('enable-upvote-article-feedback', __('Upvote Feedback', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_upvote_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
            $this->set_default('enable-upvote-article-feedback', true);
            add_settings_field('enable-downvote-article-feedback', __('Downvote Feedback', 'ht-knowledge-base'), array($this, 'articlefeedback_enable_downvote_article_feedback_option_render'), 'ht_kb_settings_articlefeedback_section', 'ht_knowledge_base_articlefeedback_settings');
            $this->set_default('enable-downvote-article-feedback', true);

            //transfer options
            add_settings_section('ht_knowledge_base_transfers_settings', __('Transfer Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_transfers_section_description'), 'ht_kb_settings_transfers_section'); 
            add_settings_field('kb-transfer-url', __('Default Transfer URL', 'ht-knowledge-base'), array($this, 'transfers_kb_transfer_url_option_render'), 'ht_kb_settings_transfers_section', 'ht_knowledge_base_transfers_settings');
            $this->set_default('kb-transfer-url', 'http://www.example.com/support-desk');
            add_settings_field('kb-transfer-new-window', __('Load in new window', 'ht-knowledge-base'), array($this, 'transfers_kb_transfer_new_window_option_render'), 'ht_kb_settings_transfers_section', 'ht_knowledge_base_transfers_settings');
            $this->set_default('kb-transfer-new-window', true);


            //license and updates
            add_settings_section('ht_knowledge_base_license_settings', __('License and Update Options', 'ht-knowledge-base'), array($this, 'ht_kb_settings_license_section_description'), 'ht_kb_settings_license_section'); 
            add_settings_field('kb-license-key', __('License Key', 'ht-knowledge-base'), array($this, 'license_kb_license_key_option_render'), 'ht_kb_settings_license_section', 'ht_knowledge_base_license_settings');
            $this->set_default('kb-license-key', '');
            
            $this->set_default('activetab', '');

            do_action('maybe_upgrade_ht_kb_settings_fields');

        }


        function ht_kb_settings_display(){
            global $ht_knowledge_base_settings;
            //enqueue style - can be removed once amalgameted with hkb-style-admin
            wp_enqueue_style( 'hkb-style-settings', plugins_url( 'css/hkb-style-settings-page.css', dirname(__FILE__) ) );  
            wp_enqueue_script( 'ht-kb-settings-page', plugins_url( 'js/hkb-admin-settings-page-js.js', dirname(__FILE__) ), array( 'jquery', 'jquery-ui-core',  'jquery-ui-tabs' ), 1.0, true );              
            
            $this->populate_reserved_terms();

            //populate existing post names
            $this->existing_post_names = array();
            //will get first 1000 posts (better than -1?)
            //apply ht_kb_slugs_check_posts_per_page filters 
            $ht_kb_slugs_check_posts_per_page = apply_filters( 'ht_kb_slugs_check_posts_per_page', 1000);
            $all_posts = get_posts( array( 'posts_per_page' => $ht_kb_slugs_check_posts_per_page ) );
            foreach ($all_posts as $key => $post) {
                $this->existing_post_names[] = $post->post_name;
            }

            $conflicted_slug_error = __('Slug cannot be the same as another HelpGuru Knowledge Base slug', 'ht-knowledge-base');
            $reserved_term_error = __('Slug cannot be a reserved WordPress term', 'ht-knowledge-base');
            $slug_in_use_error = __('Slug cannot be the same as an existing post or page, please removed post with this slug first', 'ht-knowledge-base');

            wp_localize_script( 'ht-kb-settings-page', 
                                'settingsPageObjects', 
                                array( 'reservedTerms' => $this->reserved_terms, 
                                      'existingPostNames' => $this->existing_post_names,
                                      'conflictedSlugError' => $conflicted_slug_error,
                                      'reservedTermError' => $reserved_term_error,
                                      'slugInUseError' => $slug_in_use_error ) 
                            );
            $active_tab = $this->ht_kb_settings['activetab'];
            ?>
                <div class="hkb-admin-settings-page">
                    <?php settings_errors(); ?>
                    <h1><?php _e('HelpGuru Knowledge Base Settings', 'ht-knowledge-base'); ?></h1>
                    <div id="hkb-settings-tabs">
                        <ul>
                            <?php if(apply_filters('hkb_add_general_settings_section', true)): ?>
                                <li><a href="#general-section" id="general-section-link" data-section="general" class="active"><?php _e('General', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_archive_settings_section', true)): ?>
                                <li><a href="#archive-section" id="archive-section-link" data-section="archive"><?php _e('Archive', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_article_settings_section', true)): ?>
                                <li><a href="#article-section" id="article-section-link" data-section="article"><?php _e('Article', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_search_settings_section', true)): ?>
                                <li><a href="#search-section" id="search-section-link" data-section="search"><?php _e('Search', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_slugs_settings_section', true)): ?>
                                <li><a href="#slugs-section" id="slugs-section-link" data-section="slugs"><?php _e('Slugs', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_customstyles_settings_section', true)): ?>
                                <li><a href="#customstyles-section" id="customstyles-section-link" data-section="customstyles"><?php _e('Custom Style', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_articlefeedback_settings_section', true)): ?>
                                <li><a href="#articlefeedback-section" id="articlefeedback-section-link" data-section="articlefeedback"><?php _e('Article Feedback', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_transfers_settings_section', true)): ?>
                                <li><a href="#transfers-section" id="transfers-section-link" data-section="tranfers"><?php _e('Transfers', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                            <?php if( apply_filters('hkb_add_license_settings_section', true) && !$this->theme_managed_updates() ) : ?>
                                <li><a href="#license-section" id="license-section-link" data-section="license"><?php _e('License and Updates', 'ht-knowledge-base'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                        <form action="options.php" method="post">
                            <?php settings_fields('ht_knowledge_base_settings'); ?>

                            <input type="hidden" id="activetab" name="ht_knowledge_base_settings[activetab]" value="<?php echo $active_tab; ?>" />
                            <?php if(apply_filters('hkb_add_general_settings_section', true)): ?>
                                <div id="general-section" class="hkb-settings-section active">
                                    <?php do_settings_sections('ht_kb_settings_general_section'); ?> 
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_archive_settings_section', true)): ?>
                                <div id="archive-section" class="hkb-settings-section" style="display:none;">
                                    <?php do_settings_sections('ht_kb_settings_archive_section'); ?> 
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_article_settings_section', true)): ?>
                                <div id="article-section" class="hkb-settings-section" style="display:none;">
                                    <?php do_settings_sections('ht_kb_settings_article_section'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_search_settings_section', true)): ?>
                                <div id="search-section" class="hkb-settings-section" style="display:none;"> 
                                    <?php do_settings_sections('ht_kb_settings_search_section'); ?> 
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_slugs_settings_section', true)): ?>
                                <div id="slugs-section" class="hkb-settings-section" style="display:none;">
                                    <?php do_settings_sections('ht_kb_settings_slugs_section'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_customstyles_settings_section', true)): ?>
                                <div id="customstyles-section" class="hkb-settings-section" style="display:none;">
                                    <?php do_settings_sections('ht_kb_settings_customstyles_section'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_articlefeedback_settings_section', true)): ?>
                                <div id="articlefeedback-section" class="hkb-settings-section" style="display:none;">  
                                    <?php do_settings_sections('ht_kb_settings_articlefeedback_section'); ?> 
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_transfers_settings_section', true)): ?>
                                <div id="transfers-section" class="hkb-settings-section" style="display:none;"> 
                                    <?php do_settings_sections('ht_kb_settings_transfers_section'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if(apply_filters('hkb_add_license_settings_section', true)): ?>
                                <div id="license-section" class="hkb-settings-section" style="display:none;">
                                    <?php do_settings_sections('ht_kb_settings_license_section'); ?>   
                                </div>
                            <?php endif; ?>
                            <input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e(__('Save Changes', 'ht-knowledge-base')); ?>" />
                        </form>
                    </div><!-- /tabs -->
                </div><!-- /hkb-admin-settings-page -->
            <?php

        }


        function set_default($id, $default=false){
            if(isset($this->ht_kb_settings[$id])){
                //do nothing
            } else {
                //set
                $this->ht_kb_settings[$id] = $default;
                //save
                update_option( 'ht_knowledge_base_settings', $this->ht_kb_settings );
            }
        }


        /* GENERAL SECTION */

        //section header 
        function ht_kb_settings_general_section_description(){
            ?>
                <div class="hkb-settings-general-section-start"></div>
            <?php
        }

        //breadcrumbs
        function general_display_breadcrumbs_option_render(){
            if(!apply_filters('hkb_general_display_breadcrumbs_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-breadcrumbs__input"  name="ht_knowledge_base_settings[display-breadcrumbs]" value="1" <?php checked( $this->ht_kb_settings['display-breadcrumbs'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display breadcrumbs in knowledge base', 'ht-knowledge-base'); ?></span>                
            <?php
        }        


        //sort by
        function general_sort_by_option_render(){
            if(!apply_filters('hkb_general_sort_by_option_render', true)){
                return;
            } ?>
                <select class="ht-knowledge-base-settings-sortby__input" name="ht_knowledge_base_settings[sort-by]">
                    <option value="date" <?php selected( $this->ht_kb_settings['sort-by'], 'date', true); ?> ><?php _e('Date', 'ht-knowledge-base') ?></option>
                    <option value="title" <?php selected( $this->ht_kb_settings['sort-by'], 'title', true); ?> ><?php _e('Title', 'ht-knowledge-base') ?></option>
                    <option value="commment-count" <?php selected( $this->ht_kb_settings['sort-by'], 'commment-count', true); ?> ><?php _e('Comment Count', 'ht-knowledge-base') ?></option>
                    <option value="random" <?php selected( $this->ht_kb_settings['sort-by'], 'random', true); ?> ><?php _e('Random', 'ht-knowledge-base') ?></option>
                    <option value="modified" <?php selected( $this->ht_kb_settings['sort-by'], 'modified', true); ?> ><?php _e('Modified', 'ht-knowledge-base') ?></option>
                    <option value="popular" <?php selected( $this->ht_kb_settings['sort-by'], 'popular', true); ?> ><?php _e('Popular', 'ht-knowledge-base') ?></option>
                    <option value="helpful" <?php selected( $this->ht_kb_settings['sort-by'], 'helpful', true); ?> ><?php _e('Helpful', 'ht-knowledge-base') ?></option>
                    <option value="custom" <?php selected( $this->ht_kb_settings['sort-by'], 'custom', true); ?> ><?php _e('Custom', 'ht-knowledge-base') ?></option>
                </select>   
                <span class="hkb_setting_desc"><?php _e('Sort order for display of knowledge base articles', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        //sort order
        function general_sort_order_option_render(){
            if(!apply_filters('hkb_general_sort_order_option_render', true)){
                return;
            } ?>
                <select class="ht-knowledge-base-settings-sort-order__input" name="ht_knowledge_base_settings[sort-order]">
                    <option value="asc" <?php selected( $this->ht_kb_settings['sort-order'], 'asc', true); ?> ><?php _e('Ascending', 'ht-knowledge-base') ?></option>
                    <option value="desc" <?php selected( $this->ht_kb_settings['sort-order'], 'desc', true); ?> ><?php _e('Decending', 'ht-knowledge-base') ?></option>
                </select>   
                <span class="hkb_setting_desc"><?php _e('Sort direction for display of knowledge base articles', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        //number of articles
        function general_num_articles_option_render(){
            $num_articles = isset($this->ht_kb_settings['num-articles']) ? $this->ht_kb_settings['num-articles'] : ''; 
            if(!apply_filters('hkb_general_num_articles_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-num-articles__input ht-validate-number-input" name="ht_knowledge_base_settings[num-articles]" value="<?php esc_attr_e($num_articles, 'ht-knowledge-base'); ?>" data-lower-limit="0" data-upper-limit="100" data-validation-requirements="<?php _e('Must be a valid number between 0 and 100', 'ht-knowledge-base');  ?>"></input>
                <span class="hkb_setting_desc"><?php _e('Number of articles to display for each category or tag from taxonomy archive', 'ht-knowledge-base'); ?></span>      
            <?php
        }


        /* ARCHIVE SECTION */

        //section header 
        function ht_kb_settings_archive_section_description(){
            ?>
                <div class="hkb-settings-archive-section-start"></div>
            <?php
        }

        //number of columns
        function archive_archive_columns_option_render(){
            $kbcolumns = isset($this->ht_kb_settings['archive-columns']) ? $this->ht_kb_settings['archive-columns'] : ''; 
            if(!apply_filters('hkb_archive_archive_columns_option_render', true)){
                esc_attr_e($kbcolumns, 'ht-knowledge-base');
                return;
            } ?>
                <select class="ht-knowledge-base-settings-archive-columns__input" name="ht_knowledge_base_settings[archive-columns]">
                    <option value="1" <?php selected( $this->ht_kb_settings['archive-columns'], '1', true); ?> ><?php _e('One', 'ht-knowledge-base') ?></option>
                    <option value="2" <?php selected( $this->ht_kb_settings['archive-columns'], '2', true); ?> ><?php _e('Two', 'ht-knowledge-base') ?></option>
                    <option value="3" <?php selected( $this->ht_kb_settings['archive-columns'], '3', true); ?> ><?php _e('Three', 'ht-knowledge-base') ?></option>
                    <option value="4" <?php selected( $this->ht_kb_settings['archive-columns'], '4', true); ?> ><?php _e('Four', 'ht-knowledge-base') ?></option>
                </select>   

                <span class="hkb_setting_desc"><?php _e('Number of columns for the knowledge base home', 'ht-knowledge-base'); ?></span>
            <?php
        }

        //display article count
        function archive_display_article_count_option_render(){
            if(!apply_filters('hkb_archive_display_article_count_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-article-count__input" name="ht_knowledge_base_settings[display-article-count]" value="1" <?php checked( $this->ht_kb_settings['display-article-count'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the count of articles in categories', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //articles in home
        function archive_num_articles_home_option_render(){
            //$num_articles_home = isset($this->ht_kb_settings['num-articles-home']) ? $this->ht_kb_settings['num-articles-home'] : ''; 
            if(!apply_filters('hkb_archive_num_articles_home_option_render', true)){
                return;
            } ?>
                <select class="ht-knowledge-base-settings-num-articles-home__input" name="ht_knowledge_base_settings[num-articles-home]">
                    <option value="1" <?php selected( $this->ht_kb_settings['num-articles-home'], '1', true); ?> ><?php _e('1', 'ht-knowledge-base') ?></option>
                    <option value="2" <?php selected( $this->ht_kb_settings['num-articles-home'], '2', true); ?> ><?php _e('2', 'ht-knowledge-base') ?></option>
                    <option value="3" <?php selected( $this->ht_kb_settings['num-articles-home'], '3', true); ?> ><?php _e('3', 'ht-knowledge-base') ?></option>
                    <option value="4" <?php selected( $this->ht_kb_settings['num-articles-home'], '4', true); ?> ><?php _e('4', 'ht-knowledge-base') ?></option>
                    <option value="5" <?php selected( $this->ht_kb_settings['num-articles-home'], '5', true); ?> ><?php _e('5', 'ht-knowledge-base') ?></option>
                    <option value="6" <?php selected( $this->ht_kb_settings['num-articles-home'], '6', true); ?> ><?php _e('6', 'ht-knowledge-base') ?></option>
                    <option value="7" <?php selected( $this->ht_kb_settings['num-articles-home'], '7', true); ?> ><?php _e('7', 'ht-knowledge-base') ?></option>
                    <option value="8" <?php selected( $this->ht_kb_settings['num-articles-home'], '8', true); ?> ><?php _e('8', 'ht-knowledge-base') ?></option>
                    <option value="9" <?php selected( $this->ht_kb_settings['num-articles-home'], '9', true); ?> ><?php _e('9', 'ht-knowledge-base') ?></option>
                    <option value="10" <?php selected( $this->ht_kb_settings['num-articles-home'], '10', true); ?> ><?php _e('10', 'ht-knowledge-base') ?></option>
                </select> 

                <span class="hkb_setting_desc"><?php _e('Number of articles to display for each category in knowledge base home and each subcategory in parent taxonomy archive', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        //display subcats
        function archive_display_sub_cats_option_render(){
            if(!apply_filters('hkb_archive_display_sub_cats_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-sub-cats__input" name="ht_knowledge_base_settings[display-sub-cats]" value="1" <?php checked( $this->ht_kb_settings['display-sub-cats'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the sub-categories from knowledge base home', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //subcat depth
        function archive_sub_cat_depth_option_render(){
            $subcatdepth = isset($this->ht_kb_settings['sub-cat-depth']) ? $this->ht_kb_settings['sub-cat-depth'] : ''; 
            if(!apply_filters('hkb_archive_sub_cat_depth_option_render', true)){
                return;
            } ?>
                <select class="ht-knowledge-base-settings-sub-cat-depth__input" name="ht_knowledge_base_settings[sub-cat-depth]">
                    <option value="0" <?php selected( $this->ht_kb_settings['sub-cat-depth'], '0', true); ?> ><?php _e('None', 'ht-knowledge-base') ?></option>
                    <option value="1" <?php selected( $this->ht_kb_settings['sub-cat-depth'], '1', true); ?> ><?php _e('One', 'ht-knowledge-base') ?></option>
                    <option value="2" <?php selected( $this->ht_kb_settings['sub-cat-depth'], '2', true); ?> ><?php _e('Two', 'ht-knowledge-base') ?></option>
                </select>  
                <span class="hkb_setting_desc"><?php _e('Number of sub-categories to display in knowledge base home', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        //display subcats articles
        function archive_display_sub_cat_articles_option_render(){
            if(!apply_filters('hkb_archive_display_sub_cat_articles_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-sub-cat-articles__input" name="ht_knowledge_base_settings[display-sub-cat-articles]" value="1" <?php checked( $this->ht_kb_settings['display-sub-cat-articles'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the articles in sub-categories from knowledge base home', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //hide empty categories
        function archive_hide_empty_cats_option_render(){
            if(!apply_filters('hkb_archive_hide_empty_cats_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-hide-empty-cats__input" name="ht_knowledge_base_settings[hide-empty-cats]" value="1" <?php checked( $this->ht_kb_settings['hide-empty-cats'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Hide empty categories in the knowledge base home', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        /* ARTICLE SECTION */

        //section header 
        function ht_kb_settings_article_section_description(){
            ?>
                <div class="hkb-settings-article-section-start"></div>
            <?php
        }

        //article comments
        function article_enable_article_comments_option_render(){
            if(!apply_filters('hkb_article_enable_article_comments_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-enable-article-comments__input" name="ht_knowledge_base_settings[enable-article-comments]" value="1" <?php checked( $this->ht_kb_settings['enable-article-comments'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Allow readers to comment on article', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //article usefulness display
        function article_display_article_usefulness_option_render(){
            if(!apply_filters('hkb_article_display_article_usefulness_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-article-usefulness__input" name="ht_knowledge_base_settings[display-article-usefulness]" value="1" <?php checked( $this->ht_kb_settings['display-article-usefulness'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the usefulness of article', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //article viewcount display
        function article_display_article_views_count_option_render(){
            if(!apply_filters('hkb_article_display_article_views_count_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-article-views-count__input" name="ht_knowledge_base_settings[display-article-views-count]" value="1" <?php checked( $this->ht_kb_settings['display-article-views-count'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the view count of article', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //comment count display
        function article_display_article_comment_count_option_render(){
            if(!apply_filters('hkb_article_display_article_comment_count_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-article-comment-count__input" name="ht_knowledge_base_settings[display-article-comment-count]" value="1" <?php checked( $this->ht_kb_settings['display-article-comment-count'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display the comments count of article', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //related articles display
        function article_display_related_articles_option_render(){
            if(!apply_filters('hkb_article_display_related_articles_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-related-articles__input" name="ht_knowledge_base_settings[display-related-articles]" value="1" <?php checked( $this->ht_kb_settings['display-related-articles'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display related (articles that appear in the same category)', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        /* SEARCH SECTION */

        //section header 
        function ht_kb_settings_search_section_description(){
            ?>
                <div class="hkb-settings-search-section-start"></div>
            <?php
        }

        //live search
        function search_display_live_search_option_render(){
            if(!apply_filters('hkb_search_display_live_search_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-live-search__input" name="ht_knowledge_base_settings[display-live-search]" value="1" <?php checked( $this->ht_kb_settings['display-live-search'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display knowledge base search box on knowledge base pages', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //focus search
        function search_focus_live_search_option_render(){
            if(!apply_filters('hkb_search_focus_live_search_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-focus-live-search__input" name="ht_knowledge_base_settings[focus-live-search]" value="1" <?php checked( $this->ht_kb_settings['focus-live-search'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Set the mouse focus on the knowledge base search box when page loads', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        //search placeholder
        function search_search_placeholder_text_option_render(){
            $search_placeholder_text = isset($this->ht_kb_settings['search-placeholder-text']) ? $this->ht_kb_settings['search-placeholder-text'] : ''; 
            if(!apply_filters('hkb_search_search_placeholder_text_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-search-placeholder-text__input" name="ht_knowledge_base_settings[search-placeholder-text]" value="<?php esc_attr_e($search_placeholder_text, 'ht-knowledge-base'); ?>"></input>
                <span class="hkb_setting_desc"><?php _e('Placeholder text for the knowledge base search box', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        //search results excerpt
        function search_display_search_result_excerpt_option_render(){
            if(!apply_filters('hkb_search_display_search_result_excerpt_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-display-search-result-excerpt__input" name="ht_knowledge_base_settings[display-search-result-excerpt]" value="1" <?php checked( $this->ht_kb_settings['display-search-result-excerpt'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Display an excerpt of knowledge base articles in search results', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        /* SLUGS SECTION */

        //section header 
        function ht_kb_settings_slugs_section_description(){
            ?>
                <div class="hkb-settings-slugs-section-start"></div>
            <?php
        }

        //article slug
        function slugs_kb_article_slug_option_render(){
            $kb_article_slug = isset($this->ht_kb_settings['kb-article-slug']) ? $this->ht_kb_settings['kb-article-slug'] : ''; 
            if(!apply_filters('hkb_slugs_kb_article_slug_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-kb-article-slug__input" name="ht_knowledge_base_settings[kb-article-slug]" value="<?php esc_attr_e($kb_article_slug, 'ht-knowledge-base'); ?>" data-error="<?php _e('This slug is invalid or the same as another slug', 'ht-knowledge-base'); ?>"></input>
                <span class="hkb_setting_desc"><?php _e('Defines the text slug for articles', 'ht-knowledge-base'); ?></span>      
            <?php
        }


        //category slug
        function slugs_kb_category_slug_option_render(){
            $kb_category_slug = isset($this->ht_kb_settings['kb-category-slug']) ? $this->ht_kb_settings['kb-category-slug'] : ''; 
            if(!apply_filters('hkb_slugs_kb_category_slug_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-kb-category-slug__input" name="ht_knowledge_base_settings[kb-category-slug]" value="<?php esc_attr_e($kb_category_slug, 'ht-knowledge-base'); ?>" data-error="<?php _e('This slug is invalid or the same as another slug', 'ht-knowledge-base'); ?>"></input>
                <span class="hkb_setting_desc"><?php _e('Defines the text slug for knowledge base categories', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        //tag slug
        function slugs_kb_tag_slug_option_render(){
            $kb_tag_slug = isset($this->ht_kb_settings['kb-tag-slug']) ? $this->ht_kb_settings['kb-tag-slug'] : ''; 
            if(!apply_filters('hkb_slugs_kb_tag_slug_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-kb-tag-slug__input" name="ht_knowledge_base_settings[kb-tag-slug]" value="<?php esc_attr_e($kb_tag_slug, 'ht-knowledge-base'); ?>" data-error="<?php _e('This slug is invalid or the same as another slug', 'ht-knowledge-base'); ?>"></input>
                <span class="hkb_setting_desc"><?php _e('Defines the text slug for knowledge base tags', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        /* CUSTOM STYLES SECTION */

        //section header 
        function ht_kb_settings_customstyles_section_description(){
            ?>
                <div class="hkb-settings-customstyles-section-start"></div>
            <?php
        }

        //custom styles textarea
        function customstyles_custom_kb_styline_content_option_render(){
            $custom_kb_styling_content = isset($this->ht_kb_settings['custom-kb-styling-content']) ? $this->ht_kb_settings['custom-kb-styling-content'] : ''; 
            if(!apply_filters('hkb_customstyles_custom_kb_styline_content_option_render', true)){
                return;
            } ?>
                <textarea type="text" class="ht-knowledge-base-settings-custom-kb-styling-content__input" name="ht_knowledge_base_settings[custom-kb-styling-content]"><?php esc_attr_e($custom_kb_styling_content, 'ht-knowledge-base'); ?></textarea>
                <span class="hkb_setting_desc"><?php printf(__('Important - use valid CSS only, you may want to <a href="%s" target="_blank">validate your styles</a> before saving', 'ht-knowledge-base'), 'https://jigsaw.w3.org/css-validator/#validate_by_input'); ?></span>      
            <?php
        }

        //custom styles sitewide
        function customstyles_enable_kb_styling_sitewide_option_render(){
            if(!apply_filters('hkb_customstyles_enable_kb_styling_sitewide_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-enable-kb-styling-sitewide__input" name="ht_knowledge_base_settings[enable-kb-styling-sitewide]" value="1" <?php checked( $this->ht_kb_settings['enable-kb-styling-sitewide'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Use these custom styles across entire site', 'ht-knowledge-base'); ?></span>                
            <?php
        }


        /* ARTICLE FEEDBACK SECTION */

        //section header 
        function ht_kb_settings_articlefeedback_section_description(){
            ?>
                <div class="hkb-settings-articlefeedback-section-start"></div>
            <?php
        }

        function articlefeedback_enable_article_feedback_option_render(){
            if(!apply_filters('hkb_articlefeedback_enable_article_feedback_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-enable-article-feedback__input" name="ht_knowledge_base_settings[enable-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-article-feedback'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Allow readers to vote', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        function articlefeedback_enable_anon_article_feedback_option_render(){
            if(!apply_filters('hkb_articlefeedback_enable_anon_article_feedback_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-enable-anon-article-feedback__input" name="ht_knowledge_base_settings[enable-anon-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-anon-article-feedback'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Allow readers to vote that are not logged in', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        function articlefeedback_enable_upvote_article_feedback_option_render(){
            if(!apply_filters('hkb_articlefeedback_enable_upvote_article_feedback_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-enable-upvote-article-feedback__input" name="ht_knowledge_base_settings[enable-upvote-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-upvote-article-feedback'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Collect feedback for upvotes', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        function articlefeedback_enable_downvote_article_feedback_option_render(){
            if(!apply_filters('hkb_articlefeedback_enable_downvote_article_feedback_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-enable-downvote-article-feedback__input" name="ht_knowledge_base_settings[enable-downvote-article-feedback]" value="1" <?php checked( $this->ht_kb_settings['enable-downvote-article-feedback'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Collect feedback for downvotes', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        /* TRANSFERS SECTION */

        //section header 
        function ht_kb_settings_transfers_section_description(){
            ?>
                <div class="hkb-settings-transfers-section-start"></div>
            <?php
        }

        function transfers_kb_transfer_url_option_render(){
            $kb_transfer_url = isset($this->ht_kb_settings['kb-transfer-url']) ? $this->ht_kb_settings['kb-transfer-url'] : ''; 
            if(!apply_filters('hkb_transfers_kb_transfer_url_option_render', true)){
                return;
            } ?>
                <input type="text" class="ht-knowledge-base-settings-kb-transfer-url__input" name="ht_knowledge_base_settings[kb-transfer-url]" value="<?php esc_attr_e($kb_transfer_url, 'ht-knowledge-base'); ?>"></input>
                <span class="hkb_setting_desc"><?php _e('Default location that users will be transferred to if no URL specified', 'ht-knowledge-base'); ?></span>      
            <?php
        }

        function transfers_kb_transfer_new_window_option_render(){
            if(!apply_filters('hkb_transfers_kb_transfer_new_window_option_render', true)){
                return;
            } ?>
                <input type="checkbox" class="ht-knowledge-base-settings-kb-transfer-new-window__input" name="ht_knowledge_base_settings[kb-transfer-new-window]" value="1" <?php checked( $this->ht_kb_settings['kb-transfer-new-window'], 1 ); ?> />
                <span class="hkb_setting_desc hkb_setting_desc--inline"><?php _e('Load transfer in a new window', 'ht-knowledge-base'); ?></span>                
            <?php
        }

        /* LICENSE AND UPDATES SECTION */

        //section header 
        function ht_kb_settings_license_section_description(){
            $ht_kb_license_key = get_option( 'ht_kb_license_key' );
            if($this->theme_managed_updates()){
                _e('Updates of this plugin are controlled by the theme, there is nothing required in this section', 'ht-knowledge-base');
                return;
            }
            
            $default_license_status_text = '';
            $license_key_status_class = '';
            if(empty($ht_kb_license_key)){
                $default_license_status_text = __('Please enter your license key below to enable support and updates, this is contained in your download email', 'ht-knowledge-base');
                $license_key_status_class = 'hkb-license-status--invalid';
            } else {
                $default_license_status_text = $ht_kb_license_key;
                $default_license_status_text = sprintf(__('Unverified, Inactive or Expired - check you license status on your account at <a href="%s" target="_blank">HeroThemes</a>' , 'ht-knowledge-base'), 'http://www.herothemes.com');
                $license_key_status_class = 'hkb-license-status--invalid';
            }
            //supporting theme 
            $ht_kb_license_status = get_option('ht_kb_license_status');
            $ht_kb_license_status_form = empty($ht_kb_license_status) ? '' : '(' . $ht_kb_license_status . ') ';

            $ht_kb_license_status_text = ('valid'==$ht_kb_license_status) ? __('Valid and Active', 'ht-knowledge-base') : $ht_kb_license_status_form . $default_license_status_text;

            //set the status class
            if('valid'==$ht_kb_license_status){
                $license_key_status_class = 'hkb-license-status--valid';
            }

            //counter transients
            $ht_kb_license_function = get_transient( '_ht_kb_license_function' );
            if(isset($ht_kb_license_function)){
                //$ht_kb_license_status_text = __('Updating - Refresh page to see license status', 'ht-knowledge-base');
            }
            ?>

                <div class="hkb-license-status <?php echo $license_key_status_class; ?>">
                    <?php echo $ht_kb_license_status_text; ?>
                </div>
            <?php
        }

        function license_kb_license_key_option_render(){
            $kb_license_key = isset($this->ht_kb_settings['kb-license-key']) ? $this->ht_kb_settings['kb-license-key'] : ''; 
            if($this->theme_managed_updates()){
                _e('Managed by theme (Not required)', 'ht-knowledge-base');
                return;
            }
            ?>
                <input type="text" class="ht-knowledge-base-settings-kb-license-key__input" name="ht_knowledge_base_settings[kb-license-key]" value="<?php esc_attr_e($kb_license_key, 'ht-knowledge-base'); ?>"></input>
                <span class="hkb_setting_desc"><?php _e('Enter your HelpGuru Knowledge Base license key', 'ht-knowledge-base'); ?></span>      
            <?php
        }


        /* VALIDATION OF SETTINGS */

        function ht_knowledge_base_settings_validate($input){

            $output = array();



            //boolean validate breadcrumbs
            if( isset($input['display-breadcrumbs']) ) {
                $output['display-breadcrumbs'] = true;
            } else {
                $output['display-breadcrumbs'] = false;
            }

            //sort by
            if( isset($input['sort-by']) ) {
                //check sort by is one of the accepted values
                $sort_by = $input['sort-by'];
                if('date'==$sort_by||'title'==$sort_by||'commment-count'==$sort_by||'random'==$sort_by||'modified'==$sort_by||'popular'==$sort_by||'helpful'==$sort_by||'custom'==$sort_by){
                    //valid sort_by
                    $output['sort-by'] = $sort_by;
                }
            }

            //sort order
            if( isset($input['sort-order']) ) {
                //check sort order is one of the accepted values
                $sort_order = $input['sort-order'];
                if('asc'==$sort_order||'desc'==$sort_order){
                    //valid sort_order
                    $output['sort-order'] = $sort_order;
                }
            }

            //number of articles
            if( isset($input['num-articles']) ) {
                //check num-articles is one of the accepted values
                $num_articles = $input['num-articles'];

                if(is_numeric($num_articles) ){
                    $num_articles = intval($num_articles);
                    if ($num_articles >= 1 && $num_articles <= 100 ){
                        $output['num-articles'] = $num_articles;
                    } else {
                        add_settings_error(
                            'num-articles',
                            'num_articles_error',
                            __('Number of articles must be between 1-100', 'ht-knowledge-base'),
                            'error'                        // type of message
                        );
                    }
                }     
            } 

            //number of columns
            if( isset($input['archive-columns']) ) {
                //check archive-columns is one of the accepted values
                $archive_columns = $input['archive-columns'];

                if(is_numeric($archive_columns) ){
                    $archive_columns = intval($archive_columns);
                    if ($archive_columns >= 1 && $archive_columns <= 4 ){
                        $output['archive-columns'] = $archive_columns;
                    }
                }     
            } 

            //boolean validate displayarticlecount
            if( isset($input['display-article-count']) ) {
                $output['display-article-count'] = true;
            } else {
                $output['display-article-count'] = false;
            }

            //number of articles in home
            if( isset($input['num-articles-home']) ) {
                //check num-articles-home is one of the accepted values
                $num_articles_home = $input['num-articles-home'];

                if(is_numeric($num_articles_home) ){
                    $num_articles_home = intval($num_articles_home);
                    if ($num_articles_home >= 0 && $num_articles_home <= 10 ){
                        $output['num-articles-home'] = $num_articles_home;
                    }
                }     
            }

            //boolean validate displaysubcats
            if( isset($input['display-sub-cats']) ) {
                $output['display-sub-cats'] = true;
            } else {
                $output['display-sub-cats'] = false;
            }


            //subcat depth
            if( isset($input['sub-cat-depth']) ) {
                //check sub-cat-depth is one of the accepted values
                $sub_cat_depth = $input['sub-cat-depth'];

                if(is_numeric($sub_cat_depth) ){
                    $sub_cat_depth = intval($sub_cat_depth);
                    if ($sub_cat_depth >= 0 && $sub_cat_depth <= 2 ){
                        $output['sub-cat-depth'] = $sub_cat_depth;
                    }
                }     
            } 

            //boolean validate display-sub-cat-articles
            if( isset($input['display-sub-cat-articles']) ) {
                $output['display-sub-cat-articles'] = true;
            } else {
                $output['display-sub-cat-articles'] = false;
            }

            //boolean validate hide-empty-cats
            if( isset($input['hide-empty-cats']) ) {
                $output['hide-empty-cats'] = true;
            } else {
                $output['hide-empty-cats'] = false;
            }

            //boolean validate enable-article-comments
            if( isset($input['enable-article-comments']) ) {
                $output['enable-article-comments'] = true;
            } else {
                $output['enable-article-comments'] = false;
            }

            //boolean validate display-article-usefulness
            if( isset($input['display-article-usefulness']) ) {
                $output['display-article-usefulness'] = true;
            } else {
                $output['display-article-usefulness'] = false;
            }

            //boolean validate display-article-views-count
            if( isset($input['display-article-views-count']) ) {
                $output['display-article-views-count'] = true;
            } else {
                $output['display-article-views-count'] = false;
            }

            //boolean validate display-article-comment-count
            if( isset($input['display-article-comment-count']) ) {
                $output['display-article-comment-count'] = true;
            } else {
                $output['display-article-comment-count'] = false;
            }

            //boolean validate display-related-articles
            if( isset($input['display-related-articles']) ) {
                $output['display-related-articles'] = true;
            } else {
                $output['display-related-articles'] = false;
            }

            //boolean validate display-live-search
            if( isset($input['display-live-search']) ) {
                $output['display-live-search'] = true;
            } else {
                $output['display-live-search'] = false;
            }

            //boolean validate focus-live-search
            if( isset($input['focus-live-search']) ) {
                $output['focus-live-search'] = true;
            } else {
                $output['focus-live-search'] = false;
            }

            //search placeholder
            if( isset($input['search-placeholder-text']) ) {
                $output['search-placeholder-text'] = esc_attr($input['search-placeholder-text']);
            } else {
                $output['search-placeholder-text'] = '';
            }

            //boolean validate display-search-result-excerpt
            if( isset($input['display-search-result-excerpt']) ) {
                $output['display-search-result-excerpt'] = true;
            } else {
                $output['display-search-result-excerpt'] = false;
            }

            //kb-article-slug
            if( isset($input['kb-article-slug']) ) {
                $slug = $this->validate_slug($input['kb-article-slug'], $this->ht_kb_settings['kb-article-slug'], 'kb-article-slug');
                $output['kb-article-slug'] =  $slug;
            }

            //kb-category-slug
            if( isset($input['kb-category-slug']) ) {
                $slug = $this->validate_slug($input['kb-category-slug'], $this->ht_kb_settings['kb-category-slug'], 'kb-category-slug');
                $output['kb-category-slug'] =  $slug;
            }

            //kb-tag-slug
            if( isset($input['kb-tag-slug']) ) {
                $slug = $this->validate_slug($input['kb-tag-slug'], $this->ht_kb_settings['kb-tag-slug'], 'kb-tag-slug');
                $output['kb-tag-slug'] =  $slug;
            }

            //no validation required on custom-kb-styling-content?
            if( isset($input['custom-kb-styling-content']) ) {
                $output['custom-kb-styling-content'] =  $input['custom-kb-styling-content'];
            }


            //boolean validate enable-kb-styling-sitewide
            if( isset($input['enable-kb-styling-sitewide']) ) {
                $output['enable-kb-styling-sitewide'] = true;
            } else {
                $output['enable-kb-styling-sitewide'] = false;
            }

            //boolean validate enable-article-feedback
            if( isset($input['enable-article-feedback']) ) {
                $output['enable-article-feedback'] = true;
            } else {
                $output['enable-article-feedback'] = false;
            }

            //boolean validate enable-anon-article-feedback
            if( isset($input['enable-anon-article-feedback']) ) {
                $output['enable-anon-article-feedback'] = true;
            } else {
                $output['enable-anon-article-feedback'] = false;
            }

            //boolean validate enable-upvote-article-feedback
            if( isset($input['enable-upvote-article-feedback']) ) {
                $output['enable-upvote-article-feedback'] = true;
            } else {
                $output['enable-upvote-article-feedback'] = false;
            }

            //boolean validate enable-downvote-article-feedback
            if( isset($input['enable-downvote-article-feedback']) ) {
                $output['enable-downvote-article-feedback'] = true;
            } else {
                $output['enable-downvote-article-feedback'] = false;
            }

            //kb-transfer-url
            if( isset($input['kb-transfer-url']) ) {
                $output['kb-transfer-url'] = esc_attr($input['kb-transfer-url']);
            } else {
                $output['kb-transfer-url'] = '';
            }

            //boolean validate kb-transfer-new-window
            if( isset($input['kb-transfer-new-window']) ) {
                $output['kb-transfer-new-window'] = true;
            } else {
                $output['kb-transfer-new-window'] = false;
            }

            //kb-license-key
            if( isset($input['kb-license-key']) ) {
                $output['kb-license-key'] = esc_attr($input['kb-license-key']);
                $this->validate_license(esc_attr($input['kb-license-key']), $this->ht_kb_settings['kb-license-key']);
            } else {
                $output['kb-license-key'] = '';
                $this->validate_license('', $this->ht_kb_settings['kb-license-key']);
            }

            //activetab
            if( isset($input['activetab']) ) {
                $output['activetab'] = esc_attr($input['activetab']);
            } else {
                $output['activetab'] = '';
            }


            return apply_filters( 'ht_knowledge_base_settings_validate', $output, $input );
        }


        function validate_slug($slug, $existing_value, $field) {

            $error = false;

            //replace spaces in slugs
            $slug = preg_replace('/\s+/', '', $slug);

            //slug cant be less than 2 characters
            if( strlen($slug) < 2 ){
                $error = true;
            }

            //slug should not be reserved term
            if($this->is_reserved_term($slug)){
                $error = true;
            }

            //slug can't end with a /
            if (substr($slug, -1) == '/' ){
                $slug = substr($slug, 0, -1);
                //recursive call
                return $this->validate_slug($slug, $existing_value, $field);
            }

            if ( $error ) {
                $slug = $existing_value;
            } else {
                //if no error and value has changed, flag to flush rewrite rules
                if( $slug!=$existing_value )
                    update_option('ht_kb_flush_rewrite_required', true);
            }
            return $slug;
        }

        function is_reserved_term($slug){
            $this->populate_reserved_terms();
            if ( in_array( $slug, $this->reserved_terms )){
                return true;
            } else {
                return false;
            }
        }

        function validate_license($new_license_key, $current_license_key) {
            if ($current_license_key != $new_license_key ){
                if( isset($current_license_key) && $current_license_key!='' ){
                    //deactivate old license
                    HT_Knowledge_Base_Updater::deactivate_license($current_license_key);
                }
                if( isset($new_license_key) && $new_license_key!='' ){
                    //activate new license
                    HT_Knowledge_Base_Updater::activate_license($new_license_key);
                }
            } else {
                //else just check license
                HT_Knowledge_Base_Updater::check_license($new_license_key);
            }

            if ( ($current_license_key != $new_license_key ) && ( ! isset( $new_license_key ) || empty( $new_license_key ) ) ) {
                //empty license can remove key if deactivated and empty
                HT_Knowledge_Base_Updater::deactivate_license($current_license_key);
                delete_option( 'ht_kb_license_key' );
                delete_option( 'ht_kb_license_status' );
            } else {
                //other checks?
            }
            //always returns true
            return true;
        }   


        function theme_managed_updates(){
            if( ( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates') ) ){
                return true;
            } else {
                return false;
            }
        }

        function populate_reserved_terms(){
            //populate reserved terms
            $this->reserved_terms = array(   'attachment',
                                             'attachment_id',
                                             'author',
                                             'author_name',
                                             'calendar',
                                             'cat',
                                             'category',
                                             'category__and',
                                             'category__in',
                                             'category__not_in',
                                             'category_name',
                                             'comments_per_page',
                                             'comments_popup',
                                             'cpage',
                                             'day',
                                             'debug',
                                             'error',
                                             'exact',
                                             'feed',
                                             'hour',
                                             'link_category',
                                             'm',
                                             'minute',
                                             'monthnum',
                                             'more',
                                             'name',
                                             'nav_menu',
                                             'nopaging',
                                             'offset',
                                             'order',
                                             'orderby',
                                             'p',
                                             'page',
                                             'page_id',
                                             'paged',
                                             'pagename',
                                             'pb',
                                             'perm',
                                             'post',
                                             'post__in',
                                             'post__not_in',
                                             'post_format',
                                             'post_mime_type',
                                             'post_status',
                                             'post_tag',
                                             'post_type',
                                             'posts',
                                             'posts_per_archive_page',
                                             'posts_per_page',
                                             'preview',
                                             'robots',
                                             's',
                                             'search',
                                             'second',
                                             'sentence',
                                             'showposts',
                                             'static',
                                             'subpost',
                                             'subpost_id',
                                             'tag',
                                             'tag__and',
                                             'tag__in',
                                             'tag__not_in',
                                             'tag_id',
                                             'tag_slug__and',
                                             'tag_slug__in',
                                             'taxonomy',
                                             'tb',
                                             'term',
                                             'type',
                                             'w',
                                             'withcomments',
                                             'withoutcomments',
                                             'year',
                                          );

            //apply ht_kb_slugs_check_reserved_terms filters
            $this->reserved_terms = apply_filters( 'ht_kb_slugs_check_reserved_terms', $this->reserved_terms );
        }


        

    }//end class

}

if (class_exists('Knowledge_Base_Settings')) {
    $ht_kb_settings_page_init = new Knowledge_Base_Settings();
}