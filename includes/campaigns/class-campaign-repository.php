<?php
/**
 * Lite campaign storage (option-based; replace with CPT in production).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Campaign repository.
 */
class WP_EXT_RULE_Pricing_Campaign_Repository {

	const OPTION_KEY = 'wp_ext_rule_pricing_campaigns';

	/**
	 * Default rule groups (Finale wcct_rule shape).
	 *
	 * @return array<string, array<string, array<string, mixed>>>
	 */
	public static function default_rule_groups() {
		$rule_id = 'rule' . wp_generate_password( 8, false, false );

		return array(
			'group0' => array(
				$rule_id => array(
					'rule_type' => 'general_always',
					'operator'  => '==',
					'condition' => '',
				),
			),
		);
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public static function all() {
		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			return array();
		}

		$list = array_values( $stored );

		/**
		 * Filter campaigns before REST/list output.
		 *
		 * @param array<int, array<string, mixed>> $list Campaign rows.
		 */
		return apply_filters( 'wp_ext_rule_pricing_campaigns', $list );
	}

	/**
	 * @param int|string $id Campaign id.
	 * @return array<string, mixed>|null
	 */
	public static function get( $id ) {
		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			return null;
		}

		$key = (string) $id;
		if ( ! isset( $stored[ $key ] ) ) {
			return null;
		}

		return $stored[ $key ];
	}

	/**
	 * @param array<string, mixed> $payload Campaign data.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function create( $payload ) {
		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$id   = self::next_id( $stored );
		$now  = current_time( 'mysql' );
		$item = self::sanitize_campaign(
			array_merge(
				array(
					'id'         => $id,
					'title'      => __( 'Untitled campaign', 'wp-ext-rule-pricing' ),
					'status'     => 'draft',
					'priority'   => 10,
					'event'      => __( 'Always', 'wp-ext-rule-pricing' ),
					'category'   => '',
					'rules'      => self::default_rule_groups(),
					'settings'   => WP_EXT_RULE_Pricing_Campaign_Fields_Registry::default_settings(),
					'created_at' => $now,
					'updated_at' => $now,
				),
				$payload
			)
		);

		$stored[ (string) $id ] = $item;
		update_option( self::OPTION_KEY, $stored, false );

		return $item;
	}

	/**
	 * @param int|string           $id Campaign id.
	 * @param array<string, mixed> $payload Partial update.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function update( $id, $payload ) {
		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			return new WP_Error( 'campaign_not_found', __( 'Campaign not found.', 'wp-ext-rule-pricing' ), array( 'status' => 404 ) );
		}

		$key = (string) $id;
		if ( ! isset( $stored[ $key ] ) ) {
			return new WP_Error( 'campaign_not_found', __( 'Campaign not found.', 'wp-ext-rule-pricing' ), array( 'status' => 404 ) );
		}

		$item = self::sanitize_campaign(
			array_merge(
				$stored[ $key ],
				$payload,
				array(
					'id'         => (int) $stored[ $key ]['id'],
					'updated_at' => current_time( 'mysql' ),
				)
			)
		);

		$stored[ $key ] = $item;
		update_option( self::OPTION_KEY, $stored, false );

		return $item;
	}

	/**
	 * @param int|string $id Campaign id.
	 * @return bool|WP_Error
	 */
	public static function delete( $id ) {
		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			return new WP_Error( 'campaign_not_found', __( 'Campaign not found.', 'wp-ext-rule-pricing' ), array( 'status' => 404 ) );
		}

		$key = (string) $id;
		if ( ! isset( $stored[ $key ] ) ) {
			return new WP_Error( 'campaign_not_found', __( 'Campaign not found.', 'wp-ext-rule-pricing' ), array( 'status' => 404 ) );
		}

		unset( $stored[ $key ] );
		update_option( self::OPTION_KEY, $stored, false );

		return true;
	}

	/**
	 * @param array<string, array<string, mixed>> $stored Stored campaigns.
	 * @return int
	 */
	private static function next_id( $stored ) {
		$max = 0;
		foreach ( $stored as $row ) {
			if ( isset( $row['id'] ) ) {
				$max = max( $max, (int) $row['id'] );
			}
		}

		return $max + 1;
	}

	/**
	 * @param array<string, mixed> $campaign Raw campaign.
	 * @return array<string, mixed>
	 */
	private static function sanitize_campaign( $campaign ) {
		$allowed_status = array( 'draft', 'active', 'paused' );
		$status         = isset( $campaign['status'] ) ? sanitize_key( $campaign['status'] ) : 'draft';
		if ( ! in_array( $status, $allowed_status, true ) ) {
			$status = 'draft';
		}

		$rules = isset( $campaign['rules'] ) && is_array( $campaign['rules'] )
			? $campaign['rules']
			: self::default_rule_groups();

		$settings = isset( $campaign['settings'] ) && is_array( $campaign['settings'] )
			? $campaign['settings']
			: array();

		return array(
			'id'         => isset( $campaign['id'] ) ? absint( $campaign['id'] ) : 0,
			'title'      => isset( $campaign['title'] ) ? sanitize_text_field( $campaign['title'] ) : '',
			'status'     => $status,
			'priority'   => isset( $campaign['priority'] ) ? absint( $campaign['priority'] ) : 10,
			'event'      => isset( $campaign['event'] ) ? sanitize_text_field( $campaign['event'] ) : '',
			'category'   => isset( $campaign['category'] ) ? sanitize_text_field( $campaign['category'] ) : '',
			'rules'      => $rules,
			'settings'   => $settings,
			'created_at' => isset( $campaign['created_at'] ) ? sanitize_text_field( $campaign['created_at'] ) : current_time( 'mysql' ),
			'updated_at' => isset( $campaign['updated_at'] ) ? sanitize_text_field( $campaign['updated_at'] ) : current_time( 'mysql' ),
		);
	}
}
