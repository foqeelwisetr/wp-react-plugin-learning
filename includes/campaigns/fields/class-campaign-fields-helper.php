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

	/**
	 * Grid column width for multi-field rows (1 = full, 2 = half, 3 = third, etc.).
	 *
	 * @param int|string             $col   Column count (1–6) or string "col-2".
	 * @param array<string, mixed>   $field Field.
	 * @return array<string, mixed>
	 */
	public static function col( $col, $field ) {
		$field['col'] = $col;
		return $field;
	}

	/**
	 * Group fields on one table row (label from first field in group).
	 *
	 * @param string               $row_id Row group id.
	 * @param array<string, mixed> $field  Field.
	 * @return array<string, mixed>
	 */
	public static function row( $row_id, $field ) {
		$field['row'] = sanitize_key( (string) $row_id );
		return $field;
	}

	/**
	 * Extra CSS class(es) on the field column wrapper.
	 *
	 * @param string               $class CSS class string.
	 * @param array<string, mixed> $field Field.
	 * @return array<string, mixed>
	 */
	public static function css_class( $class, $field ) {
		$class = trim( (string) $class );
		if ( '' === $class ) {
			return $field;
		}
		if ( ! empty( $field['class'] ) ) {
			$field['class'] .= ' ' . $class;
		} else {
			$field['class'] = $class;
		}
		return $field;
	}

	/**
	 * CSS class on the row grid wrapper (all columns in the row).
	 *
	 * @param string               $class CSS class string.
	 * @param array<string, mixed> $field Field (usually first in row).
	 * @return array<string, mixed>
	 */
	/**
	 * Date picker field (HTML5 date — YYYY-MM-DD).
	 *
	 * @param string               $id    Field id.
	 * @param string               $label Label.
	 * @param array<string, mixed> $extra Extra keys (min, max, depends_on, …).
	 * @return array<string, mixed>
	 */
	public static function date( $id, $label, $extra = array() ) {
		return array_merge(
			array(
				'id'    => $id,
				'type'  => 'date',
				'label' => $label,
			),
			$extra
		);
	}

	/**
	 * Time picker field (HTML5 time — HH:MM).
	 *
	 * @param string               $id    Field id.
	 * @param string               $label Label.
	 * @param array<string, mixed> $extra Extra keys (min, max, step, …).
	 * @return array<string, mixed>
	 */
	public static function time( $id, $label, $extra = array() ) {
		return array_merge(
			array(
				'id'    => $id,
				'type'  => 'time',
				'label' => $label,
			),
			$extra
		);
	}

	public static function row_class( $class, $field ) {
		$class = trim( (string) $class );
		if ( '' === $class ) {
			return $field;
		}
		if ( ! empty( $field['row_class'] ) ) {
			$field['row_class'] .= ' ' . $class;
		} else {
			$field['row_class'] = $class;
		}
		return $field;
	}
}
