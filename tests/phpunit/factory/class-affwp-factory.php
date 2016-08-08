<?php

/**
 * A factory for making WordPress data with a cross-object type API.
 *
 * Tests should use this factory to generate test fixtures.
 */
class AffWP_Factory extends WP_UnitTest_Factory {

	/**
	 * @var AffWP_Factory_For_Affiliates
	 */
	public $affiliate;

	/**
	 * @var AffWP_Factory_For_Creatives
	 */
	public $creative;

	/**
	 * @var AffWP_Factory_For_Referrals
	 */
	public $referral;

	/**
	 * @var AffWP_Factory_For_Visits
	 */
	public $visit;

	function __construct() {
		$this->affiliate = new AffWP_Factory_For_Affiliates( $this );
		$this->creative  = new AffWP_Factory_For_Creatives( $this );
		$this->referral  = new AffWP_Factory_For_Referrals( $this );
		$this->visit     = new AffWP_Factory_For_Visits( $this );
	}
}
