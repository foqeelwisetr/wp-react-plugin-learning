<?php
/**
 * Always rule (lite).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * General → Always.
 */
class WP_EXT_RULE_Pricing_Rule_Type_Always extends WP_EXT_RULE_Pricing_Abstract_Rule_Type {

	public function get_slug() {
		return 'general_always';
	}

	public function get_label() {
		return __( 'Always', 'wp-ext-rule-pricing' );
	}

	public function get_group() {
		return __( 'General', 'wp-ext-rule-pricing' );
	}

	public function get_fields() {
		return array();
	}

	public function get_operators() {
		return array();
	}

	public function get_summary( $rule = array() ) {
		return __( 'Campaign will always display for all visitors on your site.', 'wp-ext-rule-pricing' );
	}

	public function get_order() {
		return 1;
	}
}
