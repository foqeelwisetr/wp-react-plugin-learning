<?php
/**
 * Inventory tab sections.
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array<string, mixed>>
 */
function wp_ext_rule_pricing_campaign_inventory_sections() {
	$h = 'WP_EXT_RULE_Pricing_Campaign_Fields_Helper';

	$dep_inv = $h::dep_yes( 'inventory_enabled' );

	return array(
		array(
			'id'     => 'inventory_main',
			'title'  => __( 'Inventory', 'wp-ext-rule-pricing' ),
			'fields' => array(
				$h::yes_no(
					'inventory_enabled',
					__( 'Enable', 'wp-ext-rule-pricing' ),
					'no',
					array(
						'description' => __( 'Enable this to define units of item to be sold during campaign.', 'wp-ext-rule-pricing' ),
					)
				),
				$h::help(
					__( 'Need Help with setting up Inventory?', 'wp-ext-rule-pricing' ),
					'https://xlplugins.com/'
				),
				array(
					'id'         => 'quantity_mode',
					'type'       => 'radio',
					'label'      => __( 'Quantity to be Sold', 'wp-ext-rule-pricing' ),
					'default'    => 'custom',
					'options'    => array(
						array( 'value' => 'custom', 'label' => __( 'Custom Stock Quantity', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'existing', 'label' => __( 'Existing Stock Quantity', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on' => $dep_inv,
				),
				array(
					'id'         => 'quantity_range',
					'type'       => 'radio',
					'label'      => __( 'Quantity', 'wp-ext-rule-pricing' ),
					'default'    => 'basic',
					'options'    => array(
						array( 'value' => 'basic', 'label' => __( 'Basic Range', 'wp-ext-rule-pricing' ) ),
						array(
							'value' => 'advanced',
							'label' => __( 'Advanced', 'wp-ext-rule-pricing' ),
							'pro'   => true,
						),
					),
					'depends_on' => array(
						$dep_inv,
						array( 'field' => 'quantity_mode', 'value' => 'custom' ),
					),
				),
				array(
					'id'          => 'quantity_amount',
					'type'        => 'number',
					'label'       => __( 'Quantity', 'wp-ext-rule-pricing' ),
					'description' => __( 'Custom Quantity is the new overall quantity of a product available for purchase.', 'wp-ext-rule-pricing' ),
					'default'     => 10,
					'min'         => 1,
					'depends_on'  => array(
						$dep_inv,
						array( 'field' => 'quantity_mode', 'value' => 'custom' ),
					),
				),
				array(
					'id'         => 'sold_units_calc',
					'type'       => 'radio',
					'label'      => __( 'Calculate Sold Units (for counter bar)', 'wp-ext-rule-pricing' ),
					'default'    => 'current',
					'options'    => array(
						array( 'value' => 'current', 'label' => __( 'Current Occurrence', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'overall', 'label' => __( 'Overall Campaign', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on' => $dep_inv,
				),
				$h::help( __( 'Need help? Learn More', 'wp-ext-rule-pricing' ) ),
				$h::yes_no(
					'out_of_stock_setup',
					__( 'Setup campaign on Out of Stock Products', 'wp-ext-rule-pricing' ),
					'no',
					array( 'depends_on' => $dep_inv )
				),
				$h::yes_no(
					'end_campaign_when_sold',
					__( 'End Campaign', 'wp-ext-rule-pricing' ),
					'yes',
					array(
						'description' => __( 'When all the units set up in the campaign are sold.', 'wp-ext-rule-pricing' ),
						'depends_on'  => $dep_inv,
					)
				),
			),
		),
	);
}
