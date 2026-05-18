<?php
/**
 * Extensible campaign settings tabs (Schedule, Discount, … for Pro add-ons).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Campaign tab registry.
 */
class WP_EXT_RULE_Pricing_Campaign_Tab_Registry {

	/**
	 * @var array<int, array<string, mixed>>|null
	 */
	private static $tabs = null;

	/**
	 * @return void
	 */
	public static function init() {
		self::register_core_tabs();
	}

	/**
	 * @return void
	 */
	private static function register_core_tabs() {
		$tabs = array(
			array(
				'id'          => 'schedule',
				'label'       => __( 'Schedule', 'wp-ext-rule-pricing' ),
				'description' => __( 'When the campaign runs.', 'wp-ext-rule-pricing' ),
				'icon'        => 'calendar',
				'locked'      => false,
				'default'     => true,
				'component'   => 'schedule',
				'order'       => 10,
			),
			array(
				'id'          => 'discount',
				'label'       => __( 'Discount', 'wp-ext-rule-pricing' ),
				'description' => __( 'Product discounts.', 'wp-ext-rule-pricing' ),
				'icon'        => 'tag',
				'locked'      => false,
				'component'   => 'discount',
				'order'       => 20,
			),
			array(
				'id'          => 'inventory',
				'label'       => __( 'Inventory', 'wp-ext-rule-pricing' ),
				'description' => __( 'Stock goals.', 'wp-ext-rule-pricing' ),
				'icon'        => 'inventory',
				'locked'      => false,
				'component'   => 'inventory',
				'order'       => 30,
			),
			array(
				'id'          => 'elements',
				'label'       => __( 'Elements', 'wp-ext-rule-pricing' ),
				'description' => __( 'Timer, bar, badges.', 'wp-ext-rule-pricing' ),
				'icon'        => 'dashboard',
				'locked'      => false,
				'component'   => 'elements',
				'order'       => 40,
			),
			array(
				'id'          => 'ideas_factory',
				'label'       => __( 'Ideas Factory', 'wp-ext-rule-pricing' ),
				'icon'        => 'lightbulb',
				'pro'         => true,
				'upsell_id'   => 'tab_ideas_factory',
				'locked'      => true,
				'component'   => 'ideas_factory',
				'order'       => 50,
			),
			array(
				'id'          => 'contacts',
				'label'       => __( 'Contacts', 'wp-ext-rule-pricing' ),
				'icon'        => 'groups',
				'pro'         => true,
				'upsell_id'   => 'tab_contacts',
				'locked'      => true,
				'component'   => 'contacts',
				'order'       => 55,
			),
			array(
				'id'          => 'coupons',
				'label'       => __( 'Coupons', 'wp-ext-rule-pricing' ),
				'icon'        => 'tickets',
				'locked'      => false,
				'component'   => 'coupons',
				'order'       => 60,
				'description' => __( 'WooCommerce coupons for this campaign.', 'wp-ext-rule-pricing' ),
			),
			array(
				'id'        => 'events',
				'label'     => __( 'Events', 'wp-ext-rule-pricing' ),
				'icon'      => 'chart',
				'pro'       => true,
				'upsell_id' => 'tab_events',
				'locked'    => true,
				'component' => 'events',
				'order'     => 70,
			),
			array(
				'id'        => 'actions',
				'label'     => __( 'Actions', 'wp-ext-rule-pricing' ),
				'icon'      => 'bolt',
				'locked'    => true,
				'component' => 'actions',
				'order'     => 80,
			),
			array(
				'id'          => 'rules',
				'label'       => __( 'Rules', 'wp-ext-rule-pricing' ),
				'description' => __( 'Conditional display rules.', 'wp-ext-rule-pricing' ),
				'icon'        => 'filter',
				'locked'      => false,
				'component'   => 'rules',
				'order'       => 90,
			),
			array(
				'id'        => 'advanced',
				'label'     => __( 'Advanced', 'wp-ext-rule-pricing' ),
				'icon'      => 'admin-generic',
				'locked'    => true,
				'component' => 'advanced',
				'order'     => 100,
			),
		);

		foreach ( $tabs as $tab ) {
			self::register( $tab );
		}
	}

	/**
	 * @param array<string, mixed> $tab Tab config.
	 * @return void
	 */
	public static function register( $tab ) {
		if ( empty( $tab['id'] ) ) {
			return;
		}

		if ( null === self::$tabs ) {
			self::$tabs = array();
		}

		$id = sanitize_key( $tab['id'] );

		$sections = WP_EXT_RULE_Pricing_Campaign_Fields_Registry::get_sections( $id );

		self::$tabs[ $id ] = wp_parse_args(
			$tab,
			array(
				'id'          => $id,
				'label'       => '',
				'description' => '',
				'icon'        => '',
				'locked'      => false,
				'pro'         => false,
				'upsell_id'   => '',
				'component'   => $id,
				'order'       => 50,
				'addon'       => '',
				'default'     => false,
				'sections'    => $sections,
			)
		);

		if ( ! empty( $sections ) ) {
			self::$tabs[ $id ]['sections'] = $sections;
		}
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_tabs() {
		if ( null === self::$tabs ) {
			self::init();
		}

		$tabs = array_values( self::$tabs );

		usort(
			$tabs,
			static function ( $a, $b ) {
				return (int) $a['order'] - (int) $b['order'];
			}
		);

		return apply_filters( 'wp_ext_rule_pricing_campaign_tabs', $tabs );
	}
}

/**
 * Register a campaign tab from add-ons.
 *
 * @param array<string, mixed> $tab Tab config.
 * @return void
 */
function wp_ext_rule_pricing_register_campaign_tab( $tab ) {
	WP_EXT_RULE_Pricing_Campaign_Tab_Registry::register( $tab );
}
