<?php
/**
* Dummy data creator for exits
*/

if (!class_exists('HT_KB_Exits_Dummy_Data_Creator')) {

    class HT_KB_Exits_Dummy_Data_Creator {

        private $exits_database;

        //constructor
        public function __construct() {
            //add test data listener
            add_action( 'admin_init' , array( $this, 'add_test_data' ));
            //enqueue scripts and styles
            //add_action( 'admin_head' , array( $this, 'enqueue_scripts_and_styles' ));

            include_once('ht-kb-exits-database.php');
            $this->exits_database = new HT_KB_Exits_Database();
        }



        /**
        * Testing / Debug function
        */
        function  add_test_data(){ 
            $action = (isset($_GET['add_test_exits']) && $_GET['add_test_exits']) ? $_GET['add_test_exits'] : '';
            if('articles'===$action){
                /*$nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';
                if ( ! wp_verify_nonce( $nonce, 'ht-kb-exits-add-dummy' ) ) {
                        die( 'Security check' ); 
                }
                */            

                //create sample views
                $this->create_sample_exits_on_all_kb_posts(5);                
            }
            if('categories'===$action){
                /*$nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';
                if ( ! wp_verify_nonce( $nonce, 'ht-kb-exits-add-dummy' ) ) {
                        die( 'Security check' ); 
                }
                */            

                //create sample views
                $this->create_sample_exits_on_all_kb_categories(5);                
            }
            if('archive'===$action){
                /*$nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';
                if ( ! wp_verify_nonce( $nonce, 'ht-kb-exits-add-dummy' ) ) {
                        die( 'Security check' ); 
                }
                */            

                //create sample views
                $this->create_sample_exits_on_kb_archive(5);                
            }
        }


        /**
        * Testing / Debug function
        */
        function create_sample_exits_on_all_kb_posts($number_of_exits_per_post=10){
            //get all ht_kb articles
            $args = array(
                      'post_type' => 'ht_kb',
                      'posts_per_page' => -1,
                     );
            $ht_kb_posts = get_posts( $args );

            //loop and upgrade
            foreach ( $ht_kb_posts as $post ) {
                //create sample exit
               $this->create_sample_exits_on_object($post->ID, 'ht_kb_article', $number_of_exits_per_post);
            }
        }

        /**
        * Testing / Debug function
        */
        function create_sample_exits_on_all_kb_categories($number_of_exits_per_cat=10){
            //get all ht_kb articles
            $ht_kb_categories = get_ht_kb_categories();

            //loop and upgrade
            foreach ( $ht_kb_categories as $key => $cat) {
                //create sample exit
               $this->create_sample_exits_on_object($cat->term_id, 'ht_kb_category', $number_of_exits_per_cat);
            }
        }

        /**
        * Testing / Debug function
        */
        function create_sample_exits_on_kb_archive($number_of_exits=10){
            $this->create_sample_exits_on_object(0, 'ht_kb_archive', $number_of_exits);
        }


        /**
        * Testing / Debug function
        */
        function create_sample_exits_on_object($object_id, $type, $number_of_exits){

            
            for ($i=0; $i < $number_of_exits; $i++) { 
                //polpulate data array
                $data = array(
                            'object_type' => $type,
                            'object_id' => $object_id,
                            'source' => $this->source_spinner(),
                            'url' => 'http://example.com/testsample'
                    );
                //add data
                $this->exits_database->add_tracked_exit_to_db($data);
            }
            
        
            
        }

        //return either widget, shortcode or end
        function source_spinner(){
            $value = rand ( 0, 2 );

            $source = 'shortcode';

            switch ($value) {
                case 0:
                     $source = 'shortcode';
                    break;
                case 1:
                     $source = 'widget';
                    break;
                case 2:
                     $source = 'end';
                    break;
                
                default:
                    # code...
                    break;
            }

            return $source;
        }





    }
} //end if class_exist

//run the module
if(class_exists('HT_KB_Exits_Dummy_Data_Creator')){
    $ht_kb_views_dummy_data_creator_init = new HT_KB_Exits_Dummy_Data_Creator();
}