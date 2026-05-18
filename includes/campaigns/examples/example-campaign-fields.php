<?php
/**
 * Example: register tab fields with defaults from PHP (Autonami-style schema).
 *
 * Copy patterns into your Pro add-on. Do not load in production.
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add fields to an existing tab (e.g. inventory) via filter.
 */
function wp_ext_rule_pricing_example_inventory_fields( $sections, $tab_id ) {
	if ( 'inventory' !== $tab_id ) {
		return $sections;
	}

	$h = 'WP_EXT_RULE_Pricing_Campaign_Fields_Helper';

	$sections[0]['fields'][] = $h::field(
		'my_custom_flag',
		'yes_no',
		__( 'My custom flag', 'wp-ext-rule-pricing' ),
		'yes',
		array(
			'description' => __( 'Default is Yes — blue button on load.', 'wp-ext-rule-pricing' ),
			'depends_on'  => $h::dep_yes( 'inventory_enabled' ),
		)
	);

	return $sections;
}
// add_filter( 'wp_ext_rule_pricing_campaign_tab_sections', 'wp_ext_rule_pricing_example_inventory_fields', 10, 2 );

/**
 * Register a whole new tab with default toggles + radios.
 */
function wp_ext_rule_pricing_example_register_custom_tab() {
	$h = 'WP_EXT_RULE_Pricing_Campaign_Fields_Helper';

	wp_ext_rule_pricing_register_campaign_tab(
		array(
			'id'       => 'my_automation',
			'label'    => __( 'My Automation', 'wp-ext-rule-pricing' ),
			'order'    => 85,
			'sections' => array(
				array(
					'id'     => 'my_automation_main',
					'title'  => __( 'My Automation', 'wp-ext-rule-pricing' ),
					'fields' => array(
						$h::yes_no(
							'feature_enabled',
							__( 'Enable', 'wp-ext-rule-pricing' ),
							'yes'
						),
						$h::show_hide(
							'show_banner',
							__( 'Show banner', 'wp-ext-rule-pricing' ),
							'show',
							array(
								'depends_on' => $h::dep_yes( 'feature_enabled' ),
							)
						),
						$h::field(
							'run_mode',
							'radio',
							__( 'Run mode', 'wp-ext-rule-pricing' ),
							'immediate',
							array(
								'options'    => array(
									array(
										'value' => 'immediate',
										'label' => __( 'Immediate', 'wp-ext-rule-pricing' ),
									),
									array(
										'value' => 'delayed',
										'label' => __( 'Delayed', 'wp-ext-rule-pricing' ),
									),
								),
								'depends_on' => $h::dep_yes( 'feature_enabled' ),
							)
						),
					),
				),
			),
		)
	);
}
// add_action( 'wp_ext_rule_pricing_register_campaign_tabs', 'wp_ext_rule_pricing_example_register_custom_tab' );
