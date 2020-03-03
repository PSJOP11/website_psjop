<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://cheshirewebsolutions.com/
 * @since             2.0.0
 * @package           CWS_Google_Picasa_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       Google Photos & Picasa Viewer
 * Plugin URI:        http://cheshirewebsolutions.com/
 * Description:       Display Google Photo Albums in your website.
 * Version:           3.2.3
 * Author:            Cheshire Web Solutions
 * Author URI:        http://cheshirewebsolutions.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cws_google_picasa_pro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cws-google-picasa-pro-activator.php
 */
function activate_cws_google_picasa_pro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cws-google-picasa-pro-activator.php';
	CWS_Google_Picasa_Pro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cws-google-picasa-pro-deactivator.php
 */
function deactivate_cws_google_picasa_pro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cws-google-picasa-pro-deactivator.php';
	CWS_Google_Picasa_Pro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cws_google_picasa_pro' );
register_deactivation_hook( __FILE__, 'deactivate_cws_google_picasa_pro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cws-google-picasa-pro.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_cws_google_picasa_pro() {
    
    $plugin = new CWS_Google_Picasa_Pro();
	$plugin->run();

}
run_cws_google_picasa_pro();