<?php
/**
 * Specific products rule (lite).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product → Products.
 */
class WP_EXT_RULE_Pricing_Rule_Type_Product_Select extends WP_EXT_RULE_Pricing_Abstract_Rule_Type {

	public function get_slug() {
		return 'product_select';
	}

	public function get_label() {
		return __( 'Products', 'wp-ext-rule-pricing' );
	}

	public function get_group() {
		return __( 'Product', 'wp-ext-rule-pricing' );
	}

	public function get_fields() {
		return array(
			array(
				'key'         => 'condition',
				'type'        => 'text',
				'placeholder' => __( 'Product IDs (comma separated)', 'wp-ext-rule-pricing' ),
			),
		);
	}

	public function get_summary( $rule = array() ) {
		$condition = isset( $rule['condition'] ) ? $rule['condition'] : '';
		if ( '' === $condition ) {
			return __( 'Select one or more products.', 'wp-ext-rule-pricing' );
		}

		return sprintf(
			/* translators: %s: product ids */
			__( 'Applies to product ID(s): %s', 'wp-ext-rule-pricing' ),
			$condition
		);
	}

	public function get_order() {
		return 20;
	}
}
