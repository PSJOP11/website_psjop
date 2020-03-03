<?php
/*
echo '<pre>';
print_r( $response[0]['mediaItems'] );
//print_r($settings['dimensions']['maxWidth']);
echo '</pre>';
*/
//die();

    $strOutput = '';
    $cws_album_title    = '';

    // moved this here to fix WP 5 'update failed' bug
    if( $plugin->get_isPro() == 1 ){
        $pathPhotoswipe = plugin_dir_path( __FILE__ ) . '../partials_pro/photoswipe.html';
        $strPhotoswipe = file_get_contents($pathPhotoswipe);
        //var_dump($strPhotoswipe);
        $strOutput .= $strPhotoswipe;
    }

    if( isset( $_GET['cws_album_title'] ) ){ $cws_album_title =  stripslashes( $_GET[ 'cws_album_title' ] ); }
    if ( $cws_album_title ) { $strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n"; }

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

    $strOutput .= "<style type=\"text/css\" scoped>.grid .thumbnail{ width:".$args['thumb_size']."px;} .grid-item.images .thumbnail { width:".$args['thumb_size']."px !important; } </style>";
    $strOutput .= "<div class=\"mygallery grid\">";
    $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"isFitWidth\": true }'>\n";

    // Loop over the images
    if( isset($response[0]['mediaItems']) ) {
    for ( $i = 0; $i <= count( $response[0]['mediaItems'] ) -1; $i++ ) {
        /*
        $strOutput .= "ID: " . $response[0]['mediaItems'][$i]['id'] . "<br>";
        $strOutput .= "Description: " . $response[0]['mediaItems'][$i]['description'] . "<br>";
        $strOutput .= "ProductUrl: " . $response[0]['mediaItems'][$i]['productUrl'] . "<br>";
        $strOutput .= "baseUrl: " . $response[0]['mediaItems'][$i]['baseUrl'] . "<br>";                
        $strOutput .= "mimeType: " . $response[0]['mediaItems'][$i]['mimeType'] . "<br>";

        // media meta data
        $strOutput .= "cameraMake: " . $response[0]['mediaItems'][$i]['mediaMetadata']['photo']['cameraMake'] . "<br>";
        $strOutput .= "cameraModel: " . $response[0]['mediaItems'][$i]['mediaMetadata']['photo']['cameraModel'] . "<br>";
        $strOutput .= "focalLength: " . $response[0]['mediaItems'][$i]['mediaMetadata']['photo']['focalLength'] . "<br>";
        $strOutput .= "apertureFNumber: " . $response[0]['mediaItems'][$i]['mediaMetadata']['photo']['apertureFNumber'] . "<br>";
        $strOutput .= "isoEquivalent: " . $response[0]['mediaItems'][$i]['mediaMetadata']['photo']['isoEquivalent'] . "<br>";
        */
        $desc           = '';
        $description    = '';
        $title          = pathinfo( $response[0]['mediaItems'][$i]['filename'], PATHINFO_FILENAME );                        // Filename without the extension to use as title    

        $strOriginalImgwidth = $response[0]['mediaItems'][$i]['mediaMetadata']['width'];
        $strBaseUrl = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( $strOriginalImgwidth, null, 0 );              
  

        if( isset($response[0]['mediaItems'][$i]['description']) ) { 
            $description    = $response[0]['mediaItems'][$i]['description'];                                                    // Description
        }
                                                       // Description
        if( $plugin->get_isPro() == 1 ){
            $imgUrlBIG      = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( $args['imgmax'], null, 0 );        // Big image for overlay, setting a max width
        } else {
            $imgUrlBIG      = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions( 800, null, 0 );        // Big image for overlay, setting a max width
        }
        $imgUrl         = $response[0]['mediaItems'][$i]['baseUrl'] . $google_photos->addDimensions($args['thumb_size'], $args['thumb_size'], $args['crop']);

        $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$i."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";   
        $strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$i."\" style='width:".$args['thumb_size']."px;'>\n";

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
// echo "width: $width<br>ratio_orig: $ratio_orig<br>height: $height<br>";
        // include overlay data attributes required for Photo Swipe
        $strOutput .= "<a itemprop='$imgUrlBIG' data-size='{$width}x{$height}' data-index=\"".$i."\" class='result-image-link' href='$imgUrlBIG' data-lightbox='result-set' data-title='$title'>\n";
        $strOutput .="<img data-index=\"".$i."\" class='result-image' src='" . $imgUrl . "' alt='$title'/>";

            // if fx value has been set in shortcode...
            if( $args['fx'] ) {
                $strOutput .= "<figcaption>\n";
                // Get the title ready...
                if( $args['show_title'] ) {
                    $strOutput .= "<h6><span>$title</span></h6>\n";
                }

                // Get the details ready...
                if( $args['show_details'] ) {
                    $strOutput .= "<p>$description[0]</p>\n";
                    $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
                } else {
                    $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
                }

                $strOutput .= "</figcaption>";
            } 
            // No FX
            else {
                // Caption used in lightbox (when no $fx defined)                
                $strOutput .= "<figcaption style=\"display:none;\">\n";
                $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
                $strOutput .= "</figcaption>";               
            }
            
            $strOutput .= "</a>\n";

            // if NO fx value has been set in shortcode...
            if( $args['fx'] === NULL ) {

                // Create this div only if title or details are to be shown
                if( $args['show_title']  || $args['show_details'] ) {
                    $strOutput .= "<div class='details'>\n";
                    $strOutput .= "<ul>\n";
                }

                if ( $args['show_title'] ) {
                    $strOutput .= "<li class='title'>{$title}</li>\n";
                }

                if ( $args['show_details'] && $description != ""  ) {
                    $output = "<li><small>{$description}</small>";
                    $strOutput .= $output;
                }

                if( $args['show_title'] || $args['show_details'] ) {
                    $strOutput .= "</ul>\n";
                }

                // Close this div only if title or details are to be shown
                if( $args['show_title'] || $args['show_details'] ) {
                    $strOutput .= "</div>\n"; // End .details
                }

            }

        $strOutput .= "</div>"; //end thumbnail

            // Display link to original image file
            if( $plugin->get_isPro() == 1 && $args['enable_download'] == true ) {
                $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $strBaseUrl . "\"><button class=\"cws_download\" title=\"Download\"></button></a></div>";
            }

        $strOutput .= "</figure>\n";
    }

} // if isset mediaItems
  
    $strOutput .= "</div>"; // End .grid
    $strOutput .= "</div>"; // End #mygallery    