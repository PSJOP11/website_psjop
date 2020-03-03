<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPPicasa Admin Functions
 * 
 * Loads main functions used by admin menu and front-end.
 * 
 * Copyright (c) 2011, cheshirewebsolutions.com, Ian Kennerley (info@cheshirewebsolutions.com).
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/ 


/**
 *
 *  Allow redirection, even if my theme starts to send output to the browser
 *
 */	

add_action( 'init', 'cws_do_output_buffer' );
function cws_do_output_buffer() {
	ob_start();
}

// Retrieve and display the URL parameter
function cws_gpp_output_album_id() {
	global $wp_query;
	
	if( isset( $wp_query->query_vars['album_id'] ) ) {
		return $wp_query->query_vars['album_id'];
	}
}

function custom_query_vars_filter($vars) {
	$vars[] = 'cws_page';
	$vars[] .= 'cws_album';
	$vars[] .= 'cws_album_title'; // pass album title to results pages, expander, grid, list
  $vars[] .= 'cws_debug'; // add for simple way to enable debugging via address bar
  return $vars;
}
///add_filter( 'query_vars', 'custom_query_vars_filter' );

function getWPPM() {

  if ( ! did_action('wp_loaded') ) {
    $msg = 'Please call getCurrentUser after wp_loaded is fired.';
    return new WP_Error( 'to_early_for_user', $msg );
  }

  static $wp_pm = NULL;

  if ( is_null( $wp_pm ) ) {
    $wp_pm = new WP_PM( new WP_PM_User( get_current_user_id() ) );
  }

  return $wp_pm;
}

function getCurrentUser() {

  $wppm = getWPPM();

  if ( is_wp_error( $wppm ) ) return $wppm;

  $user = $wppm->getUser();

  if ( $user instanceof WP_PM_User ) return $user;
}

add_action( 'wp_loaded', 'getCurrentUser' );



function displayUpgradeID() {

  $current_user = getCurrentUser();
  if ( $current_user instanceof WP_PM_User ) {
    // $plugin = new CWS_Google_Picasa_Pro( $plugin_name, $version, $isPro );
    $plugin = new CWS_Google_Picasa_Pro();
    $plugin_admin = new CWS_Google_Picasa_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );
    $plugin_admin->cws_gpp_admin_installed_notice($current_user);
    $plugin_admin->cws_gpp_ignore_upgrade($current_user);
  } else { //echo 'No one logged in'; 
  }
}

add_action( 'wp_loaded', 'displayUpgradeID', 30 );


/**
 * Google client class Instantiate
 * @since      3.0.10
 * @return object
 */
function cws_gpp_google_class() {
    static $client;
    if ( !$client ) {
        $plugin_path = CWS_GPP_PATH . '/api-libs';
        set_include_path( $plugin_path . PATH_SEPARATOR . get_include_path());

        require_once CWS_GPP_PATH . '/api-libs/Google/Client.php';
        $client = new Google_Client();
    }

    return $client;
}