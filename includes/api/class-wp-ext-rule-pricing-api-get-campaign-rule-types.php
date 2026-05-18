<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GET /wp-ext-rule-pricing/v1/campaigns/rule-types
 */
class WP_EXT_RULE_PRICING_API_Get_Campaign_Rule_Types extends WP_EXT_RULE_PRICING_API_Base {

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
		$this->route      = '/campaigns/rule-types';
		$this->public_api = false;
	}

	public function process_api_call() {
		$export = WP_EXT_RULE_Pricing_Rule_Type_Registry::export_for_rest();

		$this->response_code = 200;

		return $this->success_response(
			$export,
			__( 'Rule types loaded successfully.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_PRICING_API_Loader::register( 'WP_EXT_RULE_PRICING_API_Get_Campaign_Rule_Types' );
