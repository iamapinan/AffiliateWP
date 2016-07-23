<?php
namespace AffWP\REST;

/**
 * Base REST controller.
 *
 * @since 1.9
 * @abstract
 */
abstract class Controller {

	/**
	 * AffWP REST namespace.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $namespace = 'affwp/v1';

	/**
	 * The base of this controller's route.
	 *
	 * Should be defined and used by subclasses.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 *
	 * Looks for a register_routes() method in the sub-class and hooks it up to 'rest_api_init'.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 15 );
	}

	/**
	 * Converts an object or array of objects into a \WP_REST_Response object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param object|array $response Object or array of objects.
	 * @return \WP_REST_Response REST response.
	 */
	public function response( $response ) {
		if ( is_array( $response ) ) {
			$response = array_map( function( $object ) {
				$object->id = $object->ID;

				return $object;
			}, $response );
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves the query parameters for collections.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context'                => $this->get_context_param(),
			'page'                   => array(
				'description'        => __( 'Current page of the collection.' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
				'validate_callback'  => 'rest_validate_request_arg',
				'minimum'            => 1,
			),
			'per_page'               => array(
				'description'        => __( 'Maximum number of items to be returned in result set.' ),
				'type'               => 'integer',
				'default'            => 10,
				'minimum'            => 1,
				'maximum'            => 100,
				'sanitize_callback'  => 'absint',
				'validate_callback'  => 'rest_validate_request_arg',
			),
			'search'                 => array(
				'description'        => __( 'Limit results to those matching a string.' ),
				'type'               => 'string',
				'sanitize_callback'  => 'sanitize_text_field',
				'validate_callback'  => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Retrieves the magical context param.
	 *
	 * Ensures consistent description between endpoints, and populates enum from schema.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \WP_REST_Controller::get_context_param()
	 *
	 * @param array $args {
	 *     Optional. Parameter details. Default empty array.
	 *
	 *     @type string   $description       Parameter description.
	 *     @type string   $type              Parameter type. Accepts 'string', 'integer', 'array',
	 *                                       'object', etc. Default 'string'.
	 *     @type callable $sanitize_callback Parameter sanitization callback. Default 'sanitize_key'.
	 *     @type callable $validate_callback Parameter validation callback. Default empty.
	 * }
	 * @return array Context parameter details.
	 */
	public function get_context_param( $args = array() ) {
		$param_details = array(
			'description'       => __( 'Scope under which the request is made; determines fields present in response.' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => '',
		);

		return array_merge( $param_details, $args );
	}
}
