<?php
/**
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
function cws_gpp_shortcode_images_in_album( $atts ) {
	
    $cws_debug = '';
    $cws_page = '';
    
    if( isset( $_GET[ 'cws_debug' ] ) ){
        $cws_debug = $_GET[ 'cws_debug' ]; // $cws_debug = get_query_var('cws_debug');
    }

    //$plugin = new CWS_Google_Picasa_Pro($plugin_name, $version, $isPro);
    $plugin = new CWS_Google_Picasa_Pro();
    
    $plugin_admin = new CWS_Google_Picasa_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

    // If authenticated get list of albums
    if( $plugin_admin->isAuthenticated() == true  ) {
        
        $options = get_option( 'cws_gpp_options' );
        $album_thumb_size = $options['album_thumb_size'];

        // set some defaults...
        $num_pages = 0;

        $options['show_image_details'] = isset($options['show_image_details']) ? $options['show_image_details'] : "";
        $options['theme'] = isset($options['theme']) ? $options['theme'] : "";
        $options['id'] = isset($options['id']) ? $options['id'] : "";
        $options['results_page'] = isset($options['results_page']) ? $options['results_page'] : "";
        $options['hide_albums'] = isset($options['hide_albums']) ? $options['hide_albums'] : "";
        $options['row_height'] = isset($options['row_height']) ? $options['row_height'] : "251";
        $options['enable_download'] = isset($options['enable_download']) ? $options['enable_download'] : "";

        extract( shortcode_atts( array(
            'thumb_size'        => $options['thumb_size'], 
            'show_title'        => $options['show_image_title'],
            'show_details'      => $options['show_image_details'],
            'num_results'       => $options['num_image_results'],
            'theme'             => $options['theme'],   
            'id'                => $options['id'],
            'imgmax'            => $options['lightbox_image_size'],
            'results_page'      => $options['results_page'],
            'enable_cache'      => $options['enable_cache'],
            'enable_download'   => $options['enable_download'],
            'hide_albums'       => $options['hide_albums'],  
            'row_height'        => $options['row_height'], 
            'fx'                => NULL,
            'album_title'       => 1,      
        ), $atts ) );

        if( $hide_albums !== NULL ) {
            $hide_albums = array_map( 'trim', explode( ',', $hide_albums ) );
        }

        //var_dump($fx);

        // Create array from multiple ids set in shortcode, seperated by ','
        /*
        if( $id !== NULL ) {
            $ids = array_map( 'trim', explode( ',', $id ) );
        }
        */

        // echo '<pre>';
        // print_r($ids);
        // echo '</pre>';

        if ( $show_title === 'false' ) $show_title = false; // just to be sure...
        if ( $show_title === 'true' ) $show_title = true; // just to be sure...
        if ( $show_title === '0' ) $show_title = false; // just to be sure...
        if ( $show_title === '1' ) $show_title = true; // just to be sure...
        $show_title = ( bool ) $show_title;          

        if ( $show_details === 'false' ) $show_details = false; // just to be sure...
        if ( $show_details === 'true' ) $show_details = true; // just to be sure...
        if ( $show_details === '0' ) $show_details = false; // just to be sure...
        if ( $show_details === '1' ) $show_details = true; // just to be sure...
        $show_details = ( bool ) $show_details;       

        // Grab album id
		// $cws_album = get_query_var( 'cws_album' );
        
        // if no id set from [cws_gpp_images_by_albumid] then use $cws_album
        if( $id === NULL || $id == "" ){

            if( isset( $_GET['cws_album'] ) ){
                $cws_album = $_GET[ 'cws_album' ]; // $cws_album = get_query_var( 'cws_album' );
            }
            
        } else {
            $cws_album = $id;
        }

        // Grab page from url
        if( isset( $_GET['cws_page'] ) ){
            $cws_page = $_GET[ 'cws_page' ]; // $cws_page = get_query_var( 'cws_page' );
        }
        
        // Grab the access token
        $AccessToken = get_option( 'cws_gpp_access_token' );

        // no pagintaion required if results are carousel
        if( $theme == "carousel" ) { $num_results = 0; }

// var_dump($cws_album);
/*
echo "AccessToken: $AccessToken<br>";
echo "thumb_size: $thumb_size<br>";
echo "show_title: $show_title<br>";
echo "cws_page: $cws_page<br>";
echo "num_results: $num_results<br>";
echo "cws_album: $cws_album<br>";
echo "imgmax: $imgmax<br>";
*/

// test start foreach here...
//foreach($ids as $cws_album) {

$strOutput = "";
$strOutput .=  $strOutput;

    //echo "Call Picasa API for album ID: $cws_album<br>";
    //print_r($ids);
    // Get images in a specific album

        #----------------------------------------------------------------------------
        # Cache the album feed if it does not exist 
        #----------------------------------------------------------------------------
        // Set cache location
        $myAlbumCache =  plugin_dir_path( dirname( __FILE__ ) ) . 'cache'; 
        $cacheImage = new JG_Cache(  $myAlbumCache ); // Make sure it exists and is writeable

        // set some defaults...
        $total_num_albums = isset($total_num_albums) ? $total_num_albums : "0";
        $num_photos  = isset($num_photos) ? $num_photos : "0";

        // Make cache name
        $cacheName = 'imagesList-' . $total_num_albums . '-' . $num_results . '-' . $cws_page . '-' . $cws_album;
        $response = $cacheImage->get($cacheName);

        if( $cws_debug == "1" ){ 
            echo "<strong>From cache, response.</strong><br>";
            echo '<pre>';
            print_r($response);
            echo '</pre>';
        }

        if ( $enable_cache === true ) {
                //error_log("Calling feed to cache...");                    

            // If no cache file found get feed and cache it...
            if ( $response === FALSE ) {
                //error_log( '***** CALLING IMAGE FEED AND CACHING RESULT *******' );
                // $response = $plugin_admin->getAlbumImages( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results, $cws_album, $imgmax );
                $response = $plugin_admin->getAlbumImages( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results, $cws_album, $imgmax, $theme );

                if( $cws_debug == "1" ){ 
                    echo "<strong>Setting cache, response.</strong><br>";
                    echo '<pre>';
                    print_r($response);
                    echo '</pre>';
                }

                $cacheImage->set($cacheName, $response);
            } else {
                //error_log( '***** IMAGE FEED IS ALREADY CACHED *******' );
            }
        } else {

                // try and empty cache...
                $files = glob( $myAlbumCache . '/*' ); // get all file names
                
                if ( !empty( $files ) ) {
                    foreach( $files as $file ){ // iterate files
                      if( is_file( $file ) )
                        unlink( $file ); // delete file
                    }
                }

                // $response = $plugin_admin->getAlbumImages( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results, $cws_album, $imgmax );
                $response = $plugin_admin->getAlbumImages( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results, $cws_album, $imgmax, $theme );  
                //error_log("Calling feed normally...");                       
        }
        //
        //error_log("Calling Feed for album id: $cws_album");  
        #----------------------------------------------------------------------------
        # Convert the XML response into SimpleXML Object
        #----------------------------------------------------------------------------
        if (!function_exists('produce_XML_object_tree')) {

            function produce_XML_object_tree( $response ) {
                libxml_use_internal_errors( true );
                try {
                    // Create simplexml object from feed
                    $xml = new SimpleXMLElement( $response );
                }  catch ( Exception $e ) {
                    // Something went wrong.
                    $error_message = 'SimpleXMLElement threw an exception.';
                    foreach( libxml_get_errors() as $error_line ) {
                        $error_message .= "\t" . $error_line->message;
                    }
                    //trigger_error( $error_message );
                    return false;
                }
                return $xml;
            } // end function produce

        }

        // Create SimpleXML Object
        $xml = produce_XML_object_tree ( $response );

        if( $cws_debug == "1" ){ 
            echo "<strong>xml tree.</strong><br>";
            echo '<pre>';
            print_r($xml);
            echo '</pre>';
        }

        // Decide which layout to use to display the albums
        switch( $theme ) {

            #----------------------------------------------------------------------------
            # Justified Image Grid Layout *** PRO ONLY ***
            #----------------------------------------------------------------------------
            case "projig":
                // enque styles
                if( $plugin->get_isPro() == 1 ){

                    $dataToBePassed = array();

                    // Start include PhotoSwipe files
                    wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/photoswipe.css' );
                    wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/default-skin/default-skin.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe-ui-default.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/init_ps.js', array( 'jquery' ), false, false );

// added this line to support jig and grid on the same page...
                    wp_enqueue_script( 'cws_gpp_init_ps_for_grid', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/grid/js/init_ps.js', array( 'jquery' ), false, false );


                    // end inclucde PhotoSwipe files

                    wp_enqueue_style( 'projig-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/projig/css/justifiedGallery.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_jig', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/projig/js/jquery.justifiedGallery.js', array( 'jquery' ), false, false ); 
                    
                    // Initialize any scripts?
                    wp_register_script( 'cws_gpp_init_jig', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/projig/js/init_jig.js' );
                    
                    $dataToBePassed = array(
                        'imgmax'    => "$imgmax",
                        'rowheight' => "$row_height",
                    );
                    wp_localize_script( 'cws_gpp_init_jig', 'php_vars', $dataToBePassed );
                    wp_enqueue_script( 'cws_gpp_init_jig', array( 'cws_gpp_jig' ), false , false );

                    ////wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propbs/js/lightbox.js', array( 'jquery' ), false, true );


                    include 'partials_pro/pro_jig.php';
                    include 'partials_pro/photoswipe.html'; 
                }
                break;


            #----------------------------------------------------------------------------
            # Photbooth Strips Layout *** PRO ONLY ***
            #----------------------------------------------------------------------------
            // This view has been deprecated 28/11/16
            // Left code in to support users still using it...
            case "propbs":
                // enque styles
                if( $plugin->get_isPro() == 1 ){
                    wp_enqueue_style( 'propbs-style3', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propbs/css/style.css' );
                    wp_enqueue_style( 'propbs-style2', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propbs/css/demo.css' );
                    wp_enqueue_style( 'propbs-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propbs/css/lightbox.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propbs/js/lightbox.js', array( 'jquery' ), false, true ); 
                    //wp_enqueue_script( 'cws_gpp_modernizr', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propbs/js/modernizr.custom.52731.js', array(  ), false, false ); 
                  
                    // Initialize any scripts?
                    include 'partials_pro/pro_pbs.php';
                }
                break;

            #----------------------------------------------------------------------------
            # Polaroid Stack Grid Layout *** PRO ONLY ***
            #----------------------------------------------------------------------------
            // This view has been deprecated 28/11/16
            // Left code in to support users still using it...                
            case "propsg":
                // enque styles
                if( $plugin->get_isPro() == 1 ){

                    wp_enqueue_style( 'propsg-style3', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propsg/css/square-loader.min.css' );
                    wp_enqueue_style( 'propsg-style2', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propsg/css/demo.css' );
                    wp_enqueue_style( 'propsg-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propsg/css/component.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_classie', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propsg/js/classie.js', array(  ), false, true ); 
                    wp_enqueue_script( 'cws_gpp_dynamics', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propsg/js/dynamics.min.js', array(  ), false, true ); 
                    wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propsg/js/imagesloaded.pkgd.min.js', array(  ), false, true ); 
                    wp_enqueue_script( 'cws_gpp_main', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propsg/js/main.js', array(  ), false, true ); 

                  
                    // Initialize any scripts?

                    include 'partials_pro/pro_psg.php';
                }
                break;

            #----------------------------------------------------------------------------
            # Grid Layout
            #----------------------------------------------------------------------------
            case "grid":

                include 'partials/results_grid.php';
                
                // Include Masonry
                wp_enqueue_script( 'cws_gpp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                    
                // Initialize Masonry
                wp_enqueue_script( 'cws_gpp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_gpp_masonry' ), false , true );

                // If Pro use Photoswipe for improved responsiveness and better ux
                if( $plugin->get_isPro() == 1 ){
                    //include 'partials_pro/photoswipe.html'; 

                    if( $plugin->get_isPro() == 1 ){
                        // Enque Pro FX CSS
                        wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );
                    }
                    
                    // Start include PhotoSwipe files
                    wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/photoswipe.css' );
                    wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/default-skin/default-skin.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe-ui-default.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/grid/js/init_ps.js', array( 'jquery' ), false, false );
                    // end inclucde PhotoSwipe files
                } else {
       /// wp_enqueue_style( 'lightbox', plugin_dir_url( __FILE__ ) . '../public/css/lightbox/lightbox.css', array(), $this->version, 'all' );
                    
                    wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/lightbox.js', array( 'jquery' ), false, true );                                         
                    // Initialize Lightbox
                    wp_enqueue_script( 'cws_gpp_init_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/init_lightbox.js', array( 'cws_gpp_lightbox' ), false , true );
                }
                break;


            #----------------------------------------------------------------------------
            # List Layout
            #----------------------------------------------------------------------------   
            case "list":

                include 'partials/results_list.php';

                // Enque Pro FX CSS
                wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );

                // If Pro use Photoswipe for improved responsiveness and better ux
                if( $plugin->get_isPro() == 1 ){

                    //include 'partials_pro/photoswipe.html'; 

                    // Start include PhotoSwipe files
                    wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/photoswipe.css' );
                    wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/default-skin/default-skin.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe-ui-default.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/list/js/init_ps.js', array( 'jquery' ), false, false );
                    // end inclucde PhotoSwipe files

                } else {
                    wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/lightbox.js', array( 'jquery' ), false, true );                 
                        
                    // Initialize Lightbox
                    wp_enqueue_script( 'cws_gpp_init_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/init_lightbox.js', array( 'cws_gpp_lightbox' ), false , true );                   
                }
              
                break;


            #----------------------------------------------------------------------------
            # Carousel Layout
            #----------------------------------------------------------------------------   
            case "carousel":

                include 'partials/results_carousel.php';

                // Enque Pro FX CSS
//                wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );

                extract(shortcode_atts(array(
                    "arrows"    => true,
                    "infinite"  => true,
                    "autoplay"  => true,
                    "autoplay_interval"  => 1000,
                    "dots"      => false,
                    "slidestoshow"  => 4,
                    "slidestoscroll"  => 1,
                    "speed"  => 2000,
                ), $atts));
/*
echo "slidestoshow: $slidestoshow<br>";
echo "arrows: $arrows<br>";
echo "autoplay_interval: $autoplay_interval<br>";
echo "slidestoscroll: $slidestoscroll<br>";
*/

                if( $arrows ) { $arrows = $arrows; } else { $arrows = false; }
                if( $infinite ) { $infinite = $infinite; } else { $infinite = false; }
                if( $autoplay ) { $autoplay = $autoplay; } else { $autoplay = false; }
                if( $autoplay_interval ) { $autoplay_interval = $autoplay_interval; } else { $autoplay_interval = 2000; }
                if( $dots ) { $dots = $dots; } else { $dots = false; }
                if( $slidestoshow ) { $slidestoshow = $slidestoshow; } else { $slidestoshow = 3; }
                if( $slidestoscroll ) { $slidestoscroll = $slidestoscroll; } else { $slidestoscroll = false; }
                if( $speed ) { $speed = $speed; } else { $speed = 3000; }

                $dataToBePassed = array();

                $dataToBePassed = array (
                    // Wrap values in an inner array to protect boolean and integers
                    'inner' => array(
                        'arrows' => (bool)$arrows, 
                        'infinite' => (bool)$infinite, 
                        'autoplay' => (bool)$autoplay, 
                        'autoplay_interval' => (int)$autoplay_interval,      
                        'dots' => (bool)$dots,
                        'slidestoshow' => (int)$slidestoshow,
                        'slidestoscroll' => (int)$slidestoscroll,
                        'speed' => (int)$speed,
                    ),
                );                


                // If Pro use Photoswipe for improved responsiveness and better ux
                if( $plugin->get_isPro() == 1 ){

                    //include 'partials_pro/photoswipe.html'; 

                    // Start include PhotoSwipe files
                    wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/photoswipe.css' );
                    wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/default-skin/default-skin.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe-ui-default.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . 'partials/carousel/js/init_ps.js', array( 'jquery' ), false, false );
                    // end inclucde PhotoSwipe files

                    // Include Slick
                    wp_enqueue_script( 'cws_gpp_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/slick.min.js', array( 'jquery' ), false, true ); 
                        
                    // Initialize Slick
                    // wp_enqueue_script( 'cws_gpp_init_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/init_slick_pro.js', array( 'cws_gpp_slick' ), false , true );
                    wp_register_script( 'cws_gpp_init_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/init_slick_pro.js' );
                    wp_localize_script( 'cws_gpp_init_slick', 'php_vars', $dataToBePassed );
                    wp_enqueue_script( 'cws_gpp_init_slick', array( 'cws_gpp_slick_lb' ), false , true ); // Initialize Slick WITH options!

                // Enque Pro FX CSS
                wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );
                    

                } else {

                    wp_enqueue_script( 'cws_gpp_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/slick.min.js', array( 'jquery' ), false, false );  // Include Slick
                    wp_enqueue_script( 'cws_gpp_slick_lb', plugin_dir_url( __FILE__ )  . '../public/js/slick-lightbox/slick-lightbox.js', array( 'cws_gpp_slick' ), false, true );  // Include Slick Lightbox
                    //wp_enqueue_script( 'cws_gpp_init_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/init_slick.js', array( 'cws_gpp_slick_lb' ), false , true ); // Initialize Slick

                    // Initialize any scripts?
                    wp_register_script( 'cws_gpp_init_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/init_slick.js' );
                    wp_localize_script( 'cws_gpp_init_slick', 'php_vars', $dataToBePassed );
                    wp_enqueue_script( 'cws_gpp_init_slick', array( 'cws_gpp_slick_lb' ), false , true ); // Initialize Slick WITH options!

                }
                 
                break;

            #----------------------------------------------------------------------------
            # Expander Layout - is this being used by people? Needs improving!
            #----------------------------------------------------------------------------  
            case "expander";

                // Add Javascript
                // Include Modernizr
                wp_enqueue_script( 'cws_gpp_modernizr', plugin_dir_url( __FILE__ )  . '../public/js/modernizr.custom.js', array(), false, false );  

                // Include Grid
                wp_enqueue_script( 'cws_gpp_grid', plugin_dir_url( __FILE__ )  . '../public/js/grid.js', array( 'cws_gpp_modernizr' ), false , true );
                
                // Initialize Grid
                wp_enqueue_script( 'cws_gpp_init_grid', plugin_dir_url( __FILE__ )  . '../public/js/init_grid.js', array( 'cws_gpp_grid' ), false , true );
            
                include 'partials/results_expander.php';
                break;

            #----------------------------------------------------------------------------
            # Default Layout - Grid
            #----------------------------------------------------------------------------   
            default:

                include 'partials/results_grid.php';
                
                // Include Masonry
                wp_enqueue_script( 'cws_gpp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                    
                // Initialize Masonry
                wp_enqueue_script( 'cws_gpp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_gpp_masonry' ), false , true );

                // If Pro use Photoswipe for improved responsiveness and better ux
                if( $plugin->get_isPro() == 1 ){
                    include 'partials_pro/photoswipe.html'; 

                // Enque Pro FX CSS
                wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );

                    // Start include PhotoSwipe files
                    wp_enqueue_style( 'props-style1', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/photoswipe.css' );
                    wp_enqueue_style( 'props-style2', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/css/default-skin/default-skin.css' );

                    // Include Javascript
                    wp_enqueue_script( 'cws_gpp_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe-ui-default.min.js', array(), false, false ); 
                    wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/grid/js/init_ps.js', array( 'jquery' ), false, false );
                    // end inclucde PhotoSwipe files
                } else {                    
                    wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/lightbox.js', array( 'jquery' ), false, true );                                         
                    // Initialize Lightbox
                    wp_enqueue_script( 'cws_gpp_init_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/init_lightbox.js', array( 'cws_gpp_lightbox' ), false , true );
                }

        }
        //echo "before pagintaion<br>";
        #----------------------------------------------------------------------------
        # Show output for pagination
        #----------------------------------------------------------------------------
        /*
        echo "num_photos: $num_photos<br>";
        echo "num_results: $num_results<br>";
        echo "num_pages: $num_pages<br>";
        */
        
        if( $num_results > 0 ){
            $num_pages  = ceil( $num_photos / $num_results );
        }

        // If someone has done some url hacking reset page to something sensible
        if ( isset($cws_page) && $cws_page > $num_pages ) { $cws_page = $num_pages; }
        // var_dump($cws_album);
        // total results, num to show per page, current page, album id optional
        $strOutput .= $plugin_admin->get_pagination( $num_photos, $num_results, $cws_page, $cws_album );

        return $strOutput;


//} // test end foreach


    }   // end if authenticated check 
    
}
