<?php
/**
 * Coupons tab — opens Pro modal in React (schema type: pro_modal).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array<string, mixed>>
 */
function wp_ext_rule_pricing_campaign_coupons_sections() {
	return array(
		array(
			'id'     => 'coupons_main',
			'title'  => __( 'Coupons', 'wp-ext-rule-pricing' ),
			'fields' => array(
				array(
					'id'          => 'coupons_intro',
					'type'        => 'help',
					'label'       => '',
					'description' => __( 'Create and assign WooCommerce coupons to this campaign.', 'wp-ext-rule-pricing' ),
					'store'       => false,
				),
				array(
					'id'          => 'coupons_manage',
					'type'        => 'pro_modal',
					'label'       => __( 'Campaign coupons', 'wp-ext-rule-pricing' ),
					'description' => __( 'Manage coupon rules, auto-apply, and limits.', 'wp-ext-rule-pricing' ),
					'upsell_id'   => 'campaign_coupons',
					'button_text' => __( 'Configure coupons', 'wp-ext-rule-pricing' ),
					'pro'         => true,
				),
				WP_EXT_RULE_Pricing_Campaign_Fields_Helper::pro(
					array(
						'id'          => 'coupon_auto_apply',
						'type'        => 'yes_no',
						'label'       => __( 'Auto-apply coupon', 'wp-ext-rule-pricing' ),
						'default'     => 'no',
					)
				),
			),
		),
	);
}
