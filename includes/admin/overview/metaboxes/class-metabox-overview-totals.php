<?php
namespace AffWP\Meta_Box;

/**
 * The AffWP\Meta_Box\Overview_Totals class.
 *
 * This class extends AffWP\Meta_Box\Base, and defines
 * a meta box which displays an overview of recent affiliate
 * earnings activity, and related totals during
 * various date ranges.
 *
 * @since  1.9
 * @see    AffWP\Meta_Box\Base
 */
class Overview_Totals extends Base {

	/**
	 * Initialize.
	 *
	 * Define the meta box name, meta box id,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 * $this->action        = 'affwp_overview_meta_boxes';
	 * $this->meta_box_name = __( 'Name of the meta box', 'affiliate-wp' );
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		$this->action        = 'affwp_overview_meta_boxes';
		$this->meta_box_name = __( 'Totals', 'affiliate-wp' );
		$this->meta_box_id   = 'overview-totals';
		$this->context       = 'primary';
	}

	/**
	 * Displays the content of the metabox.
	 *
	 * @return mixed content The metabox content.
	 * @since  1.9
	 */
	public function content() { ?>

		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _e( 'Paid Earnings', 'Paid Earnings column table header', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Paid Earnings This Month', 'Paid Earnings This Month column table header', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Paid Earnings Today', 'Paid Earnings Today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->paid_earnings(); ?></td>
					<td><?php echo affiliate_wp()->referrals->paid_earnings( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->paid_earnings( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>

		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _e( 'Unpaid Referrals', 'Unpaid Referrals column table header', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Unpaid Referrals This Month', 'Unpaid Referrals This Month column table header', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Unpaid Referrals Today', 'Unpaid Referrals Today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->unpaid_count(); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_count( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_count( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>
		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _e( 'Unpaid Earnings', 'Unpaid Earnings column table header', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Unpaid Earnings This Month', 'Unpaid Earnings This Month', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Unpaid Earnings Today', 'Unpaid Earnings Today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings(); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>
<?php }
}

new Overview_Totals;
