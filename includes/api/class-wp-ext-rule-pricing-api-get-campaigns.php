<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GET /wp-ext-rule-pricing/v1/campaigns
 */
class WP_EXT_RULE_PRICING_API_Get_Campaigns extends WP_EXT_RULE_PRICING_API_Base {

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
		$this->route      = '/campaigns';
		$this->public_api = false;
	}

	public function process_api_call() {
		WP_EXT_RULE_Pricing_Campaign_Demo_Data::maybe_seed();
		$list = WP_EXT_RULE_Pricing_Campaign_Repository::all();

		$this->response_code = 200;
		$this->total_count   = count( $list );

		return $this->success_response(
			$list,
			__( 'Campaigns loaded successfully.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_PRICING_API_Loader::register( 'WP_EXT_RULE_PRICING_API_Get_Campaigns' );
