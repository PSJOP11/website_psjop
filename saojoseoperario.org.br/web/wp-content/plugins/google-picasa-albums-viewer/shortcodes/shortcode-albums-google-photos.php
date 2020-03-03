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
function cws_gpp_shortcode_albums_google_photos( $atts ) {


    $cws_debug = '';
    $cws_page = '';
    $nextPageToken = '';

    if ( isset( $_GET['cws_debug'] ) ) {
        $cws_debug = $_GET[ 'cws_debug' ]; // $cws_debug = get_query_var('cws_debug');    
    }

    // Grab page from url
    if ( isset( $_GET['cws_page'] ) ) {
        $cws_page = $_GET[ 'cws_page' ]; // $cws_page = get_query_var('cws_page');
    }

    if ( isset( $_GET['nextToken'] ) ) {
        $nextPageToken = $_GET['nextToken'];
    }

    $strOutput = "";

    $plugin         = new CWS_Google_Picasa_Pro();
    $plugin_admin   = new CWS_Google_Picasa_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

    // If authenticated get list of albums
    if( $plugin_admin->isAuthenticated() == true  ) {

        // Grab options stored in db
        $options = get_option( 'cws_gpp_options' );

        // set some defaults...
        $options['results_page']    = isset($options['results_page']) ? $options['results_page'] : "";
        $options['hide_albums']     = isset($options['hide_albums']) ? $options['hide_albums'] : "";
        $options['theme']           = isset($options['theme']) ? $options['theme'] : "";

        // Extract the options from db and overwrite with any set in the shortcode
        /*
        extract( shortcode_atts( array(
            'thumb_size'        => $options['thumb_size'],
            'album_thumb_size'  => $options['album_thumb_size'], 
            'show_title'        => $options['show_album_title'],
            'show_details'      => $options['show_album_details'],
            'num_results'       => $options['num_album_results'],
            'visibility'        => $options['private_albums'],           
            'results_page'      => $options['results_page'],
            'hide_albums'       => $options['hide_albums'],
            'theme'             => $options['theme'],
            'imgmax'            => $options['lightbox_image_size'],
            'enable_cache'      => $options['enable_cache'], 
            'fx'                => NULL,
        ), $atts ) );
        */
        $args = shortcode_atts( array(
            'thumb_size'        => $options['thumb_size'],
            'album_thumb_size'  => $options['album_thumb_size'], 
            'show_title'        => $options['show_album_title'],
            'show_details'      => $options['show_album_details'],
            'num_results'       => $options['num_album_results'],
            'visibility'        => $options['private_albums'],           
            'results_page'      => $options['results_page'],
            'hide_albums'       => $options['hide_albums'],
            'theme'             => $options['theme'],
            'imgmax'            => $options['lightbox_image_size'],
            //'enable_cache'      => $options['enable_cache'], 
            'fx'                => NULL, 
            'access'                => NULL,                     

                                    ), $atts );

            // Define allowed access values and set to 'own' if unrecognised.
            $arrAccessOptions = array('own', 'shared');
            if( ! in_array( $args['access'], $arrAccessOptions ) ) { $args['access'] = 'own'; }

/*
echo '<pre>';
print_r($args);
echo '</pre>';
*/

        // Map albums names to hide to array and trim white space    
        if( $args['hide_albums'] !== NULL ) {
            $args['hide_albums'] = array_map( 'trim', explode( ',', $args['hide_albums'] ) );
        }

        // Grab the access token
        $AccessToken = get_option( 'cws_gpp_access_token' ); 

        // Get Albums
        // $response = $plugin_admin->getAlbumListGooglePhotos( $AccessToken, $album_thumb_size, $show_title, $cws_page, $num_results, $visibility );
        $google_photos = new CWS_Google_Photos_Pro();
        $response = $google_photos->getAlbumListGooglePhotos( $AccessToken, $args['results_page'], $args['num_results'], $nextPageToken, $args['access']  );

// echo '<pre>';
// print_r($response);
// echo '</pre>';

//die();
       $albums = isset( $response[0]['albums'] ) ? $response[0]['albums'] : $response[0]['sharedAlbums'];
            // $albums = isset( $response['albums'] ) ? $response['albums'] : $response['sharedAlbums'];


        $settings = array();
        $settings['debug'] = 0;
        $settings['dimensions']['maxWidth'] = $args['album_thumb_size']; // guess these would be passed in via shortcode eventually therefor in partial
        $settings['dimensions']['maxHeight'] = $args['album_thumb_size'];
        $settings['crop'] = 1;
        $settings['pageSize'] = 5;
        $settings['results_page'] = $args['results_page'];

        #----------------------------------------------------------------------------
        # Iterate over the array and extract the info we want
        #----------------------------------------------------------------------------

        // Decide which layout to use to display the albums
        switch( $args['theme'] ) {

            #----------------------------------------------------------------------------
            # Grid Layout
            #----------------------------------------------------------------------------
            case "grid":
                // Include Masonry
                wp_enqueue_script( 'cws_gpp_masonry', plugin_dir_url( __FILE__ )  . '../public/js/masonry.pkgd.min.js', array( 'jquery' ), false, true ); 
                wp_enqueue_script( 'cws_gpp_imagesLoaded', plugin_dir_url( __FILE__ )  . '../public/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true ); 
                    
                // Initialize Masonry
                wp_enqueue_script( 'cws_gpp_init_masonry', plugin_dir_url( __FILE__ )  . '../public/js/init_masonry.js', array( 'cws_gpp_masonry' ), false , true );            
                include 'partials/gridGP.php';

                if( $plugin->get_isPro() == 1 ){
                    // Enque Pro FX CSS
                    wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );
                }

                break;

            #----------------------------------------------------------------------------
            # List Layout
            #----------------------------------------------------------------------------
            case "list":
                include 'partials/listGP.php';
                
                if( $plugin->get_isPro() == 1 ){
                    // Enque Pro FX CSS
                    wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );
                }

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
                
                include 'partials/carouselGP.php';

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
                include 'partials/gridGP.php';

                if( $plugin->get_isPro() == 1 ){
                    // Enque Pro FX CSS
                    wp_enqueue_style( 'cws_pro_fx', plugin_dir_url( __FILE__ )  . '../shortcodes/partials_pro/css/style_fx.css' );
                } 
            }

            #----------------------------------------------------------------------------
            # Show output for pagination
            #----------------------------------------------------------------------------
            // Pagination - why is there no previousPageToken!?
            if( isset( $response[0]['nextPageToken'] ) )
            {
                // $next = '?nextToken=' . $response[0]['nextPageToken'];
                $next = '?nextToken=' . $response[0]['nextPageToken'];
                $strOutput .= "<a class='cws-next-button' href='$next'>Next</a>";
            }

        return $strOutput;

    } // end if authenticated check 
    
}