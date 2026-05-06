<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PHP-driven settings tabs + field schema (sidebar + sections).
 *
 * Add or override tabs with filter: {@see 'wp_ext_rule_pricing_settings_tabs'}.
 *
 * @package WP_EXT_RULE_PRICING
 */

/** Option key for tabbed form values (flat per tab). */
if ( ! defined( 'WP_EXT_RULE_PRICING_TABS_OPTION_NAME' ) ) {
	define( 'WP_EXT_RULE_PRICING_TABS_OPTION_NAME', 'wp_ext_rule_pricing_tabs_settings' );
}

/**
 * Registered tabs (slug => config built from list).
 *
 * @return array<int, array{slug:string,label:string,sections:array}>
 */
function wp_ext_rule_pricing_get_settings_tabs_registry() {
	$tabs = array(
		array(
			'slug'     => 'setting1',
			'label'    => __( 'Settings 1', 'wp-ext-rule-pricing' ),
			'sections' => array(
				array(
					'id'          => 'business',
					'title'       => __( 'Business Details', 'wp-ext-rule-pricing' ),
					'description' => __( 'Example section description.', 'wp-ext-rule-pricing' ),
					'fields'      => array(
						array(
							'id'          => 'business_name',
							'type'        => 'text',
							'label'       => __( 'Business Name', 'wp-ext-rule-pricing' ),
							'placeholder' => __( 'Company Inc.', 'wp-ext-rule-pricing' ),
							'default'     => '',
						),
						array(
							'id'          => 'business_address',
							'type'        => 'textarea',
							'label'       => __( 'Business Address', 'wp-ext-rule-pricing' ),
							'description' => __( 'Shown where regulations require a physical address.', 'wp-ext-rule-pricing' ),
							'default'     => '',
							'rows'        => 3,
						),
						array(
							'id'      => 'business_email',
							'type'    => 'email',
							'label'   => __( 'Contact Email', 'wp-ext-rule-pricing' ),
							'default' => '',
						),
						array(
							'id'          => 'business_url',
							'type'        => 'url',
							'label'       => __( 'Website', 'wp-ext-rule-pricing' ),
							'placeholder' => 'https://',
							'default'     => '',
						),
					),
				),
				array(
					'id'          => 'brand',
					'title'       => __( 'Brand Styles', 'wp-ext-rule-pricing' ),
					'description' => __( 'Logo and accent color.', 'wp-ext-rule-pricing' ),
					'fields'      => array(
						array(
							'id'          => 'brand_logo_id',
							'type'        => 'image',
							'label'       => __( 'Brand Logo', 'wp-ext-rule-pricing' ),
							'description' => __( 'Pick an image from the media library.', 'wp-ext-rule-pricing' ),
							'default'     => 0,
						),
						array(
							'id'          => 'brand_color',
							'type'        => 'color',
							'label'       => __( 'Brand Color', 'wp-ext-rule-pricing' ),
							'default'     => '#2271b1',
						),
					),
				),
			),
		),
		array(
			'slug'     => 'setting4',
			'label'    => __( 'Setting2222', 'wp-ext-rule-pricing' ),
			'sections' => array(
				array(
					'id'          => 'field_types_demo',
					'title'       => __( 'All Field Types', 'wp-ext-rule-pricing' ),
					'description' => __( 'Demo controls driven entirely by PHP schema.', 'wp-ext-rule-pricing' ),
					'fields'      => array(
						array(
							'id'          => 'demo_text',
							'type'        => 'text',
							'label'       => __( 'Text', 'wp-ext-rule-pricing' ),
							'default'     => '',
						),
						array(
							'id'      => 'demo_textarea',
							'type'    => 'textarea',
							'label'   => __( 'Textarea', 'wp-ext-rule-pricing' ),
							'default' => '',
							'rows'    => 4,
						),
						array(
							'id'      => 'demo_number',
							'type'    => 'number',
							'label'   => __( 'Number', 'wp-ext-rule-pricing' ),
							'default' => 0,
							'min'     => 0,
							'max'     => 100,
							'step'    => 1,
						),
						array(
							'id'          => 'demo_password',
							'type'        => 'password',
							'label'       => __( 'Password (masked)', 'wp-ext-rule-pricing' ),
							'description' => __( 'Stored as plain option value; restrict admin access.', 'wp-ext-rule-pricing' ),
							'default'     => '',
						),
						array(
							'id'      => 'demo_toggle',
							'type'    => 'toggle',
							'label'   => __( 'Toggle', 'wp-ext-rule-pricing' ),
							'default' => false,
						),
						array(
							'id'      => 'demo_select',
							'type'    => 'select',
							'label'   => __( 'Select', 'wp-ext-rule-pricing' ),
							'default' => 'a',
							'options' => array(
								array( 'label' => __( 'Option A', 'wp-ext-rule-pricing' ), 'value' => 'a' ),
								array( 'label' => __( 'Option B', 'wp-ext-rule-pricing' ), 'value' => 'b' ),
								array( 'label' => __( 'Option C', 'wp-ext-rule-pricing' ), 'value' => 'c' ),
							),
						),
						array(
							'id'      => 'demo_radio',
							'type'    => 'radio',
							'label'   => __( 'Radio', 'wp-ext-rule-pricing' ),
							'default' => 'one',
							'options' => array(
								array( 'label' => __( 'One', 'wp-ext-rule-pricing' ), 'value' => 'one' ),
								array( 'label' => __( 'Two', 'wp-ext-rule-pricing' ), 'value' => 'two' ),
							),
						),
						array(
							'id'      => 'demo_checkbox_group',
							'type'    => 'checkbox_group',
							'label'   => __( 'Checkbox group', 'wp-ext-rule-pricing' ),
							'default' => array(),
							'options' => array(
								array( 'label' => __( 'Alpha', 'wp-ext-rule-pricing' ), 'value' => 'alpha' ),
								array( 'label' => __( 'Beta', 'wp-ext-rule-pricing' ), 'value' => 'beta' ),
								array( 'label' => __( 'Gamma', 'wp-ext-rule-pricing' ), 'value' => 'gamma' ),
							),
						),
						array(
							'id'          => 'demo_multiselect',
							'type'        => 'multiselect',
							'label'       => __( 'Multi-select', 'wp-ext-rule-pricing' ),
							'description' => __( 'Hold Ctrl/Cmd to pick multiple.', 'wp-ext-rule-pricing' ),
							'default'     => array(),
							'options'     => array(
								array( 'label' => __( 'North', 'wp-ext-rule-pricing' ), 'value' => 'n' ),
								array( 'label' => __( 'East', 'wp-ext-rule-pricing' ), 'value' => 'e' ),
								array( 'label' => __( 'South', 'wp-ext-rule-pricing' ), 'value' => 's' ),
								array( 'label' => __( 'West', 'wp-ext-rule-pricing' ), 'value' => 'w' ),
							),
						),
						array(
							'id'              => 'demo_search_lists',
							'type'            => 'search_multiselect',
							'label'           => __( 'Add List', 'wp-ext-rule-pricing' ),
							'description'     => __( 'Searchable multi-select: filter options as you type.', 'wp-ext-rule-pricing' ),
							'placeholder'     => __( 'Search by name', 'wp-ext-rule-pricing' ),
							'default'         => array(),
							'allow_free_text' => false,
							'options'         => array(
								array( 'label' => __( 'List Alpha', 'wp-ext-rule-pricing' ), 'value' => 'alpha' ),
								array( 'label' => __( 'List Beta', 'wp-ext-rule-pricing' ), 'value' => 'beta' ),
								array( 'label' => __( 'List Gamma', 'wp-ext-rule-pricing' ), 'value' => 'gamma' ),
							),
						),
						array(
							'id'          => 'demo_color',
							'type'        => 'color',
							'label'       => __( 'Color', 'wp-ext-rule-pricing' ),
							'default'     => '#7c3aed',
						),
						array(
							'id'      => 'demo_image',
							'type'    => 'image',
							'label'   => __( 'Image', 'wp-ext-rule-pricing' ),
							'default' => 0,
						),
						array(
							'id'          => 'demo_hidden_note',
							'type'        => 'hidden',
							'default'     => 'registry-v1',
						),
						array(
							'id'          => 'demo_html',
							'type'        => 'html',
							'label'       => __( 'Read-only HTML', 'wp-ext-rule-pricing' ),
							'content'     => '<p class="description">' . esc_html__( 'Informational block from PHP schema.', 'wp-ext-rule-pricing' ) . '</p>',
						),
					),
				),
			),
		),
		array(
			'slug'     => 'advanced',
			'label'    => __( 'Advanced', 'wp-ext-rule-pricing' ),
			'sections' => array(
				array(
					'id'     => 'advanced_main',
					'title'  => __( 'Maintenance', 'wp-ext-rule-pricing' ),
					'fields' => array(
						array(
							'id'          => 'delete_all_on_deactivate',
							'type'        => 'toggle',
							'label'       => __( 'Remove data when uninstalling (demo)', 'wp-ext-rule-pricing' ),
							'description' => __( 'Keep disabled unless you need cleanup.', 'wp-ext-rule-pricing' ),
							'default'     => false,
						),
					),
				),
			),
		),
		array(
			'slug'     => 'setting_3',
			'label'    => __( 'Setting 3', 'wp-ext-rule-pricing' ),
			'sections' => array(
				array(
					'id'          => 'setting_3_intro',
					'title'       => __( 'Extra Tab', 'wp-ext-rule-pricing' ),
					'description' => __( 'Registered via slug `setting_3`. Add more tabs from PHP using the filter.', 'wp-ext-rule-pricing' ),
					'fields'      => array(
						array(
							'id'      => 'setting_3_note',
							'type'    => 'textarea',
							'label'   => __( 'Notes', 'wp-ext-rule-pricing' ),
							'default' => '',
						),
					),
				),
			),
		),
		array(
			'slug'     => 'setting_4',
			'label'    => __( 'Setting 4', 'wp-ext-rule-pricing' ),
			'sections' => array(
				array(
					'id'          => 'setting_4_intro',
					'title'       => __( 'Extra Tab', 'wp-ext-rule-pricing' ),
					'description' => __( 'Registered via slug `setting_4`. Add more tabs from PHP using the filter.', 'wp-ext-rule-pricing' ),
					'fields'      => array(
						array(
							'id'      => 'setting_4_note',
							'type'    => 'textarea',
							'label'   => __( 'Notes', 'wp-ext-rule-pricing' ),
							'default' => '',
						),
					),
				),
			),
		),
	);

	return apply_filters( 'wp_ext_rule_pricing_settings_tabs', $tabs );
}

/**
 * Slugs from registry (order preserved).
 *
 * @return string[]
 */
function wp_ext_rule_pricing_settings_tab_slugs() {
	$slugs = array();
	foreach ( wp_ext_rule_pricing_get_settings_tabs_registry() as $tab ) {
		if ( ! empty( $tab['slug'] ) ) {
			$slugs[] = sanitize_key( $tab['slug'] );
		}
	}

	return $slugs;
}

/**
 * Flat defaults for all fields keyed by tab slug.
 *
 * @return array<string, array<string, mixed>>
 */
function wp_ext_rule_pricing_settings_tabs_defaults() {
	$defaults = array();
	foreach ( wp_ext_rule_pricing_get_settings_tabs_registry() as $tab ) {
		$slug = isset( $tab['slug'] ) ? sanitize_key( $tab['slug'] ) : '';
		if ( '' === $slug ) {
			continue;
		}
		$defaults[ $slug ] = array();
		foreach ( $tab['sections'] as $section ) {
			if ( empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
				continue;
			}
			foreach ( $section['fields'] as $field ) {
				$fid = isset( $field['id'] ) ? sanitize_key( $field['id'] ) : '';
				if ( '' === $fid ) {
					continue;
				}
				if ( isset( $field['default'] ) ) {
					$defaults[ $slug ][ $fid ] = $field['default'];
				}
			}
		}
	}

	return $defaults;
}

/**
 * Saved tab values merged with defaults.
 *
 * @return array<string, array<string, mixed>>
 */
function wp_ext_rule_pricing_get_tabs_settings_values() {
	$saved    = get_option( WP_EXT_RULE_PRICING_TABS_OPTION_NAME, array() );
	$saved    = is_array( $saved ) ? $saved : array();
	$defaults = wp_ext_rule_pricing_settings_tabs_defaults();
	$out      = array();

	foreach ( $defaults as $slug => $fields ) {
		$tab_saved    = isset( $saved[ $slug ] ) && is_array( $saved[ $slug ] ) ? $saved[ $slug ] : array();
		$out[ $slug ] = array_merge( $fields, $tab_saved );
	}

	return $out;
}

/**
 * Field schema by tab + field id.
 *
 * @param string $tab_slug Tab slug.
 * @param string $field_id Field id.
 * @return array<string, mixed>|null
 */
function wp_ext_rule_pricing_find_field_schema( $tab_slug, $field_id ) {
	$tab_slug = sanitize_key( $tab_slug );
	$field_id = sanitize_key( $field_id );

	foreach ( wp_ext_rule_pricing_get_settings_tabs_registry() as $tab ) {
		if ( sanitize_key( $tab['slug'] ?? '' ) !== $tab_slug ) {
			continue;
		}
		foreach ( $tab['sections'] as $section ) {
			if ( empty( $section['fields'] ) ) {
				continue;
			}
			foreach ( $section['fields'] as $field ) {
				if ( sanitize_key( $field['id'] ?? '' ) === $field_id ) {
					return $field;
				}
			}
		}
	}

	return null;
}

/**
 * Allowed option values for select/radio/checkbox/multiselect.
 *
 * @param array<string, mixed> $field Field schema.
 * @return string[]
 */
function wp_ext_rule_pricing_field_allowed_values( $field ) {
	$allowed = array();
	if ( empty( $field['options'] ) || ! is_array( $field['options'] ) ) {
		return $allowed;
	}
	foreach ( $field['options'] as $opt ) {
		if ( isset( $opt['value'] ) ) {
			$allowed[] = (string) $opt['value'];
		}
	}

	return $allowed;
}

/**
 * Sanitize one field value from schema.
 *
 * @param array<string, mixed> $field Field schema.
 * @param mixed                $value Raw value.
 * @return mixed
 */
function wp_ext_rule_pricing_sanitize_settings_field_value( $field, $value ) {
	$type = isset( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';

	switch ( $type ) {
		case 'textarea':
			return sanitize_textarea_field( (string) $value );

		case 'email':
			return sanitize_email( (string) $value );

		case 'url':
			return esc_url_raw( (string) $value );

		case 'number':
			$num = is_numeric( $value ) ? (float) $value : 0;
			if ( isset( $field['min'] ) ) {
				$num = max( (float) $field['min'], $num );
			}
			if ( isset( $field['max'] ) ) {
				$num = min( (float) $field['max'], $num );
			}
			if ( isset( $field['step'] ) && (float) $field['step'] >= 1 ) {
				return (int) round( $num );
			}

			return $num;

		case 'toggle':
			return rest_sanitize_boolean( $value );

		case 'password':
			return is_string( $value ) ? trim( $value ) : '';

		case 'select':
		case 'radio':
			$value   = (string) $value;
			$allowed = wp_ext_rule_pricing_field_allowed_values( $field );

			return in_array( $value, $allowed, true ) ? $value : ( isset( $field['default'] ) ? (string) $field['default'] : '' );

		case 'checkbox_group':
		case 'multiselect':
			if ( ! is_array( $value ) ) {
				$value = array();
			}
			$allowed = wp_ext_rule_pricing_field_allowed_values( $field );
			$clean   = array();
			foreach ( $value as $v ) {
				$s = (string) $v;
				if ( in_array( $s, $allowed, true ) ) {
					$clean[] = $s;
				}
			}

			return array_values( array_unique( $clean ) );

		case 'search_multiselect':
			if ( ! is_array( $value ) ) {
				$value = array();
			}
			$allow_free = ! empty( $field['allow_free_text'] );
			$allowed    = wp_ext_rule_pricing_field_allowed_values( $field );
			$has_static = ! empty( $allowed );
			$clean      = array();
			$seen       = array();

			foreach ( $value as $item ) {
				if ( is_string( $item ) || is_numeric( $item ) ) {
					$key   = (string) $item;
					$label = $key;
				} elseif ( is_array( $item ) ) {
					$key   = isset( $item['key'] ) ? (string) $item['key'] : '';
					$label = isset( $item['label'] ) ? (string) $item['label'] : $key;
				} else {
					continue;
				}

				$key   = sanitize_text_field( $key );
				$label = sanitize_text_field( $label );
				if ( '' === $key || '' === $label ) {
					continue;
				}
				if ( strlen( $key ) > 191 || strlen( $label ) > 500 ) {
					continue;
				}

				// Static option list without free text: only whitelisted keys.
				if ( $has_static && ! $allow_free && ! in_array( $key, $allowed, true ) ) {
					continue;
				}

				$dedupe = strtolower( $key );
				if ( isset( $seen[ $dedupe ] ) ) {
					continue;
				}
				$seen[ $dedupe ] = true;

				$clean[] = array(
					'key'   => $key,
					'label' => $label,
				);
				if ( count( $clean ) >= 100 ) {
					break;
				}
			}

			return $clean;

		case 'color':
			$s = sanitize_hex_color( (string) $value );
			if ( $s ) {
				return $s;
			}
			$s = (string) $value;
			if ( preg_match( '/^#[0-9A-Fa-f]{6}$/', $s ) ) {
				return $s;
			}

			return isset( $field['default'] ) ? (string) $field['default'] : '#000000';

		case 'image':
			$id = absint( $value );
			if ( $id && 'attachment' === get_post_type( $id ) ) {
				return $id;
			}

			return 0;

		case 'hidden':
			return isset( $field['default'] ) ? $field['default'] : $value;

		case 'html':
			return null;

		case 'text':
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * Sanitize payload for one tab.
 *
 * @param string               $tab_slug Tab slug.
 * @param array<string, mixed> $patch Raw fields.
 * @return array<string, mixed>|WP_Error
 */
function wp_ext_rule_pricing_sanitize_tab_patch( $tab_slug, $patch ) {
	$tab_slug = sanitize_key( $tab_slug );
	$slugs    = wp_ext_rule_pricing_settings_tab_slugs();

	if ( ! in_array( $tab_slug, $slugs, true ) ) {
		return new WP_Error(
			'invalid_tab',
			__( 'Unknown settings tab.', 'wp-ext-rule-pricing' ),
			array( 'status' => 400 )
		);
	}

	if ( ! is_array( $patch ) ) {
		return new WP_Error(
			'invalid_payload',
			__( 'Invalid settings payload.', 'wp-ext-rule-pricing' ),
			array( 'status' => 400 )
		);
	}

	$merged  = wp_ext_rule_pricing_get_tabs_settings_values();
	$current = isset( $merged[ $tab_slug ] ) ? $merged[ $tab_slug ] : array();
	$out     = $current;

	foreach ( $patch as $key => $raw_val ) {
		$fid = sanitize_key( $key );
		if ( '' === $fid ) {
			continue;
		}
		$schema = wp_ext_rule_pricing_find_field_schema( $tab_slug, $fid );
		if ( null === $schema ) {
			continue;
		}
		$ftype = isset( $schema['type'] ) ? sanitize_key( $schema['type'] ) : 'text';
		if ( 'html' === $ftype ) {
			continue;
		}
		$out[ $fid ] = wp_ext_rule_pricing_sanitize_settings_field_value( $schema, $raw_val );
	}

	return $out;
}

/**
 * Persist merged tab values (full store).
 *
 * @param array<string, array<string, mixed>> $all_tabs All tabs.
 * @return bool
 */
function wp_ext_rule_pricing_update_tabs_settings_values( $all_tabs ) {
	return update_option( WP_EXT_RULE_PRICING_TABS_OPTION_NAME, $all_tabs, false );
}
