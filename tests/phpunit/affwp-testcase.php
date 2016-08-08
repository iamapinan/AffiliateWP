<?php

require_once dirname( __FILE__ ) . '/factory.php';

/**
 * Defines a basic fixture to run multiple tests.
 *
 * Resets the state of the WordPress installation before and after every test.
 *
 * Includes utility functions and assertions useful for testing WordPress.
 *
 * All WordPress unit tests should inherit from this class.
 */
class AffiliateWP_UnitTestCase extends WP_UnitTestCase {

	function __get( $name ) {
		if ( 'affwp' === $name ) {
			return self::affwp();
		} else {
			// Needed to ensure $this->factory still works for core objects.
			return parent::__get( $name );
		}
	}

	protected static function affwp() {
		static $factory = null;
		if ( ! $factory ) {
			$factory = new AffWP_Factory();
		}
		return $factory;
	}

}
