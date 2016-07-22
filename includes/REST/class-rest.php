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
	 * Registers REST routes.
	 *
	 * Must be defined by sub-classes.
	 *
	 * @since 1.9
	 * @access public
	 * @abstract
	 *
	 * @param WP_REST_Server $wp_rest_server Server object.
	 */
	abstract public function register_routes( $wp_rest_server );

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
}
