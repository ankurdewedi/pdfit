<?php
/**
 * @package PdfIt
 */
/*
Plugin Name: Download a pdf
Description:  Provide an image and text and you will get a pdf.
Version: 1.0.0
License: MIT
Text Domain: PdfIt
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'PDFIT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'PdfIt', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'PdfIt', 'plugin_deactivation' ) );

require_once( PDFIT__PLUGIN_DIR . 'class.pdfit.php' );

add_action( 'init', array( 'PdfIt', 'init' ) );
