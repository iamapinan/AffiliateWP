<?php
/**
 * Tests for Affiliate_WP_DB_Affiliates class
 *
 * @covers Affiliate_WP_DB_Affiliates
 * @group database
 * @group affiliates
 */
class Affiliate_DB_Tests extends AffiliateWP_UnitTestCase {

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_should_return_array_of_Affiliate_objects_if_not_count_query() {
		$this->affwp->affiliate->create_many( 4 );

		$results = affiliate_wp()->affiliates->get_affiliates();

		// Check a random affiliate.
		$this->assertInstanceOf( 'AffWP\Affiliate', $results[ rand( 0, 3 ) ] );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_should_return_integer_if_count_query() {
		$this->affwp->affiliate->create_many( 4 );

		$results = affiliate_wp()->affiliates->get_affiliates( array(), $count = true );

		$this->assertTrue( is_numeric( $results ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_method_should_allow_searching_by_display_name() {
		$display_name = 'Foo';

		$user = $this->factory->user->create_and_get( array(
			'display_name' => $display_name
		) );

		// Add the affiliate.
		$this->affwp->affiliate->create( array(
			'user_id' => $user->ID
		) );

		// Get affiliates based on the search term(s).
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'search' => 'foo'
		) );

		// Assert that results were found.
		$this->assertNotEmpty( $results );

		// Assert that the result we're looking for was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user->ID ) ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_method_should_allow_searching_by_user_login() {
		$user_login = 'foo_bar';

		$user = $this->factory->user->create_and_get( array(
			'user_login' => $user_login
		) );

		// Add the affiliate.
		$this->affwp->affiliate->create( array(
			'user_id' => $user->ID
		) );

		// Get affiliates based on the search term(s).
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'search' => 'foo'
		) );

		// Assert that results were found.
		$this->assertNotEmpty( $results );

		// Assert that the result we're looking for was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user->ID ) ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_method_should_allow_searching_either_display_name_or_user_login() {
		$display_name = 'Bar Baz';
		$user_login   = 'foo_bar';

		$user = $this->factory->user->create( array(
			'display_name' => $display_name,
			'user_login'   => 'Scooby Doo'
		) );

		$user2 = $this->factory->user->create( array(
			'display_name' => 'Garfield',
			'user_login'   => $user_login
		) );

		$user3 = $this->factory->user->create( array(
			'display_name' => 'Nemo',
			'user_login'   => 'Dory'
		) );

		// Add affiliates.
		foreach ( array( $user, $user2, $user3 ) as $id ) {
			$this->affwp->affiliate->create( array(
				'user_id' => $id
			) );
		}

		// Get affiliates based on the search term(s).
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'search' => 'bar'
		) );

		// Assert two results were found.
		$this->assertCount( 2, $results );

		// Assert that the first user was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user ) ) );

		// Assert that the second user was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user2 ) ) );

		// Assert that the third user wasn't found.
		$this->assertEmpty( wp_list_filter( $results, array( 'user_id' => $user3 ) ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_with_integer_user_id_should_return_affiliate_for_that_user() {
		$user = $this->factory->user->create_and_get();

		// Add the affiliate.
		$affilite_id = $this->affwp->affiliate->create( array(
			'user_id' => $user->ID
		) );

		// Query affiliates.
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'user_id' => $user->ID
		) );

		$this->assertEqualSets( array( $affilite_id ), wp_list_pluck( $results, 'affiliate_id' ) );
		$this->assertEqualSets( array( $user->ID ), wp_list_pluck( $results, 'user_id' ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_with_array_of_user_ids_should_return_matching_affiliates() {
		$users = $this->factory->user->create_many( 3 );

		// Add affiliates.
		foreach ( $users as $user_id ) {
			$this->affwp->affiliate->create( array(
				'user_id' => $user_id
			) );
		}

		// Query affiliates.
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'user_id' => $users
		) );

		$found_users = wp_list_pluck( $results, 'user_id' );
		// Users.
		$this->assertTrue( in_array( $users[0], $found_users ) );
		$this->assertTrue( in_array( $users[1], $found_users ) );
		$this->assertTrue( in_array( $users[2], $found_users ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_with_integer_affiliate_id_should_return_that_affiliate() {
		$user = $this->factory->user->create_and_get();

		// Add the affiliate.
		$affiliate_id = $this->affwp->affiliate->create( array(
			'user_id' => $user->ID
		) );

		// Query affiliates.
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'affiliate_id' => $affiliate_id
		) );

		$this->assertEqualSets( array( $affiliate_id ), wp_list_pluck( $results, 'affiliate_id' ) );
		$this->assertEqualSets( array( $user->ID ), wp_list_pluck( $results, 'user_id' ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_with_array_of_affiliate_ids_should_return_matching_affiliates() {
		$users = $this->factory->user->create_many( 3 );
		$affiliates = array();

		// Add affiliates.
		foreach ( $users as $user_id ) {
			$affiliates[] = $this->affwp->affiliate->create( array(
				'user_id' => $user_id
			) );
		}

		// Query affiliates.
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'affiliate_id' => $affiliates
		) );

		$found_affiliates = wp_list_pluck( $results, 'affiliate_id' );

		$this->assertTrue( in_array( $affiliates[0], $found_affiliates ) );
		$this->assertTrue( in_array( $affiliates[1], $found_affiliates ) );
		$this->assertTrue( in_array( $affiliates[2], $found_affiliates ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_default_orderby_should_order_by_affiliate_id() {
		$affiliates = $this->affwp->affiliate->create_many( 3 );

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'order' => 'ASC'
		) );

		// Order should be as created, 0, 1, 2.
		$this->assertEquals( $affiliates[0], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[1], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[2], $results[2]->affiliate_id );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_orderby_status_should_order_by_status() {
		$affiliates = array(
			$this->affwp->affiliate->create( array( 'status' => 'active' ) ),
			$this->affwp->affiliate->create( array( 'status' => 'rejected' ) ),
			$this->affwp->affiliate->create( array( 'status' => 'inactive' ) )
		);

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby' => 'status',
			'order'   => 'ASC', // A-Z
		) );

		// Order should be alphabetical: 0 (active), 2 (inactive), 1 (rejected).
		$this->assertEquals( $affiliates[0], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[2], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[1], $results[2]->affiliate_id );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_orderby_date_should_order_by_registered_date() {
		$affiliates = array(
			$this->affwp->affiliate->create( array( 'date_registered' => ( time() - WEEK_IN_SECONDS ) ) ),
			$this->affwp->affiliate->create( array( 'date_registered' => ( time() + WEEK_IN_SECONDS ) ) ),
			$this->affwp->affiliate->create()
		);

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby' => 'date', // Default 'order' is DESC
		) );

		// Order should be newest to oldest: 2, 0, 1.
		$this->assertEquals( $affiliates[2], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[0], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[1], $results[2]->affiliate_id );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_orderby_name_should_order_by_user_display_name() {
		$users = array(
			$this->factory->user->create( array( 'display_name' => 'Bravo' ) ),
			$this->factory->user->create( array( 'display_name' => 'Alpha' ) ),
			$this->factory->user->create( array( 'display_name' => 'Charlie' ) )
		);

		$affiliates = array();

		foreach ( $users as $user_id ) {
			$affiliates[] = $this->affwp->affiliate->create( array(
				'user_id' => $user_id
			) );
		}

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby' => 'name', // Default 'order' is 'DESC'
		) );

		// Order should be reverse alphabetical: 2 (Charlie), 0 (Beta), 1 (Alpha).
		$this->assertEquals( $affiliates[2], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[0], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[1], $results[2]->affiliate_id );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_orderby_username_should_order_by_user_login() {
		$users = array(
			$this->factory->user->create( array( 'user_login' => 'delta' ) ),
			$this->factory->user->create( array( 'user_login' => 'foxtrot' ) ),
			$this->factory->user->create( array( 'user_login' => 'echo' ) )
		);

		$affiliates = array();

		foreach ( $users as $user_id ) {
			$affiliates[] = $this->affwp->affiliate->create( array(
				'user_id' => $user_id
			) );
		}

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby' => 'username',
			'order'   => 'ASC'
		) );

		// Order should be 0 (delta), 2 (echo), 1 (foxtrot).
		$this->assertEquals( $affiliates[0], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[2], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[1], $results[2]->affiliate_id );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_orderby_valid_referral_status_should_order_by_that_referral_status_count() {
		$affiliates = $this->affwp->affiliate->create_many( 3 );

		$referrals = array();

		// Add 1 'unpaid' referral for affiliate 1.
		$this->affwp->referral->create( array(
			'affiliate_id' => $affiliates[0],
			'status'       => 'unpaid'
		) );

		// Add 3 'unpaid' referrals for affiliate 2.
		for ( $i = 0; $i < 3; $i++ ) {
			$this->affwp->referral->create( array(
				'affiliate_id' => $affiliates[1],
				'status'       => 'unpaid'
			) );
		}

		// Add 2 'paid' referrals for affiliate 3.
		for ( $i = 0; $i < 2; $i++ ) {
			$this->affwp->referral->create( array(
				'affiliate_id' => $affiliates[2],
				'status'       => 'paid'
			) );
		}

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby' => 'unpaid',
			'order'   => 'ASC', // Small to large.
		) );

		// Order should be 2 (zero unpaid), 0 (1 unpaid), 1 (3 unpaid).
		$this->assertEquals( $affiliates[2], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[0], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[1], $results[2]->affiliate_id );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_orderby_invalid_referral_status_should_default_to_order_by_primary_key() {
		$affiliates = $this->affwp->affiliate->create_many( 3 );

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby' => rand_str( 15 )
		) );

		// With invalid orderby, should return ordered by affiliate_id, descending.
		$this->assertEquals( $affiliates[2], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[1], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[0], $results[2]->affiliate_id );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_orderby_earnings_should_order_by_earnings() {
		$affiliates = $this->affwp->affiliate->create_many( 3 );

		affiliate_wp()->affiliates->update( $affiliates[0], array( 'earnings' => '20' ) );
		affiliate_wp()->affiliates->update( $affiliates[1], array( 'earnings' => '10' ) );
		affiliate_wp()->affiliates->update( $affiliates[2], array( 'earnings' => '30' ) );

		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby' => 'earnings',
			'order'   => 'ASC',
		) );

		// Order should least to greatest: 1 (10), 0 (20), 2 (30).
		$this->assertEquals( $affiliates[1], $results[0]->affiliate_id );
		$this->assertEquals( $affiliates[0], $results[1]->affiliate_id );
		$this->assertEquals( $affiliates[2], $results[2]->affiliate_id );
	}

}
