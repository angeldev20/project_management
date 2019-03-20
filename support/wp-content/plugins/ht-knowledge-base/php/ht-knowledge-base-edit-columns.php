<?php
/*
* Self contained edit columns functionality
*/

if( !class_exists( 'HKB_Edit_Columns' ) ){
    class HKB_Edit_Columns {

        //constructor
        function __construct() {
            
            //display custom meta in the articles listing in the admin
            add_action( 'manage_ht_kb_posts_custom_column' , array( $this,  'data_kb_custom_data_column' ), 10, 2 );

            //manage columns
            add_filter( 'manage_ht_kb_posts_columns',  array( $this,  'add_kb_custom_data_column' ) );
            //sortable columns
            add_filter( 'manage_edit-ht_kb_sortable_columns', array( $this, 'register_kb_custom_data_sortable_columns' ) );
            //column sortable filter
            add_filter( 'pre_get_posts', array( $this, 'kb_custom_data_orderby' ), 10000 );       

        }

        /* BACKEND FUNCTIONS */

        /**
         * Add kb post view and attachment count data
         * @param (String) $column Column slug
         * @param (String) $post_id Post ID
         */
        function data_kb_custom_data_column( $column, $post_id ) {
            switch ( $column ) {
                case 'article_views':
                    echo get_post_meta( $post_id , HT_KB_POST_VIEW_COUNT_KEY , true );
                    break;
                case 'attachment_count':
                    $attachments = hkb_get_attachments($post_id);
                    if(!empty($attachments)){
                        foreach ($attachments as $key => $attachment_url) {
                            $attachment_id =  url_to_postid( $attachment_url );
                            $attachment_name = basename($attachment_url);
                            $attachment_edit_link = get_edit_post_link( $attachment_id );
                            echo $attachment_name;
                            //printf('<a href="%s" title="%s">%s</a>', $attachment_edit_link, __('Edit attachment', 'ht-knowledge-base'), $attachment_name  );
                            echo '<br/>';
                        }
                    }
                    break;
            }
        }

        /**
         * Add kb post view count column
         * @param (Array) $columns Current columns on the list post
         * @return (Array) Filtered columns on the list post
         */
        function add_kb_custom_data_column( $columns ) {
            $column_name = __('Article Views', 'ht-knowledge-base');
            $column_meta = array( 'article_views' => $column_name );
            $columns = array_slice( $columns, 0, 5, true ) + $column_meta + array_slice( $columns, 5, NULL, true );
            $column_name = __('Attachment(s)', 'ht-knowledge-base');
            $column_meta = array( 'attachment_count' => $column_name );
            $columns = array_slice( $columns, 0, 5, true ) + $column_meta + array_slice( $columns, 5, NULL, true );
            return $columns;
        }

        /**
         * Register the column as sortable
         * @param (Array) $columns Current columns on the list post
         * @return (Array) Filtered columns on the list post
         */
        function register_kb_custom_data_sortable_columns( $columns ) {
            $columns['article_views'] = 'article_views' ;
            $columns['attachment_count'] = 'attachment_count' ;
            return $columns;
        }


        /**
         * Allow order by HT_KB_POST_VIEW_COUNT_KEY      
         * @param (Array) $query Unfiltered query
         * @return (Array) Filtered query
         */
        function kb_custom_data_orderby( $query ) {
            if( ! is_admin() )
                return;
         
            $orderby = $query->get( 'orderby' );

         
            if( 'article_views' == $orderby ) {
                $query->set('meta_key',HT_KB_POST_VIEW_COUNT_KEY);
                $query->set('orderby','meta_value_num');
            }

            if( 'attachment_count' == $orderby ) {
                $query->set('meta_key','_ht_knowledge_base_file_advanced');
                $query->set('orderby','meta_value');
                /*@todo - also include articles not containing attachments in this sort
                $query->set('meta_query',
                                array( array(
                                        'key' => '_ht_knowledge_base_file_advanced', 
                                        'value' => '', 
                                        'compare' => '!='
                                        )
                                    )
                            );
                */
            }
        }

    }
}

//run the module
if( class_exists( 'HKB_Edit_Columns' ) ){
    $hkb_edit_columns_init = new HKB_Edit_Columns();
}