<?php
    $strOutput = "";

    // moved this here to fix WP 5 'update failed' bug
    if( $plugin->get_isPro() == 1 ){
        $pathPhotoswipe = plugin_dir_path( __FILE__ ) . '../partials_pro/photoswipe.html';
        $strPhotoswipe = file_get_contents($pathPhotoswipe);
        // var_dump($strPhotoswipe);
        $strOutput .= $strPhotoswipe;
    }

    wp_register_style( 'cws_gpp_cws_gpp_slick_carousel_css', plugins_url( '../../public/css/slick/slick.css',__FILE__ ) , '', 1 );
    wp_register_style( 'cws_gpp_cws_gpp_slick_carousel_css_theme', plugins_url( '../../public/css/slick/slick-theme.css',__FILE__ ) , '', 1 );
    wp_register_style( 'cws_gpp_cws_gpp_slick_lb_css', plugins_url( '../../public/css/slick-lightbox/slick-lightbox.css',__FILE__ ) , '', 1 );

    if ( function_exists( 'wp_enqueue_style' ) ) {
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_carousel_css' );
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_carousel_css_theme' );
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_lb_css' );
    } 

    $strOutput .=  "<div class='mygallery carousel grid'>\n";

    if( isset( $_GET[ 'cws_album_title' ] ) ) {
        $cws_album_title = $_GET[ 'cws_album_title' ];
        $strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n";                        
    }

    $strOutput .= "<div class=\"multiple-items-sc\">\n";
        
    $num_photos = 9;

    // Loop over the images
    for ( $i = 0; $i <= count( $response[0]['mediaItems'] ) -1; $i++ ) {

        // Get the filename without the extension to use as title
        $desc           = '';
        $description    = '';
        $title          = pathinfo( $response[0]['mediaItems'][$i]['filename'], PATHINFO_FILENAME );                        // Filename without the extension to use as title      

        if( isset($response[0]['mediaItems'][$i]['description']) ) { 
            $description    = $response[0]['mediaItems'][$i]['description'];                                                    // Description
        }

        if( $plugin->get_isPro() == 1 ){
            $imgUrlBIG      = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( $args['imgmax'], null, 0 );        // Big image for overlay, setting a max width
        } else {
            $imgUrlBIG      = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( 800, null, 0 );        // Big image for overlay, setting a max width
        }
        
        $imgUrl         = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions($args['thumb_size'], $args['thumb_size'], $args['crop']);

        //$img = "<a href='{$settings['results_page']}?cws_album={$albums[$i]['id']}&cws_album_title={$albums[$i]['title']}'><img src='$imgUrl'></a>";

        switch( $args['fx'] ) {
            case "style1":
            $strFXStyle = "sarah";
            break;

            case "style2":
            $strFXStyle = "sadie";
            break;    

            case "style3":
            $strFXStyle = "lily";
            break;

            default:
            $strFXStyle = '';
            break;
        }

        $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$i."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";   

        if( $args['fx']===NULL ){
            // $strOutput .=  "<div class=\"item\" style=\"max-width:" . $args['thumb_size'] . "px; max-height:" . $args['thumb_size'] . "px !important;\">\n";                        
            $strOutput .=  "<div class=\"item\" style=\"max-width:" . $args['thumb_size'] . "px;\">\n";                        

        } else {
            $strOutput .=  "<div class=\"item\" style=\"max-width:" . $args['thumb_size'] . "px; max-height:" . $args['thumb_size'] . "px !important;\">\n";    

        }    

        // need to bring in media width and height here
        $width_orig     = $response[0]['mediaItems'][$i]['mediaMetadata']['width'];
        $height_orig    = $response[0]['mediaItems'][$i]['mediaMetadata']['height'];
        $ratio_orig     = $width_orig/$height_orig;                                     // work out ratio of original image
        $width          = $args['imgmax'];                                       
        $height         = $width / $ratio_orig;    

        if( $plugin->get_isPro() == 1 ){
            // include overlay data attributes required for Photo Swipe       
            $strOutput .= "<a itemprop='$imgUrlBIG' data-size='{$width}x{$height}' data-index=\"".$i."\" class='result-image-link' href='$imgUrlBIG' data-lightbox='result-set' data-title='$title'>\n";          
            $strOutput .="<img data-index=\"".$i."\" class='result-image' src='" . $imgUrl . "' alt='$title'/>";
        } else{
            $strOutput .= "<a itemprop=\"contentUrl\" data-size='{$width}x{$height}' data-index=\"".$i."\" href='" . $imgUrl . "' data-largesrc='" . $imgUrlBIG . "' data-title='' data-description=''>\n";
            $strOutput .= "<img src='" . $imgUrl . "' alt='' />\n";
        }



        // if fx value has been set in shortcode...
        if( $args['fx'] ) {
            $strOutput .= "<figcaption>\n";
            // Get the title ready...
            if( $args['show_title'] ) {
                $strOutput .= "<h6><span>xx$title</span></h6>\n";
            }

            // Get the details ready...
            if( $args['show_details'] ) {
                $strOutput .= "<p>yy$description</p>\n"; // displayed in carousel on hover
                $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
            } else {
                $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
            }

            $strOutput .= "</figcaption>";
        } else {

            if( $args['show_details'] ){
                if( $args['show_title'] ) {
                    $desc .= "$title\n";
                }
                if($args['show_title'] && $args['show_details']){

                    // need to also check if description is not empty
                    if(!empty($description)){
                        $desc .= ' - ';
                    }

                }
                if ( $args['show_details'] ) {
                    $desc .= "$description";
                }
            }

            // Caption used in lightbox (when no $args['fx'] defined)
             $strOutput .= "<figcaption>\n";
                $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
            $strOutput .= "</figcaption>";               
        }

        $strOutput .= "</a>\n";

        // if NO fx value has been set in shortcode...
        if( $args['fx'] === NULL ) 
        {                        
            if ( $args['show_title'] ) { $strOutput .=  "<div id='album_title'><span>$title</span></div>\n"; }
            if ( $args['show_details'] ) { $strOutput .=  "<div id='album_details'><span>$description</span></div>\n"; }
        }

            $strOutput .= "</div>\n"; // End .item
        $strOutput .= "</figure>\n";

    } // for    

    
    $strOutput .= "</div>\n"; // End .multiple-items-sc
    $strOutput .= "</div>\n"; // End .carousel