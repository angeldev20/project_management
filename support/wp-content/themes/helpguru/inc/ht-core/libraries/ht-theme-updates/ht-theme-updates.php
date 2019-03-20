<?php
/**
 * Update function base
 * Easy Digital Downloads.
 *
 * exerpts from EDD files
 */

// This is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'HT_EDD_SL_STORE_URL', 'https://herothemes.com/' ); // add your own unique prefix to prevent conflicts


if( !class_exists( 'HT_Theme_Updates' ) ){

	class HT_Theme_Updates {

		function __construct(){

			if ( !class_exists( 'EDD_SL_Theme_Updater' ) ) {
				// Load our custom theme updater
				include( dirname( __FILE__ ) . '/EDD_SL_Theme_Updater.php' );
			}

			add_action( 'admin_init', array( $this, 'edd_sl_theme_updater' ) );

			add_action( 'admin_menu', array( $this, 'edd_theme_license_menu' ) );

			add_action( 'admin_init', array( $this, 'edd_theme_register_option' ) );

			add_action( 'admin_init', array( $this, 'edd_theme_activate_license' ) );

			add_action( 'admin_init', array( $this, 'edd_theme_deactivate_license' ) );

			add_action( 'admin_notices', array( $this, 'ht_license_page_information_message' ) );

		}

		

		function edd_sl_theme_updater() {

			$theme_license = trim( get_option( 'hero_theme_license_key' ) );

			$theme_name = wp_get_theme()->get( 'Name' );
			$theme_version = wp_get_theme()->get( 'Version' );

			$edd_updater = new EDD_SL_Theme_Updater( array(
					'remote_api_url' 	=> HT_EDD_SL_STORE_URL, 	// Our store URL that is running EDD
					'version' 			=> $theme_version, 				// The current theme version we are running
					'license' 			=> $theme_license, 		// The license key (used get_option above to retrieve from DB)
					'item_name' 		=> $theme_name,	// The name of this theme
					'author'			=> 'HeroThemes'	// The author's name
				)
			);
		}
		
		/***********************************************
		* Add our menu item
		***********************************************/

		function edd_theme_license_menu() {
			if(current_theme_supports( 'ht-core', 'theme-updates' ))
				add_theme_page( 'Theme License', 'Theme License', 'manage_options', 'hero-themes-license', array( $this, 'edd_theme_license_page' ) );
		}
		
		/***********************************************
		* Sample settings page, substitute with yours
		***********************************************/

		function edd_theme_license_page() {
			$license 	= get_option( 'hero_theme_license_key' );
			$status 	= get_option( 'hero_theme_license_key_status' );
			?>
			<div class="wrap">
				<h2><?php _e('Theme License Options', 'helpguru'); ?></h2>
				<form method="post" action="options.php">

					<?php settings_fields('edd_theme_license'); ?>

					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e('License Key', 'helpguru'); ?>
								</th>
								<td>
									<input id="hero_theme_license_key" name="hero_theme_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
									<label class="description" for="hero_theme_license_key"><?php _e('Enter your license key', 'helpguru'); ?></label>
								</td>
							</tr>
							<?php if( false !== $license ) { ?>
								<tr valign="top">
									<th scope="row" valign="top">
										<?php _e('Activate License', 'helpguru'); ?>
									</th>
									<td>
										<?php if( $status !== false && $status == 'valid' ) { ?>
											<span style="color:green;"><?php _e('active', 'helpguru'); ?></span>
											<?php wp_nonce_field( 'edd_license_check_nonce', 'edd_license_check_nonce' ); ?>
											<input type="submit" class="button-secondary" name="edd_theme_license_deactivate" value="<?php _e('Deactivate License', 'helpguru'); ?>"/>
										<?php } else {
											wp_nonce_field( 'edd_license_check_nonce', 'edd_license_check_nonce' ); ?>
											<input type="submit" class="button-secondary" name="edd_theme_license_activate" value="<?php _e('Activate License', 'helpguru'); ?>"/>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php submit_button(); ?>

				</form>
			<?php
		}

		function edd_theme_register_option() {
			// creates our settings in the options table
			register_setting('edd_theme_license', 'hero_theme_license_key', array( $this, 'edd_theme_sanitize_license' ) );
		}
		


		/***********************************************
		* Gets rid of the local license status option
		* when adding a new one
		***********************************************/

		function edd_theme_sanitize_license( $new ) {
			$old = get_option( 'hero_theme_license_key' );
			if( $old && $old != $new ) {
				delete_option( 'hero_theme_license_key_status' ); // new license has been entered, so must reactivate
			}
			return $new;
		}

		/***********************************************
		* Illustrates how to activate a license key.
		***********************************************/

		function edd_theme_activate_license() {

			if( isset( $_POST['edd_theme_license_activate'] ) ) {
			 	if( ! check_admin_referer( 'edd_license_check_nonce', 'edd_license_check_nonce' ) )
					return; // get out if we didn't click the Activate button

				global $wp_version;

				$license = trim( get_option( 'hero_theme_license_key' ) );

				$theme_name = wp_get_theme()->get( 'Name' );
				$theme_version = wp_get_theme()->get( 'Version' );

				$api_params = array(
					'edd_action' => 'activate_license',
					'license' => $license,
					'item_name' => urlencode( $theme_name )
				);

				$response = wp_remote_get( add_query_arg( $api_params, HT_EDD_SL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

				if ( is_wp_error( $response ) )
					return false;

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				
				//update_option( 'hero_theme_license_key_sent', $api_params );
				//update_option( 'hero_theme_license_key_info', $license_data );
				
				// $license_data->license will be either "active" or "inactive"
				update_option( 'hero_theme_license_key_status', $license_data->license );

			}
		}
		

		/***********************************************
		* Illustrates how to deactivate a license key.
		* This will descrease the site count
		***********************************************/

		function edd_theme_deactivate_license() {

			// listen for our activate button to be clicked
			if( isset( $_POST['edd_theme_license_deactivate'] ) ) {

				// run a quick security check
			 	if( ! check_admin_referer( 'edd_license_check_nonce', 'edd_license_check_nonce' ) )
					return; // get out if we didn't click the Activate button

				// retrieve the license from the database
				$license = trim( get_option( 'hero_theme_license_key' ) );

				$theme_name = wp_get_theme()->get( 'Name' );
				$theme_version = wp_get_theme()->get( 'Version' );


				// data to send in our API request
				$api_params = array(
					'edd_action'=> 'deactivate_license',
					'license' 	=> $license,
					'item_name' => urlencode( $theme_name ) // the name of our product in EDD
				);

				// Call the custom API.
				$response = wp_remote_get( add_query_arg( $api_params, HT_EDD_SL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

				// make sure the response came back okay
				if ( is_wp_error( $response ) )
					return false;

				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// $license_data->license will be either "deactivated" or "failed"
				if( $license_data->license == 'deactivated' )
					delete_option( 'hero_theme_license_key_status' );

			}
		}

		function ht_license_page_information_message(){
			$screen = get_current_screen();

			if( empty( $screen ) )
				return;

			if( $screen->base != 'appearance_page_hero-themes-license' )
				return;

			?>

			<div class="update-nag">
		        <p><?php _e( 'From this page you will shortly be able enable automatic updates for your Hero Theme. While we are working on this feature, be sure to follow @HeroThemes on Twitter for news.', 'helpguru' ); ?></p>
			</div>
			<?php




		}


		function edd_sample_theme_check_license() {

			global $wp_version;

			$license = trim( get_option( 'hero_theme_license_key' ) );

			$theme_name = wp_get_theme()->get( 'Name' );
			$theme_version = wp_get_theme()->get( 'Version' );

			$api_params = array(
				'edd_action' => 'check_license',
				'license' => $license,
				'item_name' => urlencode( $theme_name )
			);

			$response = wp_remote_get( add_query_arg( $api_params, HT_EDD_SL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if( $license_data->license == 'valid' ) {
				echo 'valid'; exit;
				// this license is still valid
			} else {
				echo 'invalid'; exit;
				// this license is no longer valid
			}
		}

	}

	//run the updater
	$ht_theme_updates_init = new HT_Theme_Updates();

}

