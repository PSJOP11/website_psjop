<?php
/*
echo '<pre>';
print_r( $response[0]['mediaItems'] );
//print_r($settings['dimensions']['maxWidth']);
echo '</pre>';
*/
    $strOutput = "";
    $strOutput .=  "<div class='listview'>\n";

    // moved this here to fix WP 5 'update failed' bug
    if( $plugin->get_isPro() == 1 ){
        $pathPhotoswipe = plugin_dir_path( __FILE__ ) . '../partials_pro/photoswipe.html';
        $strPhotoswipe = file_get_contents($pathPhotoswipe);
        // var_dump($strPhotoswipe);
        $strOutput .= $strPhotoswipe;
    }

    $strFXStyle = '';
    $cws_album_title = '';

    if( isset( $_GET['cws_album_title'] ) ){ $cws_album_title =  stripslashes( $_GET[ 'cws_album_title' ] ); }
    
    if ( $cws_album_title ) {$strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n"; }
 
    // Add 20% extra to thumbsize...
    $my_thumb_size = $args['thumb_size'] * 1.2; // don't like this do I need it?

    $strOutput .= "<div class=\"mygallery grid\">";
    //$strOutput .= "<div class=\"mygallery list\">";


        // Loop over the images
        for ( $i = 0; $i <= count( $response[0]['mediaItems'] )-1; $i++ ) {

            $desc           = '';
            $description    = '';
            $title          = pathinfo( $response[0]['mediaItems'][$i]['filename'], PATHINFO_FILENAME );                        // Filename without the extension to use as title      

            $strOriginalImgwidth = $response[0]['mediaItems'][$i]['mediaMetadata']['width'];
            $strBaseUrl = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( $strOriginalImgwidth, null, 0 );              
  
            if( isset($response[0]['mediaItems'][$i]['description']) ) { 
                $description    = $response[0]['mediaItems'][$i]['description'];                                                    // Description
            }

            if( $plugin->get_isPro() == 1 ){
                $imgUrlBIG      = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( $args['imgmax'], null, 0 );        // Big image for overlay, setting a max width
            } else {
                $imgUrlBIG      = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( 800, null, 0 );        // Big image for overlay, setting a max width
            }
            $imgUrl         = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( $args['thumb_size'], $args['thumb_size'], $args['crop'] );   // Thumbnail image url with dimensions 

            // Get the description
            //if( str_word_count( $description ) > 0 ) { $description = $description; }
        
            // Stitch together details (title and decsription) used in overlay
            if( $args['show_details'] || $args['show_title'] ) {
                if ( $args['show_title'] ) { $desc = $title; }
                if ( $args['show_title'] && $args['show_details'] ) { $desc .= " - "; }
                if ( $args['show_details'] ) { $desc .= "$description"; }
            }

            // need to bring in media width and height here
            $width_orig     = $response[0]['mediaItems'][$i]['mediaMetadata']['width'];
            $height_orig    = $response[0]['mediaItems'][$i]['mediaMetadata']['height'];
            $ratio_orig     = $width_orig/$height_orig;                                     // work out ratio of original image
            $width          = $args['imgmax'];                                       
            $height         = $width / $ratio_orig;                                         // work out height for image 

            // Generate output
            $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$i."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";   
            $strOutput .= "<div class='thumbimage thumbnail grid-item' data-index=\"".$i."\" style='width: " . $args['thumb_size'] . "px;'>\n";                    
            $strOutput .= "<a itemprop='$imgUrlBIG' data-caption=\"" . $desc .  "\" data-size='{$width}x{$height}' data-index=\"".$i."\" class='result-image-link' href='$imgUrlBIG' data-lightbox='result-set' data-title='$title'>\n";
            $strOutput .= "<img alt='" . $title . "' src='" . $imgUrl . "' title='" . $title . "' />";
            $strOutput .= "</a>";
            $strOutput .= "</div>\n"; // End .thumbimage
            $strOutput .= "<div class='details' style=\"height:".$args['thumb_size']."px; margin-left:".$my_thumb_size."px; margin-top:20px;\">\n";

            $strOutput .=  "<ul>\n";

            if ( $args['show_title'] ) { $strOutput .=  "<li class='title'>$title</li>\n"; }

            if ( $args['show_details'] &&  $description != ""  ) {
                // limit number of word to prevent layout issues
                // TODO: make an option
                $strTruncatedText = wp_trim_words( $description, 40 );
                $strOutput .= "<li class='detail-meta'>$strTruncatedText</li>\n";
            }

            $strOutput .=  "</ul>\n";
         
            $strOutput .=  "</div>\n"; // End .details

            // Display link to original image file
            if( $plugin->get_isPro() == 1 && $args['enable_download'] == true ) {
                $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $strBaseUrl . "\"><button class=\"cws_download\" title=\"Download\"></button></a></div>";
            }

            $strOutput .= "</figure>\n";

        } // end foreach $feed

    $strOutput .= "</div>"; // End #mygallery    
    $strOutput .=  "</div>\n"; // End .listview