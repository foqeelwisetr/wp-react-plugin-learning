<?php
/**
 * Campaign tab field schemas (PHP-driven, dependent fields via depends_on).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field schemas per tab.
 */
class WP_EXT_RULE_Pricing_Campaign_Fields_Registry {

	/**
	 * @param string $tab_id Tab id.
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_sections( $tab_id ) {
		$map      = self::get_all_sections();
		$sections = isset( $map[ $tab_id ] ) ? $map[ $tab_id ] : array();

		return apply_filters( 'wp_ext_rule_pricing_campaign_tab_sections', $sections, $tab_id );
	}

	/**
	 * @return array<string, array<int, array<string, mixed>>>
	 */
	public static function get_all_sections() {
		$sections = array(
			'schedule'  => wp_ext_rule_pricing_campaign_schedule_sections(),
			'discount'  => wp_ext_rule_pricing_campaign_discount_sections(),
			'inventory' => wp_ext_rule_pricing_campaign_inventory_sections(),
			'elements'  => wp_ext_rule_pricing_campaign_elements_sections(),
			'coupons'   => wp_ext_rule_pricing_campaign_coupons_sections(),
			'rules'     => array(),
		);

		return apply_filters( 'wp_ext_rule_pricing_campaign_fields_registry', $sections );
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function default_settings() {
		$defaults = array();

		foreach ( self::get_all_sections() as $tab_id => $sections ) {
			$defaults[ $tab_id ] = array();
			foreach ( $sections as $section ) {
				foreach ( $section['fields'] ?? array() as $field ) {
					if ( empty( $field['id'] ) ) {
						continue;
					}
					if ( isset( $field['store'] ) && false === $field['store'] ) {
						continue;
					}
					$defaults[ $tab_id ][ $field['id'] ] = isset( $field['default'] ) ? $field['default'] : '';
				}
			}
		}

		return apply_filters( 'wp_ext_rule_pricing_campaign_default_settings', $defaults );
	}

	/**
	 * Merge saved settings with schema defaults (missing keys only).
	 *
	 * @param array<string, mixed>|null $settings Stored settings.
	 * @return array<string, mixed>
	 */
	public static function merge_settings_with_defaults( $settings ) {
		$defaults = self::default_settings();
		$merged   = is_array( $settings ) ? $settings : array();

		foreach ( $defaults as $tab_id => $tab_defaults ) {
			if ( ! is_array( $tab_defaults ) ) {
				continue;
			}
			if ( ! isset( $merged[ $tab_id ] ) || ! is_array( $merged[ $tab_id ] ) ) {
				$merged[ $tab_id ] = $tab_defaults;
				continue;
			}
			foreach ( $tab_defaults as $field_id => $default_value ) {
				if ( ! array_key_exists( $field_id, $merged[ $tab_id ] ) ) {
					$merged[ $tab_id ][ $field_id ] = $default_value;
					continue;
				}
				$current = $merged[ $tab_id ][ $field_id ];
				if ( ( '' === $current || null === $current ) && '' !== $default_value && null !== $default_value ) {
					$merged[ $tab_id ][ $field_id ] = $default_value;
				}
			}
		}

		return $merged;
	}
}
