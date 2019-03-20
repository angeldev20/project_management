<?php
/**
 * 2.6.5 New Settings Page
 */

//exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Knowledge_Base_Debug_Info')) {

    class Knowledge_Base_Debug_Info {

        private $ht_kb_settings;
        private $reserved_terms;
        private $existing_post_names;

        //Constructor
        function __construct(){  
            global $ht_knowledge_base_settings;


            //get option
            $this->ht_kb_settings = get_option( 'ht_knowledge_base_settings' );

            $ht_knowledge_base_settings = $this->ht_kb_settings;

            //add settings page
            add_action('admin_menu', array($this, 'add_ht_kb_debug_info_page'), 10 ); 

            //remove submenu page from menu    
            add_action('admin_menu', array($this, 'remove_ht_kb_debug_info_page_from_menu'), 15 );  

            //download debug info   
            add_action('admin_init', array($this, 'download_debug_info'), 5 );    

        }

        function add_ht_kb_debug_info_page(){

            //add the submenu page
            add_submenu_page(
                    'edit.php?post_type=ht_kb',
                    __('HelpGuru Knowledge Base Debug Info', 'ht-knowledge-base'), 
                    __('Debug', 'ht-knowledge-base'), 
                    'manage_options', 
                    'ht_knowledge_base_debug_info', 
                   array($this, 'ht_kb_debug_info_display')
                );

        }

        function remove_ht_kb_debug_info_page_from_menu(){
            remove_submenu_page( 'edit.php?post_type=ht_kb', 'ht_knowledge_base_debug_info' );
        }

        function ht_kb_debug_info_display(){
            
            $download_debug_info_url = filter_input(INPUT_SERVER, 'REQUEST_URI') . '&download_debug_info=ht_kb';
            $download_sec_debug_info_url = wp_nonce_url( $download_debug_info_url, 'download_ht_kb_debug', 'download_ht_kb_debug' );
            ?>
                <div class="hkb-admin-settings-page">
                    <h1><?php _e('HelpGuru Knowledge Base Debug Info', 'ht-knowledge-base'); ?></h1>
                    <form>
                        <textarea rows="20" cols="80" readonly="readonly"><?php echo $this->get_debug_info(); ?></textarea>
                    </form>
                    <a class="button" href="<?php echo $download_sec_debug_info_url; ?>" target="_blank"><?php _e('Download Debug Info', 'ht-knowledge-base'); ?></a>
                </div><!-- /hkb-admin-settings-page -->

            <?php
        }

        function download_debug_info(){
            if($_GET && is_array($_GET) && array_key_exists( 'download_debug_info', $_GET )){
                if($_GET['download_debug_info'] == 'ht_kb'){
                    //security check
                    check_admin_referer( 'download_ht_kb_debug', 'download_ht_kb_debug' );
                    header('Content-Type: application/download');
                    header('Content-Disposition: attachment; filename="ht-knowledbase-debug-info.txt"');
                    echo wp_strip_all_tags( $this->get_debug_info() );
                    die();
                }
            }

        }


        function get_debug_info(){
            //start a new output buffer
            ob_start();
            $site_url = site_url();
            $wp_version = get_bloginfo('version');
            $php_version = phpversion();
            $current_theme = wp_get_theme();
            $theme_name = $current_theme->get( 'Name' );
            $theme_version = $current_theme->get( 'Version' );
            $parent_theme_directory = $current_theme->get( 'Template' );
            if(!empty($parent_theme_directory)){
                $parent_theme_object = wp_get_theme($parent_theme_directory);
                $parent_theme_name = $parent_theme_object->get( 'Name' );
                $parent_theme_version = $parent_theme_object->get( 'Version' );
            }
            $mb_support =  ( function_exists( 'mb_strpos' ) ) ? 'enabled' : 'disabled';
            $permalink_option = get_option('permalink_structure') ;
            $ht_kb_cpt_slug = ht_kb_get_cpt_slug();
            $ht_kb_category_slug = ht_kb_get_cat_slug();
            $ht_kb_tag_slug = ht_kb_get_tag_slug();
            //send correct header info
            printf( __('Debug info for %s', 'ht-knowledge-base') , $site_url );
            echo PHP_EOL;
            echo PHP_EOL;
            echo __('WordPress Version', 'ht-knowledge-base') . ' : ' . $wp_version;
            echo PHP_EOL;
            echo __('PHP Version', 'ht-knowledge-base') . ' : ' . $php_version;
            echo PHP_EOL;
            echo __('HelpGuru Knowledge Base Plugin Version', 'ht-knowledge-base') . ' : ' . HT_KB_VERSION_NUMBER;
            echo PHP_EOL;
            echo __('Active Theme', 'ht-knowledge-base') . ' : ' . sprintf(__('%1$s - version %2$s', 'ht-knowledge-base'), $theme_name, $theme_version);
            echo PHP_EOL;
            if( isset( $parent_theme_object ) ):
                echo __('Parent Theme', 'ht-knowledge-base') . ' : ' . sprintf(__('%1$s - version %2$s', 'ht-knowledge-base'), $parent_theme_name, $parent_theme_version);
            else: 
                _e('No parent theme detected', 'ht-knowledge-base');
            endif;
            echo PHP_EOL;
            echo PHP_EOL;
            if( $permalink_option ):
                echo __('Permalinks Settings', 'ht-knowledge-base') . ' : ' . $permalink_option;
            else: 
                _e('Pretty Permalinks Disabled', 'ht-knowledge-base');
            endif;
            echo PHP_EOL;
            echo __('MB Support', 'ht-knowledge-base') . ' : ' . $mb_support;
            echo PHP_EOL;
            echo __('ht_kb CPT slug', 'ht-knowledge-base') . ' : ' . $ht_kb_cpt_slug;
            echo PHP_EOL;
            echo __('ht_kb_category taxonomy slug', 'ht-knowledge-base') . ' : ' . $ht_kb_category_slug;
            echo PHP_EOL;
            echo __('ht_kb_tag taxonomy slug', 'ht-knowledge-base') . ' : ' . $ht_kb_tag_slug;
            echo PHP_EOL;
            echo PHP_EOL;

            $plugins = get_plugins();
            $active_plugins = get_option( 'active_plugins', array() );
            echo __('Active Plugins', 'ht-knowledge-base') . ' : ';
            echo PHP_EOL;
            foreach ( $plugins as $plugin_path => $plugin ) {
                if( in_array( $plugin_path, $active_plugins ) ){
                    echo "\t" . $plugin['Name'] . ' : ' . $plugin['Version'];
                    echo PHP_EOL;
                }
                
            }
            echo PHP_EOL;
            echo PHP_EOL;


            $output = ob_get_clean();
            return strip_tags($output);
        }



   

    }//end class

}

if (class_exists('Knowledge_Base_Debug_Info')) {
    $ht_kb_settings_debug_info_init = new Knowledge_Base_Debug_Info();
}