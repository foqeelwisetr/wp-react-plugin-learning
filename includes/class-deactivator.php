<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.acmeit.org/
 * @since      1.0.0
 *
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/includes
 * @author     codersantosh <codersantosh@gmail.com>
 */
class WP_EXT_RULE_PRICING_Deactivator {

	/**
	 * Fired during plugin deactivation.
	 *
	 * Removing options and all data related to plugin if user select remove data on deactivate.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		if ( wp_ext_rule_pricing_get_options( 'deleteAll' ) ) {
			delete_option( WP_EXT_RULE_PRICING_OPTION_NAME );
		}
	}
}
