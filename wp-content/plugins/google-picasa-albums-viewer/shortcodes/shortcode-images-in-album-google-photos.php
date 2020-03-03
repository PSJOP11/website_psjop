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
function cws_gpp_shortcode_images_in_album_google_photos( $atts ) {
	
    $cws_debug  = '';
    $cws_page   = '';
    $nextPageToken = '';
    
    if( isset( $_GET[ 'cws_debug' ] ) ) {
        $cws_debug = $_GET[ 'cws_debug' ];
    }

    $plugin         = new CWS_Google_Picasa_Pro();
    $plugin_admin   = new CWS_Google_Picasa_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

    // If authenticated get list of albums
    if( $plugin_admin->isAuthenticated() == true  ) {
        
        // $cws_album = $_GET[ 'cws_album' ]; 

        if( isset( $_GET[ 'nextToken' ] ) ) {
            $nextPageToken = $_GET['nextToken'];
        }

        // Grab the access token
        $AccessToken = get_option( 'cws_gpp_access_token' ); 

        $options = get_option( 'cws_gpp_options' );
        $album_thumb_size = $options['album_thumb_size'];

        // set some defaults...
        $num_pages = 0;
        $options['crop'] = '';

        $options['show_image_details'] = isset($options['show_image_details']) ? $options['show_image_details'] : "";
        $options['theme'] = isset($options['theme']) ? $options['theme'] : "";
        $options['id'] = isset($options['id']) ? $options['id'] : "";
        $options['results_page'] = isset($options['results_page']) ? $options['results_page'] : "";
        $options['hide_albums'] = isset($options['hide_albums']) ? $options['hide_albums'] : "";
        $options['row_height'] = isset($options['row_height']) ? $options['row_height'] : "251";
        $options['enable_download'] = isset($options['enable_download']) ? $options['enable_download'] : "";

        // get options from shortcode and merge with defaults
        // replaced extract()
        $args = shortcode_atts( array(
                'thumb_size'        => $options['thumb_size'], 
                'crop'              => $options['crop'], 
                'show_title'        => $options['show_image_title'],
                'show_details'      => $options['show_image_details'],
                'num_results'       => $options['num_image_results'],
                'theme'             => $options['theme'],   
                'id'                => $options['id'],
                'imgmax'            => $options['lightbox_image_size'],
                'results_page'      => $options['results_page'],
                //'enable_cache'      => $options['enable_cache'],
                'enable_download'   => $options['enable_download'],
                'hide_albums'       => $options['hide_albums'],  
                'row_height'        => $options['row_height'], 
                'fx'                => NULL,
                'album_title'       => 1,                       
                                    ), $atts );

    // if we have one in url
    if(isset( $_GET[ 'cws_album' ]) ){
        $cws_album = $_GET[ 'cws_album' ]; 
    }
    // this is Pro only!
    // 
    elseif( $plugin->get_isPro() == 1 ) {
        $cws_album = $args['id'];
    }
    
    // set default for lightbox if missing
    if(empty($args['imgmax'])){
        $args['imgmax'] = 800;
    }


        $google_photos = new CWS_Google_Photos_Pro();
        $response = $google_photos->getAlbumImagesGooglePhotos( $AccessToken, $args['thumb_size'], $args['show_title'], $cws_page, $args['num_results'], $cws_album, $args['imgmax'], $args['theme'], $nextPageToken );


if( isset($_GET['cws_debug']) == 1 ) {
    echo '<pre>';
    print_r($args);
    echo '</pre>'; 

    echo 'response $response<br>';
    echo '<pre>';
    print_r($response);
    echo '</pre>';   
}

        /*
        echo '<pre>';        
        print_r($response);
        //print_r($response[0]['mediaItems'][0]['mediaMetaData']['photo']['cameraMake']);
        echo '</pre>';
        */
        ///die();
   
        /*
        echo '<pre>';        
        print_r($response[0]['mediaItems'][0]);
        //print_r($response[0]['mediaItems'][0]['mediaMetaData']['photo']['cameraMake']);
        echo '</pre>';

        die();
        */
        $settings = array();
        $settings['dimensions']['maxWidth'] = $args['thumb_size']; // guess these would be passed in via shortcode eventually therefor in partial
        $settings['dimensions']['maxHeight'] = $args['thumb_size'];
        $settings['crop'] = 1;
        // $settings['results_page'] = $results_page;

        //$theme = 'grid';
// var_dump($args['theme']);
        // Decide which layout to use to display the albums
        switch( $args['theme'] ) {

            #----------------------------------------------------------------------------
            # Justified Image Grid Layout *** PRO ONLY ***
            #----------------------------------------------------------------------------
            case "projig":

                // enque styles
                if( $plugin->get_isPro() == 1 ) {

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
                        'imgmax'    => "{$args['imgmax']}",
                        'rowheight' => "{$args['row_height']}",
                    );
                    wp_localize_script( 'cws_gpp_init_jig', 'php_vars', $dataToBePassed );
                    wp_enqueue_script( 'cws_gpp_init_jig', array( 'cws_gpp_jig' ), false , false );

                    ////wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/propbs/js/lightbox.js', array( 'jquery' ), false, true );


                    include 'partials_pro/pro_jigGP.php';
                    // include 'partials_pro/photoswipe.html'; // moved this into related partial and used file_get_contents to fix WP 5 'update failed' bug
                }
                break;


            #----------------------------------------------------------------------------
            # Grid Layout
            #----------------------------------------------------------------------------
            case "grid":

                include 'partials/results_gridGP.php';
                
                // Include Masonry
                wp_enqueue_script( 'cws_gpp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                    
                // Initialize Masonry
                wp_enqueue_script( 'cws_gpp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_gpp_masonry' ), false , true );

                // If Pro use Photoswipe for improved responsiveness and better ux
                if( $plugin->get_isPro() == 1 ){
                    // include 'partials_pro/photoswipe.html'; // moved this into related partial and used file_get_contents to fix WP 5 'update failed' bug

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
                    //wp_enqueue_script( 'cws_gpp_psui', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/props/js/photoswipe-ui-default.js', array(), false, false ); 

                    wp_enqueue_script( 'cws_gpp_init_ps', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/grid/js/init_ps.js', array( 'jquery' ), false, false );
                    // end inclucde PhotoSwipe files
                } 
                // Lite version
                else {
                    wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/lightbox.js', array( 'jquery' ), false, true );                                         
                    wp_enqueue_script( 'cws_gpp_init_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/init_lightbox.js', array( 'cws_gpp_lightbox' ), false , true );
                }

                break;

            #----------------------------------------------------------------------------
            # List Layout
            #----------------------------------------------------------------------------
            case "list":

                include 'partials/results_listGP.php';

                // Enque Pro FX CSS
                wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );

                // If Pro use Photoswipe for improved responsiveness and better ux
                if( $plugin->get_isPro() == 1 ){

                    //include 'partials_pro/photoswipe.html'; // moved this into related partial and used file_get_contents to fix WP 5 'update failed' bug

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

                include 'partials/results_carouselGP.php';

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

                    // include 'partials_pro/photoswipe.html'; // moved this into related partial and used file_get_contents to fix WP 5 'update failed' bug

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
            # Default Layout - Grid
            #----------------------------------------------------------------------------   
            default:

                include 'partials/results_gridGP.php';
                
                // Include Masonry
                wp_enqueue_script( 'cws_gpp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                    
                // Initialize Masonry
                wp_enqueue_script( 'cws_gpp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_gpp_masonry' ), false , true );

                // If Pro use Photoswipe for improved responsiveness and better ux
                if( $plugin->get_isPro() == 1 ){
                    // include 'partials_pro/photoswipe.html'; // moved this into related partial and used file_get_contents to fix WP 5 'update failed' bug

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
                } 
                // Lite version
                else {
                    wp_enqueue_script( 'cws_gpp_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/lightbox.js', array( 'jquery' ), false, true );                                         
                    wp_enqueue_script( 'cws_gpp_init_lightbox', plugin_dir_url( __FILE__ )  . '../public/js/lightbox/init_lightbox.js', array( 'cws_gpp_lightbox' ), false , true );
                }
        }
            // Pagination - why is there no previousPageToken!?
            if( isset($response[0]['nextPageToken'] ) ) {
                // $next = '?nextToken=' . $response[0]['nextPageToken'];
                $next = '?nextToken=' . $response[0]['nextPageToken'] . '&cws_album=' . $cws_album; // also inc album id some how
                $strOutput .= "<a class='cws-next-button' href='$next'>Next</a>";
            }

        //die($response);
        //die('Album id from get ' . $cws_album);

            return $strOutput;

    }   // end if authenticated check 
    
}
