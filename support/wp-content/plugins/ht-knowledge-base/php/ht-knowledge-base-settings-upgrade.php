<?php
/**
 * 2.6.5 New Settings Page (Upgrade Routines)
 */

if (!class_exists('Knowledge_Base_Settings_Upgrade')) {

    if(!defined('HT_KB_OLD_SETTINGS_KEY')){
        define('HT_KB_OLD_SETTINGS_KEY', 'ht_knowledge_base_options');
    }

    if(!defined('HT_KB_NEW_SETTINGS_KEY')){
        define('HT_KB_NEW_SETTINGS_KEY', 'ht_knowledge_base_settings');
    }

    if(!defined('HT_KB_TWO_SEVEN_UPGRADE_KEY')){
        define('HT_KB_TWO_SEVEN_UPGRADE_KEY', 'ht_knowledge_base_2_7_upgrade_complete');
    }

    if(!defined('HT_KB_TWO_SEVEN_BACKUP_SETTINGS_KEY')){
        define('HT_KB_TWO_SEVEN_BACKUP_SETTINGS_KEY', 'ht_knowledge_base_2_7_settings_backup');
    }

    class Knowledge_Base_Settings_Upgrade {

        private $old_settings_array;
        private $new_settings_array;


        //constructor
        function __construct(){  

            //upgrade functionality hooked to ht_kb_activate and maybe_upgrade_ht_kb_settings_fields
            add_action('maybe_upgrade_ht_kb_settings_fields', array($this, 'ht_kb_settings_upgrade'), 10 );
            add_action('ht_kb_activate', array($this, 'ht_kb_settings_upgrade'), 10 );         

        }

        /**
        * Settings upgrade routine
        */
        function ht_kb_settings_upgrade(){

            //populate new settings array
            $this->new_settings_array = get_option(HT_KB_NEW_SETTINGS_KEY);
            if(false==$this->new_settings_array){
                //initialize array
                $this->new_settings_array = array();
            }

            //test upgrade already done, exit as no further action required
            if(get_option(HT_KB_TWO_SEVEN_UPGRADE_KEY)){
                return;    
            } else {
                //else initialize new settings array
                $this->new_settings_array = array();
            }
                   

            //populate old settings array
            $this->old_settings_array = get_option(HT_KB_OLD_SETTINGS_KEY);


            //MAPPINGS

            //breadcrumbs-display => display-breadcrumbs
            $this->upgrade_setting('breadcrumbs-display', 'display-breadcrumbs');

            //sort-by => sort-by
            $this->upgrade_setting('sort-by', 'sort-by');

            //sort-order => sort-order
            $this->upgrade_setting('sort-order', 'sort-order');

            //tax-cat-article-number  => num-articles
            $this->upgrade_setting('tax-cat-article-number', 'num-articles');

            //kb-home => N/A         
            //N/A

            //archive-columns => archive-columns
            $this->upgrade_setting('archive-columns', 'archive-columns');

            //sub-cat-article-count   => display-article-count
            $this->upgrade_setting('sub-cat-article-count', 'display-article-count');

            //sub-cat-article-number  => num-articles-home
            $this->upgrade_setting('sub-cat-article-number', 'num-articles-home');

            //sub-cat-display => display-sub-cats
            $this->upgrade_setting('sub-cat-display', 'display-sub-cats');

            //sub-cat-depth  => sub-cat-depth
            $this->upgrade_setting('sub-cat-depth', 'sub-cat-depth');

            //sub-cat-article-display => display-sub-cat-articles
            $this->upgrade_setting('sub-cat-article-display', 'display-sub-cat-articles');

            //hide-empty-kb-categories    => hide-empty-cats
            $this->upgrade_setting('hide-empty-kb-categories', 'hide-empty-cats');

            //article-comments  => enable-article-comments
            $this->upgrade_setting('article-comments', 'enable-article-comments');

            //usefulness-display  => display-article-usefulness
            $this->upgrade_setting('usefulness-display', 'display-article-usefulness');

            //viewcount-display   =>display-article-views-count
            $this->upgrade_setting('viewcount-display', 'display-article-views-count');

            //comments-display    =>display-article-comment-count
            $this->upgrade_setting('comments-display', 'display-article-comment-count');

            //related-display =>display-related-articles
            $this->upgrade_setting('related-display', 'display-related-articles');

            //search-display  =>display-live-search
            $this->upgrade_setting('search-display', 'display-live-search');

            //search-focus-box    =>focus-live-search
            $this->upgrade_setting('search-focus-box', 'focus-live-search');

            //search-placeholder-text =>search-placeholder-text
            $this->upgrade_setting('search-placeholder-text', 'search-placeholder-text');

            //search-types    array   {'ht-kb'}    
            //NA           

            //search-excerpt  => display-search-result-excerpt
            $this->upgrade_setting('search-excerpt', 'display-search-result-excerpt');

            //ht-kb-slug  => kb-article-slug
            $this->upgrade_setting('ht-kb-slug', 'kb-article-slug');

            //ht-kb-cat-slug  =>  kb-category-slug
            $this->upgrade_setting('ht-kb-cat-slug', 'kb-category-slug');

            //ht-kb-tag-slug  =>  kb-tag-slug
            $this->upgrade_setting('ht-kb-tag-slug', 'kb-tag-slug');

            //ht-kb-custom-styles =>  custom-kb-styling-content
            $this->upgrade_setting('ht-kb-custom-styles', 'custom-kb-styling-content');

            //ht-kb-custom-styles-sitewide    => enable-kb-styling-sitewide
            $this->upgrade_setting('ht-kb-custom-styles-sitewide', 'enable-kb-styling-sitewide');

            //voting-display  => enable-article-feedback
            $this->upgrade_setting('voting-display', 'enable-article-feedback');

            //anon-voting =>enable-anon-article-feedback
            $this->upgrade_setting('anon-voting', 'enable-anon-article-feedback');

            //upvote-feedback => enable-upvote-article-feedback
            $this->upgrade_setting('upvote-feedback', 'enable-upvote-article-feedback');

            //downvote-feedback   => enable-downvote-article-feedback
            $this->upgrade_setting('downvote-feedback', 'enable-downvote-article-feedback');

            //exit-default-url    =>  kb-transfer-url
            $this->upgrade_setting('exit-default-url',  'kb-transfer-url');

            //exit-new-window => kb-transfer-new-window
            $this->upgrade_setting('exit-new-window', 'kb-transfer-new-window');

            //ht-kb-license   =>  kb-license-key
            $this->upgrade_setting('ht-kb-license',  'kb-license-key');

            //save new settings array back to the db
            update_option(HT_KB_NEW_SETTINGS_KEY, $this->new_settings_array);


            //flag upgrade complete
            update_option(HT_KB_TWO_SEVEN_UPGRADE_KEY, true);


            //backup old settings
            update_option(HT_KB_TWO_SEVEN_BACKUP_SETTINGS_KEY, $this->old_settings_array);

            //delete the old settings
            delete_option(HT_KB_OLD_SETTINGS_KEY);

            //finally, we need to flush the rewrite rules to ensure CPT works again
            update_option('ht_kb_flush_rewrite_required', true);


        }

        /**
        * Upgrade an individual option and populate the new settings array
        * @param (string) $old_name The old key name for the option
        * @param (string) $new_name The new key name for the option
        */
        function upgrade_setting($old_name, $new_name){
            //get old value
            $old_value = isset($this->old_settings_array[$old_name]) ? $this->old_settings_array[$old_name] : null;

           
            //if no old value, no further action required - will be set by default
            if(null===$old_value){
                return;
            } else {
                //set new value
                $this->new_settings_array[$new_name] = $old_value;
            }


            //$this->new_settings_array[$new_name] = $old_value;

        }
        

    }//end class

}

if (class_exists('Knowledge_Base_Settings_Upgrade')) {
    $ht_kb_settings_upgrade_init = new Knowledge_Base_Settings_Upgrade();
}