<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GET /wp-ext-rule-pricing/v1/campaigns/pro-upsells
 */
class WP_EXT_RULE_Pricing_API_Get_Campaign_Pro_Upsells extends WP_EXT_RULE_PRICING_API_Base {

	public static $ins;

	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function __construct() {
		parent::__construct();
		$this->method     = WP_REST_Server::READABLE;
		$this->route      = '/campaigns/pro-upsells';
		$this->public_api = false;
	}

	public function process_api_call() {
		$upsells = WP_EXT_RULE_Pricing_Campaign_Pro_Upsell_Registry::get_all();

		$this->response_code = 200;

		return $this->success_response(
			array(
				'is_pro'  => (bool) apply_filters( 'wp_ext_rule_pricing_is_pro', false ),
				'upsells' => $upsells,
			),
			__( 'Pro upsells loaded.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_Pricing_API_Loader::register( 'WP_EXT_RULE_Pricing_API_Get_Campaign_Pro_Upsells' );
