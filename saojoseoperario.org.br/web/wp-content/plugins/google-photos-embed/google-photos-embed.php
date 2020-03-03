<?php
/**
  Plugin Name: Google Photos embed
  Description: Using shared short URL of Google Photos, you can embed the image easy to blog.
  Version: 1.0.1
  Plugin URI: http://celtislab.net/wp_plugin_google_photos_embed/
  Author: enomoto@celtislab
  Author URI: http://celtislab.net/
  License: GPLv2
  */

class Celtis_google_photos_embed {
    
    //To Do Memo 要検討
    //アクセス過多時に 403 Rate Limit Exceeded エラーになる可能性が高いので、キャッシュを使って表示するオプションが必要？    
    //google photos embed の html コード取得して html を編集する機能
    //link 切れ時の代替用に画像データの設定や解除機能 
//            $(function() {
//                $('img').error(function(){
//                    $(this).attr('src', './icon_jpeg13.jpg');
//                });
//            });    
    
	function __construct() {
        //OGP Google Photos　(WP4.0 >=)
        if ( version_compare( $GLOBALS['wp_version'], '4.0', '>=' ) ){
            //自作の Blog card 処理に干渉しない用ダミーでプロバイダーをセット
            // Ver0.9.2 shared link url changed -> photos.app.goo.gl 
            wp_oembed_add_provider( '#https\://photos\.app\.goo\.gl/(.+)#i', 'https://photos\.app\.goo\.gl', true );            
            wp_oembed_add_provider( '#https\://goo\.gl/photos/(.+)#i', 'https://goo\.gl/photos', true );
            add_filter( 'oembed_ttl', array(&$this, 'oembed_ogp'), 10, 4 );
            add_filter( 'embed_oembed_html', array('Celtis_google_photos_embed', 'embed_oembed_html_filter'), 9, 4);

            if ( function_exists('register_uninstall_hook') )
                register_uninstall_hook(__FILE__, 'Celtis_google_photos_embed::uninstall_hook');
        }
    }
    
    //All embed Google Photos meta cache clear
    public static function uninstall_hook()
    {
        $all_posts = get_posts(array( 'post_type' => array('post','page'), 'numberposts' => -1));
        foreach ($all_posts as $gp) {
            $post_metas = get_post_custom_keys( $gp->ID );
            if ( empty($post_metas) )
                continue;
            foreach( $post_metas as $meta_key ) {
                if ( '_oembed_' == substr( $meta_key, 0, 8 ) ){
                    $cache = get_post_meta( $gp->ID, $meta_key, true );
                    if(false !== strpos($cache, "embed-gphotos")){
                        $meta_timekey = preg_replace('|oembed|', "oembed_time", $meta_key);
                        delete_post_meta( $gp->ID, $meta_key );
                        delete_post_meta( $gp->ID, $meta_timekey );
                    }
                }
            }
        }
    }
		 
    //Google photos image HTML make by OGP data 
    // embed short code parameter
    //   align   : alignnone/alignleft/alignright/aligncenter
    //   caption : キャプション 'faise'=非表示
    //   picopt  : google photos image parameter
    //   width   : 幅   ※picopt未指定時のみ使用
    //   height  : 高さ ※picopt未指定時のみ使用
    //   type    : gif ※可能ならGIFアニメサムネイル形式で表示（未指定時 jpg）
    //
    public function gphotos_html_get( $url, $attr ) {
        $html = '';
        $args = array( 'timeout' => 10, 'httpversion' => '1.1' );
        $response = wp_safe_remote_get( $url, $args );
        if ( is_wp_error( $response ) || $response['response']['code'] !== 200 ) {
            $response = wp_safe_remote_get( $url, $args );
        }
        if ( ! is_wp_error( $response ) && $response['response']['code'] === 200 ) {
            //Ver0.9.2 photos.app.goo.gl の変更に合わせて URL がリダイレクトされるようになったので、リダイレクト先のデータを取得
            if(!empty($response['http_response'])){
                $robj = $response['http_response']->get_response_object();
                if(!empty($robj->redirects) && !empty($robj->url)){
                    $url = $robj->url;
                    $response = wp_safe_remote_get( $url, $args );
                }
            }
        }    
        if ( ! is_wp_error( $response ) && $response['response']['code'] === 200 ) {
            //fix https://wordpress.org/support/topic/use-strpos-instead-of-preg_match/
            //if(preg_match('#(<head>.+?</head>)#s', $response['body'], $match)){
            //    if(!empty($match[1])){
            //        $ogp = self::parse( mb_convert_encoding($match[1], 'HTML-ENTITIES', 'UTF-8'));                    
            $head_pos_0 = strpos($response['body'], '<head>');      // starting character position
            $head_pos_n = strpos($response['body'], '</head>') + 6; // ending character position
            if($head_pos_0 !== false && $head_pos_n !== false){
                $head = substr($response['body'], $head_pos_0, $head_pos_n - $head_pos_0 + 1);            
                if(!empty($head)){
                    $ogp = self::parse( mb_convert_encoding($head, 'HTML-ENTITIES', 'UTF-8'));            
                    $thumbnail = '';
                    if(empty($ogp['og:url'])){
                        $ogp['og:url'] = $url;
                    }
                    $url = esc_url($url);
                    if(!empty($ogp['og:image']) && !empty($ogp['og:image:width']) && !empty($ogp['og:image:height'])){
                        //og:image のデフォルトで付いているパラメータを削除
                        $img = (!is_array($ogp['og:image']))? $ogp['og:image'] : $ogp['og:image'][0];
                        $img = preg_replace('#=w([0-9]+)\-h([0-9]+)(\-p[\-a-z0-9]*)?#', '', $img);  
                        if(empty($attr['picopt'])){
                            $opt = "";
                            if(!empty($attr['width']) && !empty($attr['height'])){
                                $opt = "=w{$attr['width']}-h{$attr['height']}";
                            }
                            if(!empty($attr['type'])){
                                $type = strtolower($attr['type']);
                                //meybe -no option video->GIF conversion thumbnail 
                                if(in_array($type, array('gif')))
                                    $opt .= '-no';
                            }
                        }
                        else {  //パラメータ付け替え
                            $opt = '=' . esc_attr($attr['picopt']);
                        }
                        $img .= "$opt";
                        
                        $caption = '';
                        if(!empty($attr['caption'])){
                            if($attr['caption'] !== 'false')
                                $caption = '<a href="' . $url . '" target="_blank">' . esc_attr($attr['caption']). ' / Google Photos</a>';
                        }
                        else if(!empty($ogp['og:title'])){
                            $caption = '<a href="' . $url . '" target="_blank">' . esc_attr($ogp['og:title']). ' / Google Photos</a>';
                        }

                        $align = '';
                        if(!empty($attr['align']) && in_array($attr['align'], array('alignnone','alignleft','alignright','aligncenter'))){
                            $align = $attr['align'];
                        }
                        $default_attr = array(
                            'src'	=> $img,
                            'class'	=> ""
                        );
                        $imgattr = $default_attr;
                        if(empty($caption)){
                            $imgattr['class'] = $align;
                        }
                        $imgattr = array_map( 'esc_attr', $imgattr );
                        $thumbnail = '<img ';
                        foreach ( $imgattr as $name => $value ) {
                            if(!empty($value))
                                $thumbnail .= " $name=" . '"' . $value . '"';
                        }
                        $thumbnail .= ' >';
                        if(!empty($thumbnail)){
                            $thumbnail = '<a href="' . $url . '" target="_blank">' . $thumbnail . '</a>';
                            if(!empty($caption)){
                                $align .= ' wp-caption';
                                if ( current_theme_supports( 'html5', 'caption' ) ) 
                                    $caption = '<figcaption class="wp-caption-text">' . $caption . '</figcaption>';
                                else 
                                    $caption = '<p class="wp-caption-text">' . $caption . '</p>';
                            }
                            else {
                                $align = ''; //img tag セット済みなので２重セットに注意
                            }
                            if ( current_theme_supports( 'html5', 'caption' ) ) {
                                $html = '<figure class="embed-gphotos ' .$align .'">' . $thumbnail . $caption . '</figure>';
                            }
                            else {
                                $html = '<div class="embed-gphotos ' .$align .'">' . $thumbnail . $caption . '</div>';
                            }
                            //html customize filter hook
                            $html = apply_filters( 'embed_gphotos_html', $html, $url, $attr, $ogp );
                        }
                    }
                }
            }
        }
        return $html;
    }
    
    /**
     * Filter the oEmbed TTL value (time to live).
     *
     * @since 4.0.0
     *
     * @param int    $time    Time to live (in seconds).
     * @param string $url     The attempted embed URL.
     * @param array  $attr    An array of shortcode attributes.
     * @param int    $post_ID Post ID.
     */
    function oembed_ogp( $time, $url, $attr, $post_ID ) {
        
		if ( function_exists( '_wp_oembed_get_object' ) ){
            $key_suffix = md5( $url . serialize( $attr ) );
            $cachekey = '_oembed_' . $key_suffix;
            $cachekey_time = '_oembed_time_' . $key_suffix;

			$cache = get_post_meta( $post_ID, $cachekey, true );
            //ver1.0.0 amp-img 変換用に img に width, height がなければ画像データを取得してサイズセット
            if(!empty($cache) && false !== strpos( $cache, 'class="embed-gphotos') && false === strpos( $cache, 'width=')) {
                $html = preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*?)>#imsu', "self::img_add_size", $cache);
                if ( $cache != $html ) {  
                    update_post_meta( $post_ID, $cachekey, $html );
                    update_post_meta( $post_ID, $cachekey_time, time() );
                } 
            }

       		$attr['discover'] = false;
            $html = '';
            //$oembed = _wp_oembed_get_object();
            //$provider = $oembed->get_provider( $url, $attr );
            // goo.gl/photos ショートURL時に Google Photos image html データ生成
            // Ver0.9.2 shared link url changed -> photos.app.goo.gl 
            if(preg_match('#https\://photos\.app\.goo\.gl/(.+)#im', $url)){
                if ( empty($GLOBALS['wp_embed']->usecache) ) { 
                    $html = $this->gphotos_html_get($url, $attr);
                }
                elseif (empty($cache) || $cache === "{{unknown}}" ){
                    $html = $this->gphotos_html_get($url, $attr);
                }
            } else if(preg_match('#https\://goo\.gl/photos/(.+)#im', $url)){
                if ( empty($GLOBALS['wp_embed']->usecache) ) { 
                    $html = $this->gphotos_html_get($url, $attr);
                }
                elseif (empty($cache)){
                    $html = $this->gphotos_html_get($url, $attr);
                }
            }
            //embed html update
            if ( !empty($html) ) {
                //ver1.0.0 amp-img 変換用に img に width, height がなければ画像データを取得してサイズセット
                if(false !== strpos( $html, 'class="embed-gphotos') && false === strpos( $html, 'width=')) {
                    $html = preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*?)>#imsu', "self::img_add_size", $html);
                }
                update_post_meta( $post_ID, $cachekey, $html );
                update_post_meta( $post_ID, $cachekey_time, time() );
			} 
        }
        return($time);
    }

    //img add width and height attribute
    // $matches[0]  img タグ全体
    // $matches[1]  src 前の記述
    // $matches[2]  src 本体
    // $matches[3]  src 後の記述
    static function img_add_size($matches) {
        $content = $matches[0];
        if(false === strpos( $matches[0], 'width=') && function_exists('imagecreatefromstring')){
            /*  getimagesize() は allow_url_fopen=0 だと PHP Warning が発生するので止める
            $size = getimagesize( $matches[2] );
            if(!empty($size) && $size[0] != 0 && $size[1] != 0){
                //[0]    width
                //[1]    height
                //[2]    画像形式定数
                //[3]    "width="640" height="360""
                //[bits]   color depth (optional)
                //[channels]  RGB = 3、CMYK = 4 (optional)
                //[mime]  "image/jpeg"
                $content = '<img ' . $matches[1] . ' src="' . $matches[2] . '" ' . $size[3] . ' ' . $matches[3] . ' >';
            }
            */
            $args = array( 'timeout' => 20, 'httpversion' => '1.1' );
            if(!empty($_SERVER['HTTP_USER_AGENT']))
                $args['user-agent'] = $_SERVER['HTTP_USER_AGENT'];
            $response = wp_safe_remote_get( $matches[2], $args );
            if ( ! is_wp_error( $response ) && $response['response']['code'] === 200 ) {
                $image = imagecreatefromstring( $response['body'] );
                if(!empty($image)){
                    $size =  'width="' . imagesx($image) . '" height="' . imagesy($image) . '" ';
                    $content = '<img ' . $matches[1] . ' src="' . $matches[2] . '" ' . $size . $matches[3] . ' >';
                }
            }
        }
        return $content;
    }
   
    //amp-image convert
    static function img_to_ampimg($matches) {
        $minwidth = '';
        if(preg_match('/width="([0-9\.]+)"/', $matches[0], $smatch))
            $minwidth = ' sizes="(min-width: ' . $smatch[1]. 'px) ' . $smatch[1] . 'px, 100vw"';
        $content = '<amp-img ' . $matches[1] . ' src="' . $matches[2] . '" ' . $matches[3] . $minwidth . ' ></amp-img>';
        if(false !== strpos($content, 'class=')){
            $content = preg_replace('#(class=[\'"])#im', "$1amp-wp-enforced-sizes ", $content);
        } else {
            $content = preg_replace('#<amp-img#im', '<amp-img class="amp-wp-enforced-sizes"', $content);
        }
        return $content;
    }
    
    //embed html cache filter
    static function embed_oembed_html_filter($cache, $url, $attr, $post_ID ) { 
        if(false !== strpos( $cache, 'class="embed-gphotos')){
            //AMP template used?
            if (did_action( 'pre_amp_render_post' ) !== 0 && false !== strpos( $cache, '<img')) {
                $cache = preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*?)>#imsu', "self::img_to_ampimg", $cache);
            }
        }
        return $cache;
    }
    
	/**
	 * Open Graph Protocol parser.
	 * Very simple open graph parser that parses open graph headers out of a given bit of php.
     * https://github.com/mapkyca/php-ogp  Marcus Povey
	 * 
     * Parse content into an array.
     * @param $content html The HTML
     * @return array
	 */
    public static function parse($content) {

         $doc = new DOMDocument();
         @$doc->loadHTML($content);
		 //$interested_in = array('og', 'fb', 'twitter'); // Open graph namespaces we're interested in (open graph + extensions)
         $interested_in = array('og');

         $ogp = array();
         $metas = $doc->getElementsByTagName('meta'); 
         if (!empty($metas)) {
             for ($n = 0; $n < $metas->length; $n++) {

                 $meta = $metas->item($n);

                 foreach (array('name','property') as $name) {
                     $meta_bits = explode(':', $meta->getAttribute($name)); 
                     if (in_array($meta_bits[0], $interested_in)) {

                         // If we're adding to an existing element, convert it to an array
                         if (isset($ogp[$meta->getAttribute($name)]) && (!is_array($ogp[$meta->getAttribute($name)])))
                            $ogp[$meta->getAttribute($name)] = array($ogp[$meta->getAttribute($name)], $meta->getAttribute('content'));
                         else if (isset($ogp[$meta->getAttribute($name)]) && (is_array($ogp[$meta->getAttribute($name)])))
                            $ogp[$meta->getAttribute($name)][] = $meta->getAttribute('content');
                         else
                            $ogp[$meta->getAttribute($name)] = $meta->getAttribute('content');

                     }
                 }
             }
         }
         return $ogp;
    }
    
	public static function init() {
        $celtis_google_photos_embed = new Celtis_google_photos_embed();
    }
}
add_action( 'init', array( 'Celtis_google_photos_embed', 'init' ) );
