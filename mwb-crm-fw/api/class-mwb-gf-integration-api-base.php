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
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Mwb_Gf_Integration_Api_Base_New {

	/**
	 * Base usrl of the api
	 *
	 * @var     string $base_url
	 * @since   1.0.0
	 */
	public $base_url;

	// mautic user_name.
	/**
	 * User Name variable
	 *
	 * @var string $user_name
	 */
	private $user_name;

	// mautic password.
	/**
	 * Password variable
	 *
	 * @var string $password
	 */
	private $password;

	/**
	 * Last Request variable
	 *
	 * @var string $last_request
	 */
	private $last_request;

	/**
	 * Last Response variable
	 *
	 * @var string $last_response
	 */
	private $last_response;

	/**
	 * Constructor.
	 *
	 * @param string $base_url Base url of your mautic instance.
	 * @param string $user_name Mautic user name.
	 * @param string $password Mautic password.
	 */
	public function __construct( $base_url, $user_name, $password ) {

		$this->base_url  = $base_url;
		$this->user_name = $user_name;
		$this->password  = $password;
	}

	/**
	 * Get headers.
	 *
	 * @return array
	 */
	public function get_auth_header() {
		// phpcs:disable
		$headers = array(
			'Authorization' => sprintf( 'Basic %s', base64_encode( $this->user_name . ':' . $this->password ) ),
		);
		// phpcs:enable
		return $headers;
	}

	/**
	 * Log error
	 *
	 * @param string $code Http response code.
	 * @param string $message Response message.
	 * @param array  $data Reponse data.
	 */
	public function create_error_log( $code, $message, $data = array() ) {

		$upload_dir = wp_get_upload_dir();

		if ( ! empty( $upload_dir ) && isset( $upload_dir['basedir'] ) ) {
			$file = $upload_dir['basedir'] . '/mwb-wp-mautic-error.log';
			$log  = 'Url : ' . $this->last_request['url'] . PHP_EOL;
			$log .= 'Method : ' . $this->last_request['method'] . PHP_EOL;
			$log .= "Code : $code" . PHP_EOL;
			$log .= "Message : $message" . PHP_EOL;
			if ( isset( $data['errors'] ) && is_array( $data['errors'] ) ) {
				foreach ( $data['errors'] as $key => $value ) {
					$log .= 'Error : ' . $value['message'] . PHP_EOL;
				}
				$log .= 'Response: ' . wp_json_encode( $this->last_response ) . PHP_EOL;
				$log .= 'Req: ' . wp_json_encode( $this->last_request ) . PHP_EOL;
			}
			$log .= 'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL;
			$log .= '------------------------------------' . PHP_EOL;
			//phpcs:disable
			file_put_contents( $file, $log, FILE_APPEND );
			//phpcs:enable
		}
	}

	/**
	 * Reset last request data
	 */
	private function reset_request_data() {
		$this->last_request  = '';
		$this->last_response = '';
	}

	/**
	 * Get Request.
	 *
	 * @param    string $endpoint    Api endpoint of mautic.
	 * @param    array  $data        Data to be used in request.
	 * @param    array  $headers     Header to be used in request.
	 * @since    1.0.0
	 * @return   array
	 */
	public function get( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'GET', $endpoint, $data, $headers );
	}

	/**
	 * Post Request.
	 *
	 * @param    string $endpoint    Api endpoint of mautic.
	 * @param    array  $data        Data to be used in request.
	 * @param    array  $headers     Header to be used in request.
	 * @since    1.0.0
	 * @return   array
	 */
	public function post( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'POST', $endpoint, $data, $headers );
	}

	/**
	 * Patch Request.
	 *
	 * @param    string $endpoint    Api endpoint of mautic.
	 * @param    array  $data        Data to be used in request.
	 * @param    array  $headers     Header to be used in request.
	 * @since    1.0.0
	 * @return   array
	 */
	public function patch( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'PATCH', $endpoint, $data, $headers );
	}

	/**
	 * Delete Request.
	 *
	 * @param string $endpoint Api endpoint of mautic.
	 * @param array  $data Data to be used in request.
	 * @param array  $headers header to be used in request.
	 */
	public function delete( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'DELETE', $endpoint, $data, $headers );
	}

	/**
	 * Send api request
	 *
	 * @param     string $method      HTTP method.
	 * @param     string $endpoint    Api endpoint.
	 * @param     array  $data        Request data.
	 * @param     array  $headers     Header to be used in request.
	 * @since     1.0.0
	 * @return    array
	 */
	private function request( $method, $endpoint, $data = array(), $headers = array() ) {

		$this->reset_request_data();
		$crm_slug   = get_current_crm_mautic( 'slug' );
		$method     = strtoupper( trim( $method ) );
		$mautic_url = get_option( 'mwb-' . $crm_slug . '-gf-redirect-url' );
		$url        = $mautic_url . $endpoint;
		$headers    = array_merge( $headers, $this->get_headers() );
		$args       = array(
			'method'    => $method,
			'headers'   => $headers,
			'timeout'   => 20,
			'sslverify' => apply_filters( 'mwb_' . $crm_slug . '_gf_use_sslverify', true ),
		);
		if ( ! empty( $data ) ) {
			if ( in_array( $method, array( 'GET', 'DELETE' ), true ) ) {
				$url = add_query_arg( $data, $url );
			} else {
				$args['headers']['content-type'] = 'application/json';
				$args['body']                    = wp_json_encode( $data );
			}
		}
		$args                = apply_filters( 'mwb_' . $crm_slug . '_gf_http_request_args', $args, $url );
		$response            = wp_remote_request( $url, $args );
		$args['url']         = $url;
		$args['method']      = $method;
		$this->last_request  = $args;
		$this->last_response = $response;
		try {
			$data = $this->parse_response( $response );
		} catch ( Exception $e ) {
			$data = $e->getMessage();
		}
		$this->log_request( $method, $url, $data, $response ); // Keep log of all api interactions.
		return $data;
	}

	/**
	 * Parse Api response.
	 *
	 * @param     array $response   Raw response.
	 * @return    array             filtered reponse.
	 * @throws    Exception         Throws   Exception on error.
	 * @since     1.0.0
	 */
	private function parse_response( $response ) {

		if ( $response instanceof WP_Error ) {
			throw new Exception( 'Error', 0 );
		}
		$code    = (int) wp_remote_retrieve_response_code( $response );
		$message = wp_remote_retrieve_response_message( $response );
		$body    = wp_remote_retrieve_body( $response );
		$data    = json_decode( $body, ARRAY_A );
		return compact( 'code', 'message', 'data' );
	}

	/**
	 * Get headers.
	 *
	 * @since    1.0.0
	 * @return   array   Headers.
	 */
	public function get_headers() {
		return array();
	}

	/**
	 * Log request in sync log.
	 *
	 * @param  string $method   Request Method.
	 * @param  string $url      Request Url.
	 * @param  array  $request  Request data.
	 * @param  array  $response Response data.
	 */
	private function log_request( $method, $url, $request, $response ) {

		$crm_slug        = get_current_crm_mautic( 'slug' );
		$connect         = 'Mwb_Gf_Integration_Connect_' . $crm_slug . '_Framework';
		$connect_manager = $connect::get_instance();
		$path            = $connect_manager->create_log_folder( 'mwb-' . $crm_slug . '-gf-logs' );
		$log_dir         = $path . '/mwb-' . $crm_slug . '-gf-' . gmdate( 'Y-m-d' ) . '.log';

		$log  = 'Url : ' . $url . PHP_EOL;
		$log .= 'Method : ' . $method . PHP_EOL;
		$log .= 'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL;
		$log .= 'Request : ' . wp_json_encode( $request ) . PHP_EOL;
		$log .= 'Response : ' . wp_json_encode( $response ) . PHP_EOL;
		$log .= '------------------------------------' . PHP_EOL;
		file_put_contents( $log_dir, $log, FILE_APPEND ); //phpcs:ignore
	}
}
