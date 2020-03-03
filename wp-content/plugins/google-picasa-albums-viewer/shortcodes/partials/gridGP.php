<?php
    $strOutput .= "<div class='listviewxxx'>\n";
    $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"isOriginLeft\": true, \"isFitWidth\": true }'>\n";

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

        // Do not display if album name has been hidden
        if( !in_array( $albums[$i]['title'], $args['hide_albums'] ) ) {

            $imgUrl = $albums[$i]['coverPhotoBaseUrl'] . $google_photos->addDimensions($args['album_thumb_size'], null, $settings['crop']);
            $img = "<a href='{$settings['results_page']}?cws_album={$albums[$i]['id']}&cws_album_title={$albums[$i]['title']}'><img src='$imgUrl'>";

                switch($args['fx']) {
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
            $strOutput .=  "<div class='thumbimage'>\n";

            //$productUrl = $albums[$i]['productUrl'];
            //$link = "<a href='$productUrl' target='_blank'>Open in Google</a>";
            //$strOutput .= 'Id: ' . $albums[$i]['id'] . "<br>";
            ///$strOutput .= 'Title: ' . $albums[$i]['title'] . "<br>";
            ///$strOutput .= 'Images: ' . $albums[$i]['mediaItemsCount'] . "<br>";
            //$strOutput .= 'Product Link: ' . $link . "<br>";

            //
            if( $args['fx'] === NULL ) {
                $strOutput .= "<div class='thumbnail grid-item albums' style='width:" . $args['album_thumb_size'] . "px;'>\n";
            } else {
                $strOutput .= "<div class='thumbnail grid-item albums' style='width:" . $args['album_thumb_size'] . "px; height:" . $args['album_thumb_size'] . "px;'>\n";
            }

            $strOutput .=  "<div class='thumbimage'>\n";

            if ( get_option( 'permalink_structure' ) ) { 
                //$urltitle = urlencode( $title );                        
                ///$strOutput .=  "<a href='" . $results_page . "?cws_album=$id[0]&cws_album_title=$urltitle'><img alt='$title' src='$a[0]' title='$title' />\n";
                $strOutput .= $img;

                // if fx value has been set in shortcode...
                if( $args['fx'] ) {

                    $strOutput .= "<figcaption>\n";

                    // Get the title ready...
                    if( $args['show_title'] ) {
                        $strOutput .= "<h6><span>{$albums[$i]['title']}</span></h6>\n";
                    }

                    if( $args['show_details'] ) {
                        $strOutput .= "<p><small>Images: {$albums[$i]['mediaItemsCount']}</small><br/></p>\n";
                    }

                    $strOutput .= "</figcaption>";
                }
                $strOutput .= "</a>\n";

            } else {
                $urltitle = urlencode( $title );
                /// $strOutput .=  "<a href='" . $results_page . "&cws_album=$id[0]&cws_album_title=$urltitle'><img alt='$title' src='$a[0]' title='$title' />\n";
                $strOutput .= $img;
           
                // if fx value has been set in shortcode...
                if( $args['fx'] ) {

                    $strOutput .= "<figcaption>\n";

                    // Get the title ready...
                    if( $args['show_title'] ) {
                        $strOutput .= "<h6><span>$title</span></h6>\n";
                    }

                    if( $args['show_details'] ) {
                        $strOutput .= "<p><small>Images: $num_photos</small><br/><small>$published</small></p>\n";
                    }

                    $strOutput .= "</figcaption>";
                }
                $strOutput .= "</a>\n";
            }

            $strOutput .=  "</div>\n"; // End .thumbimage

            // if NO fx value has been set in shortcode...
            if( $args['fx'] === NULL ) {

                if( $args['show_details'] || $args['show_title'] ) {
                    $strOutput .=  "<div class='details'><ul>\n";
                }

                if ( $args['show_title'] ) {
                    if ( get_option( 'permalink_structure' ) ) { 
                        $urltitle = urlencode( $albums[$i]['title'] );
                        $strOutput .=  "<li class='title'><a href='" .$args['results_page'] . "?cws_album={$albums[$i]['id']}&cws_album_title=$urltitle'>{$albums[$i]['title']}</a></li>\n";
                    } else {
                        $urltitle = urlencode( $albums[$i]['title'] );
                        $strOutput .=  "<li class='title'><a href='" .$args['results_page'] . "&cws_album={$albums[$i]['id']}&cws_album_title=$urltitle'>{$albums[$i]['title']}</a></li>\n";
                    }
                }

                if ( $args['show_details'] ) {
                   
                    //$strOutput .=  "<ul>\n";

                    $strOutput .= "<li><small>Images: {$albums[$i]['mediaItemsCount'] }</small>";
                    /* $strOutput .= "<li><small>$published</small>"; */

                    /*if ( $desc != "" ) { 
                        $strOutput .= " $desc ";
                    }*/
                    /*
                    if ( $location != "" ) {
                        $strOutput .= " $location </li>\n";
                    }*/
                }

                if( $args['show_details'] || $args['show_title'] ) {
                    $strOutput .=  "</ul>\n";
                    $strOutput .=  "</div>\n"; // End .details
                }
            }

            $strOutput .=  "</div>\n"; // End .thumbnail
            $strOutput .= "</figure>\n";

        } // end do not display
    }// for

    $strOutput .=  "</div>\n"; // added to fix sidebar bug that knocked positioning off ...
    $strOutput .=  "</div>\n"; // End .grid    