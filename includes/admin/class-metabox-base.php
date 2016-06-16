<?php
/**
 * AffiliateWP Admin Metabox Base class
 * Provides a base structure for AffiliateWP content meta boxes
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Metaboxes
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.9
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The main AffWP_Metabox_Base class.
 * This class may be extended using the example below.
 *
 * @abstract
 * @since  1.9
 */
abstract class AffWP_Metabox_Base {

    /**
     * An AffiliateWP meta box can be added to AffiliateWP by any 3rd-party source, by extending this class.
     *
     * Example:
     *
     * class My_Integration_AffWP_Metabox extends AffWP_Metabox_Base {
     *
     *    public $meta_box_id   = 'my_integration_affwp_metabox';
     *
     *    public $meta_box_name = 'My Integration AffWP Metabox';
     *
     *    // Optional; defaults to primary AffiliateWP Reports Overview page
     *    // (screen id: affiliates_page_affiliate-wp-reports)
     *    public $affwp_screen  = 'affiliates_page_affiliate-wp-reports' || array( multiple screens );
     *
     *    public function content() {
     *        $this->my_meta_box_content();
     *    }
     *
     *    public function my_meta_box_content() {
     *        echo 'Here is some super content I'd like to share with AffiliateWP users!;
     *    }
     *
     * }
     *
     * new My_Integration_AffWP_Metabox;
     *
     **/

    /**
     * The ID of the meta box. Must be unique.
     *
     * @abstract
     * @access  public
     * @var     $meta_box_id The ID of the meta box
     * @since   1.9
     */
    public $meta_box_id;

    /**
     * The name of the meta box. Must be unique.
     *
     * @abstract
     * @access  public
     * @var     $meta_box_name The name of the meta box
     * @since   1.9
     */
    public $meta_box_name;

    /**
     * The AffiliateWP screen on which to show the meta box.
     * Defaults to affiliates_page_affiliate-wp-reports, the AffiliateWP Reports Overview tab page.
     * The uri of this page is: admin.php?page=affiliate-wp-reports.
     *
     * @access  private
     * @var     $affwp_screen The screen ID of the page on which to display this meta box.
     * @since   1.9
     */
    private $affwp_screen = array( 'toplevel_page_affiliate-wp',
                                   'affiliates_page_affiliate-wp-affiliates',
                                   'affiliates_page_affiliate-wp-referrals',
                                   'affiliates_page_affiliate-wp-visits',
                                   'affiliates_page_affiliate-wp-creatives',
                                   'affiliates_page_affiliate-wp-reports',
                                   'affiliates_page_affiliate-wp-tools',
                                   'affiliates_page_affiliate-wp-settings',
                                   'affiliates_page_affiliate-wp-add-ons'
                                   );
    /**
     * The position in which the meta box will be loaded
     * Options are: 'normal', side', or 'advanced'
     *
     * 'primary'   will load in the left column
     * 'secondary' will load in the center column
     * 'tertiary'  will load in the right column
     *
     * All columns will collapse as needed on smaller screens.
     *
     * @access  public
     * @var     $context
     * @since   1.9
     */
    public $context = 'primary';

    /**
     * Constructor
     *
     * @access  public
     * @return void
     * @since   1.9
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Initialize
     *
     * @access  public
     * @return  void
     * @since   1.9
     */
    public function init() {
        add_action( 'add_meta_box',     array( $this, 'add_meta_box' ) );
        add_action( 'affwp_reports_meta_boxes', array( $this, 'add_meta_box' ) );
        $this->meta_box_name = __( 'AffiliateWP meta box name', 'affiliate-wp' );
    }

    /**
     * Adds the meta box
     *
     * @return  A meta box which will display on the specified AffiliateWP Reports admin page.
     * @uses  add_meta_box
     * @since   1.9
     */
    public function add_meta_box() {
        add_meta_box(
                                    $this->meta_box_id,
            __( $this->meta_box_name, 'affiliate-wp' ),
                             array( $this, 'content' ),
                                   $this->affwp_screen,
                                        $this->context,
                                              'default'
        );
    }

    /**
     * Defines the meta box content, as well as a
     * filter by which the content may be adjusted.
     *
     * Given a $meta_box_id value of 'my-metabox-id',
     * the filter would be: affwp_meta_box_my-meta-box-id.
     *
     * @return mixed string The content of the meta box
     * @since  1.9
     */
    public function content() {
        echo apply_filters( 'affwp_meta_box_' . $this->meta_box_id, $this->content );
    }
}