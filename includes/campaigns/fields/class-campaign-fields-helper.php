<?php
/**
 * Helpers for building campaign field schemas (pro flags, depends_on, help rows).
 *
 * Usage: WP_EXT_RULE_Pricing_Campaign_Fields_Helper::pro( array( 'id' => 'x', ... ) );
 *
 * @package WP_EXT_RULE_Pricing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field schema helpers.
 */
class WP_EXT_RULE_Pricing_Campaign_Fields_Helper {

	/**
	 * Whether Pro features are unlocked in admin UI.
	 *
	 * @return bool
	 */
	public static function is_pro() {
		return (bool) apply_filters( 'wp_ext_rule_pricing_is_pro', false );
	}

	/**
	 * Mark field as Pro-only (lite shows lock unless is_pro).
	 *
	 * @param array<string, mixed> $field Field.
	 * @return array<string, mixed>
	 */
	public static function pro( $field ) {
		$field['pro'] = true;
		return $field;
	}

	/**
	 * Show / Hide button group.
	 *
	 * @param string               $id      Field id.
	 * @param string               $label   Label.
	 * @param string               $default show|hide.
	 * @param array<string, mixed> $extra   Extra keys.
	 * @return array<string, mixed>
	 */
	public static function show_hide( $id, $label, $default = 'show', $extra = array() ) {
		return array_merge(
			array(
				'id'      => $id,
				'type'    => 'show_hide',
				'label'   => $label,
				'default' => $default,
			),
			$extra
		);
	}

	/**
	 * Yes / No radio.
	 *
	 * @param string               $id      Field id.
	 * @param string               $label   Label.
	 * @param string               $default yes|no.
	 * @param array<string, mixed> $extra   Extra.
	 * @return array<string, mixed>
	 */
	public static function yes_no( $id, $label, $default = 'no', $extra = array() ) {
		return array_merge(
			array(
				'id'      => $id,
				'type'    => 'yes_no',
				'label'   => $label,
				'default' => $default,
			),
			$extra
		);
	}

	/**
	 * Help / docs row (no stored value).
	 *
	 * @param string $text Help text.
	 * @param string $url  Optional URL.
	 * @return array<string, mixed>
	 */
	public static function help( $text, $url = '' ) {
		return array(
			'id'          => 'help_' . wp_generate_password( 6, false, false ),
			'type'        => 'help',
			'label'       => '',
			'description' => $text,
			'help_url'    => $url,
			'store'       => false,
		);
	}

	/**
	 * Depends on field = show.
	 *
	 * @param string $field_id Visibility field id.
	 * @return array<string, mixed>
	 */
	public static function dep_show( $field_id ) {
		return array(
			'field' => $field_id,
			'value' => 'show',
		);
	}

	/**
	 * Depends on yes_no = yes.
	 *
	 * @param string $field_id Field id.
	 * @return array<string, mixed>
	 */
	public static function dep_yes( $field_id ) {
		return array(
			'field' => $field_id,
			'value' => 'yes',
		);
	}

	/**
	 * Depends on toggle/checkbox true.
	 *
	 * @param string $field_id Field id.
	 * @return array<string, mixed>
	 */
	public static function dep_enabled( $field_id ) {
		return array(
			'field' => $field_id,
			'value' => true,
		);
	}
}
