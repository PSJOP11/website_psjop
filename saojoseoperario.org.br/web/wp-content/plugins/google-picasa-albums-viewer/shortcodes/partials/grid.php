<?php
    $strOutput .=  "<div class='listviewxxx'>\n";
    
    if( $fx===NULL ){
        $strOutput .=  "<style>.grid-item.albums{ width: " . $album_thumb_size . "px !important; }</style>\n";
    } else {
        $strOutput .=  "<style>.grid-item.albums{ width: " . $album_thumb_size . "px !important; height: " . $album_thumb_size . "px !important; }</style>\n";
    }

    //$intColumnWidth = $thumb_size + 20;
$intColumnWidth = $thumb_size * 1.3;
    /*
        echo "on grid<br>";
        echo '<pre>';
        print_r($hide_albums);
        echo '</pre>';
    */

    // Init masonry 
    //$strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"columnWidth\": ".$intColumnWidth.", \"isOriginLeft\": true, \"isFitWidth\": true }'>\n";
    //$strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"columnWidth\": figure, \"isOriginLeft\": true, \"isFitWidth\": true }'>\n";
      $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"isOriginLeft\": true, \"isFitWidth\": true }'>\n";

    if( $xml === false ) {
        echo 'Sorry there has been a problem with your feed.';
    } else {

        $xml->registerXPathNamespace('gphoto', 'http://schemas.google.com/photos/2007');
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');

        // register namespace to get access to total number of albums found
        $xml->registerXPathNamespace('opensearch', 'http://a9.com/-/spec/opensearch/1.1/');
        $totalResults = $xml->xpath( "opensearch:totalResults" );
        $total_num_albums = $totalResults[0];
    /*
        foreach( $totalResults as $total_num_albums ){
            //echo "totalResults: $total_num_albums<br>";
            //$total_num_albums = $val;
        }
    */

        $intCounter = 0;

        // loop over the albums
        foreach( $xml->entry as $feed ) {

            // get the data we need
            $title = $feed->title;

            // do not display if album name has been hidden
            if( !in_array( $title, $hide_albums ) ) {

                $gphoto = $feed->children( 'http://schemas.google.com/photos/2007' );
                $num_photos = $gphoto->numphotos; 
                $published = $feed->published; 
                $published = trim( $published );
                $published = substr( $published, 0, 10 );
                //$published = date( "d-m-Y", strtotime( $published ) );  // Change date format to dd-mm-yyyy  

                $group = $feed->xpath( './media:group/media:thumbnail' );
                $a = $group[0]->attributes(); // we need thumbnail path
                $id = $feed->xpath( './gphoto:id' ); // and album id for our thumbnail

                switch($fx) {
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

                // $strOutput .= "<div class='thumbnail grid-item albums' style=\"" . $thumb_size . "\">\n";
                if( $fx === NULL ){
                    $strOutput .= "<div class='thumbnail grid-item albums' style='width:" . $thumb_size . "px;'>\n";
                } else {
                    $strOutput .= "<div class='thumbnail grid-item albums' style='width:" . $thumb_size . "px; height:" . $thumb_size . "px;'>\n";
                }

                    $strOutput .=  "<div class='thumbimage'>\n";

                    if ( get_option( 'permalink_structure' ) ) { 
                        $urltitle = urlencode( $title );                        
                        $strOutput .=  "<a href='" . $results_page . "?cws_album=$id[0]&cws_album_title=$urltitle'><img alt='$title' src='$a[0]' title='$title' />\n";
                        
                        // if fx value has been set in shortcode...
                        if( $fx ) {

                            $strOutput .= "<figcaption>\n";

                            // Get the title ready...
                            if( $show_title ) {
                                $strOutput .= "<h6><span>$title</span></h6>\n";
                            }

                            if( $show_details ) {
                                $strOutput .= "<p><small>Images: $num_photos</small><br/><small>$published</small></p>\n";
                            }

                            $strOutput .= "</figcaption>";
                        }
                        $strOutput .= "</a>\n";

                    } else {
                        $urltitle = urlencode( $title );
                        $strOutput .=  "<a href='" . $results_page . "&cws_album=$id[0]&cws_album_title=$urltitle'><img alt='$title' src='$a[0]' title='$title' />\n";
                   
                        // if fx value has been set in shortcode...
                        if( $fx ) {

                            $strOutput .= "<figcaption>\n";

                            // Get the title ready...
                            if( $show_title ) {
                                $strOutput .= "<h6><span>$title</span></h6>\n";
                            }

                            if( $show_details ) {
                                $strOutput .= "<p><small>Images: $num_photos</small><br/><small>$published</small></p>\n";
                            }

                            $strOutput .= "</figcaption>";
                        }
                        $strOutput .= "</a>\n";
                    }

                    $strOutput .=  "</div>\n"; // End .thumbimage

                    // if NO fx value has been set in shortcode...
                    if( $fx === NULL ) {

                        if( $show_details || $show_title ) {
                            $strOutput .=  "<div class='details'><ul>\n";
                        }

                        if ( $show_title ) {
                            if ( get_option( 'permalink_structure' ) ) { 
                                $urltitle = urlencode( $title );
                                $strOutput .=  "<li class='title'><a href='" .$results_page . "?cws_album=$id[0]&cws_album_title=$urltitle'>$title</a></li>\n";
                            } else {
                                $urltitle = urlencode( $title );
                                $strOutput .=  "<li class='title'><a href='" .$results_page . "&cws_album=$id[0]&cws_album_title=$urltitle'>$title</a></li>\n";
                            }
                        }

                        if ( $show_details ) {
                           
                            //$strOutput .=  "<ul>\n";

                            $strOutput .= "<li><small>Images: $num_photos</small>";
                            $strOutput .= "<li><small>$published</small>";

                            /*if ( $desc != "" ) { 
                                $strOutput .= " $desc ";
                            }*/
                            /*
                            if ( $location != "" ) {
                                $strOutput .= " $location </li>\n";
                            }*/
                        }

                        if( $show_details || $show_title ) {
                            $strOutput .=  "</ul>\n";
                            $strOutput .=  "</div>\n"; // End .details
                        }
                    }

                $strOutput .=  "</div>\n"; // End .thumbnail
                $strOutput .= "</figure>\n";
                
                $intCounter++;

            } // end do not display
        }

    } // end else

    $strOutput .=  "</div>\n"; // added to fix sidebar bug that knocked positioning off ...

    $strOutput .=  "</div>\n"; // End .grid