<?php
    $desc = '';
    $strOutput = '';
    $cws_album_title = '';

    // moved this here to fix WP 5 'update failed' bug
    if( $plugin->get_isPro() == 1 ){
        $pathPhotoswipe = plugin_dir_path( __FILE__ ) . '../partials_pro/photoswipe.html';
        $strPhotoswipe = file_get_contents($pathPhotoswipe);
        //var_dump($pathPhotoswipe);
        $strOutput .= $strPhotoswipe;
    }

    if( isset( $_GET['cws_album_title'] ) ){
        $cws_album_title =  stripslashes( $_GET[ 'cws_album_title' ] );
    }
 
    if ( $album_title ) {
        $strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n";       
    }
    $strOutput .= "<style type=\"text/css\" scoped>.grid .thumbnail{ width:".$thumb_size."px;} .grid-item.images .thumbnail { width:".$thumb_size."px !important; } </style>";

    // $lala = $thumb_size + 35;
    $lala = $thumb_size * 1.3;

    // $strOutput .= "<div id=\"mygallery\" class=\"grid\">";
    $strOutput .= "<div class=\"mygallery grid\">";

        //$strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"columnWidth\": " . $lala . ", \"isFitWidth\": true }'>\n";
        //$strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"columnWidth\": figure, \"isFitWidth\": true }'>\n";
        $strOutput .= "<div class='grid js-masonry' data-masonry-options='{ \"itemSelector\": \"figure\", \"isFitWidth\": true }'>\n";

    if( $xml === false ) {
        //echo 'Sorry there has been a problem with your feed.';
    } 
    else 
    {
        // Define NamesSpaces
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
        $xml->registerXPathNamespace('gphoto', 'http://schemas.google.com/photos/2007');
        // echo $xml->asXML();die();
        
        // Get total number of albums found
        $num_photos = $xml->xpath( "gphoto:numphotos" );
        $num_photos = $num_photos[0];

        $intCounter = 0;

        // Loop over the images
        foreach( $xml->entry as $feed ) {

            // get the data we need
            // Get the filename without the extension to use as title
            $title = $feed->title;
            $title = pathinfo( $title, PATHINFO_FILENAME);

            // Get the thumbnail tag
            $group = $feed->xpath( './media:group/media:thumbnail' );
            $description = $feed->xpath( './media:group/media:description' );

            //https://developers.google.com/picasa-web/docs/2.0/reference
            $content = $feed->xpath( './media:group/media:content' );
            // echo "width: " . ($content[0]['width']) . " - height: " . ($content[0]['height']) . "<br>";

            // Extract the thumb link
            $a = $group[0]->attributes(); 

            // Convert "content" attributes into array
            $b = $feed->content->attributes(); 

            // Album id for our thumbnail
            $id = $feed->xpath('./gphoto:id'); 
            // var_dump($b['src']);
            //print_r($feed->asXML());

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
                $strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$intCounter."\" style='width:" . $thumb_size . "px;'>\n";
            } else {
                $strOutput .= "<div class='thumbnail grid-item images' data-index=\"".$intCounter."\" style='width:" . $thumb_size . "px; height:" . $thumb_size . "px;'>\n";
            }

            //$strOutput .= "<a itemprop=\"contentUrl\" data-size=\"". $content[0]['width'] ."x".$content[0]['height']."\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $b['src'] . "' data-lightbox='result-set' data-title='$title'><img data-index=\"".$intCounter."\" class='result-image' src='" . $a['url'] . "' alt='$title'/></a>\n";
            // $strOutput .= "<a itemprop=\"contentUrl\" data-size=\"". $content[0]['width'] ."x".$content[0]['height']."\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $b['src'] . "' data-lightbox='result-set' data-title='$title'><img data-index=\"".$intCounter."\" class='result-image' src='" . $a['url'] . "' alt='$title'/>\n";
            $strOutput .= "<a itemprop=\"contentUrl\" data-size=\"". $content[0]['width'] ."x".$content[0]['height']."\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $b['src'] . "' data-lightbox='result-set' data-title='$title'>\n";

            $strOutput .="<img data-index=\"".$intCounter."\" class='result-image' src='" . $a['url'] . "' alt='$title'/>";
//var_dump($description);
//die();

                if( $show_details || $show_title ){
                    $desc = '';

                    if ( $show_title ) {
                        $desc = $title;
                    }

                    if ( $show_title && $show_details ) {
                        
                        if( strlen($description[0]) > 0 ){
                            $desc .= " - ";
                        }
                    }

                    if ( $show_details ) {
                        $desc .= "$description[0]";
                    }

                }

            // if fx value has been set in shortcode...
            if( $fx ) {
                $strOutput .= "<figcaption>\n";
                // Get the title ready...
                if( $show_title ) {
                    $strOutput .= "<h6><span>$title</span></h6>\n";
                }

                // Get the details ready...
                if( $show_details ) {
                    $strOutput .= "<p>$description[0]</p>\n";
                    $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";

                    // $strOutput .= "<p>$desc</p>\n";
                } else {
                    // $strOutput .= "<p style=\"display:none;\">$description[0]</p>\n";
                    $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
                }

                $strOutput .= "</figcaption>";
            } else {
                // Caption used in lightbox (when no $fx defined)                
                 $strOutput .= "<figcaption>\n";

                    $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
                $strOutput .= "</figcaption>";               
            }
            
            $strOutput .= "</a>\n";

            // if NO fx value has been set in shortcode...
            if( $fx === NULL ) {

                // Create this div only if title or details are to be shown
                if( $show_title  || $show_details ) {
                    $strOutput .= "<div class='details'>\n";
                    $strOutput .= "<ul>\n";
                }
                if ( $show_title ) {
                    $strOutput .= "<li class='title'>$title</li>\n";
                }

                if ( $show_details && $description != ""  ) {
                    $output = "<li><small>$description[0]</small>";
                    $strOutput .= $output;
                }

                if( $show_title || $show_details ) {
                    $strOutput .= "</ul>\n";
                }

                // Close this div only if title or details are to be shown
                if( $show_title || $show_details ) {
                    $strOutput .= "</div>\n"; // End .details
                }


            // Display link to original image file
            /*if( $plugin->get_isPro() == 1 && $enable_download == true ){
                $origUpload = str_replace( "/s$imgmax/", "/d/", $b['src'] );
                // $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $origUpload . "\"><span>Download Image</span></a></div>";
                $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $origUpload . "\"><button class=\"cws_download\" title=\"Download\"></button></a></div>";
                
            }*/

            }

            $strOutput .= "</div>"; // End .thumbnail


            // Display link to original image file
            if( $plugin->get_isPro() == 1 && $enable_download == true ){
                $origUpload = str_replace( "/s$imgmax/", "/d/", $b['src'] );

                // $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $origUpload . "\"><span>Download Image</span></a></div>";
                $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $origUpload . "\"><button class=\"cws_download\" title=\"Download\"></button></a></div>";
            }

            $strOutput .= "</figure>\n";

            $intCounter++;

        } // foreach $feed    
  
    }

    $strOutput .= "</div>"; // End .grid

    $strOutput .= "</div>"; // End #mygallery    