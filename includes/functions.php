<?php
/**
 * Helper Functions
 *
 * @package     EDD\Continue Shopping\Functions
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Add Continue Shopping link to the checkout cart.
 *
 * @since       1.0.0
 */
function edd_common_footer_display() {
	global $post, $edd_options;
	$go1 = ( isset($edd_options['edd_common_footer_all']) ) ? true : false;  //edd_get_option( 'edd_common_footer_all' )
	$go2 = ( get_post_meta( $post->ID, '_edd_common_footer_enabled', true ) ) ? true : false;
	if ( class_exists( 'Easy_Digital_Downloads' ) && ($go1 or $go2) )	{
		echo edd_get_option( 'edd_common_footer_content' );
	}
}