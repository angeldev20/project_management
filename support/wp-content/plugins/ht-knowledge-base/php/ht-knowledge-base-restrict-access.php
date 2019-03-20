<?php
/**
* Self contained restrict access functionality
*/

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'HKB_Restrict_Access' ) ){
    class HKB_Restrict_Access {

        //constructor
        function __construct() {

            //define access levels
            add_filter( 'hkb_restrict_access_levels', array( $this,  'hkb_restrict_access_levels' ), 10, 2 );

            //posts where filter
            add_filter( 'posts_where',  array( $this,  'hkb_restrict_posts' ), 10 );

            //get terms filter
            add_filter( 'hkb_master_tax_terms',  array( $this,  'hkb_restrict_terms' ), 10 ); 

            //posts where taxonomy filter
            add_filter( 'posts_where',  array( $this,  'hkb_restrict_taxonomy' ), 10 ); 

            //restrict hkb post content (by term)
            add_filter( 'the_excerpt', array( $this,  'hkb_restrict_the_excerpt' ), 499 ); 
            add_filter( 'the_content', array( $this,  'hkb_restrict_the_content' ), 500 ); 

            //restrict hkb post title (by term)
            add_filter( 'the_title', array( $this,  'hkb_restrict_the_title' ), 500 ); 
   

        }

        /**
        * Restrict levels filter
        * @param (Array) $levels Existing access levels array
        * @param (String) $location The location where (for later use eg archive vs category)
        * @return $levels The various access levels (array as key=>label pairs)
        */
        function hkb_restrict_access_levels($levels=null, $location='archive'){
            //init levls array
            if(!is_array($levels)){
                $levels = array();
            }
            //add public
            $levels['public'] = __('Public', 'ht-knowledge-base');
            //add logged in
            $levels['loggedin'] = __('Logged In', 'ht-knowledge-base');

            //private not yet supported in beta
            //$levels['private'] = __('Private', 'ht-knowledge-base');

            return $levels;
        }


        /**
        * Simple posts restrict 
        * @return $where The filtered where clause
        */
        function hkb_restrict_posts($where){
            //if the current user does not have the read_private_post cap, remove any ht_kb items from post query
            $hkb_posts_access_restrict_level = function_exists('hkb_restrict_access') ? hkb_restrict_access() : '';
            //apply logic
            switch ($hkb_posts_access_restrict_level) {
                case 'private':
                    if( !current_user_can('read_private_posts') ){
                        //remove all post ht_kb post types 
                        $where .= ' AND post_type != "ht_kb" ';
                    }
                    break;
                case 'loggedin':
                    if( !is_user_logged_in() ){
                        //remove all post ht_kb post types 
                        $where .= ' AND post_type != "ht_kb" ';
                    }
                    break;
                
                default:
                    break;
            }

            return $where;
        }

        /**
        * Terms restrict 
        * This will remove terms resolved with the get_terms call, based on access restriction
        * Behaviour - In an archive view, term will not show if not public. In term archive, no posts will be displayed 
        * @return $terms The filtered terms
        */
        function hkb_restrict_terms($terms){
            //don't use in admin?
            if(is_admin()){
                return $terms;
            }

            if(is_array( $terms )){
                foreach ( $terms as $term => $term_object ) {
                    //check current terms is an article category
                    if(  ( isset($term_object->taxonomy) && 'ht_kb_category' == $term_object->taxonomy )  ){
                        
                        //FIRST restrict term based on the global hkb restrict access setting
                        $hkb_posts_access_restrict_level = function_exists('hkb_restrict_access') ? hkb_restrict_access() : '';                      

                         switch ($hkb_posts_access_restrict_level) {
                            case 'private':
                                if( !current_user_can('read_private_posts') ){
                                    //unset term
                                    unset($terms[$term]);
                                    //next
                                    continue;
                                }
                                break;
                            case 'loggedin':
                                if( !is_user_logged_in() ){
                                    //unset term
                                    unset($terms[$term]);
                                    //next
                                    continue;
                                }
                                break;
                            
                            default:
                                break;
                        }

                        

                        //NEXT restrict term based on the term meta
                        $category_restrict_access = hkb_get_category_restrict_access_level($term_object);
                        //apply logic
                        switch ($category_restrict_access) {
                            case 'private':
                                if( !current_user_can('read_private_posts') ){
                                    //unset term
                                    unset($terms[$term]);
                                }
                                break;
                            case 'loggedin':
                                if( !is_user_logged_in() ){
                                    //unset term
                                    unset($terms[$term]);
                                }
                                break;                            
                            default:
                                break;
                        }
                    }
                }
            }

            return $terms;
        }


        /**
        * Simple taxonomy is_tax filter for query where clause
        * @return $where The filtered where clause
        */
        function hkb_restrict_taxonomy($where){

            if( is_tax('ht_kb_category') ) {
                $term_id = get_queried_object_id();
                $hkb_category_access_restrict_level = hkb_get_category_restrict_access_level($term_id);
                switch ($hkb_category_access_restrict_level) {
                    case 'private':
                        if( !current_user_can('read_private_posts') ){
                            //remove all post ht_kb post types 
                            $where .= ' AND post_type != "ht_kb" ';
                        }
                        break;
                    case 'loggedin':
                        if( !is_user_logged_in() ){
                            //remove all post ht_kb post types 
                            $where .= ' AND post_type != "ht_kb" ';
                        }
                        break;
                    
                    default:
                        break;
                }
            }

            return $where;
        }


        /**
        * Simple restrict filter on the_content (the_excerpt) for when article is in restricted category 
        * @return $content The filtered content
        */
        function hkb_restrict_the_excerpt($content){
            global $post;

            if(isset($post->post_type) || 'ht_kb' != $post_type){
                return $content;
            }

            return apply_filters('hkb_restrict_the_excerpt', $this->hkb_restrict_the_content);
        }

        /**
        * Simple restrict filter on the_content for when article is in restricted category
        * @return $content The filtered content
        */
        function hkb_restrict_the_content($content){
            global $post;

            if( !isset($post->post_type) || 'ht_kb' != $post->post_type){
                return $content;
            }

            //return content if we're in the ht_kb archive and not in a widget
            if( is_post_type_archive( 'ht_kb' ) && ( is_main_query() && in_the_loop() ) ){
                return $content;
            }

            //don't use if search result
            if( ht_kb_is_ht_kb_search() ){
                return $content;
            }


            if( apply_filters('stop_hkb_restrict_the_content', false) ){
                return $content;
            }

            //get terms for the post
            $terms = wp_get_post_terms( $post->ID, 'ht_kb_category' );

            foreach ($terms as $index => $term) {
                // put the term ID into a variable
                $t_id = $term->term_id;

                // retrieve the existing value(s) for this meta field. This returns an array
                $term_meta = get_option( "taxonomy_$t_id" );

                //get category restriction
                $category_restrict_access = ( is_array($term_meta) && isset($term_meta['restrict_access']) ) ? $term_meta['restrict_access'] : '';

                switch ($category_restrict_access) {
                    case 'private':
                        if( !current_user_can('read_private_posts') ){
                            //state restricted
                            $content = __('This content is not available', 'ht-knowledge-base');
                        }
                        break;
                    case 'loggedin':
                        if( !is_user_logged_in() ){
                            //state restricted
                            $content = __('You must log in to view this article', 'ht-knowledge-base');
                        }
                        break;
                    
                    default:
                        break;
                }

            }

            return apply_filters('hkb_restrict_the_content', $content);
        }

        /**
        * Simple restrict filter on the_title for when article is in restricted category
        * @return $title The filtered title
        */
        function hkb_restrict_the_title($title){
            global $post;

            if( !isset($post->post_type) || 'ht_kb' != $post->post_type){
                return $title;
            }

            if( $post->post_title != $title ){
                return $title;
            }

            //return title if we're in the ht_kb archive and not in a widget
            if( is_post_type_archive( 'ht_kb' ) && ( is_main_query() && in_the_loop() ) ){
                return $title;
            }

            //don't use if search result
            if( ht_kb_is_ht_kb_search() ){
                return $title;
            }


            if( apply_filters('stop_hkb_restrict_the_title', false) ){
                return $title;
            }

            //get terms for the post
            $terms = wp_get_post_terms( $post->ID, 'ht_kb_category' );

            foreach ($terms as $index => $term) {
                // put the term ID into a variable
                $t_id = $term->term_id;

                // retrieve the existing value(s) for this meta field. This returns an array
                $term_meta = get_option( "taxonomy_$t_id" );

                //get category restriction
                $category_restrict_access = isset($term_meta['restrict_access']) ? $term_meta['restrict_access'] : '';

                switch ($category_restrict_access) {
                    case 'private':
                        if( !current_user_can('read_private_posts') ){
                            //state restricted
                            $title = __('Private article', 'ht-knowledge-base');
                        }
                        break;
                    case 'loggedin':
                        if( !is_user_logged_in() ){
                            //state restricted
                            $title = __('Private article', 'ht-knowledge-base');
                        }
                        break;
                    
                    default:
                        break;
                }

            }

            return apply_filters('hkb_restrict_the_title', $title);
        }


    } //end class

} //end class exists

//run the module
if( class_exists( 'HKB_Restrict_Access' ) ){
    $hkb_restrict_access_init = new HKB_Restrict_Access();
}