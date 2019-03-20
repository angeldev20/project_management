<?php
/*
* Self contained analytics core
*/

//if you want to disable hkb data capture, remove the next line
define( 'HKB_ANALYTICS_DATA_CAPTURE', true );

if( !class_exists( 'HKB_Analytics_Core' ) ){
    class HKB_Analytics_Core {

        //constructor
        function __construct() {
            //init the saving fuctionality
            if ( defined( 'HKB_ANALYTICS_DATA_CAPTURE' ) &&  true === HKB_ANALYTICS_DATA_CAPTURE ){
                add_action( 'the_posts', array( $this, 'hkba_save_searches' ), 20 );
            }            

            //add activation action for table
            add_action( 'ht_kb_activate', array( $this, 'on_activate' ), 10, 1);
            //deactivation hook, currently unused
            //register_deactivation_hook( __FILE__, array( 'HKB_Analytics_Core', 'hkba_plugin_deactivation_hook' ) );
        }

        /**
        * Captures returned posts
        * @param (Array) $posts Array of post objects
        * @return (Array) Array of post objects, which will be unaltered
        */
        function hkba_save_searches($posts) {
            global $wp_query;

            //break if already performing a save search
            if ( defined( 'DOING_HKBA_SAVE_SEARCH' ) && true === DOING_HKBA_SAVE_SEARCH ) {
                return $posts;
            } else {
                define('DOING_HKBA_SAVE_SEARCH', true);
            }

            //check if the request is a search, and if so then save details.
            //hooked on a filter but does not change the posts

            if( is_search()
                && !is_paged() 
                && !is_admin() 
                && !empty($_GET['ht-kb-search']) )
                {
                    //get search terms
                    //search string is the raw query
                    $search_string = $wp_query->query_vars['s'];
                    if (get_magic_quotes_gpc()) {
                        $search_string = stripslashes($search_string);
                    }
                    //search terms is the words in the query
                    $search_terms = $search_string;
                    $search_terms = preg_replace('/[," ]+/', ' ', $search_terms);
                    $search_terms = trim($search_terms);
                    $hit_count = $wp_query->found_posts;
                    $details = '';

                    //sanitise as necessary
                    $search_string = esc_sql($search_string);
                    $search_terms = esc_sql($search_terms);
                    $details = esc_sql($details);
            }


           if(  is_search()
                && !empty($_GET['ht-kb-search']) //Knowledge Base search
                && !is_paged() //is not a second page search
                && !is_admin()//is not the dashboard
                && empty($_GET['ajax']) //not live search
                ){
                    //Non-Live search flow
                    //create search data object
                    $search_data = (object) array(
                        'search_string' => $search_terms,
                        'hit_count' => $hit_count,
                        'timestamp' => current_time( 'timestamp' ),
                        'details'   => ''
                    );

                    //save search to db
                    $this->ht_kb_save_search($search_data);
                    return $posts;
            } 

            return $posts;
        }

        /**
        * Saves search data in the database
        * @param (Array) $search_data Search data to be saved
        */
        private function ht_kb_save_search($search_data){
                global $wpdb;

                //user ip 
                $user_ip = hkb_get_user_ip();
                //user_id
                $user_id = hkb_get_current_user_id();

                $save_search_data = apply_filters('ht_kb_record_user_search', true, $user_id, $search_data);

                //return if set to not save search data 
                if(!$save_search_data){
                    return;
                }

                // Save search into the db
                $query = "INSERT INTO {$wpdb->prefix}hkb_analytics_search_atomic ( id ,  terms , datetime , hits, user_id, user_ip )
                VALUES (NULL, '$search_data->search_string', NOW(), $search_data->hit_count, $user_id, '$user_ip')";
                $run_query = $wpdb->query($query);
        }

        /**
        * Create database table
        */
        function hkba_create_table() {
            //add the table into the database
            global $wpdb;
            $table_name = $wpdb->prefix . "hkb_analytics_search_atomic";
            //check database version
            $db_version = get_option('hkb_analytics_search_atomic_db_version');
            if ($wpdb->get_var("SHOW tables LIKE '$table_name'") != $table_name || HT_KB_VERSION_NUMBER != $db_version ) {
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                $create_hkb_analytics_table_sql = "CREATE TABLE {$table_name} (
                                                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                                    terms VARCHAR(100) NOT NULL,
                                                    datetime DATETIME NOT NULL,
                                                    hits INT(11) NOT NULL,
                                                    user_id BIGINT(20) unsigned NOT NULL,
                                                    user_ip VARCHAR(15) NOT NULL,
                                                    PRIMARY KEY  (id)
                                                  )
                                                  CHARACTER SET utf8 COLLATE utf8_general_ci;
                                                  ";
                //dbDelta will automagically create any missing fields and update where appropriate
                dbDelta($create_hkb_analytics_table_sql);
                //set database version
                update_option('hkb_analytics_search_atomic_db_version', HT_KB_VERSION_NUMBER);
            }
        }

        /**
        * On activate function
        * @param (Bool) $network_wide True when network activate being performed
        */
        function on_activate( $network_wide = null ) {
            global $wpdb;
            //@todo - query multisite compatibility
            if ( is_multisite() && $network_wide ) {
                //store the current blog id
                $current_blog = $wpdb->blogid;
                //get all blogs in the network and activate plugin on each one
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blog_ids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    $this->hkba_create_table();
                    restore_current_blog();
                }
            } else {
                $this->hkba_create_table();
            }
        }

        static function hkba_plugin_deactivation_hook() {
            //do nothing
        }


    }
}

//run the module
if( class_exists( 'HKB_Analytics_Core' ) ){
    $hkb_analytics_core_init = new HKB_Analytics_Core();
}