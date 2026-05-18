<?php
/**
 * All products rule (lite).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product → All Products.
 */
class WP_EXT_RULE_Pricing_Rule_Type_All_Products extends WP_EXT_RULE_Pricing_Abstract_Rule_Type {

	public function get_slug() {
		return 'general_all_products';
	}

	public function get_label() {
		return __( 'All Products', 'wp-ext-rule-pricing' );
	}

	public function get_group() {
		return __( 'Product', 'wp-ext-rule-pricing' );
	}

	public function get_summary( $rule = array() ) {
		return __( 'Campaign applies to all products in your store.', 'wp-ext-rule-pricing' );
	}

	public function get_order() {
		return 10;
	}
}
