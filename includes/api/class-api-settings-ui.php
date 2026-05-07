<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST: schema-driven settings UI (tabs + values).
 *
 * GET  /wp-ext-rule-pricing/v1/settings-ui
 * POST /wp-ext-rule-pricing/v1/settings-ui  body: { "tab": "setting2", "values": { ... } }
 *
 * @package WP_EXT_RULE_PRICING
 */
if ( ! class_exists( 'WP_EXT_RULE_PRICING_Api_Settings_Ui' ) ) {

	class WP_EXT_RULE_PRICING_Api_Settings_Ui extends WP_EXT_RULE_PRICING_Api {

		public function run() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		public function register_routes() {
			$namespace = $this->namespace . $this->version;

			register_rest_route(
				$namespace,
				'/settings-ui',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_schema_and_values' ),
						'permission_callback' => array( $this, 'permissions' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'save_tab' ),
						'permission_callback' => array( $this, 'permissions' ),
					),
				)
			);
		}

		/**
		 * @return bool|WP_Error
		 */
		public function permissions() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return new WP_Error(
					'rest_forbidden',
					__( 'Sorry, you are not allowed to manage these settings.', 'wp-ext-rule-pricing' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}

			return true;
		}

		/**
		 * One field row for REST schema output.
		 *
		 * @param array<string, mixed> $field Field definition.
		 * @return array<string, mixed>|null
		 */
		protected function format_field_for_ui_response( $field ) {
			if ( ! is_array( $field ) ) {
				return null;
			}
			$f = array(
				'id'               => sanitize_key( $field['id'] ?? '' ),
				'type'             => sanitize_key( $field['type'] ?? 'text' ),
				'label'            => isset( $field['label'] ) ? wp_kses_post( $field['label'] ) : '',
				'description'      => isset( $field['description'] ) ? wp_kses_post( $field['description'] ) : '',
				'placeholder'      => isset( $field['placeholder'] ) ? sanitize_text_field( (string) $field['placeholder'] ) : '',
				'default'          => $field['default'] ?? null,
				'options'          => isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array(),
				'rows'             => isset( $field['rows'] ) ? absint( $field['rows'] ) : 4,
				'min'              => isset( $field['min'] ) ? $field['min'] : null,
				'max'              => isset( $field['max'] ) ? $field['max'] : null,
				'step'             => isset( $field['step'] ) ? $field['step'] : null,
				'content'          => isset( $field['content'] ) ? wp_kses_post( $field['content'] ) : '',
				'allow_free_text'  => ! empty( $field['allow_free_text'] ),
				'buttons'          => array(),
			);
			if ( '' === $f['id'] ) {
				return null;
			}
			if ( 'link_buttons' === $f['type'] && ! empty( $field['buttons'] ) && is_array( $field['buttons'] ) ) {
				foreach ( $field['buttons'] as $btn ) {
					if ( ! is_array( $btn ) ) {
						continue;
					}
					$f['buttons'][] = array(
						'label'            => isset( $btn['label'] ) ? sanitize_text_field( (string) $btn['label'] ) : '',
						'url'              => isset( $btn['url'] ) ? esc_url_raw( (string) $btn['url'] ) : '',
						'opens_in_new_tab' => ! empty( $btn['opens_in_new_tab'] ),
					);
				}
			}

			return $f;
		}

		/**
		 * One section row for REST schema output.
		 *
		 * @param array<string, mixed> $sec Section definition.
		 * @return array<string, mixed>
		 */
		protected function format_section_for_ui_response( $sec ) {
			$sec       = is_array( $sec ) ? $sec : array();
			$sec_row = array(
				'id'          => sanitize_key( $sec['id'] ?? 'section' ),
				'title'       => isset( $sec['title'] ) ? wp_kses_post( $sec['title'] ) : '',
				'description' => isset( $sec['description'] ) ? wp_kses_post( $sec['description'] ) : '',
				'fields'      => array(),
			);
			foreach ( $sec['fields'] ?? array() as $field ) {
				$formatted = $this->format_field_for_ui_response( $field );
				if ( null !== $formatted ) {
					$sec_row['fields'][] = $formatted;
				}
			}

			return $sec_row;
		}

		/**
		 * Strip PHP-only keys from schema sent to JS.
		 *
		 * @param array<int, array<string, mixed>> $tabs Tabs.
		 * @return array<int, array<string, mixed>>
		 */
		protected function tabs_for_response( $tabs ) {
			$out = array();
			foreach ( $tabs as $tab ) {
				$tab = is_array( $tab ) ? $tab : array();
				$row = array(
					'slug'        => sanitize_key( $tab['slug'] ?? '' ),
					'label'       => isset( $tab['label'] ) ? wp_kses_post( $tab['label'] ) : '',
					'sections'    => array(),
					'subsections' => array(),
				);
				foreach ( $tab['sections'] ?? array() as $sec ) {
					$row['sections'][] = $this->format_section_for_ui_response( $sec );
				}
				foreach ( $tab['subsections'] ?? array() as $sub ) {
					if ( ! is_array( $sub ) ) {
						continue;
					}
					$sub_id = sanitize_key( $sub['id'] ?? '' );
					if ( '' === $sub_id ) {
						continue;
					}
					$sub_row = array(
						'id'       => $sub_id,
						'label'    => isset( $sub['label'] ) ? wp_kses_post( $sub['label'] ) : '',
						'sections' => array(),
					);
					foreach ( $sub['sections'] ?? array() as $sec ) {
						$sub_row['sections'][] = $this->format_section_for_ui_response( $sec );
					}
					$row['subsections'][] = $sub_row;
				}
				if ( $row['slug'] ) {
					$out[] = $row;
				}
			}

			return $out;
		}

		/**
		 * @param WP_REST_Request $request Request.
		 * @return WP_REST_Response
		 */
		public function get_schema_and_values( $request ) {
			$tabs = wp_ext_rule_pricing_get_settings_tabs_registry();

			return rest_ensure_response(
				array(
					'tabs'   => $this->tabs_for_response( $tabs ),
					'values' => wp_ext_rule_pricing_get_tabs_settings_values(),
				)
			);
		}

		/**
		 * @param WP_REST_Request $request Request.
		 * @return WP_REST_Response|WP_Error
		 */
		public function save_tab( $request ) {
			$params = $request->get_json_params();
			if ( empty( $params ) || ! is_array( $params ) ) {
				$params = $request->get_params();
			}

			$tab = isset( $params['tab'] ) ? sanitize_key( (string) $params['tab'] ) : '';
			$raw = isset( $params['values'] ) ? $params['values'] : null;

			if ( '' === $tab ) {
				return new WP_Error(
					'rest_missing_tab',
					__( 'Missing tab parameter.', 'wp-ext-rule-pricing' ),
					array( 'status' => 400 )
				);
			}

			if ( ! is_array( $raw ) ) {
				return new WP_Error(
					'rest_invalid_param',
					__( 'Values must be an object.', 'wp-ext-rule-pricing' ),
					array( 'status' => 400 )
				);
			}

			$sanitized = wp_ext_rule_pricing_sanitize_tab_patch( $tab, $raw );
			if ( is_wp_error( $sanitized ) ) {
				return $sanitized;
			}

			$all = wp_ext_rule_pricing_get_tabs_settings_values();
			$tab = sanitize_key( $tab );
			$all[ $tab ] = $sanitized;

			wp_ext_rule_pricing_update_tabs_settings_values( $all );

			return rest_ensure_response(
				array(
					'success' => true,
					'message' => __( 'Settings saved.', 'wp-ext-rule-pricing' ),
					'values'  => wp_ext_rule_pricing_get_tabs_settings_values(),
				)
			);
		}

		public static function get_instance() {
			static $instance = null;
			if ( null === $instance ) {
				$instance = new self();
			}

			return $instance;
		}
	}
}

/**
 * @return WP_EXT_RULE_PRICING_Api_Settings_Ui
 */
function wp_ext_rule_pricing_api_settings_ui() { // phpcs:ignore
	return WP_EXT_RULE_PRICING_Api_Settings_Ui::get_instance();
}

wp_ext_rule_pricing_api_settings_ui()->run();
