<?php
/**
* Plugin updater
*/

//debug feature for testing update functionality
//set_site_transient( 'update_plugins', null );

//HeroThemes site url and product name
define( 'HT_STORE_URL', 'https://www.herothemes.com/?nocache' );
define( 'HT_KB_ITEM_NAME', 'HelpGuru Knowledge Base WordPress Plugin' ); 

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
    // load our custom updater
    include( dirname(dirname( __FILE__ )) . '/sl-updater/EDD_SL_Plugin_Updater.php' );
}

if (!class_exists('HT_Knowledge_Base_Updater')) {

    class HT_Knowledge_Base_Updater {

        //Constructor
        function __construct(){
            //init updater
            add_action( 'admin_init', array($this, 'ht_kb_updater' ), 0 );
            //admin notices
            add_filter( 'admin_notices', array( $this, 'ht_kb_license_nag' ) );
        }

        /**
        * Create the updater
        */
        function ht_kb_updater() {
            if( ( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates') ) ){
                return;
            }

            // retrieve our license key from the DB
            $license_key = trim( get_option( 'ht_kb_license_key' ) );
            // setup the updater
            $edd_updater = new EDD_SL_Plugin_Updater( HT_STORE_URL, HT_KB_MAIN_PLUGIN_FILE, array( 
                    'version'   => HT_KB_VERSION_NUMBER,               // current version number
                    'license'   => $license_key,        // license key (used get_option above to retrieve from DB)
                    'item_name' => HT_KB_ITEM_NAME,    // name of this plugin
                    'author'    => 'HeroThemes'  // author of this plugin
                )
            );
        }


    

        /**
        * License nag
        */
        function ht_kb_license_nag(){
            if( ( current_theme_supports('ht_kb_theme_managed_updates') || current_theme_supports('ht-kb-theme-managed-updates') ) ){
                //theme manages licenses updates
                return;
            }
            elseif('valid'==get_option('ht_kb_license_status')){
                //license valid
                return;
            } else {

                $screen = get_current_screen();

                //only display on options page
                if(is_admin() && is_object($screen) && ('ht_kb_page_ht_knowledge_base_settings_page' == $screen->base) ){  
                    ?>
                        <div class="error">
                            <p><?php _e( 'You have not entered a valid license key for automatic updates and support, be sure to do this in the <b>License and Updates</b> section now', 'ht-knowledge-base' ); ?></p>
                        </div>
                    <?php 
                }
            }
        }
        

        /**
        * Attempt to activate license
        * @param $sections (String) The license key to activate
        */
        public static function activate_license($key=''){
            if(empty($key)){
                return;
            }

            $license_key = $key;

            // data to send in our API request
            $api_params = array( 
                'edd_action'=> 'activate_license', 
                'license'   => $license_key, 
                'item_name' => urlencode( HT_KB_ITEM_NAME ) // the name of our product in EDD
            );
            
            // call custom EDD API
            $response = wp_remote_get( add_query_arg( $api_params, HT_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            // make sure the response came back okay
            if ( is_wp_error( $response ) ){
                $error = true;
            }                

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            
            // $license_data->license will be either "valid" or "invalid"
            update_option( 'ht_kb_license_status', $license_data->license );
            //update the license key in the database
            update_option( 'ht_kb_license_key', $license_key );
            //if valid, check if an update is required?

            //notify 
            do_action( 'ht_kb_activate_license', $license_data );

            return;

        }

        /**
        * Attempt to deactivate license
        * @param $key (String)  The license key to deactivate
        */
        public static function deactivate_license($key=''){
            if(empty($key)){
                return;
            }

            $license_key = $key;

            // data to send in our API request
            $api_params = array( 
                'edd_action'=> 'deactivate_license', 
                'license'   => $license_key, 
                'item_name' => urlencode( HT_KB_ITEM_NAME ) // the name of our product in EDD
            );

            
            // call custom EDD API
            $response = wp_remote_get( add_query_arg( $api_params, HT_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            // make sure the response came back okay
            if ( is_wp_error( $response ) )
                return false;

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            
            // $license_data->license will be either "deactivated" or "failed"
            if( $license_data->license == 'deactivated' ){
                delete_option( 'ht_kb_license_status' );
            } else {
                //remove license status, even on failed response
                delete_option( 'ht_kb_license_status' );
            }

            if(empty($license_key)){
                //remove license key from db if blank
                delete_option( 'ht_kb_license_key' );
            }

            //notify 
            do_action( 'ht_kb_deactivate_license', $license_data );

            return;    
        }

        /*
        * Check license validity
        * @param (String) $key  The license key to check
        */
        public static function check_license($key='') {
            global $wp_version;

            if(empty($key)){
                return;
            }

            $license_key = $key;
                
            $api_params = array( 
                'edd_action' => 'check_license', 
                'license' => $license_key, 
                'item_name' => urlencode( HT_KB_ITEM_NAME ),
                'url'       => home_url()
            );

            // call custom EDD API
            $response = wp_remote_get( add_query_arg( $api_params, HT_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

            if ( is_wp_error( $response ) )
                return false;

            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if( $license_data->license == 'valid' ) { 
                // this license is still valid, ensure key is correct in db
                update_option( 'ht_kb_license_status', $license_data->license );
                update_option( 'ht_kb_license_key', $license_key );
            } else {
                // this license is no longer valid, delete status
                delete_option( 'ht_kb_license_status' );
                
            }

            if(empty($license_key)){
                //remove license key from db if blank
                delete_option( 'ht_kb_license_key' );
            }

            //notify 
            do_action( 'ht_kb_check_license', $license_data );

            return;
        }

    }//end class 

}//end class exists


if (class_exists('HT_Knowledge_Base_Updater')) {
    $ht_knowledge_base_updater_init  = new HT_Knowledge_Base_Updater();
}


if ( ! class_exists( 'Redux_Validation_ht_validate_key' ) ) {
        class Redux_Validation_ht_validate_key {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since ReduxFramework 1.0.0
             */
            function __construct( $parent, $field, $value, $current ) {

                $this->parent       = $parent;
                $this->field        = $field;
                $this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : __( 'Invalid key', 'ht-knowledge-base', 'ht-knowledge-base' );
                $this->value        = $value;
                $this->current      = $current;

                $this->validate();
            } //function

            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since ReduxFramework 1.0.0
             */
            function validate() {
                if ($this->current != $this->value ){
                    if( isset($this->current) && $this->current!='' ){
                        //deactivate old license
                        HT_Knowledge_Base_Updater::deactivate_license($this->current);
                    }
                    if( isset($this->value) && $this->value!='' ){
                        //activate new license
                        HT_Knowledge_Base_Updater::activate_license($this->value);
                    }
                } else {
                    //else just check license
                    HT_Knowledge_Base_Updater::check_license($this->value);
                }

                if ( ($this->current != $this->value ) && ( ! isset( $this->value ) || empty( $this->value ) ) ) {
                    //can remove key if deactivated and empty
                    delete_option( 'ht_kb_license_key' );
                } else {
                    //other checks?
                }
            } //function
        } //class
    }