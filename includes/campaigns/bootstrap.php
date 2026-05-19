<?php
/**
 * Campaigns module bootstrap (Finale-style extensibility).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$campaigns_dir = trailingslashit( __DIR__ );

require_once $campaigns_dir . 'class-campaign-repository.php';
require_once $campaigns_dir . 'fields/class-campaign-fields-helper.php';
require_once $campaigns_dir . 'fields/sections-schedule.php';
require_once $campaigns_dir . 'fields/sections-discount.php';
require_once $campaigns_dir . 'fields/sections-inventory.php';
require_once $campaigns_dir . 'fields/sections-elements.php';
require_once $campaigns_dir . 'fields/sections-coupons.php';
require_once $campaigns_dir . 'class-campaign-fields-registry.php';
require_once $campaigns_dir . 'class-campaign-demo-data.php';
require_once $campaigns_dir . 'class-campaign-pro-upsell-registry.php';
require_once $campaigns_dir . 'class-campaign-list-registry.php';
require_once $campaigns_dir . 'class-campaign-tab-registry.php';
require_once $campaigns_dir . 'rules/abstract-class-rule-type.php';
require_once $campaigns_dir . 'rules/class-rule-type-registry.php';
require_once $campaigns_dir . 'rules/class-rule-type-always.php';
require_once $campaigns_dir . 'rules/class-rule-type-all-products.php';
require_once $campaigns_dir . 'rules/class-rule-type-product-select.php';

/**
 * Boot campaign registries.
 *
 * @return void
 */
function wp_ext_rule_pricing_campaigns_init() {
	WP_EXT_RULE_Pricing_Campaign_Pro_Upsell_Registry::init();
	WP_EXT_RULE_Pricing_Campaign_List_Registry::init();
	WP_EXT_RULE_Pricing_Campaign_Tab_Registry::init();
	WP_EXT_RULE_Pricing_Rule_Type_Registry::init();
}

add_action( 'init', 'wp_ext_rule_pricing_campaigns_init', 5 );
