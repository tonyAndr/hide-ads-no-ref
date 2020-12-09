<?php
/**
 * Plugin Name: AAA Antibot
 * Description: Hide Ads for bad referrer
 * Author: 
 * Author URI: 
 * Version: 0.1
 * Plugin URI: 
 */

function is_bot_visit_666() {
	$ref = $_SERVER['HTTP_REFERER'];
	$host = $_SERVER['HTTP_HOST'];

	$block_js = false;

	if (empty($ref) || empty(trim($ref))) { //direct
    	$block_js = true;
    } else {
    	if (strpos($ref, "yandex") === false 
        && strpos($ref, "google") === false 
        && strpos($ref, $host) === false) { // referrer not in white list
        	$block_js = true;
        }
    }

	return $block_js;
}


function referrer_disable_widgets() {

	$block_js = is_bot_visit_666();
	if ($block_js) {
    	unregister_widget( 'WP_Widget_Custom_HTML' );	
    	unregister_widget( 'WP_Widget_Text' );	
    	unregister_widget( 'ai_widget' );	
    }
    
}


//add_filter('the_content', 'show_some_info0');
function show_some_info0($content) {
	$info = $_SERVER['HTTP_REFERER'] . "<bR>" . $_SERVER['HTTP_HOST'] . "<br><br>" .$content;
return $info;
}


function remove_ai_filters() {
	remove_action('the_content', 'ai_content_hook', 99999 );
    remove_action( 'wp_footer', 'ai_hook_function_footer', 5  );
    remove_action( 'wp_footer', 'ai_wp_footer_hook', 5 );
}
function remove_ai_header() {
    	remove_action('wp_head', 'ai_wp_head_hook', 99999 );
}

function remove_ai_footer() {
    	remove_action( 'wp_footer', 'ai_hook_function_footer', 5  );
    	remove_action( 'wp_footer', 'ai_wp_footer_hook', 5 );
      remove_action ('wp_footer', 'ai_wp_footer_hook_end_buffering', 5);
      remove_action ('wp_footer', 'ai_wp_footer_hook', 9999999);
}


function referrer_disable_ads_inserter()
{
	$block_js = is_bot_visit_666();
    
    if ($block_js) {
        add_action( 'wp_head', 'remove_ai_filters' , 100);
        add_action( 'wp_head', 'remove_ai_header' , 100);
    	add_action( 'wp_footer', 'remove_ai_footer' , 0);
    } 
}
add_action('init', 'referrer_disable_ads_inserter', 100);
add_action( 'widgets_init', 'referrer_disable_widgets' );
