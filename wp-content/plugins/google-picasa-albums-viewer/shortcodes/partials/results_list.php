<?php
    $strOutput = "";
    $strOutput .=  "<div class='listview'>\n";

    $strFXStyle = '';
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
 

    // Add 20% extra to thumbsize...
    $my_thumb_size = $thumb_size * 1.2;

    // $strOutput .= "<div id=\"mygallery\" class=\"grid\">";
    $strOutput .= "<div class=\"mygallery grid\">";

    if( $xml === false ) {
        //echo 'Sorry there has been a problem with your feed.';
    } else {
        // Define NamesSpaces
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/'); // define namespace media

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
            //echo "width: " . ($content[0]['width']) . " - height: " . ($content[0]['height']) . "<br>";

            // Get the description
            if( str_word_count( $description[0] ) > 0 ) { $description = $description[0]; }

            // Extract the thumb link
            $a = $group[0]->attributes(); 

            // Convert "content" attributes into array            
            $b = $feed->content->attributes();

$desc = '';
                if( $show_details || $show_title ){

                    if ( $show_title ) {
                        $desc = $title;
                    }

                    if ( $show_title && $show_details ) {
                        $desc .= " - ";
                    }

                    if ( $show_details ) {
                        $desc .= "$description[0]";
                    }

                }



            $strOutput .= "<figure class=\"effect-$strFXStyle\" data-index=\"".$intCounter."\" itemprop=\"associatedMedia\" itemscope itemtype=\"http://schema.org/ImageObject\">\n";   

                $strOutput .=  "<div class='thumbimagexxx thumbnail grid-item' data-index=\"".$intCounter."\" style='width: " . $thumb_size . "px;'>\n";

                    // $strOutput .=  "<a class='example-image-link' href='" . $b['src'] . "' data-lightbox='example-set' data-title='" . $feed->title . "'><img alt='" . $feed->title . "' src='" . $a['url'] . "' title='" . $feed->title . "' /></a>\n";
                    //$strOutput .=  "<a itemprop=\"contentUrl\" data-size=\"". $content[0]['width'] ."x".$content[0]['height']."\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $b['src'] . "' data-lightbox='example-set' data-title='" . $feed->title . "'><img alt='" . $feed->title . "' src='" . $a['url'] . "' title='" . $feed->title . "' /></a>\n";
                    $strOutput .=  "<a itemprop=\"contentUrl\" data-caption=\"" . $desc .  "\" data-size=\"". $content[0]['width'] ."x".$content[0]['height']."\" data-index=\"".$intCounter."\" class='result-image-link' href='" . $b['src'] . "' data-lightbox='example-set' data-title='" . $feed->title . "'>\n";
                    $strOutput .= "<img alt='" . $feed->title . "' src='" . $a['url'] . "' title='" . $feed->title . "' />";

                    $strOutput .= "</a>";

                $strOutput .=  "</div>\n"; // End .thumbimage





                $strOutput .=  "<div class='details' style=\"height:".$thumb_size."px; margin-left:".$my_thumb_size."px; margin-top:20px;\">\n";



                    $strOutput .=  "<ul>\n";

                    if ( $show_title ) {
                        $strOutput .=  "<li class='title'>$title</li>\n";
                    }

            if ( $show_details ) {
                // limit number of word to prevent layout issues
                // TODO: make an option
                //$strTruncatedText = wp_trim_words( $description, 40 );
                $strTruncatedText = wp_trim_words( $description[0], 40 );

                    if ( $description != "" ) { 
                        $strOutput .= "<li class='detail-meta'>$strTruncatedText</li>\n";
                    }
                //$strOutput .=  $output;
            }


//$strOutput .= "<li class='detail-caption' style='display:none;'>$desc</li>\n";


                    $strOutput .=  "</ul>\n";

            // Display link to original image file
            if( $plugin->get_isPro() == 1 && $enable_download == true ){
                $origUpload = str_replace( "/s$imgmax/", "/d/", $b['src'] );
                // $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $origUpload . "\"><span>Download Image</span></a></div>";
                $strOutput .= "<div class='download details'><a target=\"_blank\" download=\"" . $feed->title . "\" href=\"" . $origUpload . "\"><button class=\"cws_download\" title=\"Download\"></button></a></div>";
                
            }
                    
                $strOutput .=  "</div>\n"; // End .details
            //$strOutput .=  "</div>\n"; // End .thumbnail

            $strOutput .= "</figure>\n";
            $intCounter++;

        } // end foreach $feed

        //Render photoswipe html content
        //$strOutput .= file_get_contents( plugin_dir_path( __FILE__ ) . '/../partials_pro/photoswipe.html' );  

    }

$strOutput .= "</div>"; // End #mygallery    

    $strOutput .=  "</div>\n"; // End .listview