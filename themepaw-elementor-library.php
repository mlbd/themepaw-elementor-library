<?php
/*
Plugin Name: Themepaw Elementor Library Kits
Plugin URI: https://www.themepaw.com/
Description: A toolkit for Elementor library API.
Version: 0.2
Author: Mohammad Limon
Author URI: https://www.themepaw.com
*/

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'THEMEPAW_ELEMENTOR_LIBRARY' ) ) {
	define( 'THEMEPAW_ELEMENTOR_LIBRARY', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

use Themepaw\Elementor\Library\Api;
use Themepaw\Elementor\Library\Hooks;

class ThemePawLibraryInit {

    /**
     * Plugin version.
     *
     * @var string
     */
    const version = '0.2';

    /**
	 * Call this method to get the singleton
	 *
	 * @return ThemePawLibraryInit|null
	 */
	public static function instance() {

		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new ThemePawLibraryInit();
		}

		return $instance;
	}

	public function __construct() {

        $this->define_constanst();

		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

        add_action( 'plugins_loaded', array( $this, 'init' ) );

	}

    /**
	 * Init
	 */
	public function init() {

		// Check if Elementor installed and activated
        if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );

            return;
        }


        Hooks::get_instance();
        Api::get_instance();
	}

	/**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_elementor() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'paw-elementor-library' ),
            '<strong>' . esc_html__( 'Themepaw Elementor Library Kits', 'paw-elementor-library' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'paw-elementor-library' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }


	/**
	 * plugin activation
	 *
	 * @return void
	 */
	public function activation() {

	}

	/**
	 * plugin activation
	 *
	 * @return void
	 */
	public function deactivation() {

	}

    /**
     * Define require constansts
     * 
     * @return void
     */
    public function define_constanst(){
        define( "THEMEPAW_LIBRARY_VERSION", self::version );
		define( "THEMEPAW_LIBRARY_URL", plugins_url( "/" , __FILE__ ) );
		define( 'THEMEPAW_LIBRARY_ABSPATH', dirname( THEMEPAW_ELEMENTOR_LIBRARY ) . '/' );
		define( 'THEMEPAW_LIBRARY_PLUGIN_BASENAME', plugin_basename( THEMEPAW_ELEMENTOR_LIBRARY ) );
		define( 'THEMEPAW_LIBRARY_BASE_FILE', __FILE__ );
		define( 'THEMEPAW_LIBRARY_BASE_DIR', dirname( THEMEPAW_LIBRARY_BASE_FILE ) );
        define( "THEMEPAW_LIBRARY_PATH", plugin_dir_path( __FILE__ ) );
    }

	
}

( new ThemePawLibraryInit() );
