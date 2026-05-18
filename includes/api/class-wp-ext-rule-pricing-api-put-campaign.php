<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PUT /wp-ext-rule-pricing/v1/campaigns/(?P<id>[\d]+)
 */
class WP_EXT_RULE_PRICING_API_Put_Campaign extends WP_EXT_RULE_PRICING_API_Base {

	public static $ins;

	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function __construct() {
		parent::__construct();
		$this->method       = WP_REST_Server::EDITABLE;
		$this->route        = '/campaigns/(?P<id>[\d]+)';
		$this->request_args = array(
			'id' => array(
				'required'          => true,
				'validate_callback' => static function ( $param ) {
					return is_numeric( $param ) && (int) $param > 0;
				},
			),
		);
		$this->public_api   = false;
	}

	public function process_api_call() {
		$id = (int) $this->get_sanitized_arg( 'id', 'integer' );

		$body = $this->args;
		if ( isset( $body['campaign'] ) && is_array( $body['campaign'] ) ) {
			$body = $body['campaign'];
		}

		$item = WP_EXT_RULE_Pricing_Campaign_Repository::update( $id, is_array( $body ) ? $body : array() );

		if ( is_wp_error( $item ) ) {
			return $this->error_response( $item->get_error_message(), $item, 404 );
		}

		$this->response_code = 200;

		return $this->success_response(
			$item,
			__( 'Campaign saved successfully.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_PRICING_API_Loader::register( 'WP_EXT_RULE_PRICING_API_Put_Campaign' );
