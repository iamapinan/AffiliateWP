<?php

class Capabilities_Tests extends WP_UnitTestCase {

	protected $_user_id;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->_user_id = $this->factory->user->create( array(
			'role' => 'administrator'
		) );

		// Flush the $wp_roles global.
		$this->_flush_roles();
	}

	/**
	 * @covers Affiliate_WP_Capabilities
	 */
	public function test_admin_has_caps() {

		$roles = new Affiliate_WP_Capabilities;
		$roles->add_caps();

		$user = get_user_by( 'id', $this->_user_id );

		$this->assertTrue( $user->has_cap( 'view_affiliate_reports' ) );
		$this->assertTrue( $user->has_cap( 'export_affiliate_data' ) );
		$this->assertTrue( $user->has_cap( 'export_referral_data' ) );
		$this->assertTrue( $user->has_cap( 'manage_affiliate_options' ) );
		$this->assertTrue( $user->has_cap( 'manage_affiliates' ) );
		$this->assertTrue( $user->has_cap( 'manage_referrals' ) );
		$this->assertTrue( $user->has_cap( 'manage_visits' ) );
		$this->assertTrue( $user->has_cap( 'manage_creatives' ) );

	}

	/**
	 * Helper to flush the $wp_roles global.
	 */
	public function _flush_roles() {
		/*
		 * We want to make sure we're testing against the db, not just in-memory data
		 * this will flush everything and reload it from the db
		 */
		unset( $GLOBALS['wp_user_roles'] );
		global $wp_roles;
		if ( is_object( $wp_roles ) ) {
			$wp_roles->_init();
		}
	}

}

