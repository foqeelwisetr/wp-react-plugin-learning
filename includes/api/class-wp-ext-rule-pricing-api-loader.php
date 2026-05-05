<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers {@see WP_EXT_RULE_PRICING_API_Base} subclasses.
 *
 * @package WP_EXT_RULE_PRICING
 */
class WP_EXT_RULE_PRICING_API_Loader {

	public const REST_NAMESPACE = 'wp-ext-rule-pricing/v1';

	/**
	 * @var self|null
	 */
	private static $ins = null;

	/**
	 * @var array<string, array<string, WP_EXT_RULE_PRICING_API_Base>>
	 */
	private static $registered_apis = array();

	/**
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}

	/**
	 * @param class-string<WP_EXT_RULE_PRICING_API_Base> $api_class Class name.
	 * @return void
	 */
	public static function register( $api_class ) {
		if ( ! class_exists( $api_class ) || ! is_subclass_of( $api_class, 'WP_EXT_RULE_PRICING_API_Base' ) ) {
			return;
		}

		if ( ! method_exists( $api_class, 'get_instance' ) ) {
			return;
		}

		/** @var WP_EXT_RULE_PRICING_API_Base $api_obj */
		$api_obj = $api_class::get_instance();

		if ( empty( $api_obj->route ) ) {
			return;
		}

		$slug = strtolower( $api_class );
		$slug = preg_replace( '/^wp_ext_rule_pricing_api_/', '', $slug );
		if ( '' === $slug ) {
			return;
		}

		if ( ! isset( self::$registered_apis[ $api_obj->route ] ) ) {
			self::$registered_apis[ $api_obj->route ] = array();
		}

		self::$registered_apis[ $api_obj->route ][ $slug ] = $api_obj;
	}

	/**
	 * @return void
	 */
	public static function register_routes() {
		foreach ( self::$registered_apis as $route => $registered_api ) {
			if ( empty( $registered_api ) ) {
				continue;
			}

			$api_group = array_map(
				static function ( $api ) {
					/** @var WP_EXT_RULE_PRICING_API_Base $api */
					if ( empty( $api->method ) ) {
						return false;
					}

					$route_args = array(
						'methods'             => $api->method,
						'callback'            => array( $api, 'api_call' ),
						'permission_callback' => '__return_true',
					);

					if ( false === $api->public_api ) {
						$route_args['permission_callback'] = array( $api, 'rest_permission_callback' );
					}

					if ( is_array( $api->request_args ) && ! empty( $api->request_args ) ) {
						$route_args['args'] = $api->request_args;
					}

					return $route_args;
				},
				$registered_api
			);

			$api_group = array_values( array_filter( $api_group ) );

			if ( ! empty( $api_group ) ) {
				register_rest_route( self::REST_NAMESPACE, $route, $api_group );
			}
		}

		/**
		 * @param array<string, array<string, WP_EXT_RULE_PRICING_API_Base>> $registered_apis Registered.
		 */
		do_action( 'wp_ext_rule_pricing_api_routes_registered', self::$registered_apis );
	}
}

add_action( 'rest_api_init', array( 'WP_EXT_RULE_PRICING_API_Loader', 'register_routes' ) );
