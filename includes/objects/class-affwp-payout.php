<?php
/**
 * Objects: Payout
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP\Affiliate;

/**
 * Implements a payout object.
 *
 * @since 1,9
 *
 * @see AffWP\Object
 * @see affwp_get_payouts()
 *
 * @property-read int $ID Alias for `$payout_id`.
 */
final class Payout extends \AffWP\Object {

	/**
	 * Payout ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $payout_id = 0;

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * IDs for referrals associated with the payout.
	 *
	 * @since 1.9
	 * @access public
	 * @var array
	 */
	public $referrals = array();

	/**
	 * Payout amount.
	 *
	 * @since 1.9
	 * @access public
	 * @var float
	 */
	public $amount;

	/**
	 * Payout method.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $payout_method;

	/**
	 * Payout date.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 *
	 * @see AffWP\Object::get_cache_key()
	 */
	public static $cache_token = 'affwp_payouts';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Object for accessing the affiliates DB class methods.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public static $db_group = 'affiliates:payouts';

	/**
	 * Object type.
	 *
	 * Used as the cache group and for accessing object DB classes in the parent.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $object_type = 'payouts';

	/**
	 * Sanitizes an affiliate object field.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param string $field        Object field.
	 * @param mixed  $value        Field value.
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize_field( $field, $value ) {
		if ( in_array( $field, array( 'payout_id', 'affiliate_id', 'ID' ) ) ) {
			$value = (int) $value;
		}

		if ( 'referrals' === $field ) {
			$value = implode( ',', wp_parse_id_list( $value ) );
		}

		if ( 'amount' === $field ) {
			$value = floatval( $value );
		}

		return $value;
	}

}
