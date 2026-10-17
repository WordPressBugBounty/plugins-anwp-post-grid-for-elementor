<?php
/**
 * AnWP Post Grid :: REST API Controller.
 *
 * Registers REST routes (namespace anwp-pg/v1) that replace the legacy
 * admin-ajax.php endpoints: selector search/initial (admin), load-more and
 * pagination (public), and settings save (admin).
 *
 * @since   1.3.4
 * @package AnWP_Post_Grid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core REST Controller.
 *
 * @since 1.3.4
 */
class AnWP_Post_Grid_REST {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	const REST_NAMESPACE = 'anwp-pg/v1';

	/**
	 * Parent plugin class.
	 *
	 * @var AnWP_Post_Grid
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @param AnWP_Post_Grid $plugin Main plugin object.
	 *
	 * @since 1.3.4
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.3.4
	 */
	public function hooks() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register REST routes.
	 *
	 * @since 1.3.4
	 */
	public function register_routes() {

		register_rest_route(
			self::REST_NAMESPACE,
			'/selector/search',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'selector_search' ],
				'permission_callback' => [ $this, 'can_edit_posts' ],
				'args'                => $this->get_selector_search_args(),
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/selector/initial',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'selector_initial' ],
				'permission_callback' => [ $this, 'can_edit_posts' ],
				'args'                => $this->get_selector_initial_args(),
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/posts/load-more',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'load_more_posts' ],
				'permission_callback' => '__return_true',
				'args'                => $this->get_load_more_args(),
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/posts/paginate',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'paginate_posts' ],
				'permission_callback' => '__return_true',
				'args'                => $this->get_paginate_args(),
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/settings',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'save_settings' ],
				'permission_callback' => [ $this, 'can_manage_options' ],
				'args'                => $this->get_settings_args(),
			]
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Permission callbacks
	|--------------------------------------------------------------------------
	*/

	/**
	 * Permission: can edit others' posts (Elementor editor selector).
	 *
	 * @return bool
	 * @since 1.3.4
	 */
	public function can_edit_posts() {
		return current_user_can( 'edit_others_posts' );
	}

	/**
	 * Permission: can manage options (settings save).
	 *
	 * @return bool
	 * @since 1.3.4
	 */
	public function can_manage_options() {
		return current_user_can( 'manage_options' );
	}

	/*
	|--------------------------------------------------------------------------
	| Route callbacks
	|--------------------------------------------------------------------------
	*/

	/**
	 * Selector search (replaces anwp_pg_selector_data).
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 * @since 1.3.4
	 */
	public function selector_search( WP_REST_Request $request ) {

		$html = $this->plugin->elements->get_selector_search_html(
			$request->get_param( 'context' ),
			(string) $request->get_param( 's' )
		);

		return new WP_REST_Response( [ 'html' => $html ] );
	}

	/**
	 * Selector initial (replaces anwp_pg_selector_initial).
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 * @since 1.3.4
	 */
	public function selector_initial( WP_REST_Request $request ) {

		$initial = wp_parse_id_list( $request->get_param( 'initial' ) );

		if ( empty( $initial ) ) {
			return new WP_Error( 'anwp_pg_invalid_initial', esc_html__( 'No valid IDs provided.', 'anwp-post-grid' ), [ 'status' => 400 ] );
		}

		$items = $this->plugin->elements->get_selector_initial_items(
			$request->get_param( 'data_context' ),
			$initial
		);

		return new WP_REST_Response( [ 'items' => $items ] );
	}

	/**
	 * Load more posts (replaces anwp_pg_load_more_posts).
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 * @since 1.3.4
	 */
	public function load_more_posts( WP_REST_Request $request ) {

		$post_qty    = absint( $request->get_param( 'qty' ) );
		$post_loaded = absint( $request->get_param( 'loaded' ) );

		$data           = $this->sanitize_post_query_args( (array) $request->get_param( 'args' ) );
		$data['limit']  = $post_qty + 1;
		$data['offset'] = min( $post_loaded, 100000 ); // SECURITY: same deep-pagination cap as sanitize_post_query_args().

		/** This filter is documented in includes/class-anwp-post-grid-elements.php */
		$data = apply_filters( 'anwp-pg-el/admin/elements_load_more_args', $data, $request->get_params() );

		$grid_posts = $this->plugin->elements->get_grid_posts( $data );

		// Check next time "load more".
		$next_load = count( $grid_posts ) > $post_qty;

		if ( $next_load ) {
			array_pop( $grid_posts );
		}

		return new WP_REST_Response(
			[
				'html'   => $this->render_grid_posts_html( $grid_posts, $data ),
				'next'   => $next_load,
				'offset' => $post_loaded + count( $grid_posts ),
			]
		);
	}

	/**
	 * Paginate posts (replaces anwp_pg_ajax_pagination_load).
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 * @since 1.3.4
	 */
	public function paginate_posts( WP_REST_Request $request ) {

		$current_page = absint( $request->get_param( 'page' ) );

		$data           = $this->sanitize_post_query_args( (array) $request->get_param( 'args' ) );
		$data['offset'] = min( $data['offset'] + ( $data['limit'] * ( $current_page - 1 ) ), 100000 ); // SECURITY: same deep-pagination cap as sanitize_post_query_args().

		/** This filter is documented in includes/class-anwp-post-grid-elements.php */
		$data = apply_filters( 'anwp-pg-el/admin/elements_pagination_args', $data, $request->get_params() );

		$grid_posts = $this->plugin->elements->get_grid_posts( $data );

		return new WP_REST_Response(
			[
				'html' => $this->render_grid_posts_html( $grid_posts, $data ),
			]
		);
	}

	/**
	 * Save plugin settings (replaces anwp_pg_save_options).
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 * @since 1.3.4
	 */
	public function save_settings( WP_REST_Request $request ) {

		$raw_options    = $request->get_param( 'pg_options' );
		$plugin_options = is_array( $raw_options ) ? $this->plugin->settings->sanitize_options( $raw_options ) : [];
		$old_value      = get_option( 'anwp_pg_plugin_settings', [] );

		if ( $plugin_options === $old_value || maybe_serialize( $plugin_options ) === maybe_serialize( $old_value ) ) {
			return new WP_REST_Response( [ 'updated' => false ] );
		}

		if ( ! update_option( 'anwp_pg_plugin_settings', $plugin_options, true ) ) {
			return new WP_Error( 'anwp_pg_update_failed', esc_html__( 'Settings could not be saved.', 'anwp-post-grid' ), [ 'status' => 500 ] );
		}

		return new WP_REST_Response( [ 'updated' => true ] );
	}

	/*
	|--------------------------------------------------------------------------
	| Shared helpers (also reused by the premium REST controller)
	|--------------------------------------------------------------------------
	*/

	/**
	 * Render grid post teasers to an HTML string.
	 *
	 * @param array $grid_posts Array of grid post objects.
	 * @param array $data       Sanitized query/render args.
	 *
	 * @return string
	 * @since 1.3.4
	 */
	public function render_grid_posts_html( $grid_posts, $data ) {

		ob_start();

		foreach ( $grid_posts as $grid_post ) {
			$data['grid_post'] = $grid_post;

			if ( 'classic' === $data['layout'] ) {
				anwp_post_grid()->load_partial( $data, 'teaser/classic' );
			} else {
				anwp_post_grid()->load_partial( $data, 'teaser/teaser', sanitize_key( $data['layout'] ) );
			}
		}

		return ob_get_clean();
	}

	/**
	 * Sanitize the shared post-query args (load-more, paginate, filter).
	 *
	 * Mirrors the legacy AJAX sanitization so output is identical. Defaults are
	 * applied here (not only in the REST schema) so the result is robust
	 * regardless of nested-object schema default behavior.
	 *
	 * @param array $raw_args Raw args from the request.
	 *
	 * @return array
	 * @since 1.3.4
	 */
	public function sanitize_post_query_args( $raw_args ) {

		$raw_args = wp_parse_args(
			is_array( $raw_args ) ? $raw_args : [],
			[
				'layout'                 => '',
				'posts_to_show'          => 'latest',
				'include_ids'            => '',
				'exclude_ids'            => '',
				'exclude_by_category'    => '',
				'filter_by_category'     => '',
				'filter_by_tag'          => '',
				'filter_by_post_format'  => '',
				'filter_by_author'       => '',
				'published_in_last_days' => 0,
				'limit'                  => 3,
				'offset'                 => 0,
				'grid_cols'              => 3,
				'grid_cols_tablet'       => 2,
				'grid_cols_mobile'       => 1,
				'category_limit'         => 1,
				'grid_thumbnail_size'    => 'large',
				'show_category'          => 'yes',
				'show_date'              => 'yes',
				'show_comments'          => 'yes',
				'card_height'            => '180',
				'show_excerpt'           => 'yes',
				'excerpt_num_words'      => '',
				'excerpt_source'         => '',
				'excerpt_html'           => '',
				'post_image_width'       => '1_3',
				'show_post_icon'         => 'yes',
				'show_author'            => '',
				'show_read_more'         => '',
				'read_more_label'        => '',
				'read_more_class'        => '',
				'query_source'           => '',
				'related_posts'          => '',
				'related_posts_order'    => '',
				'related_post_id'        => '',
				'card_height_mobile'     => '',
				'card_height_tablet'     => '',
				'labels_by_c_tax'        => '',
				'filter_by_c_tax'        => '',
			]
		);

		return [
			'posts_to_show'          => sanitize_text_field( $raw_args['posts_to_show'] ),
			'include_ids'            => array_slice( wp_parse_id_list( $raw_args['include_ids'] ), 0, 48 ), // SECURITY: "custom" renders every ID and ignores limit, so cap it like limit.
			'exclude_ids'            => wp_parse_id_list( $raw_args['exclude_ids'] ),
			'filter_by_category'     => wp_parse_id_list( $raw_args['filter_by_category'] ),
			'exclude_by_category'    => wp_parse_id_list( $raw_args['exclude_by_category'] ),
			'filter_by_tag'          => wp_parse_id_list( $raw_args['filter_by_tag'] ),
			'filter_by_post_format'  => sanitize_text_field( $raw_args['filter_by_post_format'] ),
			'filter_by_author'       => wp_parse_id_list( $raw_args['filter_by_author'] ),
			'published_in_last_days' => absint( $raw_args['published_in_last_days'] ),
			'limit'                  => min( absint( $raw_args['limit'] ), 48 ), // SECURITY: hard cap render size (nested args object isn't strictly validated).
			'offset'                 => min( absint( $raw_args['offset'] ), 100000 ), // SECURITY: cap offset to bound deep-pagination query cost.
			'category_limit'         => absint( $raw_args['category_limit'] ),
			'grid_cols'              => absint( $raw_args['grid_cols'] ),
			'grid_cols_tablet'       => absint( $raw_args['grid_cols_tablet'] ),
			'grid_cols_mobile'       => absint( $raw_args['grid_cols_mobile'] ),
			'grid_thumbnail_size'    => sanitize_text_field( $raw_args['grid_thumbnail_size'] ),
			'show_category'          => sanitize_text_field( $raw_args['show_category'] ),
			'show_date'              => sanitize_text_field( $raw_args['show_date'] ),
			'show_comments'          => sanitize_text_field( $raw_args['show_comments'] ),
			'card_height'            => is_array( $raw_args['card_height'] ) ? array_map( 'sanitize_text_field', $raw_args['card_height'] ) : '',
			'card_height_mobile'     => is_array( $raw_args['card_height_mobile'] ) ? array_map( 'sanitize_text_field', $raw_args['card_height_mobile'] ) : '',
			'card_height_tablet'     => is_array( $raw_args['card_height_tablet'] ) ? array_map( 'sanitize_text_field', $raw_args['card_height_tablet'] ) : '',
			'show_excerpt'           => sanitize_text_field( $raw_args['show_excerpt'] ),
			'excerpt_num_words'      => sanitize_text_field( $raw_args['excerpt_num_words'] ),
			'excerpt_html'           => sanitize_text_field( $raw_args['excerpt_html'] ),
			'excerpt_source'         => sanitize_text_field( $raw_args['excerpt_source'] ),
			'post_image_width'       => sanitize_text_field( $raw_args['post_image_width'] ),
			'layout'                 => sanitize_text_field( $raw_args['layout'] ),
			'show_author'            => sanitize_text_field( $raw_args['show_author'] ),
			'show_read_more'         => sanitize_text_field( $raw_args['show_read_more'] ),
			'read_more_label'        => sanitize_text_field( $raw_args['read_more_label'] ),
			'read_more_class'        => sanitize_text_field( $raw_args['read_more_class'] ),
			'show_post_icon'         => sanitize_text_field( $raw_args['show_post_icon'] ),
			'query_source'           => sanitize_text_field( $raw_args['query_source'] ),
			'related_posts'          => sanitize_text_field( $raw_args['related_posts'] ),
			'related_posts_order'    => sanitize_text_field( $raw_args['related_posts_order'] ),
			'related_post_id'        => sanitize_text_field( $raw_args['related_post_id'] ),
			'filter_by_c_tax'        => is_array( $raw_args['filter_by_c_tax'] ) ? array_map( 'sanitize_text_field', $raw_args['filter_by_c_tax'] ) : '',
			'labels_by_c_tax'        => sanitize_text_field( $raw_args['labels_by_c_tax'] ),
		];
	}

	/*
	|--------------------------------------------------------------------------
	| Args schema
	|--------------------------------------------------------------------------
	*/

	/**
	 * Schema for the selector search route.
	 *
	 * @return array
	 * @since 1.3.4
	 */
	private function get_selector_search_args() {
		return [
			'context' => [
				'required'          => true,
				'type'              => 'string',
				'enum'              => [ 'tags', 'categories', 'posts', 'authors' ],
				'validate_callback' => 'rest_validate_request_arg', // Required for enum to be enforced.
				'sanitize_callback' => 'sanitize_text_field',
			],
			's'       => [
				'required'          => false,
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}

	/**
	 * Schema for the selector initial route.
	 *
	 * @return array
	 * @since 1.3.4
	 */
	private function get_selector_initial_args() {
		return [
			'data_context' => [
				'required'          => true,
				'type'              => 'string',
				'enum'              => [ 'tags', 'categories', 'posts', 'authors' ],
				'validate_callback' => 'rest_validate_request_arg', // Required for enum to be enforced.
				'sanitize_callback' => 'sanitize_text_field',
			],
			'initial'      => [
				'required' => true,
				'type'     => 'string',
			],
		];
	}

	/**
	 * Schema for the load-more route.
	 *
	 * @return array
	 * @since 1.3.4
	 */
	private function get_load_more_args() {
		return [
			'loaded'                => [
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			],
			'qty'                   => [
				'required'          => true,
				'type'              => 'integer',
				'minimum'           => 1,
				'maximum'           => 48, // SECURITY: load-more derives limit from qty; this is the real DoS cap.
				'validate_callback' => 'rest_validate_request_arg', // Required for minimum/maximum to be enforced.
				'sanitize_callback' => 'absint',
			],
			'header_filter_term_id' => [
				'required'          => false,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			],
			'args'                  => $this->get_query_args_param(),
		];
	}

	/**
	 * Schema for the paginate route.
	 *
	 * @return array
	 * @since 1.3.4
	 */
	private function get_paginate_args() {
		return [
			'page'                  => [
				'required'          => true,
				'type'              => 'integer',
				'minimum'           => 1,
				'validate_callback' => 'rest_validate_request_arg', // Required for minimum to be enforced.
				'sanitize_callback' => 'absint',
			],
			'header_filter_term_id' => [
				'required'          => false,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			],
			'args'                  => $this->get_query_args_param(),
		];
	}

	/**
	 * Schema for the settings route.
	 *
	 * @return array
	 * @since 1.3.4
	 */
	private function get_settings_args() {
		return [
			'pg_options' => [
				'required' => true,
				'type'     => 'object',
			],
		];
	}

	/**
	 * Param definition for the shared `args` object used by load-more, paginate (core)
	 * and filter (premium). Public so the premium controller can reuse it.
	 *
	 * Deliberately loose: widgets send raw Elementor settings, so field types drift
	 * (NUMBER controls send integers, cleared fields send "", `limit` can be -1).
	 * A strict nested schema rejects real widgets with a 400, so every field is
	 * coerced and capped in sanitize_post_query_args() instead.
	 *
	 * @return array
	 * @since 1.3.4
	 */
	public function get_query_args_param() {
		return [
			'required'          => true,
			'type'              => 'object',
			'validate_callback' => function ( $value ) {
				return is_array( $value );
			},
			'sanitize_callback' => function ( $value ) {
				return $value; // Coerced + capped in sanitize_post_query_args().
			},
		];
	}
}
