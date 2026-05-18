<?php
/**
 * Schedule tab sections.
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array<string, mixed>>
 */
function wp_ext_rule_pricing_campaign_schedule_sections() {
	$h = 'WP_EXT_RULE_Pricing_Campaign_Fields_Helper';

	return array(
		array(
			'id'          => 'schedule_main',
			'title'       => __( 'Campaign Schedule', 'wp-ext-rule-pricing' ),
			'description' => __( 'Control when this campaign runs.', 'wp-ext-rule-pricing' ),
			'fields'      => array(
				array(
					'id'      => 'schedule_type',
					'type'    => 'schedule_type',
					'label'   => __( 'Type', 'wp-ext-rule-pricing' ),
					'default' => 'one_time',
					'options' => array(
						array(
							'value' => 'one_time',
							'label' => __( 'One Time', 'wp-ext-rule-pricing' ),
						),
						array(
							'value'     => 'recurring',
							'label'     => __( 'Recurring', 'wp-ext-rule-pricing' ),
							'pro'       => true,
							'upsell_id' => 'schedule_recurring',
						),
						array(
							'value'     => 'evergreen',
							'label'     => __( 'Evergreen', 'wp-ext-rule-pricing' ),
							'pro'       => true,
							'upsell_id' => 'schedule_evergreen',
						),
					),
				),
				$h::col(
					2,
					$h::row(
						'start_datetime',
						array(
							'id'      => 'start_date',
							'type'    => 'date',
							'label'   => __( 'Start Date & Time', 'wp-ext-rule-pricing' ),
							'default' => '',
							'depends_on'  => array(
								'field' => 'schedule_type',
								'value' => 'one_time',
							),
						)
					)
				),
				$h::col(
					2,
					$h::row(
						'start_datetime',
						array(
							'id'      => 'start_time',
							'type'    => 'time',
							'label'   => '',
							'default' => '',
							'depends_on'  => array(
								'field' => 'schedule_type',
								'value' => 'one_time',
							),
						)
					)
				),
				$h::col(
					2,
					$h::row(
						'end_datetime',
						array(
							'id'      => 'end_date',
							'type'    => 'date',
							'label'   => __( 'End Date & Time', 'wp-ext-rule-pricing' ),
							'default' => '',
							'depends_on'  => array(
								'field' => 'schedule_type',
								'value' => 'one_time',
							),
						)
					)
				),
				$h::col(
					2,
					$h::row(
						'end_datetime',
						array(
							'id'      => 'end_time',
							'type'    => 'time',
							'label'   => '',
							'default' => '',
							'depends_on'  => array(
								'field' => 'schedule_type',
								'value' => 'one_time',
							),
						)
					)
				),
				$h::pro(
					array(
						'id'         => 'recurring_pattern',
						'type'       => 'select',
						'label'      => __( 'Recurring pattern', 'wp-ext-rule-pricing' ),
						'default'    => 'weekly',
						'options'    => array(
							array( 'value' => 'daily', 'label' => __( 'Daily', 'wp-ext-rule-pricing' ) ),
							array( 'value' => 'weekly', 'label' => __( 'Weekly', 'wp-ext-rule-pricing' ) ),
							array( 'value' => 'monthly', 'label' => __( 'Monthly', 'wp-ext-rule-pricing' ) ),
						),
						'depends_on' => array(
							'field' => 'schedule_type',
							'value' => 'recurring',
						),
					)
				),
				$h::pro(
					array(
						'id'          => 'evergreen_duration',
						'type'        => 'number',
						'label'       => __( 'Evergreen duration (days)', 'wp-ext-rule-pricing' ),
						'description' => __( 'Per-user deadline length (Finale Evergreen add-on).', 'wp-ext-rule-pricing' ),
						'default'     => 3,
						'depends_on'  => array(
							'field' => 'schedule_type',
							'value' => 'evergreen',
						),
					)
				),
			),
		),
	);
}
