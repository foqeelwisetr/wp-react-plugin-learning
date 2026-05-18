<?php
/**
 * Discount tab sections.
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array<string, mixed>>
 */
function wp_ext_rule_pricing_campaign_discount_sections() {
	$h = 'WP_EXT_RULE_Pricing_Campaign_Fields_Helper';

	return array(
		array(
			'id'     => 'discount_main',
			'title'  => __( 'Discount', 'wp-ext-rule-pricing' ),
			'fields' => array(
				array(
					'id'          => 'discount_enabled',
					'type'        => 'toggle',
					'label'       => __( 'Enable', 'wp-ext-rule-pricing' ),
					'description' => __( 'Enable this to apply a discount during the campaign.', 'wp-ext-rule-pricing' ),
					'default'     => false,
				),
				array(
					'id'      => 'discount_type',
					'type'    => 'select',
					'label'   => __( 'Discount type', 'wp-ext-rule-pricing' ),
					'default' => 'percentage',
					'options' => array(
						array( 'value' => 'percentage', 'label' => __( 'Percentage', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'fixed', 'label' => __( 'Fixed amount', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on' => $h::dep_enabled( 'discount_enabled' ),
				),
				array(
					'id'         => 'discount_amount',
					'type'       => 'number',
					'label'      => __( 'Amount', 'wp-ext-rule-pricing' ),
					'default'    => 10,
					'min'        => 0,
					'step'       => 0.01,
					'depends_on' => $h::dep_enabled( 'discount_enabled' ),
				),
				$h::pro(
					array(
						'id'          => 'discount_override',
						'type'        => 'toggle',
						'label'       => __( 'Override discount', 'wp-ext-rule-pricing' ),
						'description' => __( 'Override existing sale prices on products.', 'wp-ext-rule-pricing' ),
						'default'     => false,
						'depends_on'  => $h::dep_enabled( 'discount_enabled' ),
					)
				),
				$h::pro(
					array(
						'id'          => 'discount_max_cap',
						'type'        => 'number',
						'label'       => __( 'Maximum discount cap', 'wp-ext-rule-pricing' ),
						'description' => __( 'Cap for percentage discounts.', 'wp-ext-rule-pricing' ),
						'default'     => '',
						'depends_on'  => array(
							$h::dep_enabled( 'discount_enabled' ),
							array(
								'field' => 'discount_type',
								'value' => 'percentage',
							),
						),
					)
				),
				$h::pro(
					array(
						'id'          => 'discount_apply_to_sale',
						'type'        => 'yes_no',
						'label'       => __( 'Apply on sale products', 'wp-ext-rule-pricing' ),
						'default'     => 'no',
						'depends_on'  => $h::dep_enabled( 'discount_enabled' ),
					)
				),
				$h::pro(
					array(
						'id'          => 'discount_badge_text',
						'type'        => 'text',
						'label'       => __( 'Sale badge text', 'wp-ext-rule-pricing' ),
						'default'     => __( 'Sale!', 'wp-ext-rule-pricing' ),
						'depends_on'  => $h::dep_enabled( 'discount_enabled' ),
					)
				),
			),
		),
	);
}
