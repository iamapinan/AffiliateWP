<?php
/**
 * Tests for Payout functions in payout-functions.php.
 *
 * @group payouts
 * @group functions
 */
class Payout_Function_Tests extends WP_UnitTestCase {

	protected $_payout_id, $_affiliate_id, $_referral_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->_affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->factory->user->create()
		) );

		$this->_referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $this->_affiliate_id
		) );

		$this->_payout_id = affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => $this->_affiliate_id,
			'referrals'    => $this->_referral_id,
			'amount'       => '10.00'
		) );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_delete_affiliate( $this->_affiliate_id );
		affwp_delete_referral( $this->_referral_id );

		parent::tearDown();
	}

	/**
	 * @covers affwp_get_payout()
	 */
	public function test_get_payout_with_an_invalid_payout_id_should_return_false() {
		$this->assertFalse( affwp_get_payout( 0 ) );
	}

	/**
	 * @covers affwp_get_payout()
	 */
	public function test_get_payout_with_a_valid_payout_id_should_return_a_payout_object() {
		$this->assertInstanceOf( 'AffWP\Affiliate\Payout', affwp_get_payout( $this->_payout_id ) );
	}

	/**
	 * @covers affwp_get_payout()
	 */
	public function test_get_payout_with_an_invalid_payout_object_should_return_false() {
		$this->assertFalse( affwp_get_payout( new \stdClass() ) );
	}

	/**
	 * @covers affwp_get_payout()
	 */
	public function test_get_payout_with_a_valid_payout_object_should_return_a_payout_object() {
		$payout = affwp_get_payout( $this->_payout_id );

		$this->assertInstanceOf( 'AffWP\Affiliate\Payout', affwp_get_payout( $payout ) );
	}

}
