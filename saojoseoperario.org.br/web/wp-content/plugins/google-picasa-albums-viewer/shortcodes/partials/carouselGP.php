<?php
    $strOutput = "";
    $strOutput .=  "<div class='listviewxxx'>\n";

    wp_register_style( 'cws_gpp_cws_gpp_slick_carousel_css', plugins_url( '../../public/css/slick/slick.css',__FILE__ ) , '', 1 );
    wp_register_style( 'cws_gpp_cws_gpp_slick_carousel_css_theme', plugins_url( '../../public/css/slick/slick-theme.css',__FILE__ ) , '', 1 );

    wp_register_style( 'cws_gpp_cws_gpp_slick_lb_css', plugins_url( '../../public/css/slick-lightbox/slick-lightbox.css',__FILE__ ) , '', 1 );

    if ( function_exists( 'wp_enqueue_style' ) ) {
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_carousel_css' );
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_carousel_css_theme' );
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_lb_css' );
    } 

        
    $strOutput .=  "<div class='carousel grid'>\n";
    $strOutput .=  "<div class=\"multiple-items-sc\">\n";

    $intCounter = 0;

if(isset($_GET['cws_debug'])){
    
    echo '<pre>';
    print_r($args);
    echo '</pre>';

    echo 'response $albums<br>';
    echo '<pre>';
    print_r($albums);
    echo '</pre>';
}

    for( $i = 0; $i < count( $albums ); $i++ ) {

            // get the data we need
            $title = $albums[$i]['title'];

            // Do not display if album name has been hidden
            if( !in_array( $albums[$i]['title'], $args['hide_albums'] ) ) {

            $imgUrl = $albums[$i]['coverPhotoBaseUrl'] . $google_photos->addDimensions($args['album_thumb_size'], null, $settings['crop']);
            $img = "<a href='{$settings['results_page']}?cws_album={$albums[$i]['id']}&cws_album_title={$albums[$i]['title']}'><img src='$imgUrl'>";

                // var_dump($thumb_size);
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
                    
                if( $args['fx'] === NULL ){
                    $strOutput .=  "<div class=\"item\" style=\"max-width:" . $args['album_thumb_size'] . "px; max-height:" . $args['album_thumb_size'] . "px !important;\">\n";                        
                } else {
                    $strOutput .=  "<div class=\"item\" style=\"max-width:" . $args['album_thumb_size'] . "px; max-height:" . $args['album_thumb_size'] . "px !important;\">\n";    
                }

                // check if permalinks are enabled
                if ( get_option( 'permalink_structure' ) ) { 
                    $strOutput .= $img;

                    // if fx value has been set in shortcode...
                    if( $args['fx'] ) {

                        $strOutput .= "<figcaption>\n";
                        // Get the title ready...
                        if( $args['show_title'] ) {
                            $strOutput .= "<h6><span>$title</span></h6>\n";
                        }

                        // Get the details ready...
                        if( $args['show_details'] ) {
                            $strOutput .= "<p><small>Images: {$albums[$i]['mediaItemsCount']}</small></p>\n";
                        }else{

                            if( isset( $description[0] ) ){
                                $strOutput .= "<p style=\"display:none;\">$description[0]</p>\n";
                            }
                        }

                        $strOutput .= "</figcaption>";
                    }
                    
                    $strOutput .= "</a>\n";

                } else {
                    // $strOutput .=  "<a href='" .$results_page . "&cws_album=$id[0]&cws_album_title=$title' data-largesrc='' data-title='$title' data-description=''><img src='$a[0]' alt='$title' /></a>\n";
                    $strOutput .=  "<a href='" .$results_page . "&cws_album=$id[0]&cws_album_title=$title' data-largesrc='' data-title='$title' data-description=''><img src='$a[0]' alt='$title' />\n";

                    $strOutput .= "<figcaption>\n";
                    // Get the title ready...
                    if( $args['show_title'] ) {
                        $strOutput .= "<h6><span>$title</span></h6>\n";
                    }

                    // Get the details ready...
                    if( $args['show_details'] ) {
                        $strOutput .= "<p>$description[0]</p>\n";
                    }else{
                        $strOutput .= "<p style=\"display:none;\">$description[0]</p>\n";
                    }

                    $strOutput .= "</figcaption>";

                    $strOutput .= "</a>\n";

                }

                // if NO fx value has been set in shortcode...
                if( $args['fx'] === NULL ) {                        
                    if ( $args['show_title'] ) { $strOutput .=  "<div id='album_title'><span>$title</span></div>\n"; }
                }
                
                $strOutput .=  "</div>\n"; // End .item

                $strOutput .= "</figure>\n";
                $intCounter++;

                }
            }


            $strOutput .=  "</div>\n"; // End .multiple-items-sc
        $strOutput .=  "</div>\n"; // End .carousel

                $strOutput .=  "</div>\n"; // End .carousel