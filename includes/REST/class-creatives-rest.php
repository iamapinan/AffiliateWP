<?php
namespace AffWP\Creative;

use \AffWP\REST\Controller as Controller;

/**
 * Implements REST routes and endpoints for Creatives.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class REST extends Controller {

	/**
	 * Registers Creative routes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Server $wp_rest_server Server object.
	 */
	public function register_routes( $wp_rest_server ) {
		register_rest_route( $this->namespace, '/creatives/', array(
			'methods' => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_get_creatives' ),
			'args' => array(
				/*
				 * Pass top-level args as query vars:
				 * /creatives/?status=inactive&order=desc
				 */
				'number' => array(
					'description'       => __( 'The number of creatives to query for. Use -1 for all.', 'affiliate-wp' ),
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
				 * Pass any valid get_creatives() args via filter:
				 * /creatives/?filter[status]=inactive&filter[order]=desc
				 */
				'filter' => array()
			)
		) );

		register_rest_route( $this->namespace, '/creatives/(?P<id>\d+)', array(
			'methods'  => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_creative_id' ),
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
	 * Base endpoint to retrieve all creatives.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array|\WP_Error Array of creatives, otherwise WP_Error.
	 */
	public function ep_get_creatives() {

		$args = array();

		$args['number'] = isset( $request['number'] ) ? $request['number'] : -1;
		$args['order']  = isset( $request['order'] ) ? $request['order'] : 'ASC';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve creatives in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_creatives_query_args', $args, $request );

		$creatives = affiliate_wp()->creatives->get_creatives( $args );

		if ( empty( $creatives ) ) {
			$creatives = new \WP_Error(
				'no_creatives',
				'No creatives were found.',
				array( 'status' => 404 )
			);
		}

		return $this->response( $creatives );
	}

	/**
	 * Endpoint to retrieve a creative by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $args Request arguments.
	 * @return \AffWP\Creative|\WP_Error Creative object or \WP_Error object if not found.
	 */
	public function ep_creative_id( $args ) {
		if ( ! $creative = \affwp_get_creative( $args['id'] ) ) {
			$creative = new \WP_Error(
				'invalid_creative_id',
				'Invalid creative ID',
				array( 'status' => 404 )
			);
		}

		return $this->response( $creative );
	}

}
