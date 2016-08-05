<?php
/**
 * 'Edit Payout' form
 *
 * @package    AffiliateWP\Admin\Payouts
 * @copyright  Copyright (c) 2014, Pippin Williamson
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.9
 */

$payout = affwp_get_payout( absint( $_GET['payout_id'] ) );
?>

<div class="wrap">

	<h2><?php _e( 'Edit Payout', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_edit_payout">

		<?php
		/**
		 * Fires at the top of the 'Edit Payout' form, just inside the form tag.
		 *
		 * @since 1.9
		 *
		 * @param \AffWP\Affiliate\Payout $payout Payout object.
		 */
		do_action( 'affwp_edit_payout_top', $payout );
		?>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="payout_id"><?php _e( 'Payout ID', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="small-text" type="text" name="payout_id" id="payout_id" value="<?php echo esc_attr( $payout->ID ); ?>" disabled="disabled" />
					<p class="description"><?php _e( 'The payout ID. This cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="affiliate_id"><?php _e( 'Affiliate ID', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="small-text" type="text" name="affiliate_id" id="affiliate_id" value="<?php echo esc_attr( $payout->affiliate_id ); ?>" disabled="disabled" />
					<p class="description"><?php _e( 'The affiliate&#8217;s ID. This cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="referrals"><?php _e( 'Referrals', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="regular-text" type="text" name="referrals" id="referrals" value="<?php echo esc_attr( $payout->referrals ); ?>" disabled="disabled" />
					<p class="description"><?php _e( 'The referrals associated with this payout. This list cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="amount"><?php _e( 'Amount', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="amount" id="amount" value="<?php echo esc_attr( $payout->amount ); ?>" disabled="disabled" />
					<p class="description"><?php _e( 'The amount of the payout, such as $15.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="payout_method"><?php _e( 'Payout Method', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="medium-text" type="text" name="payout_method" id="payout_method" value="<?php echo esc_attr( $payout->payout_method ); ?>" disabled="disabled" />
					<p class="description"><?php _e( 'The payout method used, such as &#8220;paypal&#8221;. This cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="status"><?php _e( 'Status', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="status" id="status" disabled="disabled">
						<option value="paid"<?php selected( 'paid', $payout->status ); ?>><?php _e( 'Paid', 'affiliate-wp' ); ?></option>
						<option value="failed"<?php selected( 'failed', $payout->status ); ?>><?php _e( 'Failed', 'affiliate-wp' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Current status of the payout.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="date"><?php _e( 'Date', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<?php echo date_i18n( get_option( 'date_format' ), strtotime( $payout->date ) ); ?>
				</td>

			</tr>

			<?php
			/**
			 * Fires at the end of the 'Edit Payout' form, just inside the closing table tag.
			 *
			 * @since 1.9
			 *
			 * @param \AffWP\Affiliate\Payout $payout Payout object.
			 */
			do_action( 'affwp_edit_payout_end', $payout );
			?>

		</table>

		<?php
		/**
		 * Fires at the end of the 'Edit Payout' form, just inside the closing form tag.
		 *
		 * @since 1.9
		 *
		 * @param \AffWP\Affiliate\Payout $payout Payout object.
		 */
		do_action( 'affwp_edit_affiliate_bottom', $affiliate );
		?>

		<input type="hidden" name="affwp_action" value="update_affiliate" />

		<?php submit_button( __( 'Update Payout', 'affiliate-wp' ) ); ?>

	</form>

</div>
