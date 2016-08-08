<?php
/**
 * Tests for Affiliate_WP_Payouts_DB class
 *
 * @covers Affiliate_WP_Payouts_DB
 * @group database
 * @group payouts
 */
class Payouts_DB_Tests extends WP_UnitTestCase {


	protected $_affiliate_id, $_affiliate_id2, $_referral_id;

	protected $_payouts = array();
	protected $_referrals = array();

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->_referrals = range( 30, 33 );

		$this->_affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->factory->user->create()
		) );

		$this->_affiliate_id2 = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->factory->user->create()
		) );

		$this->_referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $this->_affiliate_id
		) );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_delete_affiliate( $this->_affiliate_id );
		affwp_delete_affiliate( $this->_affiliate_id2 );

		affwp_delete_referral( $this->_referral_id );

		foreach ( $this->_payouts as $payout_id ) {
			affwp_delete_payout( $payout_id );
		}

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
		$this->_set_up_payouts( 1 );

		$this->assertSame( '30,31,32,33', affiliate_wp()->affiliates->payouts->get_column( 'referrals', $this->_payouts[0] ) );
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
		$this->_set_up_payouts( 1 );

		$this->assertTrue( affiliate_wp()->affiliates->payouts->payout_exists( $this->_payouts[0] ) );
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

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_columns()
	 */
	public function test_get_columns_should_return_all_columns() {
		$columns = affiliate_wp()->affiliates->payouts->get_columns();

		$expected = array(
			'payout_id'     => '%d',
			'affiliate_id'  => '%d',
			'referrals'     => '%s',
			'amount'        => '%s',
			'payout_method' => '%s',
			'status'        => '%s',
			'date'          => '%s',
		);

		$this->assertEqualSets( $expected, $columns );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_object()
	 */
	public function test_get_object_should_return_false_if_invalid_payout_id() {
		$this->assertFalse( affiliate_wp()->affiliates->payouts->get_object( 0 ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_object()
	 */
	public function test_get_object_should_return_payout_object_if_valid_payout_id() {
		$this->_set_up_payouts();

		$this->assertInstanceOf( 'AffWP\Affiliate\Payout', affiliate_wp()->affiliates->payouts->get_object( $this->_payouts[0] ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_referral_ids()
	 */
	public function test_get_referral_ids_should_return_empty_array_if_invalid_payout_id() {
		$this->assertSame( array(), affiliate_wp()->affiliates->payouts->get_referral_ids( 0 ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_referral_ids()
	 */
	public function test_get_referral_ids_should_return_empty_array_if_invalid_payout_object() {
		$this->assertSame( array(), affiliate_wp()->affiliates->payouts->get_referral_ids( new \stdClass() ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_referral_ids()
	 */
	public function test_get_referral_ids_should_return_an_array_of_referral_ids() {
		$this->_set_up_payouts();
		$this->assertEqualSets( $this->_referrals, affiliate_wp()->affiliates->payouts->get_referral_ids( $this->_payouts[0] ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_payouts()
	 */
	public function test_get_payouts_number_should_return_number_if_available() {
		$this->_set_up_payouts( 5 );

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'number' => 3
		) );

		$this->assertSame( 3, count( $payouts ) );
		$this->assertTrue( count( $payouts ) <= 3 );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_payouts()
	 */
	public function test_get_payouts_offset_should_offset_number_given() {
		$this->_set_up_payouts( 5 );

		$all_payouts = affiliate_wp()->affiliates->payouts->get_payouts();

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'number' => 3,
			'offset' => 2,
		) );

		$this->assertEqualSets( $payouts, array_slice( $all_payouts, 2, 3 ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_payouts()
	 */
	public function test_get_payouts_with_single_affiliate_id_should_return_payouts_for_that_affiliate_only() {
		// Total of 5 payouts, two different affiliates.
		$this->_set_up_payouts();

		$this->_set_up_payouts( 2, array(
			'affiliate_id' => $this->_affiliate_id2
		) );

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'affiliate_id' => $this->_affiliate_id2
		) );

		$this->assertSame( 2, count( $payouts ) );
		$this->assertSame( array( $this->_affiliate_id2 ), array_unique( wp_list_pluck( $payouts, 'affiliate_id' ) ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_payouts()
	 */
	public function test_get_payouts_with_multiple_affiliate_ids_should_return_payouts_for_multiple_assuming_number() {
		$this->_set_up_payouts( 2, array(
			'affiliate_id' => $this->_affiliate_id
		) );

		$this->_set_up_payouts( 2, array(
			'affiliate_id' => $this->_affiliate_id2
		) );

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'affiliate_id' => array( $this->_affiliate_id, $this->_affiliate_id2 )
		) );

		$affiliates = wp_list_pluck( $payouts, 'affiliate_id' );

		$this->assertTrue(
			in_array( $this->_affiliate_id, $affiliates, true )
			&& in_array( $this->_affiliate_id2, $affiliates, true )
		);
	}

	//
	// Helpers
	//

	/**
	 * Helper to set up payouts.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int   $count       Optional. Number of payouts to create. Default 3.
	 * @param array $payout_args Optional. Arguments for adding payouts. Default empty array.
	 */
	public function _set_up_payouts( $count = 3, $payout_args = array() ) {
		$args = array_merge( array(
			'affiliate_id' => $this->_affiliate_id,
			'referrals'    => $this->_referrals
		), $payout_args );

		for ( $i = 0; $i < $count; $i++ ) {
			$this->_payouts[] = affiliate_wp()->affiliates->payouts->add( $args );
		}
	}
}
