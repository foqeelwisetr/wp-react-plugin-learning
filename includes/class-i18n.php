<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.acmeit.org/
 * @since      1.0.0
 *
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/includes
 * @author     codersantosh <codersantosh@gmail.com>
 */
class WP_EXT_RULE_PRICING_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-ext-rule-pricing',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
