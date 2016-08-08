<?php

class AffWP_Factory_For_Referrals extends WP_UnitTest_Factory_For_Thing {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
	}

	function create_object( $args ) {
		$affiliate = new AffWP_Factory_For_Affiliates();

		// Only create the associated affiliate if one wasn't supplied.
		if ( empty( $args['affiliate_id'] ) ) {
			$args['affiliate_id'] = $affiliate->create();
		}

		return affiliate_wp()->referrals->add( $args );
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null  $generation_definitions
	 *
	 * @return \AffWP\Referral|int
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	function update_object( $referral_id, $fields ) {
		return affiliate_wp()->referrals->update( $referral_id, $fields, '', 'referral' );
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $referral_id
	 *
	 * @return \AffWP\Referral|false
	 */
	function get_object_by_id( $referral_id ) {
		return affwp_get_referral( $referral_id );
	}
}
