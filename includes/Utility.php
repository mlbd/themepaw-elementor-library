<?php
/**
 * Utilities.
 *
 * @package ThemepawElementorLibrary
 */

namespace Themepaw\Elementor\Library;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * Rest APIs.
 *
 * @package Themepaw\Elementor\Library\Utilities
 */
class Utility {

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function instance() {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new Utility();
		}

		return $instance;
	}

    /**
     * Get terms by post id
     *
     * @param int $post_id
     * @return array
     */
	public function get_terms( $post_id ) {
		$term_obj_list = get_the_terms( $post_id, 'elementor_library_category' );
		$categories = empty( $term_obj_list ) ? [] : wp_list_pluck( $term_obj_list, 'slug' );

		return $categories;
	}

    /**
     * Get all elementor template categories
     *
     * @return array
     */
	public function get_categories() {

		$taxonomies = get_terms( array(
			'taxonomy' => 'elementor_library_category',
			'hide_empty' => false
		) );

		$filter_taxonomies = [];
		if ( $taxonomies ) {
			foreach ( $taxonomies  as $taxonomy ) {
				$filter_taxonomies[$taxonomy->slug] = $taxonomy->name;
			}
		}

		return $filter_taxonomies;
	}

    /**
     * Get posts
     *
     * @param string $type
     * @return object
     */
    public function get_posts( $type = '' ) {
        $post_agrs = array(
			'post_type'  => 'elementor_library',
			'posts_per_page' => 5000,
			'post_status' => 'publish',
			'order' => 'ASC'
		);

        if( ! empty( $type ) ) {
            $post_agrs['meta_query'] = array(
				array(
					'key'     => '_elementor_template_type',
					'value'   => $type
				)
			);
        }

		$template_libraries = get_posts( $post_agrs );
        return $template_libraries;
    }

    /**
     * Filter Elementor templates category by type
     *
     * @return void
     */
	public function type_category() {

		$page_templates = $this->get_posts( 'page' );
		$page_categories = [];

		if( $page_templates ) {
			foreach( $page_templates as $p_template ) {
				$single_terms = $this->get_terms( $p_template->ID );
				foreach( $single_terms as $term ) {
					$page_categories[] = $term;
				}
			}
			$page_categories = array_unique( $page_categories );
		}

		$section_templates = $this->get_posts( 'section' );
		
		$section_categories = [];
		if( $section_templates ) {
			foreach( $section_templates as $p_template ) {
				$single_terms = $this->get_terms( $p_template->ID );
				foreach( $single_terms as $term ) {
					$section_categories[] = $term;
				}
			}
			$section_categories = array_unique( $section_categories );
		}

		$categories = []; 
		$categories['section'] = $section_categories;
		$categories['page'] = $page_categories;

		return $categories;
		
	}

	/**
	 * Get elementor library templates
	 *
	 * @return array
	 */
	public function get_templates() {
		$templates = $this->get_posts();

		if ( ! $templates ) {
			return [];
		}

		$mapping = array();
		foreach( $templates as $template ) {

			$template_id = $template->ID;

			$thumbnail = get_the_post_thumbnail_url( $template_id, 'full' );
			$exclude = get_post_meta( $template_id, 'paw_library_exclude', true );
			$is_pro = get_post_meta( $template_id, 'ispro', true );
			$type = get_post_meta( $template_id, '_elementor_template_type', true );
			$is_pro = ! empty( $is_pro ) ? true : false;

			// skip if post thumbnail not exists
			if( empty( $thumbnail ) || ! empty( $exclude ) ) {
				continue;
			}

			$item = array(
				"id" => $template_id,
				"title" => $template->post_title,
				"url" => get_permalink( $template_id ),
				"slug" => $template->post_name,
				"type" => $type,
				"thumbnail" => $thumbnail,
				"category" => $this->get_terms( $template_id ),
				"is_pro" => $is_pro,
			);

			$mapping[] = $item;
		}

		return $mapping;
	}

    public function api_template_list( WP_REST_Request $request ) {
		$data = [];
        $data['templates'] = $this->get_templates();
		$data['categories'] = $this->get_categories();
		$data['type_category'] = $this->type_category();
        return new WP_REST_Response( $data, 200 );
    }

}