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
	 * Route base for creatives.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $base = 'creatives';

	/**
	 * Registers Creative routes.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->base, array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_items' ),
			'args'     => $this->get_collection_params(),
		) );

		register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>\d+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_item' ),
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
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Creatives response, otherwise WP_Error.
	 */
	public function get_items( $request ) {

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
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \AffWP\Creative|\WP_Error Creative object or \WP_Error object if not found.
	 */
	public function get_item( $request ) {
		if ( ! $creative = \affwp_get_creative( $request['id'] ) ) {
			$creative = new \WP_Error(
				'invalid_creative_id',
				'Invalid creative ID',
				array( 'status' => 404 )
			);
		}

		return $this->response( $creative );
	}

	/**
	 * Retrieves the collection parameters for creatives.
	 *
	 * @since 1.9
	 * @access public
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		/*
		 * Pass top-level args as query vars:
		 * /creatives/?status=inactive&order=desc
		 */
		$params['number'] = array(
			'description'       => __( 'The number of creatives to query for. Use -1 for all.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => 'is_numeric',
		);

		$params['order'] = array(
			'description'       => __( 'How to order results. Accepts ASC (ascending) or DESC (descending).', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
			}
		);

		/*
		 * Pass any valid get_creatives() args via filter:
		 * /creatives/?filter[status]=inactive&filter[order]=desc
		 */
		$params['filter'] = array(
			'description' => __( 'Use any get_creatives() arguments to modify the response.', 'affiliate-wp' )
		);

		return $params;
	}

}
