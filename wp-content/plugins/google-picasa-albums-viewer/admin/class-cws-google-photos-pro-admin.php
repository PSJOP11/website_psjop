<?php

/**
 * The Google Photos API functionality of the plugin.
 *
 * @link       http://cheshirewebsolutions.com/
 * @since      2.0.0
 *
 * @package    CWS_Google_Picasa_Pro
 * @subpackage CWS_Google_Picasa_Pro/admin
 */

class CWS_Google_Photos_Pro extends CWS_Google_Picasa_Pro_Admin {

    function __construct(){
    }

    /**
     * Call wp_remote_request
     *
     * @param array $query_urls, array $AccessToken, $options, str $results_page
     * @return array
     */
    private function getRequest( $query_urls, $AccessToken, $options, $results_page, $flag ) {

        $out = array(); // array to hold results of processResponse()

        foreach ( $query_urls as $query_url => $options ) {

            // if we have album id, i.e we are getting images within an album
            // if( isset( $options['albumId'] ) ) {
            if( !empty( $options['albumId'] ) && $flag == 'images' ) {
                
                $myArray = array();
                $myArray['access_token']    = $AccessToken['access_token'];
                $myArray['albumId']         = $options['albumId'];
                $myArray['pageToken']       = $options['pageToken'];
                $myArray['pageSize']        = $options['pageSize'];
                // echo '<pre>';
                // print_r($myArray);
                // echo '</pre>';
                // die();
                $query_url = add_query_arg( $myArray, $query_url );
                //var_dump($query_url);die();
            }
            
            // else {
            if( $flag == 'albums' ) {
                $myArray = array();
                $myArray['access_token']    = $AccessToken['access_token'];
                $myArray['pageToken']       = $options['pageToken'];
                $myArray['pageSize']        = $options['pageSize'];
                //var_dump($myArray);//die();
                $query_url = add_query_arg( $myArray, $query_url );

                //var_dump($query_url); die();
            }
            
            // $response = wp_remote_request( $query_urlx, $options ); // use this to check $this->error();
            $response = wp_remote_request( $query_url, $options );
            
            if ( !is_wp_error( $response ) ) {
                $body = wp_remote_retrieve_body( $response );
/*
echo '<pre>';
print_r ($response);
echo '<pre>';
die();
*/
                // process response
                $out[] = $this->processResponse($body, $results_page);
            }
            else {
                // process any errors
                $out[] = $this->error($response->get_error_message());
            }
        }

        return $out;
    }


    // For albums & Photos
    private function processResponse( $body, $results_page ){

        $settings = array();
        $settings['dimensions']['maxWidth'] = '250'; // guess these would be passed in via shortcode eventually therefor in partial
        $settings['dimensions']['maxHeight'] = '250';
        $settings['crop'] = 0;
        $settings['results_page'] = $results_page;

        //$strOutput = '';

        $body = json_decode($body,true);

        // can expand this out later to display images withing album?
        if ( isset( $body['albums'] ) || isset( $body['sharedAlbums'] ) ) {
            // $albums = isset( $body['albums'] ) ? $body['albums'] : $body['sharedAlbums'];

            //return $albums; // also need a nextPageToken here
            return $body;
        } 
        // when displaying images in an album for example
        else if ( isset( $body['mediaItems'] ) ){

            // need to return albums and nextPageToken!
                /*
                echo '<pre>';
                print_r($body['mediaItems']);
                echo '</pre>';
                die();
                */
            return $body; // by returning body we get nextPageToken too
            // return $body['mediaItems']; // return the images in a selected album!
        }

        //return $strOutput;
    }


    // https://developers.google.com/photos/library/guides/access-media-items#base-urls
    public function addDimensions($width, $height, $crop) {
        $strMaxWidth = $width;
        $strMaxHeight = $height;

        $var_we_have_width = (!empty($strMaxWidth) ? 'w' . $strMaxWidth : false);
        $var_we_have_height = (!empty($strMaxHeight) ? 'h' . $strMaxHeight : false);

        if(!empty($var_we_have_width ) || !empty($var_we_have_height)) {
            $strDimensions = '=';

            if(!empty($var_we_have_width ) && !empty($var_we_have_height)) {
                $strSeperator = '-';
                $strDimensions .= $var_we_have_width . $strSeperator . $var_we_have_height;
            }
            elseif(!empty($var_we_have_width ) && empty($var_we_have_height)){
                $strDimensions .= $var_we_have_width ;
            }
            elseif(empty($var_we_have_width ) && !empty($var_we_have_height)){
                $strDimensions .= $var_we_have_height;
            }
            
            $addCrop = $crop ? '-c' : ''; // Check whether to crop image

            return $strDimensions . $addCrop; 
        }
    }

    public function error($strMsg){

        $strOutput = "<div id='cws-error'>ERROR: $strMsg</div>";
        return $strOutput;
    }

  /**
   * Get list of Albums for authenticated user via Google Photos API.
   * Integrating for better Shared Albums Support
   *
   * @since    3.1.0
   */  
    //  public function getAlbumListGooglePhotos( $AccessToken, $album_thumb_size, $show_title, $cws_page, $num_image_results, $visibility) {    
    public function getAlbumListGooglePhotos( $AccessToken, $results_page, $num_results,$nextPageToken, $access = 'own'  ) {    

//var_dump($access);
//die();

        if( $num_results > 50 || empty( $num_results ) ) { $num_results = 50; }

        $token = get_option( 'cws_gpp_access_token' );

        // options to pass to wp_remote_request
        $options = array();
        $options['method'] = 'GET';
        $options['pageSize'] = $num_results;
        $options['sslverify'] = FALSE;
        $options['access'] = 'shared' ;       
        $options['pageToken'] = $nextPageToken ;

// var_dump($access);
// die();

$options['access'] = $access;

        $query_urls = array();

        // used these when calling with curl
        // might need to use curl if using 'filters'
        //$query_urls[] = 'https://photoslibrary.googleapis.com/v1/sharedAlbums';
        //$query_urls[] = 'https://photoslibrary.googleapis.com/v1/albums';

        // used these with wp_remote_request
        // Decide which albums to show
        if ($options['access'] == 'shared') {
            $query_urls['https://photoslibrary.googleapis.com/v1/sharedAlbums'] = $options;
        }
        /*
        // need to work out a way to combine these and have pagination
        if ($options['access'] == 'all') {
            $query_urls['https://photoslibrary.googleapis.com/v1/sharedAlbums'] = $options;            
            $query_urls['https://photoslibrary.googleapis.com/v1/albums'] = $options;
        }*/
        if ($options['access'] == 'own') {
            $query_urls['https://photoslibrary.googleapis.com/v1/albums'] = $options;
        }

        // moved above block of code to function getRequest($query_urls, $AccessToken, $options)
        //$out = $this->getRequest( $query_urls, $AccessToken, $options, $results_page );
        $out = $this->getRequest( $query_urls, $AccessToken, $options, $results_page, $flag='albums' ); // add flag to stop GAPI from returning images if album id not present or not recognised.

        return $out; // deal with errors later fuckwit
    }

    function getAlbumImagesGooglePhotos( $AccessToken, $thumb_size, $show_title, $cws_page, $num_results, $cws_album, $imgmax, $theme, $nextPageToken ) {

        //echo '<h1>Calling getAlbumImagesGooglePhotos() for album id: ' . $cws_album . '</h1>';

        if( $num_results > 50 || empty( $num_results ) ) { $num_results = 50; }

        // options to pass to wp_remote_request
        $options = array();
        $options['method'] = 'POST';
        $options['pageSize'] = $num_results; // isn't reliable, Google do their best but WTF!?
        $options['sslverify'] = FALSE;
        $options['albumId'] = urlencode($cws_album) ;       
        $options['pageToken'] = $nextPageToken;

        $query_urls['https://photoslibrary.googleapis.com/v1/mediaItems:search'] = $options;

       // $query_urls['https://photoslibrary.googleapis.com/v1/albums/AF1QipMfKFlmjufoKJkzZJl5k4guIlUj_hJ5S8c3YOkU'] = $options;
        // https://developers.google.com/photos/library/reference/rest/v1/mediaItems/search?apix_params=%7B%22resource%22%3A%7B%22albumId%22%3A%22AF1QipMfKFlmjufoKJkzZJl5k4guIlUj_hJ5S8c3YOkU%22%7D%7D
        // https://photoslibrary.googleapis.com/v1/albums/{albumId}

        // $out = $this->getRequest( $query_urls, $AccessToken, $options, $results_page = null ); // don't need a results page when showing album images!
        $out = $this->getRequest( $query_urls, $AccessToken, $options, $results_page = null, $flag = 'images' ); // add flag to stop GAPI from returning images if album id not present or not recognised.

        // var_dump($options);
        return $out;
    }

}