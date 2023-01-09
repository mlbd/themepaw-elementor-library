<?php
/**
 * Hooks.
 *
 * @package ThemepawElementorLibrary
 */

namespace Themepaw\Elementor\Library;

defined( 'ABSPATH' ) || exit;

/**
 * Rest APIs.
 *
 * @package Themepaw\Elementor\Library\Hooks
 */
class Hooks {

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

		add_filter( 'elementor/template_library/sources/local/register_post_type_args', array( $this, 'pretty_permalink' ) );
		add_filter( 'post_type_link', array( $this, 'template_link' ), 1, 3 );
		add_action( 'init', array( $this, 'rewrites_init' ) );
		add_filter( 'single_template', array( $this, 'libray_single_template' ) );

	}

	/**
	 * Elementor library template
	 *
	 * @param string $single
	 * @return string
	 */
	public function libray_single_template( $single ) {
		global $post;

		/* Checks for single template by post type */
		if ( $post->post_type == 'elementor_library' ) {
			if ( defined( 'ELEMENTOR_PATH' ) && file_exists( ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php' ) ) {
				return ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';
			}
		}
	
		return $single;
	}

	/**
	 * Change post link
	 *
	 * @param string $post_link
	 * @param object|int $post
	 * @return string
	 */
	function template_link($post_link, $post = 0) {
		if($post->post_type === 'elementor_library') {
			return home_url('template/' . $post->ID . '/');
		}
		else{
			return $post_link;
		}
	}

	/**
	 * rewrite template library rules
	 *
	 * @return void
	 */
	function rewrites_init(){
		add_rewrite_rule('template/([0-9]+)?$', 'index.php?post_type=elementor_library&p=$matches[1]', 'top');
	}

	/**
	 * Change elementor library post type permalink to pretty.
	 *
	 * @param array $args
	 * @return array
	 */
	public function pretty_permalink( $args ) {
		$args['rewrite'] = array(
			'slug' => 'template',
            "with_front" => true
        );

		return $args;
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
}