<?php
/**
 * Base Api Class
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/api
 */

/**
 * Base Api Class.
 *
 * This class defines all code necessary api communication.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/api
 */
class Mwb_Gf_Integration_Mautic_Api_Base extends Mwb_Gf_Integration_Api_Base_New {

	/**
	 * Crm prefix
	 *
	 * @var    string   Crm prefix
	 * @since  1.0.0
	 */
	private static $crm_prefix;

	/**
	 * Production Base auth url.
	 *
	 * @var     string  Production Base auth url
	 * @since   1.0.0
	 */
	private static $base_auth_url = '';

	/**
	 * Sandbox base auth url.
	 *
	 * @var     string Sandbox base auth url.
	 * @since   1.0.0
	 */
	private static $sandbox_base_auth_url = '';

	/**
	 * Mautic Consumer key
	 *
	 * @var     string  Consumer key
	 * @since   1.0.0
	 */
	private static $consumer_key;

	/**
	 * Mautic Consumer Secret Key
	 *
	 * @var     string Consumer secret key
	 * @since   1.0.0
	 */
	private static $consumer_secret;

	/**
	 * Mautic Redirect URL
	 *
	 * @var     string Redirect URL
	 * @since   1.0.0
	 */
	private static $redirect_url;

	/**
	 * Mautic Username
	 *
	 * @var     string Username
	 * @since   1.0.0
	 */
	private static $username;

	/**
	 * Mautic Password
	 *
	 * @var     string Password
	 * @since   1.0.0
	 */
	private static $password;
	/**
	 * Mautic Auth type
	 *
	 * @var      string  Auth Type
	 * @since    1.0.0
	 */
	private static $auth_type;

	/**
	 * Mautic Access token data.
	 *
	 * @var     string   Stores access token data.
	 * @since   1.0.0
	 */
	private static $access_token;

	/**
	 * Mautic Refresh token data
	 *
	 * @var     string   Stores refresh token data.
	 * @since   1.0.0
	 */
	private static $refresh_token;

	/**
	 * Access token expiry data
	 *
	 * @var     integer   Stores access token expiry data.
	 * @since   1.0.0
	 */
	private static $expiry;

	/**
	 * Current instance URL
	 *
	 * @var     string    Current instance url.
	 * @since   1.0.0
	 */
	private static $instance_url;

	/**
	 * Issued at data
	 *
	 * @var     string     Issued at data by mautic
	 * @since   1.0.0
	 */
	private static $issued_at;

	/**
	 * Creates an instance of the class
	 *
	 * @var     object     An instance of the class
	 * @since   1.0.0
	 */
	protected static $_instance = null; // phpcs:ignore

	/**
	 * Mautic API
	 *
	 * @var [type]
	 */
	private static $mautic_api;
	/**
	 * Mautic API version
	 *
	 * @var     string       API version
	 * @since   1.0.0
	 */
	private static $api_version = 'v51.0';

	/**
	 * Main Mwb_Sf_Gf_Integration_Mautic_Api_Base Instance.
	 *
	 * Ensures only one instance of Mwb_Sf_Gf_Integration_Mautic_Api_Base is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @return Mwb_Sf_Gf_Integration_Mautic_Api_Base - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( self::$redirect_url, self::$username, self::$password );
		}
		self::initialize();
		return self::$_instance;
	}

	/**
	 * Initialize properties.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $token_data Saved token data.
	 */
	private static function initialize( $token_data = array() ) {

		self::$crm_prefix = get_current_crm_mautic( 'slug' );

		self::$consumer_key    = get_option( 'mwb-' . self::$crm_prefix . '-gf-consumer-key', '' );
		self::$consumer_secret = get_option( 'mwb-' . self::$crm_prefix . '-gf-consumer-secret', '' );
		self::$auth_type       = get_option( 'mwb-' . self::$crm_prefix . '-gf-auth_type', '' );
		self::$redirect_url    = get_option( 'mwb-' . self::$crm_prefix . '-gf-redirect-url', '' );
		self::$username        = get_option( 'mwb-' . self::$crm_prefix . '-gf-username', '' );
		self::$password        = get_option( 'mwb-' . self::$crm_prefix . '-gf-password', '' );

		if ( empty( $token_data ) ) {
			$token_data = get_option( 'mwb-' . self::$crm_prefix . '-gf-token-data', array() );
		}
		if ( ! empty( $token_data ) ) {
			self::$access_token  = ! empty( $token_data['access_token'] ) ? $token_data['access_token'] : '';
			self::$refresh_token = ! empty( $token_data['refresh_token'] ) ? $token_data['refresh_token'] : '';
			self::$instance_url  = ! empty( get_option( 'mwb-mautic-gf-redirect-url' ) ) ? get_option( 'mwb-mautic-gf-redirect-url' ) : '';
			self::$issued_at     = ! empty( $token_data['expires_in'] ) ? time() + $token_data['expires_in'] : '';
		}
	}

	/**
	 * Returns response.
	 *
	 * @since       1.0.0
	 * @return mixed $response Respose is returned.
	 */
	public static function get_self_user() {
		$response = array(
			'success' => false,
			'user'    => '',
			'msg'     => '',
		);
		try {
			$endpoint            = '/api/users/self';
			$mautic_api          = self::get_mautic_api();
			$headers             = $mautic_api->get_auth_header();
			$user_data           = $mautic_api->get( $endpoint, array(), $headers );
			$response['success'] = true;
			$response['user']    = isset( $user_data['data']['email'] ) ? $user_data['data']['email'] : '';
			$response['msg']     = 'Success';
			if ( ! empty( $user_data['data']['email'] ) ) {
				update_option( 'mwb-mautic-gf-active', true );
			} else {
				update_option( 'mwb-mautic-gf-active', false );
				$response['msg'] = 'Something Went Wrong';
			}
		} catch ( Exception $e ) {
			update_option( 'mwb-mautic-gf-active', false );
			$response['msg'] = $e->getMessage();
		}
		return $response;
	}

	/**
	 * Get_mautic_api.
	 *
	 * @since       1.0.0
	 * @return mixed $response Respose is returned.
	 * @throws Mwb_Wpm_Api_Exception Mwb_Wpm_Api_Exception .
	 */
	public static function get_mautic_api() {

		// @todo get details wp options
		if ( ! empty( self::$mautic_api ) ) {
			return self::$mautic_api;
		}
		$authentication_type = get_option( 'mwb-mautic-gf-auth_type' );
		$base_url            = get_option( 'mwb-mautic-gf-redirect-url' );

		if ( '' !== $base_url ) {
			$username = get_option( 'mwb-mautic-gf-username' );
			$password = get_option( 'mwb-mautic-gf-password' );
			if ( ! empty( $base_url ) && ! empty( $username ) && ! empty( $password ) ) {
				self::$mautic_api = new Mwb_Gf_Integration_Api_Base_New( $base_url, $username, $password );
				return self::$mautic_api;
			}
		}

		return false;
	}

	/**
	 * Get api domain.
	 *
	 * @since    1.0.0
	 * @return   string   Site redirecrt Uri.
	 */
	public function get_redirect_uri() {
		return admin_url();
	}

	/**
	 * Get instance url.
	 *
	 * @since    1.0.0
	 * @return   string   Instance url.
	 */
	public function get_instance_url() {
		return ! empty( self::$instance_url ) ? self::$instance_url : false;
	}

	/**
	 * Get access token.
	 *
	 * @since    1.0.0
	 * @return   string   Access token.
	 */
	public function get_access_token() {
		return ! empty( self::$access_token ) ? self::$access_token : false;
	}

	/**
	 * Get refresh token.
	 *
	 * @since     1.0.0
	 * @return    string    Refresh token.
	 */
	public function get_refresh_token() {
		return ! empty( self::$refresh_token ) ? self::$refresh_token : false;
	}

	/**
	 * Get active access token issued time.
	 *
	 * @since     1.0.0
	 * @return    string    Expiry seconds.
	 */
	public function get_access_token_issue_time() {
		if ( ! empty( self::$issued_at ) ) {
			if ( time() < self::$issued_at ) {
				$timestamp = self::$issued_at;
				$exp_time  = gmdate( 'Y-m-d\TH:i:s\Z', $timestamp );
				$date      = new DateTime( $exp_time, new DateTimeZone( 'GMT' ) );
				$date->setTimezone( new DateTimeZone( 'Asia/Kolkata' ) );
				return $date->format( 'Y-m-d H:i:s' );
				// return self::$issued_at;.
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	/**
	 * Check if access token is valid.
	 *
	 * @since    1.0.0
	 * @return   boolean
	 */
	public function is_access_token_valid() {
		return ! empty( self::$expiry ) ? ( self::$expiry > time() ) : false;
	}

	/**
	 * Get refreshed token data from api.
	 *
	 * @since     1.0.0
	 * @return    boolean.
	 */
	public function renew_access_token() {

		$refresh_token = $this->get_refresh_token();
		if ( ! empty( $refresh_token ) ) {
			$response = $this->process_access_token( false, $refresh_token );
		}

		return ! empty( $response['code'] ) && 200 == $response['code'] ? true : false; // phpcs:ignore
	}

	/**
	 * Save New token data into db.
	 *
	 * @since   1.0.0
	 * @param   string $code    Unique code to generate token.
	 */
	public function save_access_token( $code ) {
		$this->process_access_token( $code );
	}

	/**
	 * Save Company Id.
	 *
	 * @since   1.0.0
	 * @param   string $company_id   Connected Company Id.
	 */
	public function save_company_id( $company_id = false ) {
		if ( empty( $company_id ) ) {
			return;
		}
		update_option( 'mwb-' . self::$crm_prefix . '-gf-company-id', $company_id );
	}

	/**
	 * Get Base Authorization url.
	 *
	 * @since    1.0.0
	 * @return   string   Base Authorization url.
	 */
	public function base_auth_url() {
		$url = 'production' == self::$auth_type ? self::$base_auth_url : self::$sandbox_base_auth_url; // phpcs:ignore
		return $url;
	}

	/**
	 * Get Authorization url.
	 *
	 * @since    1.0.0
	 * @param    string $mautic_base_url Base Url of Mautic.
	 * @param    string $consumer_key Base Url of Mautic.
	 * @return   string Authorization url.
	 */
	public function get_auth_code_url( $mautic_base_url, $consumer_key ) {

		$nonce      = wp_create_nonce( 'mwb_' . self::$crm_prefix . '_gf_state' );
		$mautic_url = $mautic_base_url . '/oauth/v2/authorize';
		$query_args = array(
			'response_type' => 'code',
			'state'         => urlencode( $nonce ), // phpcs:ignore
			'client_id'     => $consumer_key,
			'redirect_uri'  => $this->get_redirect_uri(),
			'grant_type'    => 'authorization_code',
		);

		$mautic_login_url = add_query_arg( $query_args, $mautic_url );

		return $mautic_login_url;
	}

	/**
	 * Get Authorization url.
	 *
	 * @since    1.0.0
	 * @param    string $mautic_base_url Base Url of Mautic.
	 * @return   string Authorization url.
	 */
	public function get_auth_code_url_for_reauthorization( $mautic_base_url ) {

		$nonce      = wp_create_nonce( 'mwb_' . self::$crm_prefix . '_gf_state' );
		$mautic_url = $mautic_base_url . '/oauth/v2/authorize';
		$query_args = array(
			'response_type' => 'code',
			'state'         => urlencode( $nonce ), // phpcs:ignore
			'client_id'     => self::$consumer_key,
			'redirect_uri'  => $this->get_redirect_uri(),
			'grant_type'    => 'authorization_code',
		);

		$mautic_login_url = add_query_arg( $query_args, $mautic_url );

		return $mautic_login_url;
	}

	/**
	 * Get refresh token data from api.
	 *
	 * @since   1.0.0
	 * @param   string $code            Unique code to generate token.
	 * @param   string $refresh_token   Unique code to renew token.
	 * @return  array
	 */
	public function process_access_token( $code = '', $refresh_token = '' ) {

		$endpoint = '/oauth/v2/token';

		$this->base_url = $this->base_auth_url();

		$params = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => self::$consumer_key,
			'client_secret' => self::$consumer_secret,
			'redirect_uri'  => $this->get_redirect_uri(),
			'code'          => $code,
		);

		if ( empty( $code ) ) {
			$params['refresh_token'] = $refresh_token;
			$params['grant_type']    = 'refresh_token';
			unset( $params['code'] );
		}

		$response = $this->post( $endpoint, $params, $this->get_token_auth_header() );
		if ( isset( $response['code'] ) && 200 == $response['code'] ) { // phpcs:ignore
			// Save token.
			$token_data = ! empty( $response['data'] ) ? $response['data'] : array();
			$token_data = $this->merge_refresh_token( $token_data );
			update_option( 'mwb-' . self::$crm_prefix . '-gf-token-data', $token_data );
			self::initialize( $token_data );
		} else {
			// On failure add to log.
			delete_option( 'mwb-' . self::$crm_prefix . '-gf-token-data' );
			delete_option( 'mwb-' . self::$crm_prefix . '-gf-active' );
		}

		return $response;
	}


	/**
	 * Merge refresh token with new access token data.
	 *
	 * @since   1.0.0
	 * @param   array $new_token_data   Latest token data.
	 * @return  array                   Token data.
	 */
	public function merge_refresh_token( $new_token_data ) {

		$old_token_data = get_option( 'mwb-' . self::$crm_prefix . '-gf-token-data', array() );

		if ( empty( $old_token_data ) ) {
			return $new_token_data;
		}

		foreach ( $old_token_data as $key => $value ) {
			if ( isset( $new_token_data[ $key ] ) ) {
				$old_token_data[ $key ] = $new_token_data[ $key ];
			}
		}
		return $old_token_data;
	}

	/**
	 * Get authorization headers for getting token.
	 *
	 * @since   1.0.0
	 * @return  array   Headers.
	 */
	public function get_token_auth_header() {
		return array();
	}

	/**
	 * Get Request headers.
	 *
	 * @since    1.0.0
	 * @return   array   Headers.
	 */
	public function get_auth_header() {

		$headers = array(
			'Authorization' => 'Bearer ' . self::$access_token,
			'content-type'  => 'application/json',
		);

		return $headers;
	}


	/**
	 * Merge query response.
	 *
	 * @param    array  $query_response   Query response.
	 * @param    array  $record_data      Record data.
	 * @param    string $primary_field    Primary field.
	 * @param    array  $record_response  Record response.
	 * @since    1.0.0
	 * @return   array
	 */
	public function merge_query_response( $query_response, $record_data, $primary_field, $record_response ) {
		$primary_field_arr = array_column( $query_response, $primary_field );
		foreach ( $record_data as $key => $data ) {
			$field_key = array_search( $data[ $primary_field ], $primary_field_arr ); //phpcs:ignore
			if ( false !== $field_key ) {
				$record_response[ $key ] = array(
					'id'      => $query_response[ $field_key ]['Id'],
					'success' => true,
					'errors'  => array(),
					'message' => 'Updated',
				);
			}
		}
		return $record_response;
	}


	/**
	 * Prepare duplicate query.
	 *
	 * @param    array  $record_data      Record data.
	 * @param    string $primary_field    Primary Field.
	 * @param    array  $duplicates       Duplicate records.
	 * @param    string $object           CRM Object.
	 * @since    1.0.0
	 * @return   array
	 */
	public function prepare_duplicate_query( $record_data, $primary_field, $duplicates, $object ) {

		$duplicate_records = array_filter(
			$record_data,
			function ( $key ) use ( $duplicates ) {
				return in_array( $key, $duplicates ); // phpcs:ignore
			},
			ARRAY_FILTER_USE_KEY
		);

		$duplicate_data   = array_column( $duplicate_records, $primary_field );
		$duplicate_string = '';
		if ( ! empty( $duplicate_data ) ) {
			array_walk(
				$duplicate_data,
				function( &$item ) {
					$item = "'" . $item . "'";
				}
			);
			$duplicate_string = '( ' . implode( ' , ', $duplicate_data ) . ' )';
		}
		$duplicate_query = sprintf(
			'SELECT Id, %s FROM %s WHERE %s IN %s',
			$primary_field,
			$object,
			$primary_field,
			$duplicate_string
		);
		return $duplicate_query;
	}


	/**
	 * Create single record on Mautic
	 *
	 * @param  string  $object      CRM object name.
	 * @param  array   $record_data Request data.
	 * @param  boolean $is_bulk     Is a bulk request.
	 * @param  array   $log_data    Data to create log.
	 *
	 * @since 1.0.0
	 *
	 * @return array Response data.
	 */
	public function create_or_update_record( $object, $record_data, $is_bulk = false, $log_data = array() ) {

		$response_data = array(
			'succes' => false,
			'msg'    => __( 'Something went wrong', 'mwb-gf-integration-with-mautic' ),
		);
		$record_id     = false;
		$feed_id       = ! empty( $log_data['feed_id'] ) ? $log_data['feed_id'] : false;

		// Check for the existing record based on selected primary field.
		if ( $feed_id ) {
			$duplicate_check_fields = get_post_meta( $feed_id, 'mwb-' . self::$crm_prefix . '-gf-primary-field', true );
			$primary_field          = ! empty( $duplicate_check_fields ) ? $duplicate_check_fields : false;
		}

		if ( $primary_field ) {
			$search_response = $this->check_for_existing_record( $object, $record_data, $primary_field );
			if ( $this->if_access_token_expired( $search_response ) ) {
				$search_response = $this->check_for_existing_record( $object, $record_data, $primary_field );
			}
			// Get record id from search query result.
			$record_id = $this->may_be_get_record_id_from_search( $object, $search_response, $record_data, $primary_field );
		}

		if ( ! $record_id ) {
			$response = $this->create_record( $object, $record_data, $is_bulk, $log_data );
			if ( $this->if_access_token_expired( $response ) ) {
				$response = $this->create_record( $object, $record_data, $is_bulk, $log_data );
			}
			if ( $this->is_success( $response ) ) {
				$response_data['success']  = true;
				$response_data['msg']      = 'Create_Record';
				$response_data['response'] = $response;
				$response_data['id']       = $this->get_object_id_from_response( $object, $response );
			} else {
				$response_data['success']  = false;
				$response_data['msg']      = esc_html__( 'Error posting to CRM', 'mwb-gf-integration-with-mautic' );
				$response_data['response'] = $response;
			}
		} else {

			// Update an existing record based on record_id.
			$response = $this->update_record( $record_id, $object, $record_data, $is_bulk, $log_data );
			if ( $this->if_access_token_expired( $response ) ) {
				$response = $this->update_record( $record_id, $object, $record_data, $is_bulk, $log_data );
			}
			if ( $this->is_success( $response ) ) {

				// Insert record id and message to response.
				if ( isset( $response['message'] ) ) {
					$response['message'] = 'Updated';
				}
				if ( empty( $response['data'] ) ) {
					$response['data'] = array(
						'id' => $record_id,
					);
				}

				$response_data['success']  = true;
				$response_data['msg']      = 'Update_Record';
				$response_data['response'] = $response;
				$response_data['id']       = $record_id;
			}
		}
		// Insert log in db.
		$this->log_request_in_db( __FUNCTION__, $object, $record_data, $response, $log_data );

		return $response_data;
	}

	/**
	 * Insert log data in db.
	 *
	 * @param     string $event                Trigger event/ Feed .
	 * @param     string $sf_object            Name of mautic module.
	 * @param     array  $request              An array of request data.
	 * @param     array  $response             An array of response data.
	 * @param     array  $log_data             Data to log.
	 * @return    void
	 */
	public function log_request_in_db( $event, $sf_object, $request, $response, $log_data ) {

		$sf_id = $this->get_object_id_from_response( $sf_object, $response );

		$request  = serialize( $request ); // @codingStandardsIgnoreLine
		$response = serialize( $response ); // @codingStandardsIgnoreLine

		$feed      = ! empty( $log_data['feed_name'] ) ? $log_data['feed_name'] : false;
		$feed_id   = ! empty( $log_data['feed_id'] ) ? $log_data['feed_id'] : false;
		$event     = ! empty( $event ) ? $event : false;
		$sf_object = ! empty( $log_data['sf_object'] ) ? $log_data['sf_object'] : false;

		$time     = time();
		$log_data = compact( 'event', 'sf_object', 'request', 'response', 'sf_id', 'feed_id', 'feed', 'time' );
		$this->insert_log_data( $log_data );

	}

	/**
	 * Retrieve object ID from crm response.
	 *
	 * @param     string $object     Object of crm.
	 * @param     array  $response   An array of response data from crm.
	 * @since     1.0.0
	 * @return    integer
	 */
	public function get_object_id_from_response( $object, $response ) {
		$id = '-';
		if ( isset( $response['data'] ) && isset( $response['data'][ $object ]['id'] ) ) {
			return ! empty( $response['data'][ $object ]['id'] ) ? $response['data'][ $object ]['id'] : $id;
		}
		return $id;
	}

	/**
	 * Insert data to db.
	 *
	 * @param      array $data    Data to log.
	 * @since      1.0.0
	 * @return     void
	 */
	public function insert_log_data( $data ) {

		$connect         = 'Mwb_Gf_Integration_Connect_' . self::$crm_prefix . '_Framework';
		$connect_manager = $connect::get_instance();

		if ( 'yes' != $connect_manager->get_settings_details( 'logs' ) ) { // phpcs:ignore
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'mwb_' . self::$crm_prefix . '_gf_log';
		$wpdb->insert( $table, $data ); // phpcs:ignore
	}

	/**
	 * Check for exsiting record in search query response.
	 *
	 * @param string $object     Object of crm.
	 * @param array  $response      Search query response.
	 * @param array  $record_data   Request data of searched record.
	 * @param string $primary_field Primary field name.
	 *
	 * @return string|bool          Id of existing record or false.
	 */
	private function may_be_get_record_id_from_search( $object, $response, $record_data, $primary_field ) {
		$obj = '';
		if ( 'contact' === $object ) {
			$obj = 'contacts';
		} elseif ( 'company' === $object ) {
			$obj = 'companies';
		}
		$record_id     = false;
		$found_records = array();
		if ( isset( $response['code'] ) && 200 == $response['code'] && 'OK' == $response['message'] ) { // phpcs:ignore
			if ( ! empty( $response['data'] ) && ! empty( $response['data']['total'] ) ) {
				$found_records = $response['data'][ $obj ];
			}
		}
		if ( count( $found_records ) > 0 ) {
			foreach ( $found_records as $key => $record ) {
				foreach ( $record['fields'] as $k => $r ) {
					if ( $r[ $primary_field ]['value'] == $record_data[ $primary_field ] ) { // phpcs:ignore
						$record_id = $key;
						break;
					}
				}
			}
		}
		return $record_id;
	}

	/**
	 * Check for existing record using parameterizedSearch.
	 *
	 * @param string $object        Target object name.
	 * @param array  $record_data   Record data.
	 * @param string $primary_field Primary field.
	 *
	 * @return array                Response data array.
	 */
	private function check_for_existing_record( $object, $record_data, $primary_field ) {
		$obj = '';
		if ( 'contact' === $object ) {
			$obj = 'contacts';
		} elseif ( 'company' === $object ) {
			$obj = 'companies';
		}
		$this->base_url = self::$instance_url;
		$endpoint       = '/api/' . $obj;
		$params         = array(
			'search' => $record_data[ $primary_field ],
		);

		$auth_type = get_option( 'mwb-mautic-gf-auth_type' );
		if ( 'basic' === $auth_type ) {
			$mautic_api = self::get_mautic_api();
			$headers    = $mautic_api->get_auth_header();
		} elseif ( 'oauth2' === $auth_type ) {
			$headers = $this->get_auth_header();
		}
		$response = $this->get( $endpoint, $params, $headers );
		return $response;
	}

	/**
	 * Check if resposne has success code.
	 *
	 * @param  array $response  Response data.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean true|false.
	 */
	private function is_success( $response ) {
		if ( ! empty( $response['code'] ) ) {
			return in_array( $response['code'], array( 200, 201, 204, 202 ) ); // phpcs:ignore
		}
		return true;
	}

	/**
	 * Create a new record.
	 *
	 * @param  string  $object     Object name.
	 * @param  array   $record_data Record data.
	 * @param  boolean $is_bulk    Is a bulk request.
	 * @param  array   $log_data   Data to create log.
	 * @return array               Response data.
	 */
	private function create_record( $object, $record_data, $is_bulk, $log_data ) {

		$obj = '';
		if ( 'contact' === $object ) {
			$obj = 'contacts';
		} elseif ( 'company' === $object ) {
			$obj = 'companies';
		}
		$this->base_url = self::$instance_url;
		$endpoint       = '/api/' . $obj . '/new';
		$auth_type      = get_option( 'mwb-mautic-gf-auth_type' );
		if ( 'basic' === $auth_type ) {
			$mautic_api = self::get_mautic_api();
			$headers    = $mautic_api->get_auth_header();
		} elseif ( 'oauth2' === $auth_type ) {
			$headers = $this->get_auth_header();
		}
		$response = $this->post( $endpoint, $record_data, $headers );
		return $response;
	}

	/**
	 * Update an existing record.
	 *
	 * @param  string  $record_id   Record id to be updated.
	 * @param  string  $object      Object name.
	 * @param  array   $record_data Record data.
	 * @param  boolean $is_bulk     Is a bulk request.
	 * @param  array   $log_data    Data to create log.
	 * @return array                Response data.
	 */
	private function update_record( $record_id, $object, $record_data, $is_bulk, $log_data ) {

		$obj = '';
		if ( 'contact' === $object ) {
			$obj = 'contacts';
		} elseif ( 'company' === $object ) {
			$obj = 'companies';
		}
		$this->base_url = self::$instance_url;
		$endpoint       = '/api/' . $obj . '/' . $record_id . '/edit';
		$auth_type      = get_option( 'mwb-mautic-gf-auth_type' );
		if ( 'basic' === $auth_type ) {
			$mautic_api = self::get_mautic_api();
			$headers    = $mautic_api->get_auth_header();
		} elseif ( 'oauth2' === $auth_type ) {
			$headers = $this->get_auth_header();
		}
		$response       = $this->patch( $endpoint, $record_data, $headers );
		return $response;
	}


	/**
	 * Check if response has expired access token message.
	 *
	 * @param  array $response Api response.
	 * @return bool            Access token status.
	 */
	private function if_access_token_expired( $response ) {
		if ( isset( $response['code'] ) && 401 == $response['code'] && 'Unauthorized' == $response['message'] ) { // phpcs:ignore
			return $this->renew_access_token();
		}
		return false;
	}

	/**
	 * Get available object in crm.
	 *
	 * @return array          Response data.
	 */
	public function get_crm_objects() {

		$data = get_transient( 'mwb_mautic_gf_objects_data' );
		if ( false !== ( $data ) ) {
			return $data;
		}

		$objects = array();

		$objects = array(
			'contact' => 'Contact',
			'company' => 'Company',
		);
		set_transient( 'mwb_mautic_gf_objects_data', $objects );

		return $objects;
	}

	/**
	 * Validate connection.
	 *
	 * @since   1.0.0
	 * @return  mixed
	 */
	public function validate_crm_connection() {
		$this->base_url = self::$instance_url;
		$endpoint       = '/api/fields/contact';
		$auth_type      = get_option( 'mwb-mautic-gf-auth_type' );
		if ( 'basic' === $auth_type ) {
			$mautic_api = self::get_mautic_api();
			$headers    = $mautic_api->get_auth_header();
		} elseif ( 'oauth2' === $auth_type ) {
			$headers = $this->get_auth_header();
		}
		$response = $this->get( $endpoint, array(), $headers );
		if ( ! empty( $response ) ) {
			if ( isset( $response['code'] ) && 401 == $response['code'] && 'Unauthorized' == $response['message'] ) { // phpcs:ignore
				if ( $this->renew_access_token() ) {
					$this->base_url = self::$instance_url;
					$auth_type      = get_option( 'mwb-mautic-gf-auth_type' );
					if ( 'basic' === $auth_type ) {
						$mautic_api = self::get_mautic_api();
						$headers    = $mautic_api->get_auth_header();
					} elseif ( 'oauth2' === $auth_type ) {
						$headers = $this->get_auth_header();
					}
					$response = $this->get( $endpoint, array(), $headers );
				}
			}
		}

		return $response;
	}

	/**
	 * Get fields assosiated with an object.
	 *
	 * @param  string  $object Name of object.
	 * @param  boolean $force  Fetch from api.
	 * @return array           Response data.
	 */
	public function get_object_fields( $object, $force = false ) {

		$data = get_transient( 'mwb_mautic_gf' . $object . '_fields' );
		if ( ! $force && false !== ( $data ) ) {
			return $data;
		}

		$this->base_url = self::$instance_url;
		$endpoint       = '/api/fields/' . $object;
		$auth_type      = get_option( 'mwb-mautic-gf-auth_type' );
		if ( 'basic' === $auth_type ) {
			$mautic_api = self::get_mautic_api();
			$headers    = $mautic_api->get_auth_header();
		} elseif ( 'oauth2' === $auth_type ) {
			$headers = $this->get_auth_header();
		}
		$response = $this->get( $endpoint, array(), $headers );
		$fields   = array();
		if ( ! empty( $response ) ) {
			if ( isset( $response['code'] ) && 401 == $response['code'] && 'Unauthorized' == $response['message'] ) { // phpcs:ignore
				if ( $this->renew_access_token() ) {
					$this->base_url = self::$instance_url;
					$auth_type      = get_option( 'mwb-mautic-gf-auth_type' );
					if ( 'basic' === $auth_type ) {
						$mautic_api = self::get_mautic_api();
						$headers    = $mautic_api->get_auth_header();
					} elseif ( 'oauth2' === $auth_type ) {
						$headers = $this->get_auth_header();
					}
					$response = $this->get( $endpoint, array(), $headers );
				}
			}
			if ( isset( $response['code'] ) && 200 == $response['code'] && 'OK' == $response['message'] ) { // phpcs:ignore
				if ( ! empty( $response['data'] ) && ! empty( $response['data']['fields'] ) ) {
					$fields = $this->maybe_add_mandatory_fields( $response['data']['fields'] );
					set_transient( 'mwb_mautic_gf' . $object . '_fields', $fields );
				}
			}
		}

		return $fields;
	}

	/**
	 * Check for mandatory fields and add an index to it also retricts phone fields.
	 *
	 * @param    array $fields  An array of fields data.
	 * @since    1.0.0
	 * @return   array
	 */
	public function maybe_add_mandatory_fields( $fields = array() ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}

		$fields_arr = array();

		foreach ( $fields as $key => $field ) {
			if ( ( ( isset( $field['isPublished'] ) && true == $field['isPublished'] ) ) ) { // phpcs:ignore

				$mandatory = '';
				if ( ! empty( $field['isRequired'] ) && 1 == $field['isRequired'] ) { // phpcs:ignore
					$mandatory = true;
				}
				if ( 'email' === $field['alias'] ) {
					$mandatory = true;
				}

				$field['mandatory'] = $mandatory;
				$fields_arr[]       = $field;
			}
		}

		return $fields_arr;

	}

	// End of class.
}
