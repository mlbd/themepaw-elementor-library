<?php
/*
Plugin Name: Themepaw Elementor Library Kits
Plugin URI: https://www.themepaw.com/
Description: A toolkit for Elementor library API.
Version: 0.1
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
    const version = '0.1';

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
        Hooks::get_instance();
        Api::get_instance();
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
