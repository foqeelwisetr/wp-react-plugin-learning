<?php
/**
 * Example: how Pro / add-ons unlock features, tabs, upsell copy, and fields.
 *
 * Do not load this file in production — copy patterns into your add-on plugin.
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unlock all Pro UI (testing in Lite).
 */
// add_filter( 'wp_ext_rule_pricing_is_pro', '__return_true' );

/**
 * Register custom upsell modal copy (FunnelKit-style preview table).
 */
function wp_ext_rule_pricing_example_register_upsells() {
	wp_ext_rule_pricing_register_pro_upsell(
		array(
			'id'          => 'tab_contacts',
			'title'       => __( 'Contacts', 'wp-ext-rule-pricing' ),
			'headline'    => __( 'Unlock Contacts and segment your audience.', 'wp-ext-rule-pricing' ),
			'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
			'upgrade_url' => 'https://example.com/pro',
			'footnote'    => __( 'Top campaigns plugin: 20,000+ users.', 'wp-ext-rule-pricing' ),
			'preview'     => array(
				'type'    => 'table',
				'columns' => array( __( 'Name', 'wp-ext-rule-pricing' ), __( 'Email', 'wp-ext-rule-pricing' ), __( 'Tags', 'wp-ext-rule-pricing' ) ),
				'rows'    => array(
					array( 'Jane Doe', 'jane@example.com', 'VIP' ),
					array( 'John Smith', 'john@example.com', 'Newsletter' ),
				),
			),
		)
	);
}
// add_action( 'wp_ext_rule_pricing_register_pro_upsells', 'wp_ext_rule_pricing_example_register_upsells' );

/**
 * Register Contacts tab + fields when Pro is active.
 */
function wp_ext_rule_pricing_example_register_contacts_tab() {
	wp_ext_rule_pricing_register_campaign_tab(
		array(
			'id'        => 'contacts',
			'label'     => __( 'Contacts', 'wp-ext-rule-pricing' ),
			'pro'       => true,
			'upsell_id' => 'tab_contacts',
			'locked'    => false,
			'order'     => 55,
			'sections'  => array(
				array(
					'id'    => 'contacts_main',
					'title' => __( 'Audience', 'wp-ext-rule-pricing' ),
					'fields' => array(
						array(
							'id'    => 'list_id',
							'type'  => 'text',
							'label' => __( 'Contact list ID', 'wp-ext-rule-pricing' ),
						),
					),
				),
			),
		)
	);
}
// add_action( 'wp_ext_rule_pricing_register_campaign_tabs', 'wp_ext_rule_pricing_example_register_contacts_tab' );

/**
 * Field layout from PHP: row + col + custom classes.
 *
 * $h::col( 2, $h::row( 'my_row', array( 'id' => 'field_a', ... ) ) );
 * $h::col( 2, $h::row( 'my_row', array( 'id' => 'field_b', 'label' => '' ) ) );
 * $h::css_class( 'my-field-class', $field );
 * $h::row_class( 'my-row-class', $first_field_in_row );
 */

/**
 * Add fields to an existing tab from Pro (filter sections by tab id).
 */
function wp_ext_rule_pricing_example_extend_schedule_tab( $sections, $tab_id ) {
	if ( 'schedule' !== $tab_id || ! apply_filters( 'wp_ext_rule_pricing_is_pro', false ) ) {
		return $sections;
	}

	$sections[] = array(
		'id'    => 'pro_schedule_extra',
		'title' => __( 'Pro scheduling', 'wp-ext-rule-pricing' ),
		'fields' => array(
			array(
				'id'    => 'timezone',
				'type'  => 'text',
				'label' => __( 'Timezone', 'wp-ext-rule-pricing' ),
			),
		),
	);

	return $sections;
}
// add_filter( 'wp_ext_rule_pricing_campaign_tab_sections', 'wp_ext_rule_pricing_example_extend_schedule_tab', 10, 2 );

/**
 * Register Evergreen / Deal Pages tabs from an add-on.
 */
function wp_ext_rule_pricing_example_register_pro_tabs() {
	wp_ext_rule_pricing_register_campaign_tab(
		array(
			'id'          => 'evergreen',
			'label'       => __( 'Evergreen', 'wp-ext-rule-pricing' ),
			'description' => __( 'Per-user deadlines (Finale Evergreen).', 'wp-ext-rule-pricing' ),
			'pro'         => true,
			'upsell_id'   => 'schedule_evergreen',
			'locked'      => true,
			'component'   => 'evergreen',
			'addon'       => 'finale-evergreen-campaigns',
			'order'       => 30,
		)
	);
}
// add_action( 'wp_ext_rule_pricing_register_campaign_tabs', 'wp_ext_rule_pricing_example_register_pro_tabs' );

/**
 * Register a custom rule type from PHP.
 */
class WP_EXT_RULE_Pricing_Example_Rule_Cart_Total extends WP_EXT_RULE_Pricing_Abstract_Rule_Type {

	public function get_slug() {
		return 'cart_total';
	}

	public function get_label() {
		return __( 'Cart total', 'wp-ext-rule-pricing' );
	}

	public function get_group() {
		return __( 'Cart', 'wp-ext-rule-pricing' );
	}

	public function get_fields() {
		return array(
			array(
				'key'         => 'condition',
				'type'        => 'text',
				'placeholder' => '99.00',
			),
		);
	}

	public function is_locked() {
		return true;
	}
}

function wp_ext_rule_pricing_example_register_pro_rules( $registry ) {
	wp_ext_rule_pricing_register_rule_type( new WP_EXT_RULE_Pricing_Example_Rule_Cart_Total() );
}
// add_action( 'wp_ext_rule_pricing_register_rule_types', 'wp_ext_rule_pricing_example_register_pro_rules' );
