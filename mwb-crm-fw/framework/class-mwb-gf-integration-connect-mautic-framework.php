<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/framework
 */

if ( ! class_exists( 'Mwb_Gf_Integration_Connect_Framework_New' ) ) {
	wp_die( 'Mwb_Gf_Integration_Connect_Framework_New does not exists.' );
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/framework
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Mwb_Gf_Integration_Connect_Mautic_Framework extends Mwb_Gf_Integration_Connect_Framework_New {

	/**
	 *  The instance of this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $instance    The instance of this class.
	 */
	private static $instance;

	/**
	 * Main Mwb_Gf_Integration_Connect_Mautic_Framework Instance.
	 *
	 * Ensures only one instance of Mwb_Gf_Integration_Connect_Mautic_Framework is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Mwb_Gf_Integration_Connect_Mautic_Framework - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get current mapping scenerio for current CRM connection.
	 *
	 * @param    mixed $form_id   GF Form ID.
	 * @since    1.0.0
	 * @return   array - Current CRM to GF mapping.
	 */
	public function getMappingDataset( $form_id = '' ) {

		if ( empty( $form_id ) ) {
			return;
		}

		$obj_type = array(
			'gf_edit_forms',
		);

		$formatted_dataset = array();
		foreach ( $obj_type as $key => $obj ) {
			$formatted_dataset[ $obj ] = $this->getMappingOptions( $form_id );
		}

		$formatted_dataset = $this->parse_labels( $formatted_dataset );
		return $formatted_dataset;
	}

	/**
	 * Get current mapping scenerio for current CRM connection.
	 *
	 * @param    string $id    GF form ID.
	 * @since    1.0.0
	 * @return   array         Current CRM to GF mapping.
	 */
	public function getMappingOptions( $id = false ) {
		return $this->get_gf_meta( $id );
	}

	/**
	 * Get available filter options.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function getFilterMappingDataset() {
		return $this->get_avialable_form_filters();
	}

	/**
	 * Create log folder.
	 *
	 * @param     string $path    Name of log folder.
	 * @since     1.0.0
	 * @return    mixed
	 */
	public function create_log_folder( $path ) {

		$basepath = WP_CONTENT_DIR . '/uploads/';
		$fullpath = $basepath . $path;

		if ( ! empty( $fullpath ) ) {

			if ( ! is_dir( $fullpath ) ) {

				$folder = mkdir( $fullpath, 0755, true );

				if ( $folder ) {
					return $fullpath;
				}
			} else {
				return $fullpath;
			}
		}
		return false;
	}

	/**
	 * Get link to data sent over mautic.
	 *
	 * @param      string $object  Object of mautic.
	 * @param      string $sf_id   An array of data synced over mautic.
	 * @since      1.0.0
	 * @return     string
	 */
	public function get_mautic_link( $object, $sf_id = false ) {

		if ( false == $sf_id ) { // phpcs:ignore
			return;
		}
		$obj = '';
		if ( 'contact' === $object ) {
			$obj = 'contacts';
		} elseif ( 'company' === $object ) {
			$obj = 'companies';
		}
		$mautic_url = get_option( 'mwb-' . $this->crm_slug . '-gf-redirect-url' );
		$link       = $sf_id;

		if ( '-' != $sf_id ) { // phpcs:ignore
			$link = $mautic_url . '/s/' . $obj . '/view/' . $sf_id;
		}

		return $link;

	}

	/**
	 * Returns count of synced data.
	 *
	 * @since     1.0.0
	 * @return    integer
	 */
	public function get_synced_forms_count() {

		global $wpdb;
		$table_name  = $wpdb->prefix . 'mwb_sf_gf_log';
		$col_name    = 'mautic_id';
		$count_query = "SELECT COUNT(*) as `total_count` FROM {$table_name} WHERE {$col_name} != '-'"; // phpcs:ignore
		$count_data  = $wpdb->get_col( $count_query ); // phpcs:ignore
		$total_count = isset( $count_data[0] ) ? $count_data[0] : '0';

		return $total_count;
	}




	// End of class.
}
