<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    CWS_Google_Picasa_Pro
 * @subpackage CWS_Google_Picasa_Pro/includes
 * @author     Ian Kennerley <info@cheshirewebsolutions.com>
 */
class CWS_Google_Picasa_Pro_Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        delete_option( 'cws_gpp_token_expires' );
        delete_option( 'cws_gpp_code' );
        delete_option( 'cws_gpp_access_token' );

		$cws_gpp_options = array(
					'num_album_results' => 9,
					'num_image_results' => 4,
					'album_thumb_size' => 250,
					'thumb_size' => 200,
					'private_albums' => "All",
					'show_album_title' => 1,
					'show_album_details' => 0,
					'show_image_title' => 1,
					'imgmax' =>800,
					'results_page' => '',
					'hide_albums' => '',
					'theme' => '',
					'lightbox_image_size' => '',
					'enable_cache' => '',
					'row_height' => '250',
				);
        
		// Check to see if we already have some options
		$existing_options = get_option( 'cws_gpp_options' );

		// $result = array_merge($array1, $array2);

		if( is_array( $existing_options ) ) {
			$result = array_merge( 	$cws_gpp_options, $existing_options );
			update_option( 'cws_gpp_options', $result ); 
		} else {
			update_option( 'cws_gpp_options', $cws_gpp_options );
		}


		// delete dismiss upgrade notice
		$current_user = getCurrentUser();
		delete_user_meta( $current_user->ID, 'cws_gpp_ignore_upgrade' );
	}
}