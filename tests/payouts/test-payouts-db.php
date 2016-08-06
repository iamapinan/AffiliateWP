<?php
/**
 * Tests for Affiliate_WP_Payouts_DB class
 *
 * @covers Affiliate_WP_Payouts_DB
 * @group database
 * @group payouts
 */
class Payouts_DB_Tests extends WP_UnitTestCase {


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
	 * @covers Affiliate_WP_Payouts_DB::add()
	 */
	public function test_add_should_return_false_if_affiliate_id_undefined() {
		$this->assertFalse( affiliate_wp()->affiliates->payouts->add() );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::add()
	 */
	public function test_add_should_return_false_if_invalid_affiliate_id() {
		$this->assertFalse( affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => rand( 500, 5000 )
		) ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::add()
	 */
	public function test_add_should_return_false_if_no_referrals_defined() {
		$this->assertFalse( affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => $this->_affiliate_id
		) ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::add()
	 */
	public function test_add_should_convert_array_of_referral_ids_to_comma_separated_string() {
		$payout_id = affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => $this->_affiliate_id,
			'referrals'    => range( 1, 3 )
		) );

		$this->assertSame( '1,2,3', affiliate_wp()->affiliates->payouts->get_column( 'referrals', $payout_id ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::payout_exists()
	 */
	public function test_payout_exists_should_return_false_if_payout_does_not_exist() {
		$this->assertFalse( affiliate_wp()->affiliates->payouts->payout_exists( 0 ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::payout_exists()
	 */
	public function test_payout_exists_should_return_true_if_payout_exists() {
		$this->assertTrue( affiliate_wp()->affiliates->payouts->payout_exists( $this->_payout_id ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::payout_exists()
	 */
	public function test_column_defaults_should_return_zero_for_payout_id() {
		$defaults = affiliate_wp()->affiliates->payouts->get_column_defaults();

		$this->assertSame( 0, $defaults['payout_id'] );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::payout_exists()
	 */
	public function test_column_defaults_should_return_paid_status() {
		$defaults = affiliate_wp()->affiliates->payouts->get_column_defaults();

		$this->assertSame( 'paid', $defaults['status'] );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::payout_exists()
	 */
	public function test_column_defaults_should_return_the_current_date_for_date() {
		$defaults = affiliate_wp()->affiliates->payouts->get_column_defaults();

		$this->assertSame( date( 'Y-m-d H:i:s' ), $defaults['date'] );
	}
}
