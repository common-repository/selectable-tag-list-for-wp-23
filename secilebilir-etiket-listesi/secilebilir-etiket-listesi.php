<?php
/*
Plugin Name: Wp 2.3 Seçilebilir etiket listesi (Selectable Tag List for WP 2.3)
Plugin URI: http://www.dmry.net/wordpress-secilebilir-etiket-listesi-eklentisi
Description: Yeni bir yazı yazarken, etiket kutusu altında tüm etiketleri listeler. (List all tags under the tag input box when you are writing a new post)
Version: 1.0
Author: Hakan Demiray <hakan@dmry.net>
Author URI: http://www.dmry.net/
*/
add_action('admin_footer', 'tum_etiket_listesi');


if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	if (!get_option('wp_secilebilir_etiket_listesi_trackback')) {	
		$trackback_url = (WPLANG=='tr_TR' || WPLANG=='tr') ? 'http://www.dmry.net/wordpress-secilebilir-etiket-listesi-eklentisi/trackback' : 'http://www.dmry.net/wordpress-selectable-tag-list-plugin/trackback';
		
		$trackback_title = (WPLANG=='tr_TR' || WPLANG=='tr') ? 'Eklentinizi yükledim' : 'I installed your plugin';
		
		$trackback_text = (WPLANG=='tr_TR' || WPLANG=='tr') ? "Web siteme \"%s\" eklentinizi yükledim ve denedim" : "I installed and tried your plugin on my site \"%s\"";
		
		$trackback_body = sprintf($trackback_text, get_bloginfo('name'));
		
		secilebilir_etiket_listesi_tracback($trackback_url, $trackback_title, $trackback_body);	
		
		update_option('wp_secilebilir_etiket_listesi_trackback', 'evet');
	}
}


function tum_etiket_listesi() {
        
        global $post, $wpdb;

		$args = wp_parse_args( $args, $defaults );
		$tum_etiketler = get_tags( array_merge($args, array('orderby' => 'count', 'order' => 'DESC')) );
     
		if (is_array($tum_etiketler)) {
			$tum_etiket = array();
			foreach($tum_etiketler as $etiket) {
				$tum_etiket[] = '\'' . $etiket->name . '\'';
			}
			$tum_etiket = implode(',', $tum_etiket);
			$tum_etiket = '//<![CDATA[
				collection = [' . $tum_etiket . '];
//]]>';
        }
	echo '
	<style type="text/css">
	/* Style for Type Ahead (Wick) */ 
	table.floater { position:absolute; z-index:1000; display:none; padding:0; margin:0; }
	table.floater td { font-family: Gill, Helvetica, sans-serif; background-color:white; border:1px inset #979797; color:black; } 
	.matchedSmartInputItem { font-size:0.8em; padding: 5px 10px 1px 5px; margin:0; cursor:pointer; }
	.selectedSmartInputItem { color:white; background-color:#3875D7; }
	#smartInputResults { padding:0; margin:0; }
	.siwCredit { margin:0; padding:0; margin-top:10px; font-size:0.7em; color:black; }  
	</style>
	
	<script type="text/javascript" language="JavaScript">' . $tum_etiket . '</script>
	<script type="text/javascript" language="JavaScript" src="'.get_option('home').'/wp-content/plugins/secilebilir-etiket-listesi/wick.js"></script>
	';
 }
 




function secilebilir_etiket_listesi_tracback($trackback_url, $title, $excerpt) {
	global $wpdb, $wp_version;

	$title = urlencode($title);
	$excerpt = urlencode($excerpt);
	$blog_name = urlencode(get_settings('blogname'));
	$tb_url = $trackback_url;
	$url = urlencode(get_settings('home'));
	$query_string = "title=$title&url=$url&blog_name=$blog_name&excerpt=$excerpt";
	$trackback_url = parse_url($trackback_url);
	$http_request = 'POST ' . $trackback_url['path'] . ($trackback_url['query'] ? '?'.$trackback_url['query'] : '') . " HTTP/1.0\r\n";
	$http_request .= 'Host: '.$trackback_url['host']."\r\n";
	$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset='.get_settings('blog_charset')."\r\n";
	$http_request .= 'Content-Length: '.strlen($query_string)."\r\n";
	$http_request .= "User-Agent: WordPress/" . $wp_version;
	$http_request .= "\r\n\r\n";
	$http_request .= $query_string;
	if ( '' == $trackback_url['port'] )
		$trackback_url['port'] = 80;
	$fs = @fsockopen($trackback_url['host'], $trackback_url['port'], $errno, $errstr, 4);
	@fputs($fs, $http_request);
	@fclose($fs);
}
?>