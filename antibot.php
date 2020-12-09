<?php
/**
 * Plugin Name: AAA Antibot
 * Description: Hide Ads for bad referrer
 * Author: SeoCherry.ru
 * Author URI: SeoCherry.ru
 * Version: 0.2
 * Plugin URI: SeoCherry.ru
 */

// The visitor is bot if:
    // he has no referrer
    // he came from anywhere but yandex/google/same domain 
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

// Remove widgets with ads (WARNING, REMOVES ALL MENTIOND TYPES OF WIDGETS)
function referrer_disable_widgets() {
	$block_js = is_bot_visit_666();
	if ($block_js) {
        // This two only if you need them, and I don't need them on my sites
    	unregister_widget( 'WP_Widget_Custom_HTML' );	
        unregister_widget( 'WP_Widget_Text' );	
        
        // Keep this
    	unregister_widget( 'ai_widget' );	
    }  
}

// Remove content hooks, and footer hooks for some reason (idk why I had issues, but it works this way, lazy to change)
function remove_ai_filters() {
	remove_action('the_content', 'ai_content_hook', 99999 );
    remove_action( 'wp_footer', 'ai_hook_function_footer', 5  );
    remove_action( 'wp_footer', 'ai_wp_footer_hook', 5 );
}
// Remove header output
function remove_ai_header() {
    remove_action('wp_head', 'ai_wp_head_hook', 99999 );
}
// Remove every possible footer outputs
function remove_ai_footer() {
    remove_action( 'wp_footer', 'ai_hook_function_footer', 5  );
    remove_action( 'wp_footer', 'ai_wp_footer_hook', 5 );
    remove_action ('wp_footer', 'ai_wp_footer_hook_end_buffering', 5);
    remove_action ('wp_footer', 'ai_wp_footer_hook', 9999999);
}
// Add actions to actually fire all functions above 
function referrer_disable_ads_inserter()
{
	$block_js = is_bot_visit_666();
    
    if ($block_js) {
        add_action( 'wp_head', 'remove_ai_filters' , 100);
        add_action( 'wp_head', 'remove_ai_header' , 100);
    	add_action( 'wp_footer', 'remove_ai_footer' , 0);
    } 
}

// Final accords to make it all work
add_action('init', 'referrer_disable_ads_inserter', 100);
add_action( 'widgets_init', 'referrer_disable_widgets' );
