<?php
/**
 * Shortcodes init
 * 
 * Init main shortcodes
 *
 * Copyright (c) 2011, cheshirewebsolutions.com, Ian Kennerley (info@cheshirewebsolutions.com).
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//include_once('shortcode-albums.php');								// Displays the albums
//include_once('shortcode-images-in-album.php');					// Display images in a specified album

include_once('shortcode-albums-google-photos.php');					// Displays the Google Photo albums (calling the google photos api)
include_once('shortcode-images-in-album-google-photos.php');		// Display images in a specified album

/**
 * Shortcode creation
 **/
//add_shortcode( 'cws_gpp_albums', 'cws_gpp_shortcode_albums' );
//add_shortcode( 'cws_gpp_images_in_album', 'cws_gpp_shortcode_images_in_album' ); 	// Display all images in a specified album, album id

// New shortcodes that connect to Google Photos API
add_shortcode( 'cws_gpp_albums_gphotos', 'cws_gpp_shortcode_albums_google_photos' ); // New Google Photos shortcode to include Shared albums
add_shortcode( 'cws_gpp_images_in_album_gphotos', 'cws_gpp_shortcode_images_in_album_google_photos' ); 	// Display all images in a specified album, album id

// Point old shortcodes to new functions
add_shortcode( 'cws_gpp_albums', 'cws_gpp_shortcode_albums_google_photos' );
add_shortcode( 'cws_gpp_images_in_album', 'cws_gpp_shortcode_images_in_album_google_photos' ); 