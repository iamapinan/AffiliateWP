<?php
namespace AffWP\Visit;

use \AffWP\REST\Controller as Controller;

/**
 * Implements REST routes and endpoints for Visits.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class REST extends Controller {

	/**
	 * Registers Visit routes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Server $wp_rest_server Server object.
	 */
	public function register_routes( $wp_rest_server ) {
		register_rest_route( $this->namespace, '/visits/', array(
			'methods' => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_get_visits' ),
			'args' => array(
				/*
				 * Pass top-level args as query vars:
				 * /visits/?referral_status=pending&order=desc
				 */
				'number' => array(
					'description'       => __( 'The number of visits to query for. Use -1 for all.', 'affiliate-wp' ),
					'sanitize_callback' => 'absint',
					'validate_callback' => 'is_numeric',
				),
				'order' => array(
					'description'       => __( 'How to order results. Accepts ASC (ascending) or DESC (descending).', 'affiliate-wp' ),
					'validate_callback' => function( $param, $request, $key ) {
						return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
					}
				),

				/*
				 * Pass any valid get_visits() args via filter:
				 * /visits/?filter[referral_status]=pending&filter[order]=desc
				 */
				'filter' => array()
			)
		) );

		register_rest_route( $this->namespace, '/visits/(?P<id>\d+)', array(
			'methods'  => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_visit_id' ),
			'args'     => array(
				'id' => array(
					'required'          => true,
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				)
			),
//			'permission_callback' => function( $request ) {
//				return current_user_can( 'manage_affiliates' );
//			}
		) );
	}

	/**
	 * Base endpoint to retrieve all visits.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return array|\WP_Error Array of visits, otherwise WP_Error.
	 */
	public function ep_get_visits( $request ) {

		$args = array();

		$args['number'] = isset( $request['number'] ) ? $request['number'] : -1;
		$args['order']  = isset( $request['order'] ) ? $request['order'] : 'ASC';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve visits in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_visits_query_args', $args, $request );

		$visits = affiliate_wp()->visits->get_visits( $args );

		if ( empty( $visits ) ) {
			$visits = new \WP_Error(
				'no_visits',
				'No visits were found.',
				array( 'status' => 404 )
			);
		}

		return $this->response( $visits );
	}

	/**
	 * Endpoint to retrieve a visit by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $args Request arguments.
	 * @return \AffWP\Visit|\WP_Error Visit object or \WP_Error object if not found.
	 */
	public function ep_visit_id( $args ) {
		if ( ! $visit = \affwp_get_visit( $args['id'] ) ) {
			$visit = new \WP_Error(
				'invalid_visit_id',
				'Invalid visit ID',
				array( 'status' => 404 )
			);
		}

		return $this->response( $visit );
	}

}
