<?php
namespace AffWP\Meta_Box;

/**
 * The AffWP\Meta_Box\Overview_Recent_Referral_Visits class.
 *
 * This class extends AffWP\Meta_Box\Base, and defines
 * a meta box which displays recent referrals and visits.
 *
 * @since  1.9
 * @see    AffWP\Meta_Box\Base
 */
class Overview_Recent_Referral_Visits extends Base {

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
		$this->meta_box_name = __( 'Recent Referral Visits', 'affiliate-wp' );
		$this->meta_box_id   = 'overview-recent-referral-visits';
		$this->context       = 'tertiary';
	}

	/**
	 * Defines the content of the metabox.
	 *
	 * @return mixed content  The metabox content.
	 * @since  1.9
	 */
	public function content() {

	$visits = affiliate_wp()->visits->get_visits(
		/**
 		 * Filter the get_visits() query.
 		 *
 		 * @param array The query arguments for get_visits().
 		 *              By default, this query shows the eight
 		 *              most recent referral visits.
 		 * @since 1.9
 		 *
 		 */
		apply_filters( 'affwp_overview_recent_referral_visits',
			array( 'number' => 8 )
		)
	); ?>

		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _e( 'Affiliate', 'Affiliate column table header', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'URL', 'URL column table header', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Converted', 'Converted column table header', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
				<?php if( $visits ) : ?>
					<?php foreach( $visits as $visit ) : ?>
						<tr>
							<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $visit->affiliate_id ); ?></td>
							<td><a href="<?php echo esc_url( $visit->url ); ?>"><?php echo esc_html( $visit->url ); ?></a></td>
							<td>
								<?php $converted = ! empty( $visit->referral_id ) ? 'yes' : 'no'; ?>
								<span class="visit-converted <?php echo esc_attr( $converted ); ?>" aria-label="<?php printf( esc_attr__( 'Visit converted: %s', 'affiliate-wp' ), $converted ); ?>"><i></i></span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="3"><?php _e( 'No referral visits recorded yet', 'affiliate-wp' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>

		</table>
	<?php }
}

new Overview_Recent_Referral_Visits;
