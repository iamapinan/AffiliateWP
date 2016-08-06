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

	/**
	 * @covers affwp_delete_payout()
	 */
	public function test_delete_payout_should_return_false_if_invalid_payout_id() {
		$this->assertFalse( affwp_delete_payout( 0 ) );
	}

	/**
	 * @covers affwp_delete_payout()
	 */
	public function test_delete_payout_should_return_false_if_invalid_payout_object() {
		$this->assertFalse( affwp_delete_payout( new \stdClass() ) );
	}

	/**
	 * @covers affwp_delete_payout()
	 */
	public function test_delete_payout_should_return_true_if_payout_deleted_successfully() {
		$this->assertTrue( affwp_delete_payout( $this->_payout_id ) );
	}

	/**
	 * @covers affwp_delete_payout()
	 */
	public function test_delete_payout_should_reset_paid_referral_status_to_unpaid() {
		
	}

	/**
	 * @covers affwp_get_payout_referrals()
	 */
	public function test_get_payout_referrals_should_return_false_if_invalid_payout() {
		$this->assertFalse( affwp_get_payout_referrals( 0 ) );
		$this->assertFalse( affwp_get_payout_referrals( new \stdClass() ) );
	}

	/**
	 * @covers affwp_get_payout_referrals()
	 */
	public function test_get_payout_referrals_should_return_array_of_referral_objects() {
		$referrals = array();
		for ( $i = 1; $i <= 3; $i++ ) {
			$referrals[] = affiliate_wp()->referrals->add( array(
				'affiliate_id' => $this->_affiliate_id
			) );
		}

		$payout_id = affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => $this->_affiliate_id,
			'referrals'    => $referrals
		) );

		$payout_referrals = affwp_get_payout_referrals( $payout_id );

		$this->assertSame( $referrals, wp_list_pluck( $payout_referrals, 'referral_id' ) );
		$this->assertInstanceOf( 'AffWP\Referral', $payout_referrals[0] );
	}

	/**
	 * @covers affwp_get_payout_status_label()
	 */
	public function test_get_payout_status_label_should_return_false_if_invalid_payout() {
		$this->assertFalse( affwp_get_payout_status_label( 0 ) );
		$this->assertFalse( affwp_get_payout_status_label( new \stdClass() ) );
	}

	/**
	 * @covers affwp_get_payout_status_label()
	 */
	public function test_get_payout_status_label_should_return_paid_status_by_default() {
		$this->assertSame( 'Paid', affwp_get_payout_status_label( $this->_payout_id ) );
	}

	/**
	 * @covers affwp_get_payout_status_label()
	 */
	public function test_get_payout_status_label_should_return_payout_status_label() {
		$payout_id = affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => $this->_affiliate_id,
			'status'       => 'failed',
			'referrals'    => range( 100, 103 )
		) );

		$this->assertSame( 'Failed', affwp_get_payout_status_label( $payout_id ) );
	}

	/**
	 * @covers affwp_get_payout_status_label()
	 */
	public function test_get_payout_status_label_should_return_paid_if_invalid_status() {
		$payout_id = affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => $this->_affiliate_id,
			'status'       => 'foo',
			'referrals'    => range( 50, 55 )
		) );

		$this->assertSame( 'Paid', affwp_get_payout_status_label( $payout_id ) );
	}
}
