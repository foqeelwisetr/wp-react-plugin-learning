<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API base (Autonami-style: $route, $method, process_api_call).
 *
 * @package WP_EXT_RULE_PRICING
 */
abstract class WP_EXT_RULE_PRICING_API_Base {

	/**
	 * @var string|null
	 */
	public $route = null;

	/**
	 * @var string|null
	 */
	public $method = null;

	/**
	 * @var int
	 */
	public $response_code = 200;

	/**
	 * @var array<string, mixed>
	 */
	public $args = array();

	/**
	 * @var array<string, mixed>
	 */
	public $request_args = array();

	/**
	 * @var bool
	 */
	public $public_api = false;

	/**
	 * @var int
	 */
	public $total_count = 0;

	/**
	 * @return array<string, mixed>
	 */
	public function default_args_values() {
		return array();
	}

	public function __construct() {}

	/**
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function api_call( WP_REST_Request $request ) {
		if ( function_exists( 'wp_nocache_headers' ) ) {
			wp_nocache_headers();
		}

		$params = WP_REST_Server::EDITABLE === $this->method || WP_REST_Server::CREATABLE === $this->method
			? $request->get_params()
			: false;

		if ( false === $params ) {
			$query_params   = $request->get_query_params();
			$query_params   = is_array( $query_params ) ? $query_params : array();
			$request_params = $request->get_params();
			$request_params = is_array( $request_params ) ? $request_params : array();
			$params         = array_replace( $query_params, $request_params );
		}

		$params['files'] = $request->get_file_params();
		$this->args      = wp_parse_args( $params, $this->default_args_values() );

		try {
			return $this->process_api_call();
		} catch ( Error $e ) {
			$this->response_code = 500;

			return $this->error_response( $e->getMessage(), null, 500 );
		}
	}

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	abstract public function process_api_call();

	/**
	 * @param string        $message Message.
	 * @param WP_Error|null $wp_error Optional error.
	 * @param int           $code Status code hint.
	 * @return WP_Error
	 */
	public function error_response( $message = '', $wp_error = null, $code = 0 ) {
		if ( 0 !== absint( $code ) ) {
			$this->response_code = absint( $code );
		}

		$data = array();
		if ( $wp_error instanceof WP_Error ) {
			$message = $wp_error->get_error_message();
			$data    = $wp_error->get_error_data();
		}

		if ( ! is_string( $message ) ) {
			$message = '';
		}

		$status = $this->response_code >= 400 && $this->response_code < 600
			? $this->response_code
			: 400;

		return new WP_Error(
			'wp_ext_rule_pricing_api_error',
			$message,
			array_merge( array( 'status' => $status ), is_array( $data ) ? $data : array() )
		);
	}

	/**
	 * @param array<string, mixed> $result_array Result payload.
	 * @param string               $message Message.
	 * @return WP_REST_Response
	 */
	public function success_response( $result_array, $message = '' ) {
		$body = array(
			'code'    => $this->response_code,
			'message' => $message,
			'result'  => $result_array,
		);

		if ( ! empty( $this->total_count ) ) {
			$body['total_count'] = (int) $this->total_count;
		}

		return rest_ensure_response( $body );
	}

	/**
	 * @param string               $key Key.
	 * @param string               $type text_field|integer|email|key|bool.
	 * @param array<string, mixed> $collection Source.
	 * @return mixed|false
	 */
	public function get_sanitized_arg( $key = '', $type = 'text_field', $collection = '' ) {
		if ( ! is_array( $collection ) ) {
			$collection = $this->args;
		}

		if ( '' === $key ) {
			return false;
		}

		if ( ! isset( $collection[ $key ] ) || '' === $collection[ $key ] ) {
			return false;
		}

		$raw = $collection[ $key ];

		switch ( $type ) {
			case 'integer':
				return absint( $raw );
			case 'email':
				return sanitize_email( $raw );
			case 'key':
				return sanitize_key( $raw );
			case 'bool':
				return rest_sanitize_boolean( $raw );
			case 'text_field':
			default:
				return sanitize_text_field( (string) $raw );
		}
	}

	/**
	 * @param WP_REST_Request $request Request.
	 * @return bool|WP_Error
	 */
	public function rest_permission_callback( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to access this endpoint.', 'wp-ext-rule-pricing' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to access this endpoint.', 'wp-ext-rule-pricing' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}
}
