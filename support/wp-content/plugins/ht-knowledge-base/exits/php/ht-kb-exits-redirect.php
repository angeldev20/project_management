<?php

if( !class_exists('HT_KB_Exits_Redirect') ){

	class HT_KB_Exits_Redirect {

		//constructor
		function __construct() {
			//add template redirect
			add_action( 'template_redirect', array( $this, 'ht_kb_exit_redirect' ) );

		}


		/**
		* Redirect functionality for the knowledge base exit
		*/
		function ht_kb_exit_redirect() {

			if(isset( $_GET['hkb-redirect'] )){
				//security check
				$nonce = array_key_exists('nonce', $_GET) ? $_GET['nonce'] : '';
                if ( ! wp_verify_nonce( $nonce, 'hkb-redirect' ) ) {
                        die( 'Security check' ); 
                }

                //redirect handle
                $redirect = array_key_exists('redirect', $_GET) ? sanitize_text_field($_GET['redirect']) : '';
				$redirect = isset( $redirect ) ? esc_url(urldecode($redirect)) : '';

				$object_type = array_key_exists('otype', $_GET) ? sanitize_text_field($_GET['otype']) : '';
				$object_id = array_key_exists('oid', $_GET) ? intval( $_GET['oid'] ) : 0;
				$source = array_key_exists('redirect', $_GET) ? $_GET['source'] : '';
				

	            //populate data array
	            $data = array ( 	'object_type' => $object_type,
	            					'object_id' => $object_id,
	            					'source' => $source,
	            					'url' => $redirect	 
	            			);

	            ht_kb_exits_add_tracked_exit_details_to_db( $data );

				/**
				 * Filter redirect URL
				 * @param string  $redirect The redirect URL
				 */
				$redirect = apply_filters( 'ht_kb_exit_redirect_url', $redirect );

				/**
				 * Action hook before redirect
				 * @param string  $redirect The redirect URL
				 */
				do_action( 'ht_kb_exit_redirect', $redirect );

				if ( ! empty( $redirect ) ) {
					wp_redirect( esc_url_raw( $redirect ), 301);
					//ensure correct response
					exit;
				} else {
					wp_redirect( home_url(), 302 );
					//ensure correct response
					exit;
				}

			}

			
		}

	}

} //end class exists

//run the module
if( class_exists( 'HT_KB_Exits_Redirect' ) ){
    $hk_kb_exits_redirect_init = new HT_KB_Exits_Redirect();
}