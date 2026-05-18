<?php
/**
 * Pro upsell modal content — all text/preview controlled from PHP.
 *
 * Register: wp_ext_rule_pricing_register_pro_upsell( $config );
 * Filter:   wp_ext_rule_pricing_pro_upsells
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro upsell registry.
 */
class WP_EXT_RULE_Pricing_Campaign_Pro_Upsell_Registry {

	/**
	 * @var array<string, array<string, mixed>>|null
	 */
	private static $upsells = null;

	/**
	 * @return void
	 */
	public static function init() {
		self::register_defaults();
	}

	/**
	 * @return void
	 */
	private static function register_defaults() {
		$upgrade_url = apply_filters(
			'wp_ext_rule_pricing_pro_upgrade_url',
			'https://www.acmeit.org/'
		);

		self::register(
			array(
				'id'          => 'schedule_recurring',
				'title'       => __( 'Recurring Campaign', 'wp-ext-rule-pricing' ),
				'headline'    => __( 'Unlock Recurring Campaign and other awesome features.', 'wp-ext-rule-pricing' ),
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => __( 'Automate campaigns that repeat on a schedule — no manual restarts.', 'wp-ext-rule-pricing' ),
				'upgrade_url' => $upgrade_url,
				'preview'     => array(
					'type'    => 'table',
					'columns' => array(
						__( 'Pattern', 'wp-ext-rule-pricing' ),
						__( 'Status', 'wp-ext-rule-pricing' ),
						__( 'Next run', 'wp-ext-rule-pricing' ),
					),
					'rows'    => array(
						array( __( 'Weekly flash sale', 'wp-ext-rule-pricing' ), __( 'Active', 'wp-ext-rule-pricing' ), __( 'Mon 9:00 AM', 'wp-ext-rule-pricing' ) ),
						array( __( 'Weekend boost', 'wp-ext-rule-pricing' ), __( 'Active', 'wp-ext-rule-pricing' ), __( 'Sat 12:00 AM', 'wp-ext-rule-pricing' ) ),
						array( __( 'Monthly clearance', 'wp-ext-rule-pricing' ), __( 'Scheduled', 'wp-ext-rule-pricing' ), __( '1st of month', 'wp-ext-rule-pricing' ) ),
					),
				),
			)
		);

		self::register(
			array(
				'id'          => 'schedule_evergreen',
				'title'       => __( 'Evergreen Campaign', 'wp-ext-rule-pricing' ),
				'headline'    => __( 'Unlock Evergreen Campaign and per-user deadlines.', 'wp-ext-rule-pricing' ),
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => __( 'Works with Finale Evergreen — unique deadline per visitor.', 'wp-ext-rule-pricing' ),
				'upgrade_url' => $upgrade_url,
				'preview'     => array(
					'type'    => 'table',
					'columns' => array(
						__( 'Trigger', 'wp-ext-rule-pricing' ),
						__( 'Users', 'wp-ext-rule-pricing' ),
						__( 'Status', 'wp-ext-rule-pricing' ),
					),
					'rows'    => array(
						array( __( 'First visit', 'wp-ext-rule-pricing' ), '1,240', __( 'Running', 'wp-ext-rule-pricing' ) ),
						array( __( 'Cart page', 'wp-ext-rule-pricing' ), '380', __( 'Running', 'wp-ext-rule-pricing' ) ),
						array( __( 'Email link', 'wp-ext-rule-pricing' ), '92', __( 'Paused', 'wp-ext-rule-pricing' ) ),
					),
				),
			)
		);

		self::register(
			array(
				'id'          => 'tab_contacts',
				'title'       => __( 'Contacts', 'wp-ext-rule-pricing' ),
				'headline'    => __( 'Unlock Contacts and campaign analytics.', 'wp-ext-rule-pricing' ),
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => __( 'See who entered each campaign and track engagement.', 'wp-ext-rule-pricing' ),
				'upgrade_url' => $upgrade_url,
				'preview'     => array(
					'type'    => 'table',
					'columns' => array(
						__( 'Contact', 'wp-ext-rule-pricing' ),
						__( 'Entered', 'wp-ext-rule-pricing' ),
						__( 'Status', 'wp-ext-rule-pricing' ),
					),
					'rows'    => array(
						array( 'john@example.com', __( '2 hours ago', 'wp-ext-rule-pricing' ), __( 'Active', 'wp-ext-rule-pricing' ) ),
						array( 'jane@example.com', __( 'Yesterday', 'wp-ext-rule-pricing' ), __( 'Completed', 'wp-ext-rule-pricing' ) ),
					),
				),
			)
		);

		self::register(
			array(
				'id'          => 'tab_ideas_factory',
				'title'       => __( 'Ideas Factory', 'wp-ext-rule-pricing' ),
				'headline'    => __( 'Unlock Ideas Factory — pre-built campaign templates.', 'wp-ext-rule-pricing' ),
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => '',
				'upgrade_url' => $upgrade_url,
				'preview'     => array(
					'type'    => 'list',
					'items'   => array(
						__( 'Flash sale countdown', 'wp-ext-rule-pricing' ),
						__( 'Low stock urgency bar', 'wp-ext-rule-pricing' ),
						__( 'Cart abandonment discount', 'wp-ext-rule-pricing' ),
					),
				),
			)
		);

		self::register(
			array(
				'id'          => 'tab_events',
				'title'       => __( 'Events', 'wp-ext-rule-pricing' ),
				'headline'    => __( 'Unlock Events — dynamic discounts and goals.', 'wp-ext-rule-pricing' ),
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => '',
				'upgrade_url' => $upgrade_url,
				'preview'     => array(
					'type'  => 'text',
					'items' => array(
						__( 'Boost sold units on campaign start', 'wp-ext-rule-pricing' ),
						__( 'Increase discount when stock is low', 'wp-ext-rule-pricing' ),
					),
				),
			)
		);

		self::register(
			array(
				'id'          => 'campaign_coupons',
				'title'       => __( 'Coupons', 'wp-ext-rule-pricing' ),
				'headline'    => __( 'Unlock campaign coupons and auto-apply rules.', 'wp-ext-rule-pricing' ),
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => '',
				'upgrade_url' => $upgrade_url,
				'preview'     => array(
					'type'    => 'table',
					'columns' => array(
						__( 'Coupon', 'wp-ext-rule-pricing' ),
						__( 'Discount', 'wp-ext-rule-pricing' ),
						__( 'Uses', 'wp-ext-rule-pricing' ),
					),
					'rows'    => array(
						array( 'SAVE20', '20%', '142' ),
						array( 'FLASH10', '10%', '89' ),
					),
				),
			)
		);

		self::register(
			array(
				'id'          => 'default',
				'title'       => __( 'Pro Feature', 'wp-ext-rule-pricing' ),
				'headline'    => __( 'Unlock this feature and more with Pro.', 'wp-ext-rule-pricing' ),
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => '',
				'upgrade_url' => $upgrade_url,
				'preview'     => array(
					'type'  => 'text',
					'items' => array(
						__( 'Advanced campaign types', 'wp-ext-rule-pricing' ),
						__( 'More rule types', 'wp-ext-rule-pricing' ),
						__( 'Evergreen & deal pages add-ons', 'wp-ext-rule-pricing' ),
					),
				),
			)
		);
	}

	/**
	 * @param array<string, mixed> $config Upsell config.
	 * @return void
	 */
	public static function register( $config ) {
		if ( empty( $config['id'] ) ) {
			return;
		}

		if ( null === self::$upsells ) {
			self::$upsells = array();
		}

		$id = sanitize_key( $config['id'] );

		self::$upsells[ $id ] = wp_parse_args(
			$config,
			array(
				'id'          => $id,
				'title'       => '',
				'headline'    => '',
				'button_text' => __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ),
				'footnote'    => '',
				'upgrade_url' => '',
				'preview'     => array(),
			)
		);
	}

	/**
	 * @param string $id Upsell id.
	 * @return array<string, mixed>|null
	 */
	public static function get( $id ) {
		if ( null === self::$upsells ) {
			self::init();
		}

		$id = sanitize_key( $id );

		if ( isset( self::$upsells[ $id ] ) ) {
			return self::$upsells[ $id ];
		}

		return isset( self::$upsells['default'] ) ? self::$upsells['default'] : null;
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_all() {
		if ( null === self::$upsells ) {
			self::init();
		}

		return apply_filters( 'wp_ext_rule_pricing_pro_upsells', self::$upsells );
	}
}

/**
 * @param array<string, mixed> $config Upsell.
 * @return void
 */
function wp_ext_rule_pricing_register_pro_upsell( $config ) {
	WP_EXT_RULE_Pricing_Campaign_Pro_Upsell_Registry::register( $config );
}
