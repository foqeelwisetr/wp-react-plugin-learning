<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.acmeit.org/
 * @since      1.0.0
 *
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_EXT_RULE_PRICING
 * @subpackage WP_EXT_RULE_PRICING/admin
 * @author     codersantosh <codersantosh@gmail.com>
 */
class WP_EXT_RULE_PRICING_Admin {

	/**
	 * Menu info.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $menu_info    Admin menu information.
	 */
	private $menu_info;

	/**
	 * Gets an instance of this object.
	 * Prevents duplicate instances which avoid artefacts and improves performance.
	 *
	 * @static
	 * @access public
	 * @return object
	 * @since 1.0.0
	 */
	public static function get_instance() {
		// Store the instance locally to avoid private static replication.
		static $instance = null;

		// Only run these methods if they haven't been ran previously.
		if ( null === $instance ) {
			$instance = new self();
		}

		// Always return the instance.
		return $instance;
	}

	/**
	 * Add Admin Page Menu page.
	 *
	 * @access public
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {

		$white_label     = wp_ext_rule_pricing_include()->get_white_label();
		$this->menu_info = $white_label['admin_menu_page'];

		add_menu_page(
			$this->menu_info['page_title'],
			$this->menu_info['menu_title'],
			'manage_options',
			$this->menu_info['menu_slug'],
			array( $this, 'add_setting_root_div' ),
			$this->menu_info['icon_url'],
			$this->menu_info['position'],
		);
	}

	/**
	 * Check if current menu page.
	 *
	 * @access public
	 *
	 * @since    1.0.0
	 * @return boolean ture if current menu page else false.
	 */
	public function is_menu_page() {
		$screen              = get_current_screen();
		$admin_scripts_bases = array( 'toplevel_page_' . WP_EXT_RULE_PRICING_PLUGIN_NAME );
		if ( ! ( isset( $screen->base ) && in_array( $screen->base, $admin_scripts_bases, true ) ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Add class "at-has-hdr-stky".
	 *
	 * @access public
	 * @since    1.0.0
	 * @param string $classes  a space-separated string of class names.
	 * @return string $classes with added class if confition meet.
	 */
	public function add_has_sticky_header( $classes ) {

		if ( ! $this->is_menu_page() ) {
			return $classes;
		}

		return $classes . ' at-has-hdr-stky ';
	}

	/**
	 * Remove third-party and core admin notices on this plugin's screen only.
	 *
	 * @since    1.0.0
	 */
	public function suppress_admin_notices() {
		$white_label = wp_ext_rule_pricing_include()->get_white_label();
		$menu_slug   = isset( $white_label['admin_menu_page']['menu_slug'] ) ? $white_label['admin_menu_page']['menu_slug'] : WP_EXT_RULE_PRICING_PLUGIN_NAME;

		if ( ! isset( $_GET['page'] ) || $menu_slug !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * Add Root Div For React.
	 *
	 * @access public
	 *
	 * @since    1.0.0
	 */
	public function add_setting_root_div() {
		echo '<div id="' . esc_attr( WP_EXT_RULE_PRICING_PLUGIN_NAME ) . '"></div>';
	}

	/**
	 * Register the CSS/JavaScript Resources for the admin area.
	 *
	 * @access public
	 * Use Condition to Load it Only When it is Necessary
	 *
	 * @since    1.0.0
	 */
	public function enqueue_resources() {

		if ( ! $this->is_menu_page() ) {
			return;
		}

		/* Atomic CSS */
		wp_enqueue_style( 'atomic' );
		wp_style_add_data( 'atomic', 'rtl', 'replace' );

		/*Scripts dependency files*/
		$deps_file = WP_EXT_RULE_PRICING_PATH . 'build/admin/index.asset.php';

		/*Fallback dependency array*/
		$dependency = array();
		$version    = WP_EXT_RULE_PRICING_VERSION;

		/*Set dependency and version*/
		if ( file_exists( $deps_file ) ) {
			$deps_file  = require $deps_file;
			$dependency = $deps_file['dependencies'];
			$version    = $deps_file['version'];
		}

		wp_enqueue_script( WP_EXT_RULE_PRICING_PLUGIN_NAME, WP_EXT_RULE_PRICING_URL . 'build/admin/index.js', $dependency, $version, true );

		wp_enqueue_style( 'google-fonts-open-sans', WP_EXT_RULE_PRICING_URL . 'assets/library/fonts/open-sans.css', '', $version );
		wp_enqueue_style( WP_EXT_RULE_PRICING_PLUGIN_NAME, WP_EXT_RULE_PRICING_URL . 'build/admin/index.css', array( 'wp-components' ), $version );
		wp_style_add_data( WP_EXT_RULE_PRICING_PLUGIN_NAME, 'rtl', 'replace' );

		/* Localize */
		$localize = apply_filters(
			'wp_ext_rule_pricing_admin_localize',
			array(
				'version'     => $version,
				'root_id'     => WP_EXT_RULE_PRICING_PLUGIN_NAME,
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'store'       => WP_EXT_RULE_PRICING_PLUGIN_NAME,
				'rest_url'    => get_rest_url(),
				'white_label' => wp_ext_rule_pricing_include()->get_white_label(),
			)
		);

		wp_set_script_translations( WP_EXT_RULE_PRICING_PLUGIN_NAME, WP_EXT_RULE_PRICING_PLUGIN_NAME );
		wp_localize_script( WP_EXT_RULE_PRICING_PLUGIN_NAME, 'wpextrulepricingLocalize', $localize );
	}

	/**
	 * Get settings schema
	 * Schema: http://json-schema.org/draft-04/schema#
	 *
	 * Add your own settings fields here
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 *
	 * @return array settings schema for this plugin.
	 */
	public function get_settings_schema() {
		$setting_properties = apply_filters(
			'wp_ext_rule_pricing_options_properties',
			array(
				/*Settings -> Settings1*/
				'setting1'  => array(
					'type' => 'string',
				),
				'setting2'  => array(
					'type' => 'string',
				),
				/*Settings -> Settings2*/
				'setting3'  => array(
					'type' => 'boolean',
				),
				'setting4'  => array(
					'type' => 'boolean',
				),
				'setting5'  => array(
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_key',
				),
				/*Settings -> Advanced*/
				'deleteAll' => array(
					'type' => 'boolean',
				),
			),
		);

		return array(
			'type'       => 'object',
			'properties' => $setting_properties,
		);
	}

	/**
	 * Register settings.
	 * Common callback function of rest_api_init and admin_init
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_settings() {
		$defaults = wp_ext_rule_pricing_default_options();

		register_setting(
			'wp_ext_rule_pricing_settings_group',
			WP_EXT_RULE_PRICING_OPTION_NAME,
			array(
				'type'         => 'object',
				'default'      => $defaults,
				'show_in_rest' => array(
					'schema' => $this->get_settings_schema(),
				),
			)
		);
	}

	/**
	 * Add plugin menu items.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 * @param string[] $actions     An array of plugin action links. By default this can include
	 *                              'activate', 'deactivate', and 'delete'. With Multisite active
	 *                              this can also include 'network_active' and 'network_only' items.
	 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
	 *                              and the {@see 'plugin_row_meta'} filter for the list
	 *                              of possible values.
	 * @param string   $context     The plugin context. By default this can include 'all',
	 *                              'active', 'inactive', 'recently_activated', 'upgrade',
	 *                              'mustuse', 'dropins', and 'search'.
	 * @return array settings schema for this plugin.
	 */
	public function add_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {
		$actions[] = '<a href="' . esc_url( menu_page_url( $this->menu_info['menu_slug'], false ) ) . '">' . esc_html__( 'Settings', 'wp-ext-rule-pricing' ) . '</a>';
		return $actions;
	}
}

if ( ! function_exists( 'wp_ext_rule_pricing_admin' ) ) {
	/**
	 * Return instance of  WP_EXT_RULE_PRICING_Admin class
	 *
	 * @since 1.0.0
	 *
	 * @return WP_EXT_RULE_PRICING_Admin
	 */
	function wp_ext_rule_pricing_admin() {//phpcs:ignore
		return WP_EXT_RULE_PRICING_Admin::get_instance();
	}
}
