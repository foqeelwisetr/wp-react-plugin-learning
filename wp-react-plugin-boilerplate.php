<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.acmeit.org/
 * @since             1.0.0
 * @package           WP_EXT_RULE_PRICING
 *
 * @wordpress-plugin
 * Plugin Name:       WP React Plugin Boilerplate - WordPress Setting via React and Rest API
 * Plugin URI:        https://www.addonspress.com/wordpress-starter-plugins/wp-ext-rule-pricing
 * Description:       WordPress Setting via React and Rest API.
 * Version:           1.0.0
 * Author:            codersantosh
 * Author URI:        https://www.acmeit.org/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-ext-rule-pricing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin path.
 * Current plugin url.
 * Current plugin version.
 * Current plugin name.
 * Current plugin option name.
 */
define( 'WP_EXT_RULE_PRICING_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_EXT_RULE_PRICING_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_EXT_RULE_PRICING_VERSION', '1.0.0' );
define( 'WP_EXT_RULE_PRICING_PLUGIN_NAME', 'wp-ext-rule-pricing' );
define( 'WP_EXT_RULE_PRICING_OPTION_NAME', 'wp-ext-rule-pricing' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function wp_ext_rule_pricing_activate() {
	require_once WP_EXT_RULE_PRICING_PATH . 'includes/class-activator.php';
	WP_EXT_RULE_PRICING_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator
 */
function wp_ext_rule_pricing_deactivate() {
	require_once WP_EXT_RULE_PRICING_PATH . 'includes/class-deactivator.php';
	WP_EXT_RULE_PRICING_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wp_ext_rule_pricing_activate' );
register_deactivation_hook( __FILE__, 'wp_ext_rule_pricing_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WP_EXT_RULE_PRICING_PATH . 'includes/main.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wp_ext_rule_pricing_run() {

	$plugin = new WP_EXT_RULE_PRICING();
	$plugin->run();
}
wp_ext_rule_pricing_run();
