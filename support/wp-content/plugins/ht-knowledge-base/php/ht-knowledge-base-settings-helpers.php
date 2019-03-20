<?php
/*
* Settings helper functions
*/

if(!function_exists('hkb_show_knowledgebase_search')){
    /**
    * Get the Knowledge Base search display option
    * Filterable - hkb_show_knowledgebase_search
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_knowledgebase_search($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_knowledgebase_search = false;
        if ( isset( $ht_knowledge_base_settings['display-live-search'] ) ){
            $hkb_show_knowledgebase_search = $ht_knowledge_base_settings['display-live-search'];
        } else {
            $hkb_show_knowledgebase_search = false;
        }
        return apply_filters('hkb_show_knowledgebase_search', $hkb_show_knowledgebase_search);
    }
}

if(!function_exists('hkb_archive_columns')){
    /**
    * Number of archive columns to display
    * Filterable - hkb_archive_columns
    * @return (Int) The option
    */
    function hkb_archive_columns(){
        global $ht_knowledge_base_settings;
        $hkb_archive_columns = 2;
        if ( isset( $ht_knowledge_base_settings['archive-columns'] ) ){
            $hkb_archive_columns = $ht_knowledge_base_settings['archive-columns'];
        } else {
            $hkb_archive_columns = 2;
        }
        return apply_filters('hkb_archive_columns', $hkb_archive_columns);
    }
}

if(!function_exists('hkb_archive_sortby')){
    /**
    * Sort articles by
    * Filterable - hkb_archive_sortby
    * @return (Int) The option
    */
    function hkb_archive_sortby(){
        global $ht_knowledge_base_settings;
        $hkb_archive_sortby = 2;
        if ( isset( $ht_knowledge_base_settings['sort-by'] ) ){
            $hkb_archive_sortby = $ht_knowledge_base_settings['sort-by'];
        } else {
            $hkb_archive_sortby = 'date';
        }
        return apply_filters('hkb_archive_sortby', $hkb_archive_sortby);
    }
}

if(!function_exists('hkb_archive_sortorder')){
    /**
    * Sort order
    * Filterable - hkb_archive_sortorder
    * @return (Int) The option
    */
    function hkb_archive_sortorder(){
        global $ht_knowledge_base_settings;
        $hkb_archive_sortorder = 2;
        if ( isset( $ht_knowledge_base_settings['sort-order'] ) ){
            $hkb_archive_sortorder = $ht_knowledge_base_settings['sort-order'];
        } else {
            $hkb_archive_sortorder = 'asc';
        }
        return apply_filters('hkb_archive_sortorder', $hkb_archive_sortorder);
    }
}

if(!function_exists('hkb_archive_columns_string')){
    /**
    * Number of archive columns to display (as a string)
    * Filterable - hkb_archive_columns_string
    * @return (String) The option
    */
    function hkb_archive_columns_string(){
        // Set column variable to class needed for CSS
        $hkb_archive_columns_string = hkb_archive_columns();
        if ($hkb_archive_columns_string == '1') :
            $hkb_archive_columns_string = 'one';
        elseif ($hkb_archive_columns_string == '2') :
            $hkb_archive_columns_string = 'two';
        elseif ($hkb_archive_columns_string == '3') :
            $hkb_archive_columns_string = 'three';
        elseif ($hkb_archive_columns_string == '4') :
            $hkb_archive_columns_string = 'four';
        else :
            $hkb_archive_columns_string = 'two';
        endif; 

        return apply_filters('hkb_archive_columns_string', $hkb_archive_columns_string);
    }
}

if(!function_exists('hkb_archive_display_subcategories')){
    /**
    * Get the Knowledge Base subcategory count display option
    * Filterable - hkb_archive_display_subcategories
    * @return (Bool) The option
    */
    function hkb_archive_display_subcategories(){   
        global $ht_knowledge_base_settings;
        $hkb_archive_display_subcategories = false;
        if ( isset( $ht_knowledge_base_settings['display-sub-cats'] ) ){
            $hkb_archive_display_subcategories = $ht_knowledge_base_settings['display-sub-cats'];
        } else {
            $hkb_archive_display_subcategories = false;
        }

        return apply_filters('hkb_archive_display_subcategories', $hkb_archive_display_subcategories);
    }
}

if(!function_exists('hkb_archive_subcategory_depth')){
    /**
    * Get the Knowledge Base subcategory depth option
    * Filterable - hkb_archive_subcategory_depth
    * @return (Int) The option
    */
    function hkb_archive_subcategory_depth(){   
        global $ht_knowledge_base_settings;
        $hkb_archive_subcategory_depth = false;
        if ( isset( $ht_knowledge_base_settings['sub-cat-depth'] ) ){
            $hkb_archive_subcategory_depth = $ht_knowledge_base_settings['sub-cat-depth'];
        } else {
            $hkb_archive_subcategory_depth = 1;
        }

        return apply_filters('hkb_archive_subcategory_depth', $hkb_archive_subcategory_depth);
    }
}

if(!function_exists('hkb_archive_display_subcategory_count')){
    /**
    * Get the Knowledge Base subcategory count display option
    * Filterable - hkb_archive_display_subcategory_count
    * @return (Bool) The option
    * @deprecated No nogle used
    */
    function hkb_archive_display_subcategory_count(){
        global $ht_knowledge_base_settings;  
        $hkb_archive_display_subcategory_count = false;
        if ( isset( $ht_knowledge_base_settings['display-article-count'] ) ){
            $hkb_archive_display_subcategory_count = $ht_knowledge_base_settings['display-article-count'];
        } else {
            $hkb_archive_display_subcategory_count = false;
        }


        return apply_filters('hkb_archive_display_subcategory_count', $hkb_archive_display_subcategory_count);
    }
}

if(!function_exists('hkb_archive_display_subcategory_articles')){
    /**
    * Get the Knowledge Base subcategory list display option
    * Filterable - hkb_archive_display_subcategory_articles
    * @return (Bool) The option
    */
    function hkb_archive_display_subcategory_articles(){    
        global $ht_knowledge_base_settings;
        $hkb_archive_display_subcategory_articles = false;
        if ( isset( $ht_knowledge_base_settings['display-sub-cat-articles'] ) ){
            $hkb_archive_display_subcategory_articles = $ht_knowledge_base_settings['display-sub-cat-articles'];
        } else {
            $hkb_archive_display_subcategory_articles = false;
        }

        return apply_filters('hkb_archive_display_subcategory_articles', $hkb_archive_display_subcategory_articles);
    }
}

if(!function_exists('hkb_archive_hide_empty_categories')){
    /**
    * Get the Knowledge Base hide empty categories option
    * Filterable - hkb_archive_display_subcategory_articles
    * @return (Bool) The option
    */
    function hkb_archive_hide_empty_categories(){   
        global $ht_knowledge_base_settings;
        $hkb_archive_hide_empty_categories = false;
        if ( isset( $ht_knowledge_base_settings['hide-empty-cats'] ) ){
            $hkb_archive_hide_empty_categories = $ht_knowledge_base_settings['hide-empty-cats'];
        } else {
            $hkb_archive_hide_empty_categories = false;
        }

        return apply_filters('hkb_archive_hide_empty_categories', $hkb_archive_hide_empty_categories);
    }
}

if(!function_exists('hkb_get_knowledgebase_searchbox_placeholder_text')){
    /**
    * Get the Knowledge Base searchbox placeholder text
    * Filterable - hkb_get_knowledgebase_searchbox_placeholder_text
    * @return (String) The placeholder text
    */
    function hkb_get_knowledgebase_searchbox_placeholder_text(){
        global $post, $ht_knowledge_base_settings;
        $hkb_get_knowledgebase_searchbox_placeholder_text = '';
        //there is an issue with the global ht_knowledge_base_settings not being translated, hence we'll revert to the get_option call
        
        $hkb_get_knowledgebase_searchbox_placeholder_text =  (isset($ht_knowledge_base_settings) && is_array($ht_knowledge_base_settings) && array_key_exists('search-placeholder-text', $ht_knowledge_base_settings)) ? 
                                $ht_knowledge_base_settings['search-placeholder-text'] : 
                                __('Search the Knowledge Base', 'ht-knowledge-base');
        return apply_filters('hkb_get_knowledgebase_searchbox_placeholder_text', $hkb_get_knowledgebase_searchbox_placeholder_text);

    }
}

if(!function_exists('hkb_show_knowledgebase_breadcrumbs')){
    /**
    * Get the Knowledge Base display-breadcrumbs option
    * Filterable - hkb_show_knowledgebase_breadcrumbs
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_knowledgebase_breadcrumbs($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_knowledgebase_breadcrumbs = false;
        if ( isset( $ht_knowledge_base_settings['display-breadcrumbs'] ) ){
            $hkb_show_knowledgebase_breadcrumbs = $ht_knowledge_base_settings['display-breadcrumbs'];
        } else {
            $hkb_show_knowledgebase_breadcrumbs = false;
        }
        return apply_filters('hkb_show_knowledgebase_breadcrumbs', $hkb_show_knowledgebase_breadcrumbs);
    }
}

if(!function_exists('hkb_show_usefulness_display')){
    /**
    * Get the Knowledge Base usefulness display option
    * Filterable - hkb_show_usefulness_display
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_usefulness_display($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_usefulness_display = false;
        if ( isset( $ht_knowledge_base_settings['display-article-usefulness'] ) ){
            $hkb_show_usefulness_display = $ht_knowledge_base_settings['display-article-usefulness'];
        } else {
            $hkb_show_usefulness_display = false;
        }
        return apply_filters('hkb_show_usefulness_display', $hkb_show_usefulness_display);
    }
}

if(!function_exists('hkb_show_viewcount_display')){
    /**
    * Get the Knowledge Base viewcount display option
    * Filterable - hkb_show_viewcount_display
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_viewcount_display($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_viewcount_display = false;
        if ( isset( $ht_knowledge_base_settings['display-article-views-count'] ) ){
            $hkb_show_viewcount_display = $ht_knowledge_base_settings['display-article-views-count'];
        } else {
            $hkb_show_viewcount_display = false;
        }
        return apply_filters('hkb_show_viewcount_display', $hkb_show_viewcount_display);
    }
}

if(!function_exists('hkb_show_comments_display')){
    /**
    * Get the Knowledge Base comments display option
    * Filterable - hkb_show_comments_display
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_comments_display($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_comments_display = false;
        if ( isset( $ht_knowledge_base_settings['enable-article-comments'] ) ){
            $hkb_show_comments_display = $ht_knowledge_base_settings['enable-article-comments'];
        } else {
            $hkb_show_comments_display = false;
        }
        return apply_filters('hkb_show_comments_display', $hkb_show_comments_display);
    }
}

if(!function_exists('hkb_show_related_articles')){
    /**
    * Get the Knowledge Base show related articles
    * Filterable - hkb_show_related_articles
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_show_related_articles($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_related_articles = true;
        if ( isset( $ht_knowledge_base_settings['display-related-articles'] ) ){
            $hkb_show_related_articles = $ht_knowledge_base_settings['display-related-articles'];
        } else {
            $hkb_show_related_articles = true;
        }
        return apply_filters('hkb_show_related_articles', $hkb_show_related_articles);
    }
}

if(!function_exists('hkb_show_search_excerpt')){
    /**
    * Get the Knowledge Base search excerpt display option
    * Filterable - hkb_show_search_excerpt
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    * @deprecated
    */
    function hkb_show_search_excerpt($location=null){
        global $ht_knowledge_base_settings;
        $hkb_show_search_excerpt = false;
        if ( isset( $ht_knowledge_base_settings['display-search-result-excerpt'] ) ){
            $hkb_show_search_excerpt = $ht_knowledge_base_settings['display-search-result-excerpt'];
        } else {
            $hkb_show_search_excerpt = false;
        }
        return apply_filters('hkb_show_search_excerpt', $hkb_show_search_excerpt);
    }
}

if(!function_exists('hkb_focus_on_search_box')){
    /**
    * Get the Knowledge Base related rating display
    * Filterable - hkb_focus_on_search_box
    * @param (String) $location The location of where to display (currently unused)
    * @return (Bool) The option
    */
    function hkb_focus_on_search_box($location=null){
        global $ht_knowledge_base_settings;
        $hkb_focus_on_search_box = false;
        if ( isset( $ht_knowledge_base_settings['focus-live-search'] ) ){
            $hkb_focus_on_search_box = $ht_knowledge_base_settings['focus-live-search'];
        } else {
            $hkb_focus_on_search_box = false;
        }
        return apply_filters('hkb_focus_on_search_box', $hkb_focus_on_search_box);
    }
}

if(!function_exists('hkb_home_articles')){
    /**
    * Number of articles to display in home
    * Filterable - hkb_home_articles
    * @return (Int) The option
    */
    function hkb_home_articles($location=null){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['num-articles-home'] ) ){
            $hkb_home_articles = $ht_knowledge_base_settings['num-articles-home'];
        } else {
            $hkb_home_articles = get_option('posts_per_page');
        }
        return apply_filters('hkb_home_articles', $hkb_home_articles);
    }
}


if(!function_exists('hkb_category_articles')){
    /**
    * Number of articles to display in category
    * Filterable - hkb_category_articles
    * @return (Int) The option
    */
    function hkb_category_articles($location=null){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['num-articles'] ) ){
            $hkb_category_articles = $ht_knowledge_base_settings['num-articles'];
        } else {
            $hkb_category_articles = get_option('posts_per_page');
        }
        return apply_filters('hkb_category_articles', $hkb_category_articles);
    }
}


if(!function_exists('hkb_get_custom_styles_css')){
    /**
    * Get the Knowledge Base custom styles
    * Filterable - hkb_get_custom_styles_css
    * @return (String) Custom CSS
    */
    function hkb_get_custom_styles_css(){   
        global $ht_knowledge_base_settings;
        $hkb_get_custom_styles_css = '';
        if ( isset( $ht_knowledge_base_settings['custom-kb-styling-content']) && !empty($ht_knowledge_base_settings['custom-kb-styling-content']) ){
            $styles = '<!-- HelpGuru Knowledge Base custom styles -->';
            $styles .= '<style>';
            $styles .= $ht_knowledge_base_settings['custom-kb-styling-content'];
            $styles .= '</style>';
            $hkb_get_custom_styles_css = $styles;
        } else {
            $hkb_get_custom_styles_css = '';
        }
        return apply_filters('hkb_get_custom_styles_css', $hkb_get_custom_styles_css);
    }
}

if(!function_exists('hkb_custom_styles_sitewide')){
    /**
    * Whether to use custom styles sitewide
    * Filterable - hkb_custom_styles_sitewide
    * @return (Boolean) default false
    */
    function hkb_custom_styles_sitewide(){   
        global $ht_knowledge_base_settings;
        $hkb_custom_styles_sitewide = false;
        if ( isset( $ht_knowledge_base_settings['enable-kb-styling-sitewide']) ){
            $hkb_custom_styles_sitewide = $ht_knowledge_base_settings['enable-kb-styling-sitewide'];            
        } else {
            $hkb_custom_styles_sitewide = false;
        }
        return apply_filters('hkb_custom_styles_sitewide', $hkb_custom_styles_sitewide);
    }
}

if(!function_exists('hkb_kb_search_sitewide')){
    /**
    * Whether to use search in kb sitewide
    * Filterable - hkb_kb_search_sitewide
    * @return (Boolean) default false
    * @deprecated No longer used
    */
    function hkb_kb_search_sitewide(){   
        $hkb_kb_search_sitewide = false;

        return apply_filters('hkb_kb_search_sitewide', $hkb_kb_search_sitewide);
    }
}


if(!function_exists('hkb_kb_article_slug')){
    /**
    * Filterable - hkb_kb_article_slug
    * @return (String) option
    */
    function hkb_kb_article_slug(){ 
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-article-slug']) ){
            $hkb_kb_article_slug = $ht_knowledge_base_settings['kb-article-slug'];            
        } else {
            $hkb_kb_article_slug = 'knowledge-base';
        }  

        return apply_filters('hkb_kb_article_slug', $hkb_kb_article_slug);
    }
}

if(!function_exists('hkb_kb_category_slug')){
    /**
    * Filterable - hkb_kb_category_slug
    * @return (String) option
    */
    function hkb_kb_category_slug(){   
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-category-slug']) ){
            $hkb_kb_category_slug = $ht_knowledge_base_settings['kb-category-slug'];            
        } else {
            $hkb_kb_category_slug = 'article-categories';
        }         

        return apply_filters('hkb_kb_category_slug', $hkb_kb_category_slug);
    }
}

if(!function_exists('hkb_kb_tag_slug')){
    /**
    * Filterable - hkb_kb_tag_slug
    * @return (String) option
    */
    function hkb_kb_tag_slug(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-tag-slug']) ){
            $hkb_kb_tag_slug = $ht_knowledge_base_settings['kb-tag-slug'];            
        } else {
            $hkb_kb_tag_slug = 'article-tags';
        }         

        return apply_filters('hkb_kb_tag_slug', $hkb_kb_tag_slug);
    }
}


//exits

if(!function_exists('ht_kb_exit_display_at_end_of_article')){
    /**
    * Filterable - ht_kb_exit_display_at_end_of_article, shiv, no longer used, to deprecate
    * @return (Boolean) option 
    */
    function ht_kb_exit_display_at_end_of_article(){
        $ht_kb_exit_display_at_end_of_article = false;       

        return apply_filters('ht_kb_exit_display_at_end_of_article', $ht_kb_exit_display_at_end_of_article);
    }
}

if(!function_exists('ht_kb_exit_url_option')){
    /**
    * Filterable - ht_kb_exit_url_option
    * @return (String) option
    */
    function ht_kb_exit_url_option(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-transfer-url']) ){
            $ht_kb_exit_url_option = $ht_knowledge_base_settings['kb-transfer-url'];            
        } else {
            $ht_kb_exit_url_option = 'http://www.example.com/support-desk';
        }         

        return apply_filters('ht_kb_exit_url_option', $ht_kb_exit_url_option);
    }
}


if(!function_exists('ht_kb_exit_new_window_option')){
    /**
    * Filterable - ht_kb_exit_new_window_option
    * @return (boolean) option
    */
    function ht_kb_exit_new_window_option(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['kb-transfer-new-window']) ){
            $ht_kb_exit_new_window_option = $ht_knowledge_base_settings['kb-transfer-new-window'];            
        } else {
            $ht_kb_exit_new_window_option = true;
        }         

        return apply_filters('ht_kb_exit_new_window_option', $ht_kb_exit_new_window_option);
    }
}


if(!function_exists('ht_kb_voting_enable_feedback')){
    /**
    * Filterable - ht_kb_voting_enable_feedback
    * @return (boolean) option
    */
    function ht_kb_voting_enable_feedback(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-article-feedback']) ){
            $ht_kb_voting_enable_feedback = $ht_knowledge_base_settings['enable-article-feedback'];            
        } else {
            $ht_kb_voting_enable_feedback = true;
        }         

        return apply_filters('ht_kb_voting_enable_feedback', $ht_kb_voting_enable_feedback);
    }
}

if(!function_exists('ht_kb_voting_enable_anonymous')){
    /**
    * Filterable - ht_kb_voting_enable_anonymous
    * @return (boolean) option
    */
    function ht_kb_voting_enable_anonymous(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-anon-article-feedback']) ){
            $ht_kb_voting_enable_anonymous = $ht_knowledge_base_settings['enable-anon-article-feedback'];            
        } else {
            $ht_kb_voting_enable_anonymous = true;
        }         

        return apply_filters('ht_kb_voting_enable_anonymous', $ht_kb_voting_enable_anonymous);
    }
}

if(!function_exists('ht_kb_voting_upvote_feedback')){
    /**
    * Filterable - ht_kb_voting_upvote_feedback
    * @return (boolean) option
    */
    function ht_kb_voting_upvote_feedback(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-upvote-article-feedback']) ){
            $ht_kb_voting_upvote_feedback = $ht_knowledge_base_settings['enable-upvote-article-feedback'];            
        } else {
            $ht_kb_voting_upvote_feedback = true;
        }         

        return apply_filters('ht_kb_voting_upvote_feedback', $ht_kb_voting_upvote_feedback);
    }
}

if(!function_exists('ht_kb_voting_downvote_feedback')){
    /**
    * Filterable - ht_kb_voting_downvote_feedback
    * @return (boolean) option
    */
    function ht_kb_voting_downvote_feedback(){
        global $ht_knowledge_base_settings;
        if ( isset( $ht_knowledge_base_settings['enable-downvote-article-feedback']) ){
            $ht_kb_voting_downvote_feedback = $ht_knowledge_base_settings['enable-downvote-article-feedback'];            
        } else {
            $ht_kb_voting_downvote_feedback = true;
        }         

        return apply_filters('ht_kb_voting_downvote_feedback', $ht_kb_voting_downvote_feedback);
    }
}