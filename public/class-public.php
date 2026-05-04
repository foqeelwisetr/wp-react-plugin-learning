<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.acmeit.org/
 * @since      1.0.0
 *
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/public
 * @author     codersantosh <codersantosh@gmail.com>
 */
class WP_EXT_RULE_PRICING_Public {

	/**
	 * Gets an instance of this object.
	 *
	 * @static
	 * @access public
	 * @return object
	 * @since 1.0.0
	 */
	public static function get_instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Register the CSS/JavaScript resources for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_public_resources() {
		$script_path = WP_EXT_RULE_PRICING_PATH . 'build/public/index.js';
		if ( ! file_exists( $script_path ) ) {
			return;
		}

		$deps_file = WP_EXT_RULE_PRICING_PATH . 'build/public/index.asset.php';
		$dependency = array();
		$version    = WP_EXT_RULE_PRICING_VERSION;

		if ( file_exists( $deps_file ) ) {
			$deps_file  = require $deps_file;
			$dependency = $deps_file['dependencies'];
			$version    = $deps_file['version'];
		}

		$handle = WP_EXT_RULE_PRICING_PLUGIN_NAME . '-public';

		wp_enqueue_style( 'atomic' );
		wp_style_add_data( 'atomic', 'rtl', 'replace' );

		wp_enqueue_script( $handle, WP_EXT_RULE_PRICING_URL . 'build/public/index.js', $dependency, $version, true );

		$css_path = WP_EXT_RULE_PRICING_PATH . 'build/public/index.css';
		if ( file_exists( $css_path ) ) {
			wp_enqueue_style( $handle, WP_EXT_RULE_PRICING_URL . 'build/public/index.css', array( 'wp-components' ), $version );
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}

		$localize = apply_filters(
			'wp_ext_rule_pricing_public_localize',
			array(
				'version'     => $version,
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'rest_url'    => get_rest_url(),
				'white_label' => wp_ext_rule_pricing_include()->get_white_label(),
			)
		);

		wp_set_script_translations( $handle, WP_EXT_RULE_PRICING_PLUGIN_NAME );
		wp_localize_script( $handle, 'wpextrulepricingPublicLocalize', $localize );
	}
}

if ( ! function_exists( 'wp_ext_rule_pricing_public' ) ) {
	/**
	 * Return instance of WP_EXT_RULE_PRICING_Public class.
	 *
	 * @since 1.0.0
	 * @return WP_EXT_RULE_PRICING_Public
	 */
	function wp_ext_rule_pricing_public() { // phpcs:ignore
		return WP_EXT_RULE_PRICING_Public::get_instance();
	}
}
