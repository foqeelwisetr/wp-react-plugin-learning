<?php
/**
 * Campaign list UI: bulk actions, category filters (extensible from Pro).
 *
 * Filter: wp_ext_rule_pricing_campaign_bulk_actions
 * Filter: wp_ext_rule_pricing_campaign_list_categories
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Campaign list registry.
 */
class WP_EXT_RULE_Pricing_Campaign_List_Registry {

	/**
	 * @return void
	 */
	public static function init() {
		// Reserved for future hooks.
	}

	/**
	 * Bulk actions shown when rows are selected.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_bulk_actions() {
		$actions = array(
			array(
				'id'        => 'assign_category',
				'label'     => __( 'Assign Category', 'wp-ext-rule-pricing' ),
				'pro'       => true,
				'upsell_id' => 'bulk_assign_category',
				'icon'      => 'category',
			),
			array(
				'id'        => 'export',
				'label'     => __( 'Export', 'wp-ext-rule-pricing' ),
				'pro'       => true,
				'upsell_id' => 'bulk_export',
				'icon'      => 'download',
			),
			array(
				'id'    => 'delete',
				'label' => __( 'Delete', 'wp-ext-rule-pricing' ),
				'pro'   => false,
				'icon'  => 'trash',
			),
		);

		return apply_filters( 'wp_ext_rule_pricing_campaign_bulk_actions', $actions );
	}

	/**
	 * Toolbar actions (Export all, Import, …).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_toolbar_actions() {
		$actions = array(
			array(
				'id'        => 'export_all',
				'label'     => __( 'Export All', 'wp-ext-rule-pricing' ),
				'pro'       => true,
				'upsell_id' => 'bulk_export',
				'icon'      => 'download',
			),
			array(
				'id'        => 'import',
				'label'     => __( 'Import', 'wp-ext-rule-pricing' ),
				'pro'       => true,
				'upsell_id' => 'bulk_import',
				'icon'      => 'upload',
			),
		);

		return apply_filters( 'wp_ext_rule_pricing_campaign_toolbar_actions', $actions );
	}

	/**
	 * Category filter options for list search bar.
	 *
	 * @return array<int, array<string, string>>
	 */
	public static function get_categories() {
		$categories = array(
			array(
				'value' => '',
				'label' => __( 'All Categories', 'wp-ext-rule-pricing' ),
			),
		);

		return apply_filters( 'wp_ext_rule_pricing_campaign_list_categories', $categories );
	}

	/**
	 * Config passed to React list.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_list_config() {
		return array(
			'bulk_actions'     => self::get_bulk_actions(),
			'toolbar_actions'  => self::get_toolbar_actions(),
			'categories'       => self::get_categories(),
			'search_placeholder' => __( 'Search…', 'wp-ext-rule-pricing' ),
		);
	}
}
