<?php
/*
 * Plugin Name: Easy Post Embed Code
 * Version: 1.0
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: Adds each post's embed code to the post edit screen for easy copying.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 4.4
 * Tested up to: 4.4
 *
 * Text Domain: easy-post-embed-code
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! function_exists( 'get_post_embed_html' ) ) {
	exit;
}

// Load plugin class files
require_once( 'includes/class-easy-post-embed-code.php' );

/**
 * Returns the main instance of Easy_Post_Embed_Code to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Easy_Post_Embed_Code
 */
function Easy_Post_Embed_Code () {
	$instance = Easy_Post_Embed_Code::instance( __FILE__, '1.0.0' );
	return $instance;
}

Easy_Post_Embed_Code();
