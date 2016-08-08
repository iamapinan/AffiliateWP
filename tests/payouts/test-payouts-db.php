<?php
/**
 * Tests for Affiliate_WP_Payouts_DB class
 *
 * @covers Affiliate_WP_Payouts_DB
 * @group database
 * @group payouts
 */
class Payouts_DB_Tests extends AffiliateWP_UnitTestCase {

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
		$affiliate_id = $this->affwp->affiliate->create();

		$this->assertFalse( affiliate_wp()->affiliates->payouts->add( array(
			'affiliate_id' => $affiliate_id
		) ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::add()
	 */
	public function test_add_should_convert_array_of_referral_ids_to_comma_separated_string() {
		$payout_id = $this->affwp->payout->create();

		$referrals = affiliate_wp()->affiliates->payouts->get_referral_ids( $payout_id );
		$referrals = implode( ',', $referrals );

		$this->assertSame( $referrals, affiliate_wp()->affiliates->payouts->get_column( 'referrals', $payout_id ) );
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
		$payout_id = $this->affwp->payout->create();

		$this->assertTrue( affiliate_wp()->affiliates->payouts->payout_exists( $payout_id ) );
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
		$payout_id = $this->affwp->payout->create();

		$this->assertInstanceOf( 'AffWP\Affiliate\Payout', affiliate_wp()->affiliates->payouts->get_object( $payout_id ) );
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
		$affiliate_id = $this->affwp->affiliate->create();

		$payout_id = $this->affwp->payout->create( array(
			'affiliate_id' => $affiliate_id,
			'referrals'    => $referrals = $this->affwp->referral->create_many( 3, array(
				'affiliate_id' => $affiliate_id
			) )
		) );

		$this->assertEqualSets( $referrals, affiliate_wp()->affiliates->payouts->get_referral_ids( $payout_id ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_payouts()
	 */
	public function test_get_payouts_number_should_return_number_if_available() {
		$this->affwp->payout->create_many( 5 );

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
		$this->affwp->payout->create_many( 5 );

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
		$this->affwp->payout->create_many( 3 );

		$this->affwp->payout->create_many( 2, array(
			'affiliate_id' => $affiliate_id = $this->affwp->affiliate->create()
		) );

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'affiliate_id' => $affiliate_id
		) );

		$this->assertSame( 2, count( $payouts ) );
		$this->assertSame( array( $affiliate_id ), array_unique( wp_list_pluck( $payouts, 'affiliate_id' ) ) );
	}

	/**
	 * @covers Affiliate_WP_Payouts_DB::get_payouts()
	 */
	public function test_get_payouts_with_multiple_affiliate_ids_should_return_payouts_for_multiple_assuming_number() {
		// Total of 4 payouts, two different affiliates.
		$this->affwp->payout->create_many( 2, array(
			'affiliate_id' => $affiliate1 = $this->affwp->affiliate->create()
		) );

		$this->affwp->payout->create_many( 2, array(
			'affiliate_id' => $affiliate2 = $this->affwp->affiliate->create()
		) );

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'affiliate_id' => array( $affiliate1, $affiliate2 )
		) );

		$affiliates = wp_list_pluck( $payouts, 'affiliate_id' );

		$this->assertTrue(
			in_array( $affiliate1, $affiliates, true )
			&& in_array( $affiliate2, $affiliates, true )
		);
	}

}
