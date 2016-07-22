<?php
namespace AffWP\Affiliate;

use AffWP\REST\Controller as Controller;

/**
 * Implements REST routes and endpoints for Affiliates.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class REST extends Controller {

	/**
	 * Registers Affiliate routes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Server $wp_rest_server Server object.
	 */
	public function register_routes( $wp_rest_server ) {
		register_rest_route( $this->namespace, '/affiliates/', array(
			'methods' => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_get_affiliates' )
		) );

		register_rest_route( $this->namespace, '/affiliates/(?P<id>\d+)', array(
			'methods'  => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_affiliate_id' ),
			'args'     => array(
				'id' => array(
					'required'          => true,
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
				'user' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_string( $param );
					}
				)
			),
//			'permission_callback' => function( $request ) {
//				return current_user_can( 'manage_affiliates' );
//			}
		) );
	}

	/**
	 * Base endpoint to retrieve all affiliates.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Affiliates response object or \WP_Error object if not found.
	 */
	public function ep_get_affiliates( $request ) {

		$args = array();

		$args['number'] = isset( $request['number'] ) ? $request['number'] : -1;
		$args['order']  = isset( $request['order'] ) ? $request['order'] : 'ASC';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve affiliates in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_affiliates_query_args', $args, $request );

		$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

		if ( empty( $affiliates ) ) {
			$affiliates = new \WP_Error(
				'no_affiliates',
				'No affiliates were found.',
				array( 'status' => 404 )
			);
		} else {
			$affiliates = array_map( array( $this, 'process_for_output' ), $affiliates );
		}

		return $this->response( $affiliates );
	}

	/**
	 * Endpoint to retrieve an affiliate by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $args Request arguments.
	 * @return \WP_REST_Response|\WP_Error Affiliate object response or \WP_Error object if not found.
	 */
	public function ep_affiliate_id( $args ) {
		if ( ! $affiliate = \affwp_get_affiliate( $args['id'] ) ) {
			$affiliate = new \WP_Error(
				'invalid_affiliate_id',
				'Invalid affiliate ID',
				array( 'status' => 404 )
			);
		} else {
			$user = isset( $args['user'] ) && true == (bool) $args['user'];

			// Populate extra fields and return.
			$affiliate = $this->process_for_output( $affiliate, $user );
		}

		return $this->response( $affiliate );
	}

	/**
	 * Processes an Affiliate object for output.
	 *
	 * Populates non-public properties with derived values.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate $affiliate Affiliate object.
	 * @param bool             $user      Optional. Whether to lazy load the user object. Default false.
	 * @return \AffWP\Affiliate Affiliate object.
	 */
	protected function process_for_output( $affiliate, $user = false ) {
		if ( false !== $user ) {
			$affiliate->user = $affiliate->get_user();
		}

		return $affiliate;
	}
}
