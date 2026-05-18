<?php //phpcs:ignore
/**
 * Includes necessary files
 *
 * @package WP_EXT_RULE_PRICING
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once trailingslashit( __DIR__ ) . 'class-api.php';
require_once trailingslashit( dirname( __DIR__ ) ) . 'settings/class-wp-ext-rule-pricing-settings-registry.php';
require_once trailingslashit( __DIR__ ) . 'class-api-settings-ui.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-base.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-loader.php';
require_once trailingslashit( dirname( __DIR__ ) ) . 'campaigns/bootstrap.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-get-rules.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-get-campaigns.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-post-campaign.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-get-campaign.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-put-campaign.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-delete-campaign.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-get-campaign-rule-types.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-get-campaign-tabs.php';
require_once trailingslashit( __DIR__ ) . 'class-wp-ext-rule-pricing-api-get-campaign-pro-upsells.php';
