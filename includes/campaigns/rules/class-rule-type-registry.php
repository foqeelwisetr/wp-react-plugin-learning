<?php
/**
 * Rule type registry — register types from PHP classes or filters.
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rule type registry.
 */
class WP_EXT_RULE_Pricing_Rule_Type_Registry {

	/**
	 * @var array<string, WP_EXT_RULE_Pricing_Abstract_Rule_Type>
	 */
	private static $types = array();

	/**
	 * @return void
	 */
	public static function init() {
		self::register( new WP_EXT_RULE_Pricing_Rule_Type_Always() );
		self::register( new WP_EXT_RULE_Pricing_Rule_Type_All_Products() );
		self::register( new WP_EXT_RULE_Pricing_Rule_Type_Product_Select() );

		/**
		 * Register custom rule types (Pro / add-ons).
		 *
		 * @param WP_EXT_RULE_Pricing_Rule_Type_Registry $registry Registry instance.
		 */
		do_action( 'wp_ext_rule_pricing_register_rule_types', self::instance() );
	}

	/**
	 * @return self
	 */
	public static function instance() {
		static $inst = null;
		if ( null === $inst ) {
			$inst = new self();
		}

		return $inst;
	}

	/**
	 * @param WP_EXT_RULE_Pricing_Abstract_Rule_Type $type Rule type instance.
	 * @return void
	 */
	public static function register( $type ) {
		if ( ! $type instanceof WP_EXT_RULE_Pricing_Abstract_Rule_Type ) {
			return;
		}

		self::$types[ $type->get_slug() ] = $type;
	}

	/**
	 * @param string $slug Rule slug.
	 * @return WP_EXT_RULE_Pricing_Abstract_Rule_Type|null
	 */
	public static function get( $slug ) {
		return isset( self::$types[ $slug ] ) ? self::$types[ $slug ] : null;
	}

	/**
	 * Grouped options for &lt;select&gt; + definitions map for React.
	 *
	 * @return array<string, mixed>
	 */
	public static function export_for_rest() {
		$types = self::get_all();

		/**
		 * Filter rule type objects before REST export.
		 *
		 * @param array<string, WP_EXT_RULE_Pricing_Abstract_Rule_Type> $types Types.
		 */
		$types = apply_filters( 'wp_ext_rule_pricing_rule_types', $types );

		$groups      = array();
		$definitions = array();

		foreach ( $types as $slug => $type ) {
			$group_label = $type->get_group();
			if ( ! isset( $groups[ $group_label ] ) ) {
				$groups[ $group_label ] = array();
			}

			$groups[ $group_label ][] = array(
				'value'  => $slug,
				'label'  => $type->get_label(),
				'locked' => $type->is_locked(),
				'order'  => $type->get_order(),
			);

			$definitions[ $slug ] = array(
				'operators' => $type->get_operators(),
				'fields'    => $type->get_fields(),
				'summary'   => $type->get_summary(),
				'locked'    => $type->is_locked(),
			);
		}

		foreach ( $groups as $label => $options ) {
			usort(
				$options,
				static function ( $a, $b ) {
					return (int) $a['order'] - (int) $b['order'];
				}
			);
			$groups[ $label ] = $options;
		}

		$group_list = array();
		foreach ( $groups as $label => $options ) {
			$group_list[] = array(
				'label'   => $label,
				'options' => $options,
			);
		}

		return array(
			'groups'      => $group_list,
			'definitions' => $definitions,
		);
	}

	/**
	 * @return array<string, WP_EXT_RULE_Pricing_Abstract_Rule_Type>
	 */
	public static function get_all() {
		if ( empty( self::$types ) ) {
			self::init();
		}

		return self::$types;
	}
}

/**
 * @param WP_EXT_RULE_Pricing_Abstract_Rule_Type $type Rule type.
 * @return void
 */
function wp_ext_rule_pricing_register_rule_type( $type ) {
	WP_EXT_RULE_Pricing_Rule_Type_Registry::register( $type );
}
