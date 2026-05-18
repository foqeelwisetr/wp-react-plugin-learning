<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GET /wp-ext-rule-pricing/v1/campaigns/tabs
 */
class WP_EXT_RULE_PRICING_API_Get_Campaign_Tabs extends WP_EXT_RULE_PRICING_API_Base {

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
		$this->route      = '/campaigns/tabs';
		$this->public_api = false;
	}

	public function process_api_call() {
		$tabs = WP_EXT_RULE_Pricing_Campaign_Tab_Registry::get_tabs();

		$this->response_code = 200;
		$this->total_count   = count( $tabs );

		return $this->success_response(
			$tabs,
			__( 'Campaign tabs loaded successfully.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_PRICING_API_Loader::register( 'WP_EXT_RULE_PRICING_API_Get_Campaign_Tabs' );
