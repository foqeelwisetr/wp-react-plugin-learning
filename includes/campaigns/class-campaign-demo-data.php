<?php
/**
 * Demo campaigns for listing UI (Autonami-style examples).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seeds example campaigns once.
 */
class WP_EXT_RULE_Pricing_Campaign_Demo_Data {

	/**
	 * @return void
	 */
	public static function maybe_seed() {
		$stored = get_option( WP_EXT_RULE_Pricing_Campaign_Repository::OPTION_KEY, array() );
		if ( is_array( $stored ) && count( $stored ) > 0 ) {
			return;
		}

		$defaults = WP_EXT_RULE_Pricing_Campaign_Fields_Registry::default_settings();
		$now      = current_time( 'mysql' );

		$rows = array(
			array(
				'title'    => __( 'Countdown Timer + Bar', 'wp-ext-rule-pricing' ),
				'status'   => 'active',
				'priority' => 10,
				'event'    => __( 'Always', 'wp-ext-rule-pricing' ),
				'category' => __( 'Product Page', 'wp-ext-rule-pricing' ),
			),
			array(
				'title'    => __( 'Flash Sale — 20% Off', 'wp-ext-rule-pricing' ),
				'status'   => 'active',
				'priority' => 20,
				'event'    => __( 'Product Category', 'wp-ext-rule-pricing' ),
				'category' => __( 'Discount', 'wp-ext-rule-pricing' ),
			),
			array(
				'title'    => __( 'Inventory Goal Deal', 'wp-ext-rule-pricing' ),
				'status'   => 'paused',
				'priority' => 15,
				'event'    => __( 'All Products', 'wp-ext-rule-pricing' ),
				'category' => __( 'Inventory', 'wp-ext-rule-pricing' ),
			),
			array(
				'title'    => __( 'Homepage Sticky Header', 'wp-ext-rule-pricing' ),
				'status'   => 'draft',
				'priority' => 5,
				'event'    => __( 'Home Page', 'wp-ext-rule-pricing' ),
				'category' => __( 'Elements', 'wp-ext-rule-pricing' ),
			),
		);

		$stored = array();
		$id     = 1;

		foreach ( $rows as $row ) {
			$settings = $defaults;
			if ( 1 === $id ) {
				$settings['elements']['timer_visibility']    = 'show';
				$settings['elements']['timer_position']      = 'below_price';
				$settings['elements']['timer_skin']          = 'round_ghost';
				$settings['elements']['bar_visibility']        = 'show';
				$settings['elements']['bar_position']        = 'below_price';
				$settings['schedule']['schedule_type']       = 'one_time';
				$settings['schedule']['start_date']          = gmdate( 'Y-m-d' );
				$settings['schedule']['end_date']            = gmdate( 'Y-m-d', strtotime( '+7 days' ) );
				$settings['inventory']['inventory_enabled']  = 'yes';
				$settings['inventory']['quantity_amount']    = 50;
			}
			if ( 2 === $id ) {
				$settings['discount']['discount_enabled'] = true;
				$settings['discount']['discount_type']    = 'percentage';
				$settings['discount']['discount_amount']  = 20;
			}
			if ( 3 === $id ) {
				$settings['inventory']['inventory_enabled'] = 'yes';
				$settings['inventory']['quantity_amount'] = 100;
			}

			$stored[ (string) $id ] = array(
				'id'         => $id,
				'title'      => $row['title'],
				'status'     => $row['status'],
				'priority'   => $row['priority'],
				'event'      => $row['event'],
				'category'   => $row['category'],
				'rules'      => WP_EXT_RULE_Pricing_Campaign_Repository::default_rule_groups(),
				'settings'   => $settings,
				'created_at' => $now,
				'updated_at' => $now,
			);
			++$id;
		}

		update_option( WP_EXT_RULE_Pricing_Campaign_Repository::OPTION_KEY, $stored, false );
	}
}
