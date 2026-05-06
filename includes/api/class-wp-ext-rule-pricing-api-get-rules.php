<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GET /wp-ext-rule-pricing/v1/rules
 *
 * @package WP_EXT_RULE_PRICING
 */
class WP_EXT_RULE_PRICING_API_Get_Rules extends WP_EXT_RULE_PRICING_API_Base {

	/**
	 * @var self|null
	 */
	public static $ins;

	/**
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	public function __construct() {
		parent::__construct();
		$this->method       = WP_REST_Server::READABLE;
		$this->route        = '/rules';
		$this->request_args = array();
		$this->public_api   = false;
	}

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	public function process_api_call() {
		$rules = array(
			array(
				'id'    => 1,
				'title' => __( 'Example rule', 'wp-ext-rule-pricing' ),
			),
			array(
				'id'    => 2,
				'title' => __( 'Example rule two', 'wp-ext-rule-pricing' ),
			),
			array(
				'id'    => 3,
				'title' => __( 'Example rule three', 'wp-ext-rule-pricing' ),
			),
		);

		/**
		 * Filter rules returned by GET /rules.
		 *
		 * @param array<int, array<string, mixed>> $rules Rows.
		 */
		$rules = apply_filters( 'wp_ext_rule_pricing_rest_rules', $rules );

		if ( ! is_array( $rules ) ) {
			return $this->error_response_bad_request(
				__( 'Invalid rules data after filter; expected an array.', 'wp-ext-rule-pricing' )
			);
		}

		$this->response_code = 200;
		$this->total_count   = count( $rules );

		return $this->success_response(
			$rules,
			__( 'Rules loaded successfully.', 'wp-ext-rule-pricing' )
		);
	}
}

WP_EXT_RULE_PRICING_API_Loader::register( 'WP_EXT_RULE_PRICING_API_Get_Rules' );
