<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/public
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_With_Mautic_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Current crm slug.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $crm_slug    The current crm slug.
	 */
	public $crm_slug;

	/**
	 * Current crm name.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string   $crm_name    The current crm name.
	 */
	public $crm_name;

	/**
	 * Insatance of the current fom fields
	 *
	 * @since    1.0.0
	 * @var      array    An array of form fields data.
	 */
	public $form_fields;

	/**
	 * Instance of the request module class.
	 *
	 * @var      object $framework Instance of the request module class.
	 * @since    1.0.0
	 */
	public $request_module;

	/**
	 * Current user.
	 *
	 * @since    1.0.0
	 * @var      integer $user
	 */
	public $user_details;

	/**
	 * Instance of Admin class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $admin  Instance of the Admin class.
	 */
	private $admin;

	/**
	 * Instance of Connect manager class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $connect_manager  Instance of the Connect manager class.
	 */
	private $connect_manager;

	/**
	 * Instance of the current class.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     object    $instance    Instance of the current class.
	 */
	protected static $_instance = null; // phpcs:ignore

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		self::$_instance = $this;

		// Initialise CRM name and slug.
		$this->crm_slug = get_current_crm_mautic( 'slug' );
		$this->crm_name = get_current_crm_mautic();

		// Initialise request module class.
		$request_class        = 'Mwb_Gf_Integration_' . $this->crm_name . '_Request_Module';
		$this->request_module = $request_class::get_instance();

		// Initialise Connect manager class.
		$connect               = 'Mwb_Gf_Integration_Connect_' . $this->crm_name . '_Framework';
		$this->connect_manager = $connect::get_instance();

		// Initialise admin class.
		$this->admin = 'Mwb_Gf_Integration_With_' . $this->crm_name . '_Admin';
	}

	/**
	 * Main Mwb_Gf_Integration_With_Mautic_Public Instance.
	 *
	 * Ensures only one instance of Mwb_Gf_Integration_With_Mautic_Public is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @return Mwb_Gf_Integration_With_Mautic_Public - Main instance.
	 */
	public static function get_instance() {

		if ( null == self::$_instance ) { //phpcs:ignore

			self::$_instance = new self( 'mwb-gf-integration-with-mautic', MWB_GF_INTEGRATION_WITH_MAUTIC_VERSION );
		}
		return self::$_instance;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Gf_Integration_With_Mautic_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Gf_Integration_With_Mautic_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mwb-gf-integration-with-mautic-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Gf_Integration_With_Mautic_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Gf_Integration_With_Mautic_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mwb-gf-integration-with-mautic-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Get current looged in user.
	 *
	 * @since    1.0.1
	 * @return   void
	 */
	public function mwb_mtc_gf_logged_user_info() {
		if ( is_user_logged_in() ) {
			$this->user_details = wp_get_current_user();
		}
	}

	/**
	 * Get entry of the form after submission
	 *
	 * @param array $entry Entry fields array.
	 * @param array $form  Form attributes array with field object.
	 * @return void
	 */
	public function get_form_entries( $entry, $form ) {

		$entry = apply_filters( 'mwb_mautic_gf_modify_entry_filter', $entry );

		do_action( 'mwb_mautic_gf_use_entry_data', $entry, $form );

		$form_data         = $this->request_module->retrieve_form_data( $entry, $form );
		$this->form_fields = $this->connect_manager->getMappingOptions( $form_data['id'] );
		$this->mwb_sf_gf_create_form_submission( $form_data, $_SERVER );
	}

	/**
	 * Create form data enrty.
	 * For org instantly save data for pro create action hook to save form entries.
	 *
	 * @param    array $form_data         An array of form data.
	 * @param    array $additional_info   An array of additional information related to form entry.
	 * @since    1.0.0
	 * @return   mixed
	 */
	public function mwb_sf_gf_create_form_submission( $form_data, $additional_info ) {

		if ( empty( $form_data ) || ! is_array( $form_data ) ) {
			return;
		}
		$this->mwb_sf_gf_send_to_crm( $form_data );
	}

	/**
	 * Send form data over crm ( Mautic ).
	 *
	 * @param     array $data   An array of form data and entries.
	 * @since     1.0.0
	 * @return    mixed
	 */
	public function mwb_sf_gf_send_to_crm( $data = array() ) {

		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		if ( ! $this->connect_manager->is_crm_connected() ) { // if disconnected return.
			return;
		}

		$api          = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api_Base';
		$api_module   = $api::get_instance();
		$active_feeds = $this->request_module->get_feeds_by_form_id( $data['id'] );
		$filter_exist = false;

		if ( ! empty( $active_feeds ) && is_array( $active_feeds ) ) {
			foreach ( $active_feeds as $key => $feed_id ) {

				$filter_exist = $this->request_module->maybe_check_filter( $feed_id );
				$crm_object   = $this->fetch_feed_data( $feed_id, 'mwb-' . $this->crm_slug . '-gf-object', '' );
				$manual_sync  = $this->fetch_feed_data( $feed_id, 'mwb-' . $this->crm_slug . '-gf-manual-sync', '' );

				if ( 'yes' == $manual_sync ) { // phpcs:ignore
					continue; // If manual sync enabled skip this feed from syncing to CRM.
				}

				$log_data = array(
					'feed_id'   => $feed_id,
					'feed_name' => get_the_title( $feed_id ),
					'sf_object' => $crm_object,
				);

				$request = $this->request_module->get_crm_request( $crm_object, $feed_id, $data['values'] );
				if ( ! empty( $filter_exist ) ) {
					$filter_result = $this->mwb_sf_gf_validate_filter( $filter_exist, $data['values'] );

					if ( true === $filter_result ) { // If filter results true, then send data to CRM.
						$result = $api_module->create_or_update_record( $crm_object, $request, false, $log_data );
						$this->mwb_sf_gf_check_error_for_mail( $result, $data, $log_data, $request );

					} elseif ( is_array( $filter_result ) && false == $filter_result['result'] ) { // phpcs:ignore
						$result = $api_module->create_or_update_record( $crm_object, $request, false, $log_data );
						$this->mwb_sf_gf_check_error_for_mail( $result, $data, $log_data, $request );

					}
				} else {
					$result = $api_module->create_or_update_record( $crm_object, $request, false, $log_data );
					$this->mwb_sf_gf_check_error_for_mail( $result, $data, $log_data, $request );
				}
			}
		}

	}

	/**
	 * Fetch feeds data.
	 *
	 * @param      int    $post_id          Feed ID.
	 * @param      string $key              Data key.
	 * @param      string $default          Default value.
	 * @since      1.0.0
	 * @return     mixed
	 */
	public function fetch_feed_data( $post_id, $key, $default ) {

		$feed_data = get_post_meta( $post_id, $key, true );
		$feed_data = ! empty( $feed_data ) ? $feed_data : $default;
		return $feed_data;
	}


	/**
	 * Validate form entries with feeds filter conditions.
	 *
	 * @param     array $filters    An array of filter data.
	 * @param     array $data       Form data.
	 * @since     1.0.0
	 * @return    bool
	 */
	public function mwb_sf_gf_validate_filter( $filters = array(), $data = array() ) {

		if ( ! empty( $filters ) && is_array( $filters ) ) {

			foreach ( $filters as $or_key => $or_filters ) {
				$result = true;

				if ( is_array( $or_filters ) ) {

					foreach ( $or_filters as $and_key => $and_filter ) {
						if ( '-1' == $and_filter['field'] || '-1' == $and_filter['option'] ) { // phpcs:ignore
							return array( 'result' => false );
						}

						$form_field = $and_filter['field'];
						$feed_value = ! empty( $and_filter['value'] ) ? $and_filter['value'] : '';
						$entry_val  = $this->mwb_sf_gf_get_entry_values( $form_field, $data );
						$result     = $this->request_module->is_value_allowed( $and_filter['option'], $feed_value, $entry_val );

						if ( false == $result ) { // phpcs:ignore
							break;
						}
					}
				}

				if ( true === $result ) {
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Verify and get entered field values.
	 *
	 * @param     string $field      Form field whose value to verify.
	 * @param     array  $entries    An array of form entries.
	 * @since     1.0.0
	 * @return    mixed              value of the field
	 */
	public function mwb_sf_gf_get_entry_values( $field, $entries ) {

		$value = false;

		$form_fields = $this->form_fields;
		$field_type  = isset( $form_fields[ $field ]['type'] ) ? $form_fields[ $field ]['type'] : '';

		if ( ! empty( $field ) || ! empty( $entries ) || is_array( $entries ) ) {

			if ( isset( $entries[ $field ] ) ) {
				$value = $entries[ $field ];

				if ( is_array( $value ) && ! empty( $value['value'] ) ) {
					$value = $value['value'];
				} elseif ( ! is_array( $value ) ) {
					$value = maybe_unserialize( $value );
				}
			}
		}

		if ( ! empty( $value ) && 'file' == $field_type ) { // phpcs:ignore
			$value = false;
		} elseif ( is_array( $value ) && 1 == count( $value ) ) { // phpcs:ignore
			$value = implode( ' ', $value );
		}

		return $value;
	}

	/**
	 * Check if error occurs in response and send mail.
	 *
	 * @param     array $response    Api response.
	 * @param     array $data        Form data and entries.
	 * @param     array $log         An array of log data.
	 * @param     array $request     An array of request data.
	 * @since     1.0.0
	 * @return    mixed
	 */
	public function mwb_sf_gf_check_error_for_mail( $response = array(), $data = array(), $log = array(), $request = array() ) {

		if ( ! is_array( $response ) || ! is_array( $data ) || ! is_array( $log ) || ! is_array( $data ) ) {
			return;
		}
		if ( isset( $response['success'] ) && false == $response['success'] ) { // phpcs:ignore
			if ( null !== $this->connect_manager->get_settings_details( 'notif' ) ) {
				$this->mwb_sf_gf_send_email( $response['response'], $data );
			}
		} elseif ( isset( $response['success'] ) && true == $response['success'] ) { // phpcs:ignore 
			$response_id = ! empty( $response['id'] ) ? $response['id'] : '';

			do_action( 'mwb_' . $this->crm_slug . '_gf_successfully_synced', $response_id, $log, $data['values'], $request );
			if ( isset( $response['msg'] ) && 'Create_Record' == $response['msg'] ) { // phpcs:ignore
				$count = get_option( 'mwb-' . $this->crm_slug . '-gf-synced-forms-count', 0 );
				update_option( 'mwb-' . $this->crm_slug . '-gf-synced-forms-count', $count + 1 );
			}
		}
	}

	/**
	 * Send email on error
	 *
	 * @param     mixed $info    CRM response to send.
	 * @param     array $data    An array of form data and entries.
	 * @since     1.0.0
	 * @return    void
	 */
	public function mwb_sf_gf_send_email( $info, $data ) {

		if ( ! empty( $info ) && is_array( $info ) ) {

			$to        = $this->connect_manager->get_settings_details( 'email' );
			$from_name = get_bloginfo( 'name' );
			$subject   = esc_html__( 'Error While Posting GF form data over Mautic', 'mwb-gf-integration-with-mautic' );
			$logs_link = add_query_arg(
				array(
					'page' => 'mwb_' . $this->crm_slug . '_gf_page',
					'tab'  => 'logs',
				),
				admin_url( 'admin.php' )
			);

			$detail = array(
				'Form Title' => $data['name'],
				'Form ID'    => $data['id'],
				'Time'       => gmdate( 'd-M-y H:i:s', current_time( 'timestamp' ) ), // phpcs:ignore
				'Logs'       => $logs_link,
			);

			if ( isset( $info['data'] ) && isset( $info['data'] ) ) {
				$info_msg    = $info['data']['errors'][0]['message'];
				$info_detail = is_array( $info['data'] ) ? wp_json_encode( $info['data'] ) : $info['data'];
			}

			$email_data = array(
				'Title'          => esc_html__( 'MWB GF Integration with Mautic', 'mwb-gf-integration-with-mautic' ),
				'Code'           => ! empty( $info['code'] ) ? $info['code'] : '',
				'CRM message'    => ! empty( $info['message'] ) ? $info['message'] : '',
				'Error message ' => $info_msg,
				'Details'        => $info_detail,
				'More details'   => $detail,
			);

			$email_body = $this->mwb_sf_gf_get_email_body( $email_data );

			wp_mail(
				! empty( $to ) ? $to : '',
				$subject,
				$email_body,
				array(
					'Content-type: text/html; charset=' . get_bloginfo( 'charset' ),
					'From: ' . $from_name . '<' . get_bloginfo( 'admin_email' ) . '>',
				)
			);

		}

	}

	/**
	 * Returns email body to be sent as email.
	 *
	 * @param     array $data   An array of information to be sent as email.
	 * @since     1.0.0
	 * @return    html        Email body
	 */
	public function mwb_sf_gf_get_email_body( $data ) {

		if ( ! empty( $data ) && is_array( $data ) ) {
			ob_start();
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/email-template.php';
			return ob_get_clean();
		}
	}

}
