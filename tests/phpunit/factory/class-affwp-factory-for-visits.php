<?php

class AffWP_Factory_For_Visits extends WP_UnitTest_Factory_For_Thing {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
	}

	function create_many( $count, $args = array(), $generation_definitions = null ) {
		// Parent create_many() uses initial value of 1 and < $count.
		$count = $count - 1;

		return parent::create_many( $count, $args, $generation_definitions );
	}

	function create_object( $args ) {
		$affiliate = new AffWP_Factory_For_Affiliates();

		// Only create the associated affiliate if one wasn't supplied.
		if ( empty( $args['affiliate_id'] ) ) {
			$args['affiliate_id'] = $affiliate->create();
		}

		return affiliate_wp()->visits->add( $args );
	}

	function update_object( $visit_id, $fields ) {
		return affiliate_wp()->visits->update( $visit_id, $fields, '', 'visit' );
	}

	function get_object_by_id( $visit_id ) {
		return affwp_get_visit( $visit_id );
	}
}
