<?php
/**
 * Activation handler
 *
 * @package     edd_common_footer\ActivationHandler
 * @since       1.0.0
 */


// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * edd_common_footer Extension Activation Handler Class
 *
 * @since       1.0.0
 */
class edd_common_footer_Extension_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_edd_common_footer, $edd_common_footer_base;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        if( isset( $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] ) ) {
            $this->plugin_name = str_replace( 'Easy Digital Downloads', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );
        } else {
            $this->plugin_name = __( 'This plugin', 'edd-common-footer' );
        }

        // Is Easy Digital Downloads installed?
        foreach( $plugins as $plugin_path => $plugin ) {
            if( $plugin['Name'] == 'Easy Digital Downloads' ) {
                $this->has_edd_common_footer = true;
                $this->edd_common_footer_base = $plugin_path;
                break;
            }
        }
    }


    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // Display notice
        add_action( 'admin_notices', array( $this, 'missing_edd_common_footer_notice' ) );
    }


    /**
     * Display notice if edd_common_footer isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      string The notice to displayS
     */
    public function missing_edd_common_footer_notice() {
        if( $this->has_edd_common_footer ) {
            $url  = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $this->edd_common_footer_base ), 'activate-plugin_' . $this->edd_common_footer_base ) );
            $link = '<a href="' . $url . '">' . __( 'activate it', 'edd-common-footer' ) . '</a>';
        } else {
            $url  = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=edd_common_footer' ), 'install-plugin_edd_common_footer' ) );
            $link = '<a href="' . $url . '">' . __( 'install it', 'edd-common-footer' ) . '</a>';
        }
        
        echo '<div class="error"><p>' . $this->plugin_name . sprintf( __( ' requires Easy Digital Downloads! Please %s to continue!', 'edd-common-footer' ), $link ) . '</p></div>';
    }
}
