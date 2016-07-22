<?php
namespace AffWP\Referral;

use \AffWP\REST\Controller as Controller;

/**
 * Implements REST routes and endpoints for Referrals.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class REST extends Controller {

	/**
	 * Registers Referral routes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Server $wp_rest_server Server object.
	 */
	public function register_routes( $wp_rest_server ) {
		register_rest_route( $this->namespace, '/referrals/', array(
			'methods' => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_get_referrals' ),
			'args' => array(
				/*
				 * Pass top-level args as query vars:
				 * /referrals/?status=pending&order=desc
				 */
				'number' => array(
					'description'       => __( 'The number of referrals to query for. Use -1 for all.', 'affiliate-wp' ),
					'sanitize_callback' => 'absint',
					'validate_callback' => 'is_numeric',
				),
				'offset' => array(
					'description'       => __( 'The number of referrals to offset in the query.', 'affiliate-wp' ),
					'sanitize_callback' => 'absint',
					'validate_callback' => 'is_numeric',
				),
				'referral_id' => array(
					'description'       => __( 'The referral ID or array of IDs to query for.', 'affiliate-wp' ),
					'sanitize_callback' => 'absint',
					'validate_callback' => 'is_numeric',
				),
				'affiliate_id' => array(
					'description'       => __( 'The affiliate ID or array of IDs to query for.', 'affiliate-wp' ),
					'sanitize_callback' => 'absint',
					'validate_callback' => 'is_numeric',
				),
				'reference' => array(
					'description'       => __( 'Reference information (product ID) for the referral.', 'affiliate-wp' ),
					'sanitize_callback' => 'sanitize_text_field',
				),
				'context' => array(
					'description'       => __( 'The context under which the referral was created (integration).', 'affiliate-wp' ),
					'sanitize_callback' => 'sanitize_text_field',
				),
				'campaign' => array(
					'description'       => __( 'The associated campaign.', 'affiliate-wp' ),
					'sanitize_callback' => 'sanitize_text_field',
				),
				'status' => array(
					'description'       => __( 'The referral status or array of statuses.', 'affiliate-wp' ),
					'validate_callback' => function( $param, $request, $key ) {
						return in_array( $param, array( 'paid', 'unpaid', 'pending', 'rejected' ) );
					},
				),
				'orderby' => array(
					'description'       => __( 'Referrals table column to order by.', 'affiliate-wp' ),
					'validate_callback' => function( $param, $request, $key ) {
						return array_key_exists( $param, affiliate_wp()->referrals->get_columns() );
					}
				),
				'order' => array(
					'description'       => __( 'How to order results. Accepts ASC (ascending) or DESC (descending).', 'affiliate-wp' ),
					'validate_callback' => function( $param, $request, $key ) {
						return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
					}
				),
				'search' => array(
					'description'       => __( 'The search string to query for referrals with.', 'affiliate-wp' ),
					'sanitize_callback' => 'sanitize_text_field'
				),
				'date' => array(
					'description'       => __( 'The date array or string to query referrals within.', 'affiliate-wp' ),
					'validate_callback' => function( $param, $request, $key ) {
						return rest_parse_date( $param );
					}
				),

				/*
				 * Pass any valid get_referrals() args via filter:
				 * /referrals/?filter[status]=pending&filter[order]=desc
				 */
				'filter' => array()
			)
		) );

		register_rest_route( $this->namespace, '/referrals/(?P<id>\d+)', array(
			'methods'  => $wp_rest_server::READABLE,
			'callback' => array( $this, 'ep_referral_id' ),
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
	 * Base endpoint to retrieve all referrals.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Referrals query response, otherwise \WP_Error.
	 */
	public function ep_get_referrals( $request ) {

		$args = array();

		$args['number']       = isset( $request['number'] ) ? $request['number'] : -1;
		$args['offset']       = $request['offset'];
		$args['referral_id']  = $request['referral_id'];
		$args['affiliate_id'] = $request['affiliate_id'];
		$args['reference']    = $request['reference'];
		$args['context']      = $request['context'];
		$args['campaign']     = $request['campaign'];
		$args['status']       = $request['status'];
		$args['orderby']      = $request['orderby'];
		$args['order']        = isset( $request['order'] ) ? $request['order'] : 'ASC';
		$args['search']       = $request['search'];
		$args['date']         = $request['date'];

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve referrals in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_referrals_query_args', $args, $request );

		$referrals = affiliate_wp()->referrals->get_referrals( $args );

		if ( empty( $referrals ) ) {
			$referrals = new \WP_Error(
				'no_referrals',
				'No referrals were found.',
				array( 'status' => 404 )
			);
		}

		return $this->response( $referrals );
	}

	/**
	 * Endpoint to retrieve a referral by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $args Request arguments.
	 * @return \WP_REST_Response|\WP_Error Response object or \WP_Error object if not found.
	 */
	public function ep_referral_id( $args ) {
		if ( ! $referral = \affwp_get_referral( $args['id'] ) ) {
			$referral = new \WP_Error(
				'invalid_referral_id',
				'Invalid referral ID',
				array( 'status' => 404 )
			);
		}

		return $this->response( $referral );
	}

}
