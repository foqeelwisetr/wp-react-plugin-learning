<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * POST /wp-ext-rule-pricing/v1/campaigns
 */
class WP_EXT_RULE_PRICING_API_Post_Campaign extends WP_EXT_RULE_PRICING_API_Base {

	public static $ins;

	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function __construct() {
		parent::__construct();
		$this->method     = WP_REST_Server::CREATABLE;
		$this->route      = '/campaigns';
		$this->public_api = false;
	}

	public function process_api_call() {
		$body = $this->args;
		if ( isset( $body['campaign'] ) && is_array( $body['campaign'] ) ) {
			$body = $body['campaign'];
		}

		$item = WP_EXT_RULE_Pricing_Campaign_Repository::create( is_array( $body ) ? $body : array() );

		if ( is_wp_error( $item ) ) {
			return $this->error_response( $item->get_error_message(), $item, 400 );
		}

		$this->response_code = 201;

		return $this->success_response(
			$item,
			__( 'Campaign created successfully.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_PRICING_API_Loader::register( 'WP_EXT_RULE_PRICING_API_Post_Campaign' );
