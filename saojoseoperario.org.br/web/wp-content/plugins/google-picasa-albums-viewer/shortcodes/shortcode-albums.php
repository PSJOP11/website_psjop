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
function cws_gpp_shortcode_albums( $atts ) {

    $cws_debug = '';
    $cws_page = '';

    if ( isset( $_GET['cws_debug'] ) ) {
        $cws_debug = $_GET[ 'cws_debug' ]; // $cws_debug = get_query_var('cws_debug');    
    }

    $strOutput = "";

    //$plugin = new CWS_Google_Picasa_Pro( $plugin_name, $version, $isPro );
    $plugin = new CWS_Google_Picasa_Pro();
    $plugin_admin = new CWS_Google_Picasa_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

    // If authenticated get list of albums
    if( $plugin_admin->isAuthenticated() == true  ) {

        // Grab options stored in db
        $options = get_option( 'cws_gpp_options' );

        // set some defaults...
        $options['results_page'] = isset($options['results_page']) ? $options['results_page'] : "";
        $options['hide_albums'] = isset($options['hide_albums']) ? $options['hide_albums'] : "";
        $options['theme'] = isset($options['theme']) ? $options['theme'] : "";

        // Extract the options from db and overwrite with any set in the shortcode
        // 'visibility'         => $options['private_albums'],
        extract( shortcode_atts( array(
            'thumb_size'   => $options['thumb_size'],
            'album_thumb_size'   => $options['album_thumb_size'], 
            'show_title'         => $options['show_album_title'],
            'show_details' => $options['show_album_details'],
            'num_results'  => $options['num_album_results'],
            'visibility'         => $options['private_albums'],           
            'results_page'       => $options['results_page'],
            'hide_albums'        => $options['hide_albums'],
            'theme'              => $options['theme'],
            'imgmax'            => $options['lightbox_image_size'],
            'enable_cache'         => $options['enable_cache'], 
            'fx'                => NULL,
        ), $atts ) );

        // Map albums names to hide to array and trim white space
        //var_dump($hide_albums);
        
        if( $hide_albums !== NULL ) {
            $hide_albums = array_map( 'trim', explode( ',', $hide_albums ) );
        }
        /*
        if( isset( $hide_albums ) ) {
            $hide_albums[] = 'Auto Backup';
        }
        else {
            $hide_albums = 'Auto Backup';
        }
        */
        // TODO: cast other vars to int if required
        $thumb_size = (int) $thumb_size;

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
        
        // Grab page from url
        if ( isset( $_GET['cws_page'] ) ) {
            
            $cws_page = $_GET[ 'cws_page' ]; // $cws_page = get_query_var('cws_page');
        }

        // Grab the access token
        $AccessToken = get_option( 'cws_gpp_access_token' );

        // To remove pagination from carousel page
        ///if( $theme == 'carousel') { $num_results = 0; }

        // if page not set or is 1 could do, $num_results +1 to get around removing the auto backup result later...
        // So hacky I know... would effect all responses with album name before Auto Backup in alphabet
        // dont apply when on carousel page as it breaks first page!
        /* if( $theme != 'carousel' ) {
            if( ($cws_page == 1) || ($cws_page == '') ) {
                $num_results +=1;
            }
        } */

        // echo "num_results: $num_results<br>";

        #----------------------------------------------------------------------------
        # Cache the album feed if it does not exist 
        #----------------------------------------------------------------------------
        // set cache location
        $myAlbumCache =  plugin_dir_path( dirname( __FILE__ ) ) . 'cache'; 

        $cache = new JG_Cache(  $myAlbumCache ); //Make sure it exists and is writeable

        $total_num_albums = isset($total_num_albums) ? $total_num_albums : "0";
        //$visibility = isset($visibility) ? $visibility : "allxxx";

        // make cache name
        $cacheName = 'albumList-' . $total_num_albums . '-' . $num_results . '-' . $cws_page;
        $response = $cache->get($cacheName);

        if ( isset( $_GET['cws_debug'] ) ) {

            if( $cws_debug == "1" ){ 
                echo "<strong>From cache, response.</strong><br>";
                echo '<pre>';
                print_r($response);
                echo '</pre>';
            }
        }

        if ( $enable_cache === true ) {
            //error_log("Calling feed to cache...");                    

            // if no cache file found get feed and cache it...
            if ( $response === FALSE ) {
                // error_log('***** CALLING FEED AND CACHING RESULT *******');
                // $response = $plugin_admin->getAlbumList( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results, $visibility );
                // var_dump($album_thumb_size);

                $response = $plugin_admin->getAlbumList( $AccessToken, $album_thumb_size, $show_title, $cws_page, $num_results, $visibility );

                if( $cws_debug == "1" ){ 
                    echo "<strong>Setting cache, response.</strong><br>";
                    echo '<pre>';
                    print_r($response);
                    echo '</pre>';
                }

                //$response = $response;
                $cache->set($cacheName, $response);
            } else {
                // error_log('***** FEED IS ALREADY CACHED *******');
                //$response = $plugin_admin->getAlbumList( $AccessToken, $album_thumb_size, $show_title, $cws_page, $num_results, $visibility );             
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
                $response = $plugin_admin->getAlbumList( $AccessToken, $album_thumb_size, $show_title, $cws_page, $num_results, $visibility );
                //error_log("Calling feed normally...");                       
        }
        //
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
                    trigger_error( $error_message );
                    return false;
                }
                return $xml;
            } // end function produce

        }

        // Create SimpleXML Object
        $xml = produce_XML_object_tree ( $response );

        if ( isset( $_GET['cws_debug'] ) ) {
            if( $cws_debug == "1" ){ 
                echo "<strong>xml tree.</strong><br>";
                echo '<pre>';
                print_r($xml);
                echo '</pre>';
            }
        }


        // Get Albums
        // $response = $plugin_admin->getAlbumList( $AccessToken, $album_thumb_size, $show_title, $cws_page, $num_results, $visibility );             

        #----------------------------------------------------------------------------
        # Iterate over the array and extract the info we want
        #----------------------------------------------------------------------------

        // Get the posts that have the results shortcode on them [cws_gpp_images_in_album]
        // only use the first one
        // TODO: improve this
        // $results_posts = get_result_posts_links();

        // Decide which layout to use to display the albums
        switch( $theme ) {

            #----------------------------------------------------------------------------
            # Photbooth Strips Layout *** PRO ONLY ***
            #----------------------------------------------------------------------------
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
                } else {
                    include 'partials/upgrade.php';
                }
                break;

            #----------------------------------------------------------------------------
            # Polaroid Stack Grid Layout *** PRO ONLY ***
            #----------------------------------------------------------------------------
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
                } else {
                    include 'partials/upgrade.php';
                }
                break;


            #----------------------------------------------------------------------------
            # Grid Layout
            #----------------------------------------------------------------------------
            case "grid":
                // Include Masonry
                wp_enqueue_script( 'cws_gpp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                    
                // Initialize Masonry
                wp_enqueue_script( 'cws_gpp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_gpp_masonry' ), false , true );            
                include 'partials/grid.php';

                if( $plugin->get_isPro() == 1 ){
                    // Enque Pro FX CSS
                    wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );
                }
                
                break;

            #----------------------------------------------------------------------------
            # List Layout
            #----------------------------------------------------------------------------
            case "list":
                include 'partials/list.php';            
                break;

            #----------------------------------------------------------------------------
            # Carousel Layout
            #----------------------------------------------------------------------------
            case "carousel":


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

                // Include Slick
                wp_enqueue_script( 'cws_gpp_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/slick.min.js', array( 'jquery' ), false, true ); 
                   
                // Initialize Slick
                // wp_enqueue_script( 'cws_gpp_init_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/init_slick.js', array( 'cws_gpp_slick' ), false , true );
                    // Initialize any scripts?
                    wp_register_script( 'cws_gpp_init_slick', plugin_dir_url( __FILE__ )  . '../public/js/slick/init_slick.js' );
                    wp_localize_script( 'cws_gpp_init_slick', 'php_vars', $dataToBePassed );
                    wp_enqueue_script( 'cws_gpp_init_slick', array( 'cws_gpp_slick_lb' ), false , true ); // Initialize Slick WITH options!                
                
                include 'partials/carousel.php';

                // Enque Pro FX CSS
                wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );

                break;

            #----------------------------------------------------------------------------
            # Default Layout - Grid
            #----------------------------------------------------------------------------
            default:
                // Include Masonry
                wp_enqueue_script( 'cws_gpp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                    
                // Initialize Masonry
                wp_enqueue_script( 'cws_gpp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_gpp_masonry' ), false , true );            
                include 'partials/grid.php';

                // Enque Pro FX CSS
                wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );      
            }

        #----------------------------------------------------------------------------
        # Show output for pagination
        #----------------------------------------------------------------------------
        if( $num_results > 0 ) {
            $num_pages  = ceil( $total_num_albums / $num_results );
            // $cws_debug = get_query_var('cws_debug');

            if ( isset( $_GET['cws_debug'] ) ) {

                if( $cws_debug == "1" ){ 
                    echo "<hr>total_num_albums = $total_num_albums<br>";
                    echo "num_results = $num_results<br>";
                    echo "album_thumb_size = $album_thumb_size<br>";
                    echo "num_pages = $num_pages<br><hr>";
                }
            }

            // If someone has done some url hacking reset page to something sensible
            if ( isset($cws_page) && $cws_page > $num_pages ) { $cws_page = $num_pages; }
        }
        // total results, num to show per page, current page
        $strOutput .= $plugin_admin->get_pagination( $total_num_albums, $num_results, $cws_page );

        return $strOutput;

    } // end if authenticated check 
    
}