<?php
/**
 * Payout functions
 *
 * @since 1.9
 * @package Affiliate_WP
 */

/**
 * Retrieves a payout object.
 *
 * @since 1.9
 *
 * @param int|AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return AffWP\Affiliate\Payout|false Payout object if found, otherwise false.
 */
function affwp_get_payout( $payout = 0 ) {

	if ( is_object( $payout ) && isset( $payout->payout_id ) ) {
		$payout_id = $payout->payout_id;
	} elseif ( is_numeric( $payout ) ) {
		$payout_id = absint( $payout );
	} else {
		return false;
	}

	return affiliate_wp()->affiliates->payouts->get_object( $payout_id );
}

/**
 * Adds a payout record.
 *
 * @since 1.9
 *
 * @param array $data {
 *     Optional. Data for adding a new payout record. Default empty array.
 *
 *     @type int          $affiliate_id  Affiliate ID.
 *     @type int          $referral_id   Referral ID.
 *     @type string       $amount        Payout amount.
 *     @type string       $payout_method Payout method.
 *     @type string       $status        Payout status. Default 'paid'.
 *     @type string|array $date          Payout date.
 * }
 * @return int|false The ID for the newly-added payout, otherwise false.
 */
function affwp_add_payout( $data = array() ) {

	$args = array(
		'amount'        => 0,
		'payout_method' => '',
		'status'        => 'paid',
	);

	if ( empty( $data['referrals'] ) ) {
		return false;
	} else {
		if ( is_array( $data['referrals'] ) ) {
			$args['referrals'] = array_map( 'absint', $data['referrals'] );
		} else {
			$args['referrals'] = (array) absint( $data['referrals'] );
		}
	}

	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	} else {
		$args['affiliate_id'] = absint( $data['affiliate_id'] );
	}

	if ( ! empty( $data['amount'] ) ) {
		$args['amount'] = affwp_sanitize_amount( $data['amount'] );
	} else {
		$amount = 0;

		foreach ( $args['referrals'] as $referral_id ) {
			if ( $referral = affwp_get_referral( $referral_id ) ) {
				$amount += $referral->amount;
			}
		}
		$args['amount'] = $amount;
	}

	if ( ! empty( $data['payout_method'] ) ) {
		$args['payout_method'] = sanitize_text_field( $data['payout_method'] );
	}

	/**
	 * Filters the payout method when adding a payout.
	 *
	 * @since 1.9
	 *
	 * @param string $payout_method Payout method.
	 * @param array  $data          Data for adding a payout.
	 */
	$args['payout_method'] = apply_filters( 'affwp_add_payout_method', $args['payout_method'], $data );

	if ( ! empty( $data['status'] ) ) {
		$args['status'] = sanitize_text_field( $data['status'] );
	}

	if ( ! empty( $data['date'] ) ) {
		$args['date'] = $data['date'];
	}

	if ( $payout = affiliate_wp()->affiliates->payouts->add( $args ) ) {
		return $payout;
	}

	return false;
}

/**
 * Deletes a payout.
 *
 * @since 1.9
 *
 * @param int|\AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return bool True if the payout was successfully deleted, otherwise false.
 */
function affwp_delete_payout( $payout ) {
	if ( ! $payout = affwp_get_payout( $payout ) ) {
		return false;
	}

	// Handle updating paid referrals to unpaid.
	if ( $payout && 'paid' === $payout->status ) {
		$referrals = affiliate_wp()->affiliates->payouts->get_referral_ids( $payout );

		foreach ( $referrals as $referral_id ) {
			if ( 'paid' == affwp_get_referral_status( $referral_id ) ) {
				affwp_set_referral_status( $referral_id, 'unpaid' );
			}
		}
	}

	if ( affiliate_wp()->affiliates->payouts->delete( $payout->ID ) ) {
		/**
		 * Fires immediately after a payout has been deleted.
		 *
		 * @since 1.9
		 *
		 * @param int $payout_id Payout ID.
		 */
		do_action( 'affwp_delete_payout', $payout->ID );

		return true;
	}

	return false;
}

/**
 * Retrieves the referrals associated with a payout.
 *
 * @since 1.9
 *
 * @param int|AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return array|false List of referral objects associated with the payout, otherwise false.
 */
function affwp_get_payout_referrals( $payout = 0 ) {
	if ( ! $payout = affwp_get_payout( $payout ) ) {
		return false;
	}

	$referrals = affiliate_wp()->affiliates->payouts->get_referral_ids( $payout );

	return array_map( 'affwp_get_referral', $referrals );
}

/**
 * Retrieves the status label for a payout.
 *
 * @since 1.6
 *
 * @param int|AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return string|false The localized version of the payout status label, otherwise false.
 */
function affwp_get_payout_status_label( $payout ) {

	if ( ! $payout = affwp_get_payout( $payout ) ) {
		return false;
	}

	$statuses = array(
		'paid'     => _x( 'Paid', 'payout', 'affiliate-wp' ),
		'failed'   => __( 'Failed', 'affiliate-wp' ),
	);

	$label = array_key_exists( $payout->status, $statuses ) ? $statuses[ $payout->status ] : _x( 'Paid', 'payout', 'affiliate-wp' );

	/**
	 * Filters the payout status label.
	 *
	 * @since 1.9
	 *
	 * @param string                 $label  A localized version of the payout status label.
	 * @param AffWP\Affiliate\Payout $payout Payout object.
	 */
	return apply_filters( 'affwp_referral_status_label', $label, $payout );
}
