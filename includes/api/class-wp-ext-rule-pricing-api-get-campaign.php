<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GET /wp-ext-rule-pricing/v1/campaigns/(?P<id>[\d]+)
 */
class WP_EXT_RULE_PRICING_API_Get_Campaign extends WP_EXT_RULE_PRICING_API_Base {

	public static $ins;

	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function __construct() {
		parent::__construct();
		$this->method       = WP_REST_Server::READABLE;
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
		$id   = (int) $this->get_sanitized_arg( 'id', 'integer' );
		$item = WP_EXT_RULE_Pricing_Campaign_Repository::get( $id );

		if ( null === $item ) {
			return $this->error_response_not_found( __( 'Campaign not found.', 'wp-ext-rule-pricing' ) );
		}

		$this->response_code = 200;

		return $this->success_response(
			$item,
			__( 'Campaign loaded successfully.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_PRICING_API_Loader::register( 'WP_EXT_RULE_PRICING_API_Get_Campaign' );
