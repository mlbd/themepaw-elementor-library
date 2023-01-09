<?php
/**
 * APIs.
 *
 * @package ThemepawElementorLibrary
 */

namespace Themepaw\Elementor\Library;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use Elementor\DB;
use Elementor\Source_Local;
use Elementor\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Rest APIs.
 *
 * @package Themepaw\Elementor\Library\Api
 */
class Api {

	/**
	 * The single instance of Settings.
	 *
	 * @var    object
	 * @access private
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Local constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	
	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register API endpoints.
	 *
	 * @return void
	 */
	public function register_endpoints() {
        register_rest_route(
            'paw/v1',
            '/library-list',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'api_template_list' ),
                'permission_callback' => '__return_true',
                'args'                => array(),
            )
        );
        register_rest_route(
            'paw/v1',
            '/library' . '/(?P<id>[\d]+)',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'api_template_by_id' ),
                'permission_callback' => '__return_true',
                'args'                => array(),
            )
        );
	}

	/**
	 * Get elementor library lists api
	 *
	 * @param WP_REST_Request $request Request object.
	 * 
	 * @return WP_Error|WP_REST_Response
	 */
    public function api_template_list( WP_REST_Request $request ) {
		$data = [];
        $data['templates'] = Utility::instance()->get_templates();
		$data['categories'] = Utility::instance()->get_categories();
		$data['type_category'] = Utility::instance()->type_category();
        return new WP_REST_Response( $data, 200 );
    }

	/**
	 * Get template by id
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function api_template_by_id( WP_REST_Request $request ) {
		$params = $request->get_url_params();

		if ( ! isset( $params['id'] ) || empty( $params['id'] ) ) {
			return new WP_REST_Response( array( 'error' => 'Invalid Template ID.' ), 500 );
		}

		$template_id = (int) $params['id'];

		$document = Plugin::$instance->documents->get( $template_id );

		$template_data = $document->get_export_data();

		if ( empty( $template_data['content'] ) ) {
			return new WP_REST_Response( array( 'error' => 'The template is empty' ), 500 );
		}

		$data = [
			'title' => get_the_title( $template_id ),
			'content' => $template_data['content']
		];

		return new WP_REST_Response( $data, 200 );
	}

}