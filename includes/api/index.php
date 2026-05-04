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
require_once trailingslashit( __DIR__ ) . 'class-api-settings.php';
