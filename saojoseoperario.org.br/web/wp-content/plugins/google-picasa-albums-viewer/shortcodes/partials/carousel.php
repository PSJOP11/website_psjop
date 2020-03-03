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

    if( $xml === false ) {
        echo 'Sorry there has been a problem with your feed.';
    } else {

        $xml->registerXPathNamespace('gphoto', 'http://schemas.google.com/photos/2007');
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
        
    $strOutput .=  "<div class='carousel grid'>\n";
        $strOutput .=  "<div class=\"multiple-items-sc\">\n";

        $intCounter = 0;

        foreach( $xml->entry as $feed) {

            // get the data we need
            $title = $feed->title;

            if( !in_array( $title, $hide_albums ) ){

                $gphoto = $feed->children('http://schemas.google.com/photos/2007');
                $num_photos = $gphoto->numphotos; 
                $published = $feed->published; 
                $published = trim( $published );
                $published = substr( $published, 0, 10 );      

                $group = $feed->xpath('./media:group/media:thumbnail');
                $a = $group[0]->attributes(); // we need thumbnail path
                $id = $feed->xpath('./gphoto:id'); // and album id for our thumbnail

                // var_dump($thumb_size);
                switch( $fx ) {
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

                $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$intCounter."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";
                    
                if( $fx === NULL ){
                    $strOutput .=  "<div class=\"item\">\n";        
                } else {
                    $strOutput .=  "<div class=\"item\" style=\"max-width:" . $thumb_size . "px; max-height:" . $thumb_size . "px !important;\">\n";    
                }

                // check if permalinks are enabled
                if ( get_option( 'permalink_structure' ) ) { 
                    // $strOutput .=  "<a href='" .$results_page . "?cws_album=$id[0]&cws_album_title=$title' data-largesrc='' data-title='$title' data-description=''><img src='$a[0]' alt='$title' /></a>\n";   
                    $strOutput .=  "<a href='" .$results_page . "?cws_album=$id[0]&cws_album_title=$title' data-largesrc='' data-title='$title' data-description=''><img src='$a[0]' alt='$title' />\n";

                    // if fx value has been set in shortcode...
                    if( $fx ) {

                        $strOutput .= "<figcaption>\n";
                        // Get the title ready...
                        if( $show_title ) {
                            $strOutput .= "<h6><span>$title</span></h6>\n";
                        }

                        // Get the details ready...
                        if( $show_details ) {
                            $strOutput .= "<p><small>Images: $num_photos</small><br/><small>$published</small></p>\n";
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
                    if( $show_title ) {
                        $strOutput .= "<h6><span>$title</span></h6>\n";
                    }

                    // Get the details ready...
                    if( $show_details ) {
                        $strOutput .= "<p>$description[0]</p>\n";
                    }else{
                        $strOutput .= "<p style=\"display:none;\">$description[0]</p>\n";
                    }

                    $strOutput .= "</figcaption>";

                    $strOutput .= "</a>\n";

                }

                // if NO fx value has been set in shortcode...
                if( $fx === NULL ) {                        
                    if ( $show_title ) { $strOutput .=  "<div id='album_title'><span>$title</span></div>\n"; }
                }
                
                $strOutput .=  "</div>\n"; // End .item

                $strOutput .= "</figure>\n";
                $intCounter++;

                }
            }

        } // end else

            $strOutput .=  "</div>\n"; // End .multiple-items-sc
        $strOutput .=  "</div>\n"; // End .carousel

                $strOutput .=  "</div>\n"; // End .carousel