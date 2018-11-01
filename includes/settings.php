<?php

	if ( !defined('ABSPATH') ) {
		header( 'Status: 403 Forbidden' );
		header( 'HTTP/1.1 403 Forbidden' );
		exit();
	}

function edd_common_footer_settings_section( $sections ) {
	$sections['edd-common-footer-settings'] = __( 'Common Footer', 'edd-common-footer' );
	return $sections;
}
add_filter( 'edd_settings_sections_extensions', 'edd_common_footer_settings_section' );


	function edd_common_footer_settings($settings) {
		$common_footer_settings = array(
			array(
				'id'   => 'edd_common_footer_settings',
				'name' => '<strong>' . __( 'Common Footer Settings', 'edd-common-footer' ) . '</strong>',
				'desc' => __( 'Configure Common Footer Settings', 'edd-common-footer' ),
				'type' => 'header',
			),
			array(
				'id'   => 'edd_common_footer_all',
				'name' =>  sprintf( __( 'Apply to all %s', 'edd-common-footer' ), edd_get_label_plural() ),
				'desc' => __( 'Enable the Common Footer Content to all the Downloads.', 'edd-common-footer' ),
				'type' => 'checkbox',
				'size' => 'regular',
			),
			array(
				'id'   => 'edd_common_footer_content',
				'name' => __( 'Common Footer Content', 'edd-common-footer' ),
				'desc' => __( 'This text will show on the footer of each selected EDD Download product or all of them if enabled the previous checkbox.', 'edd-common-footer' ),
				'type' => 'rich_editor',
			),
			
		);
		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$common_footer_settings = array( 'edd-common-footer-settings' => $common_footer_settings );
		}
		return array_merge( $settings, $common_footer_settings );
	}
	add_filter( 'edd_settings_extensions', 'edd_common_footer_settings', 999, 1 );
?>