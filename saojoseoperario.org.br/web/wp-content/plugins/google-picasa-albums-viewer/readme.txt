=== Google Photos Gallery with Shortcodes ===

Contributors: nakunakifi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAZHU9ERY8W34
Tags: best gallery plugin, free gallery, gallery plugin, album, gallery, slideshow, photo, google photos, google picasa, image, images gallery, lightbox, justified gallery, picasa, picasa web, photo, photos
Requires at least: 3.0.1
Tested up to: 5.2.2
Stable tag: 3.2.3

The best Google Photos Gallery plugin to display your Google Photo Albums on your WordPress blog. It is fully responsive and looks awesome. 


== Description ==
The best Google Photos Gallery plugin to display your Google Photo Albums on your WordPress blog. It is fully responsive and looks awesome. Google Photo Gallery is based on Google Picasa API. Use the plugin to display your Google Photo (Picasa) Albums on your WordPress blog. Using the shortcodes it is simple to embed a single album or all your albums. Display albums in grid view, list view or carousel. Image lightbox supports touch devices (Pro) and is fully responsive.

https://www.youtube.com/watch?v=mOTFtKkt1nI

* You get various display options for photo albums and the images with albums.
* You can display Photo Albums in <a href="http://wordpress-plugin-google-photos.cheshirewebsolutions.com/display-albums-grid/">Grid View</a>
* You can display Photo Albums in <a href="http://wordpress-plugin-google-photos.cheshirewebsolutions.com/display-albums-list/">List View</a>
* You can display Photo Albums in <a href="http://wordpress-plugin-google-photos.cheshirewebsolutions.com/display-albums-carousel/">Carousel View</a> 
* You can display Images in Albums in <a href="http://wordpress-plugin-google-photos.cheshirewebsolutions.com/results-grid/?cws_album=6361735196361992737&cws_album_title=People">Grid View</a> 
* You can display Images in Albums in <a href="http://wordpress-plugin-google-photos.cheshirewebsolutions.com/results-list/?cws_album=6361735196361992737&cws_album_title=People">List View</a> 
* You can display Images in Albums in <a href="http://wordpress-plugin-google-photos.cheshirewebsolutions.com/results-carousel/?cws_album=6361735196361992737&cws_album_title=People">Carousel View</a> 
* You can display Images in Albums in <a href="http://wordpress-plugin-google-photos.cheshirewebsolutions.com/justified-image-gallery/">Justified Image Grid View</a>
* You can mix and match displays too. So you can show albums in carousel/grid/list and have the images in the album the user clicks on open up in grid view or list view or whatever!
* You can show/hide Album Title
* You can show/hide album details such as Number of images in Album and date published.
* You can show/hide Image Title
* You can show/hide Image Caption
* You can configure the carousel to autoplay, show/hide dots, slides to scroll, slides to show, show/hide arrows etc
* You can override nearly all general settings in each shortcode.
* You can enable/disable download original image link.
* There is no limitation to the number of albums and photos.
* The visitor can browse through the photos in each album you decide to publish
* You can add a Recent Albums Widget to the Sidebar that display your latest albums covers
* You can show Public / and or Private Albums


If you have suggestions for a new add-on, feel free to email me at info@cheshirewebsolutions.com.
Want regular updates? 
follow me on Twitter!
https://twitter.com/CheshireWebSol



== Prerequisites ==

1. PHP5.4 or above
2. cURL running on your web host


== Installation ==

1. Unzip into your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make your settings, Admin->Settings->Google Picasa
4. Use the 'Display Album' shortcode [cws_gpp_albums_gphotos access=own] on a page of your choice.
5. To display the album's images place the shortcode, [cws_gpp_images_in_album_gphotos] on a page
6. Update the shortcode used in step 4 to include the result_page option. [cws_gpp_albums_gphotos access=own results_page='page-slug-here']


== Frequently Asked Questions ==

= What if I don't want to display private (unlisted) albums? =

Simple select "Public" option from the 'Show which albums' dropdown menu.

= Does this work with Google Photos? =

Yes this plugin works with images and albums stored and created in Google Photos too.

= Can Users Download the Original Image File =

Yes this is an option on the <a href="http://www.cheshirewebsolutions.com/?utm_source=cws_gpp_config&utm_medium=text-link&utm_content=readme&utm_campaign=cws_gpp_plugin" rel="friend">Pro version</a> 

= The Cache Doesn't Seem to be Working =

Log in to you web host and check the cache/ folder is writable by the web server.


== Screenshots ==

1. An example of Google Photos Album Grid View.
2. An example of Google Photos Album List View.
3. This is the default settings page. 
4. This is an eaxmple of the Albums shortcode [cws_gpp_albums_gphotos access=own].
5. This is an example of the lightbox displaying photo you clicked on.


== Shortcodes ==

* [cws_gpp_albums_gphotos access=own] Will display your albums.

* [cws_gpp_images_in_album_gphotos] This is a place holder to display the results of an album that has been clicked on, on a separate page.

* [cws_gpp_images_by_albumid_gp id='6625618881344302193'] Will display images in the album specified via the 'id' attribute. You must use album id from Album ID helper Page


== Shortcode Usage ==

* Display Albums Covers in a Carousel
[cws_gpp_albums_gphotos access=own theme=carousel dots=1 slidestoshow=3 slidestoscroll=1 autoplay=1 arrows=1 results_page=results-carousel show_details=1 show_title=1 num_results=6 hide_albums='Profile Photos,Auto Backup']

* Display Album Covers in a Grid View
[cws_gpp_albums_gphotos access=own theme=grid results_page=results-grid show_title=1 show_details=1 num_results=6 hide_albums='Auto Backup,Profile Photos']

* Display Album Covers in a List View
[cws_gpp_albums_gphotos access=own theme=list results_page=results-list show_title=1 show_details=1 thumb_size=185 num_results=3]

* Display Images from Clicked Album Cover in a Carousel View
[cws_gpp_images_in_album_gphotos theme=carousel show_title=0 thumbsize=150]

* Display Images from Clicked Album Cover in a Grid View
[cws_gpp_images_in_album_gphotos theme=grid show_title=1 show_details=1]

* Display Images from Clicked Album Cover in a List View
[cws_gpp_images_in_album_gphotos theme=list show_title=1 show_details=1 num_results=13 thumb_size=250]

* Display Images in a Specific Album, see 'Album Shortcodes' page for shortcodes complete with your Album IDs. Only one album per page.(PRO VERSION ONLY)
[cws_gpp_images_by_albumid_gp id=5218507736478682657 theme=grid show_title=0 show_details=0]




There are 2 main shortcodes:

* [cws_gpp_albums_gphotos access=own]
* [cws_gpp_images_in_album_gphotos]


Each shortcode will work as is by using the default values saved on the plugin settings page.

The defaults can be overridden by placing attribites on the shortcode below are some examples.


*[cws_gpp_albums_gphotos access=own]*

Attributes descriptions and examples

theme='grid|list|carousel'
results_page='album-results|page-slug-here'
show_title='1|0'
show_details='1|0'
thumb_size='150'
num_results='9'
hide_albums='Auto Backup,name of album here'

For example
[cws_gpp_albums_gphotos access=own theme='grid' results_page='results-list' show_title=1 show_details=1 thumb_size='50' num_results='9']



*[cws_gpp_images_in_album_gphotos]*

theme='grid|list|carousel'
album_title='1|0'
show_title='1|0'
show_details='1|0'
thumb_size='150'
num_results='9'
crop=1

For example
[cws_gpp_images_in_album_gphotos theme='grid' album_title=1 show_title=1  show_details=1 crop=1]


== Credits ==

* Google Picasa Viewer - Ian Kennerley, http://nakunakifi.com 
* Google PHP Client Library (https://developers.google.com/api-client-library/php/)
* Slick Carouel (http://kenwheeler.github.io/slick/)
* Slick Carousel Lightbox (https://github.com/mreq/slick-lightbox)
* Lightbox2 (http://lokeshdhakar.com/projects/lightbox2)
* Masonry (http://desandro.github.io/masonry/)
* Cache - http://www.jongales.com/
* Justified Image Grid - Miro Mannino
* Photoswipe - Dmitry Semenov

== Changelog ==

= 3.2.3 =
* Fixed bug where Google API returns images if album id not specified

= 3.2.2 =
* Fixed image light box bug, dimensions was not honouring Lightbox image size in settings

= 3.2.1 =
* Updated incorrect shortcode examples
* Fixed warnings in logs
* Fixed bug where number larger than 50 return no results
* Fixed 'Next' button css in Album ID Finder page
* Trim client id / client secret of space characters
* Improved Album ID finder, defaults to own

= 3.2 =
* Removed Picasa Shortcodes
* Added API client id and client secret to fix Error 429
* Fixed Download Original Link
* Fixed fx=style* option from effecting 'click' in some browsers
* Added default setting for Lighbox size
* Various other fixes and improvements
* Point old shortcodes to new Google Photos API functionality, so old shortcodes should still work
* yada yada yada

= 3.1.5 =
* Fix broken link on album thumbnail in list view

= 3.1.4 =
* Fix remaining WP 5 related 'update failed' when update post / page

= 3.1.3 =
* Fix WP 5 related 'update failed' when update post / page

= 3.1.2 =
* Fix layout bugs
* Fix num_results limit for new shortcode [cws_gpp_images_by_albumid_gp]

= 3.1.1 =
* Add configurable extension to allow access to Picasa API, available until 15 March 2019.

= 3.1 =
* Added three new shortcodes to consume Google Photos API feed
* [cws_gpp_albums_gphotos] to list Albums from Google Photos API, use option access=own or access=shared, to specify albums returned
* [cws_gpp_images_in_album_gphotos] to list images in selected album
* [cws_gpp_images_by_albumid_gp id='6625618881344302193'] to display images withing a specified album

= 3.0.13b =
* Allow multiple album reults in same page

= 3.0.13 =
* Various improvements
* Remove columnWidth from Masonry init to allow for centre positioning
* Remove CSS opacity 0.8 on Album Thumbs
* Remove 1px border bottom on projig view (Pro)
* Fix Sidebar positioning bug that casued sidebar to appear below content

= 3.0.12 =
Changed lightbox image width to be 800px, thanks to @rhormazar for the feedback

= 3.0.11 =
Fixed bug where options saved are lost if plugin is deactivated then reactivated
Added urleencode / stripslashes to Album Title
Improve Widget Display
Add Title and Caption options to lightbox


= 3.0.10 =
Fixed bug conflict with other plugins using Google Library
Fixed bug with Album Title disppearing if pagination was clicked
Fixed album thumbnail size bug not repsecting value in plugin settings page
Added option to show/hide pagination in Widget
Various other little bits and bobs

= 3.0.9 =
Added 'Getting Started' sub menu page to admin
Fixed call for style_fx to be Pro only

= 3.0.8 =
Fixed bug where if no theme option was provided in certain shortcodes in could result in feed error notice
Added 'theme=grid' option to shortcode examples in Pro page

= 3.0.7 =
Fixed pagination bug on home page

= 3.0.6 =
Fixed grid link bug with permalinks disabled
Fixed download original link bug where image could open in lightbox

= 3.0.5 =
Fixed grid results layout bug

= 3.0.4 =
Various layout improvements
Carousel options added, arrows, infinite, slidestoshow, slidestoscroll, autoplay, autoplayinterval, speed, dots
Image thumbnail settings bug fixed
Album thumbnail settings bug fixed
Various other little bits

= 2.3.7 =
Added speed variable to init_slick.js
Added empty array check for cache files

= 2.3.6 =
Readme.txt updates and description

= 2.3.5 =
Fixed: Warning: Missing argument notice when using theme pro_pbs (PRO ONLY)
Added isFitWidth to Masonry init to allow for centering of grid via CSS

= 2.3.4 =
Added Show Albums [Public, Private, All, Visible] (PRO ONLY)

= 2.3.3 =
Added Justified Image Grid option (PRO ONLY)

= 2.3.2 =
Fixed bug effecting the sidebar appearance

= 2.3.1 =
Tidy up of error warning notices
Tested with WordPress 4.6.1

= 2.3.0 =
Many small bugfixes.
Rewrote XML from Google Feed
Added 50% Off code
Added Top level menu to admin

= 2.2.3 =
Bug fix: Carousel

= 2.2.2 =
Bux fix: Album Images

= 2.1.2 =
Paginaton added to Show Albums Widget
Added default settings when first installed
Bug fix - pagination under certain circumstance
Among other things

= 2.1.0 =
Changes Title and description.

= 2.0.1 =
Added screenshots

= 2.0 =
Complete rewrite. Brought about by Google turning off OAuth1
Now supports OAuth2
ONLY UPGRADE IF PREVIOUS VERSION STOPPED WORKING FOR YOU!
All shortcodes have changed

= 1.3.2 =
Fixed: Lost settings when updated Settings -> Reading


== Upgrade Notice ==
 
= 2.0 =
Version 2.0 introduces some radical changes to the plugin. Please make sure to back up your data just in case anything happens