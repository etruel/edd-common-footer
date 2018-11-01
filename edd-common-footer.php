<?php
/**
 * Plugin Name: EDD Common Footer
 * Plugin URI: https://github.com/etruel/edd-common-footer
 * Description: Adds a common footer to all or to selected downloads products on frontend.
 * Version: 1.0
 * Author: etruel
 * Author URI: https://etruel.com
 * License: GPL2+
 * Text Domain: edd-common-footer
 * Domain Path: /lang/
 *
 *
 * @package         etruel\edd_common_footer 
 * @author          Esteban Truelsegaard
 * @copyright       Copyright (c) 2018
 *
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'edd_common_footer' ) ) {

    // Plugin version
    if(!defined('EDD_COMMON_FOOTER_VER')) {
        define('EDD_COMMON_FOOTER_VER', '1.1' );
    }
    
    /**
     * Main edd_common_footer class
     *
     * @since       1.0.0
     */
    class edd_common_footer {

        /**
         * @var         edd_common_footer $instance The one true edd_common_footer
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true edd_common_footer
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new self();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->setup_actions();
                self::$instance->load_textdomain();

            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
       public static function setup_constants() {
            // Plugin root file
            if(!defined('EDD_COMMON_FOOTER_ROOT_FILE')) {
                define('EDD_COMMON_FOOTER_ROOT_FILE', __FILE__ );
            }
            // Plugin path
            if(!defined('EDD_COMMON_FOOTER_DIR')) {
                define('EDD_COMMON_FOOTER_DIR', plugin_dir_path( __FILE__ ) );
            }
            // Plugin URL
            if(!defined('EDD_COMMON_FOOTER_URL')) {
                define('EDD_COMMON_FOOTER_URL', plugin_dir_url( __FILE__ ) );
            }
            if(!defined('EDD_COMMON_FOOTER_STORE_URL')) {
                define('EDD_COMMON_FOOTER_STORE_URL', 'https://etruel.com'); 
            } 
            if(!defined('EDD_COMMON_FOOTER_ITEM_NAME')) {
                define('EDD_COMMON_FOOTER_ITEM_NAME', 'EDD Common Footer'); 
            } 
        }


        /**
         * Include necessary files
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public static function includes() {
            // Include scripts
            require_once EDD_COMMON_FOOTER_DIR . 'includes/functions.php';
			if( is_admin() ) {
				require_once EDD_COMMON_FOOTER_DIR . 'includes/settings.php';
			}
        }
      
		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function setup_actions() {
			global $edd_options;
			add_action( 'edd_after_download_content', 'edd_common_footer_display', 115 );
			
			// metabox
			add_action( 'edd_meta_box_settings_fields', array( $this, 'add_metabox' ) );
			add_action( 'edd_metabox_fields_save', array( $this, 'save_metabox' ) );
			
			// Settings link
			add_filter(	'plugin_action_links_' . plugin_basename( __FILE__ ) , array( $this, 'plugin_action_links') );

		}

		/**
		* Actions-Links del Plugin
		*
		* @param   array   $data  Original Links
		* @return  array   $data  modified Links
		*/
		function plugin_action_links($data)	{
			if ( !current_user_can('manage_options') ) {
				return $data;
			}
			return array_merge(	
				array(
					'<a href="edit.php?post_type=download&page=edd-settings&tab=extensions&section=edd-common-footer-settings" title="' . __('Go to Settings Page', 'edd-common-footer' ) . '">' . __('Settings', 'edd-common-footer' ) . '</a>',
				), $data
			);
		}		
		/**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public static function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_COMMON_FOOTER_DIR . '/lang/';
            $lang_dir = apply_filters( 'edd_common_footer_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-common-footer' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-common-footer', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-common-footer/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-common-footer/ folder
                load_textdomain( 'edd-common-footer', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-common-footer/lang/ folder
                load_textdomain( 'edd-common-footer', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-common-footer', false, $lang_dir );
            }
        }

		/**
		 * Add Metabox if per download email attachments are enabled
		 *
		 * @since 1.0
		*/
		public function add_metabox( $post_id ) {
			$checked = (boolean) get_post_meta( $post_id, '_edd_common_footer_enabled', true );
		?>
			<p><strong><?php echo apply_filters( 'edd_common_footer_header', sprintf( __( '%s Common Footer:', 'edd-common-footer' ), edd_get_label_singular() ) ); ?></strong></p>
			<p>
				<label for="edd_common_footer">
					<input type="checkbox" name="_edd_common_footer_enabled" id="edd_common_footer" value="1" <?php checked( true, $checked ); ?> />
					<?php echo apply_filters( 'edd_common_footer_header_label', __( 'Use Common Footer.', 'edd-common-footer' ) ); ?>
				</label>
			</p>
		<?php
		}

		/**
		 * Add to save function
		 * @param  $fields Array of fields
		 * @since 1.0
		 * @return array
		*/
		public function save_metabox( $fields ) {
			$fields[] = '_edd_common_footer_enabled';

			return $fields;
		}

    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true edd_common_footer
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \edd_common_footer The one true edd_common_footer
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */


function edd_common_footer_load() {
     if(!class_exists( 'Easy_Digital_Downloads' ) ) {
         require_once 'inc/class.extension-activation.php';
         $activation = new edd_common_footer_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
         $activation = $activation->run();

    }else {
        return edd_common_footer::instance(); 
    }
}
add_action( 'plugins_loaded', 'edd_common_footer_load', 999);



/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function edd_common_footer_activation() {
    /* Activation functions here */
}
register_activation_hook( __FILE__, 'edd_common_footer_activation' );
