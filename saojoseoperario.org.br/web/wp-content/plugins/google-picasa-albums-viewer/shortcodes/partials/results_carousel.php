<?php
    wp_register_style( 'cws_gpp_cws_gpp_slick_carousel_css', plugins_url( '../../public/css/slick/slick.css',__FILE__ ) , '', 1 );
    wp_register_style( 'cws_gpp_cws_gpp_slick_carousel_css_theme', plugins_url( '../../public/css/slick/slick-theme.css',__FILE__ ) , '', 1 );
    wp_register_style( 'cws_gpp_cws_gpp_slick_lb_css', plugins_url( '../../public/css/slick-lightbox/slick-lightbox.css',__FILE__ ) , '', 1 );

    if ( function_exists( 'wp_enqueue_style' ) ) {
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_carousel_css' );
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_carousel_css_theme' );
        wp_enqueue_style( 'cws_gpp_cws_gpp_slick_lb_css' );
    } 

    // moved this here to fix WP 5 'update failed' bug
    if( $plugin->get_isPro() == 1 ){
        $pathPhotoswipe = plugin_dir_path( __FILE__ ) . '../partials_pro/photoswipe.html';
        $strPhotoswipe = file_get_contents($pathPhotoswipe);
        //var_dump($pathPhotoswipe);
        $strOutput .= $strPhotoswipe;
    }


    // Some naughty inline styles
    //$strOutput .=  "<style>.grid-item { width: " . $thumb_size . "px; height: " . $thumb_size . "px;  padding:1px;}</style>\n";

    // $strOutput .=  "<div id=\"mygallery\" class='carousel grid'>\n";
    $strOutput .=  "<div class='mygallery carousel grid'>\n";

    if(isset($_GET[ 'cws_album_title' ])){
        $cws_album_title = $_GET[ 'cws_album_title' ]; // $cws_album_title = get_query_var('cws_album_title');
        $strOutput .= "<div id='album_title'><h2>$cws_album_title</h2></div>\n";                        
    }

    $strOutput .= "<div class=\"multiple-items-sc\">\n";

    if( $xml === false ) {
        //echo 'Sorry there has been a problem with your feed.';
    } else {
        // Define NamesSpaces
        $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
        $xml->registerXPathNamespace('gphoto', 'http://schemas.google.com/photos/2007');
        // echo $xml->asXML();die();
        
        // Get total number of albums found
        $num_photos = $xml->xpath( "gphoto:numphotos" );
        $num_photos = $num_photos[0];
        //echo "num_photos: $num_photos<br>";

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
            // $strOutput .= "<div class=\"item\">\n";

            if( $fx===NULL ){
                $strOutput .=  "<div class=\"item\">\n";        
            } else {
                $strOutput .=  "<div class=\"item\" style=\"max-width:" . $thumb_size . "px; max-height:" . $thumb_size . "px !important;\">\n";    
            }    

            $strOutput .= "<a itemprop=\"contentUrl\" data-size=\"". $content[0]['width'] ."x".$content[0]['height']."\" data-index=\"".$intCounter."\" href='" . $b['src'] . "' data-largesrc='" . $b['src'] . "' data-title='' data-description=''>\n";
            $strOutput .= "<img src='" . $a['url'] . "' alt='' />\n";

$desc = '';
/*
if( $show_details || $show_title ){

    if ( $show_title ) {
        $desc .= "Title: ".$title;
    }

    if ( $show_title && $show_details ) {
        $desc .= " - ";
    }

    if ( $show_details ) {
        // $desc .= "$description[0]";
        $desc = $description[0];
    }

}
*/
            if( $show_details ){
                $desc = '';

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
                    $strOutput .= "<p>$description[0]</p>\n"; // displayed in carousel on hover
                    $strOutput .= "<p class=\"caption\" style=\"display:none;\">$desc</p>\n";
                } else {
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
                if ( $show_title ) { $strOutput .=  "<div id='album_title'><span>$title</span></div>\n"; }
                if ( $show_details ) { $strOutput .=  "<div id='album_details'><span>$desc</span></div>\n"; }
            }

                $strOutput .= "</div>\n"; // End .item
            $strOutput .= "</figure>\n";

            $intCounter++;

        } // foreach $feed    

    }

    $strOutput .= "</div>\n"; // End .multiple-items-sc
    $strOutput .= "</div>\n"; // End .carousel