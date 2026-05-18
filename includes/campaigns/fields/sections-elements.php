<?php
/**
 * Elements tab sections (timer + counter bar).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array<string, mixed>>
 */
function wp_ext_rule_pricing_campaign_elements_sections() {
	$h   = 'WP_EXT_RULE_Pricing_Campaign_Fields_Helper';
	$dep = $h::dep_show( 'timer_visibility' );
	$dep_bar = $h::dep_show( 'bar_visibility' );

	$timer_skins = array(
		array( 'value' => 'round_fill', 'label' => __( 'Round Fill', 'wp-ext-rule-pricing' ) ),
		array( 'value' => 'round_ghost', 'label' => __( 'Round Ghost', 'wp-ext-rule-pricing' ) ),
		array( 'value' => 'square_fill', 'label' => __( 'Square Fill', 'wp-ext-rule-pricing' ) ),
		array( 'value' => 'square_ghost', 'label' => __( 'Square Ghost', 'wp-ext-rule-pricing' ) ),
		array( 'value' => 'highlight', 'label' => __( 'Highlight', 'wp-ext-rule-pricing' ) ),
		array( 'value' => 'default', 'label' => __( 'Default', 'wp-ext-rule-pricing' ) ),
	);

	return array(
		array(
			'id'     => 'timer',
			'title'  => __( 'Single Product Countdown Timer', 'wp-ext-rule-pricing' ),
			'fields' => array(
				$h::show_hide(
					'timer_visibility',
					__( 'Visibility', 'wp-ext-rule-pricing' ),
					'show',
					array(
						'description' => __( 'Enable this to show Countdown Timer.', 'wp-ext-rule-pricing' ),
					)
				),
				$h::help(
					__( 'Need Help with setting up Countdown Timer? Watch Video or Read Docs', 'wp-ext-rule-pricing' )
				),
				array(
					'id'          => 'timer_position',
					'type'        => 'select',
					'label'       => __( 'Position', 'wp-ext-rule-pricing' ),
					'description' => __( 'Select Position for Single Product Page.', 'wp-ext-rule-pricing' ),
					'default'     => 'below_price',
					'options'     => array(
						array( 'value' => 'below_price', 'label' => __( 'Below the Price', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'above_price', 'label' => __( 'Above the Price', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'below_title', 'label' => __( 'Below the Title', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on'  => $dep,
				),
				array(
					'id'          => 'timer_skin',
					'type'        => 'radio',
					'label'       => __( 'Countdown Timer Skins', 'wp-ext-rule-pricing' ),
					'default'     => 'round_ghost',
					'options'     => $timer_skins,
					'depends_on'  => $dep,
				),
				array(
					'id'         => 'timer_bg_color',
					'type'       => 'color',
					'label'      => __( 'Background/Border', 'wp-ext-rule-pricing' ),
					'default'    => '#ffffff',
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_label_color',
					'type'       => 'color',
					'label'      => __( 'Label', 'wp-ext-rule-pricing' ),
					'default'    => '#333333',
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_font_size',
					'type'       => 'number',
					'label'      => __( 'Timer Font Size (px)', 'wp-ext-rule-pricing' ),
					'default'    => 26,
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_label_font_size',
					'type'       => 'number',
					'label'      => __( 'Label Font Size (px)', 'wp-ext-rule-pricing' ),
					'default'    => 13,
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_label_days',
					'type'       => 'text',
					'label'      => __( 'Timer Labels — days', 'wp-ext-rule-pricing' ),
					'default'    => 'days',
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_label_hrs',
					'type'       => 'text',
					'label'      => __( 'hrs', 'wp-ext-rule-pricing' ),
					'default'    => 'hours',
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_label_mins',
					'type'       => 'text',
					'label'      => __( 'mins', 'wp-ext-rule-pricing' ),
					'default'    => 'minutes',
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_label_secs',
					'type'       => 'text',
					'label'      => __( 'secs', 'wp-ext-rule-pricing' ),
					'default'    => 'seconds',
					'depends_on' => $dep,
				),
				array(
					'id'          => 'timer_display_text',
					'type'        => 'textarea',
					'label'       => __( 'Display', 'wp-ext-rule-pricing' ),
					'description' => '{{countdown_timer}}: Outputs the countdown timer. {{campaign_start_date}}, {{campaign_end_date}}',
					'default'     => 'Prices go up when the timer hits zero.',
					'rows'        => 3,
					'depends_on'  => $dep,
				),
				array(
					'id'         => 'timer_border_style',
					'type'       => 'select',
					'label'      => __( 'Border Style', 'wp-ext-rule-pricing' ),
					'default'    => 'none',
					'options'    => array(
						array( 'value' => 'none', 'label' => __( 'None', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'solid', 'label' => __( 'Solid', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_border_width',
					'type'       => 'number',
					'label'      => __( 'Border Width (px)', 'wp-ext-rule-pricing' ),
					'default'    => 1,
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_border_color',
					'type'       => 'color',
					'label'      => __( 'Border Color', 'wp-ext-rule-pricing' ),
					'default'    => '#cccccc',
					'depends_on' => $dep,
				),
				array(
					'id'         => 'timer_mobile_scale',
					'type'       => 'number',
					'label'      => __( 'Reduce Countdown Timer Size on Mobile (%)', 'wp-ext-rule-pricing' ),
					'default'    => 90,
					'min'        => 50,
					'max'        => 100,
					'depends_on' => $dep,
				),
			),
		),
		array(
			'id'     => 'counter_bar',
			'title'  => __( 'Single Product Counter Bar', 'wp-ext-rule-pricing' ),
			'fields' => array(
				$h::show_hide(
					'bar_visibility',
					__( 'Visibility', 'wp-ext-rule-pricing' ),
					'hide',
					array(
						'description' => __( 'Enable this to show Counter Bar. Inventory Goal should be enabled to display the Counter Bar.', 'wp-ext-rule-pricing' ),
					)
				),
				$h::help(
					__( 'Need Help with setting up Counter Bar? Watch Video or Read Docs', 'wp-ext-rule-pricing' )
				),
				array(
					'id'          => 'bar_position',
					'type'        => 'select',
					'label'       => __( 'Position', 'wp-ext-rule-pricing' ),
					'description' => __( 'Select Position for Single Product Page.', 'wp-ext-rule-pricing' ),
					'default'     => 'below_price',
					'options'     => array(
						array( 'value' => 'below_price', 'label' => __( 'Below the Price', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'above_price', 'label' => __( 'Above the Price', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on'  => $dep_bar,
				),
				array(
					'id'         => 'bar_edges',
					'type'       => 'radio',
					'label'      => __( 'Edges', 'wp-ext-rule-pricing' ),
					'default'    => 'rounded',
					'options'    => array(
						array( 'value' => 'rounded', 'label' => __( 'Rounded', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'smooth', 'label' => __( 'Smooth', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'sharp', 'label' => __( 'Sharp', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on' => $dep_bar,
				),
				array(
					'id'         => 'bar_direction',
					'type'       => 'radio',
					'label'      => __( 'Direction', 'wp-ext-rule-pricing' ),
					'description' => __( 'Right to Left indicates decrease in stocks.', 'wp-ext-rule-pricing' ),
					'default'    => 'ltr',
					'options'    => array(
						array( 'value' => 'ltr', 'label' => __( 'Left to Right', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'rtl', 'label' => __( 'Right to Left', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on' => $dep_bar,
				),
				array(
					'id'         => 'bar_bg_color',
					'type'       => 'color',
					'label'      => __( 'Background/Border', 'wp-ext-rule-pricing' ),
					'default'    => '#eeeeee',
					'depends_on' => $dep_bar,
				),
				array(
					'id'         => 'bar_active_color',
					'type'       => 'color',
					'label'      => __( 'Active', 'wp-ext-rule-pricing' ),
					'default'    => '#2271b1',
					'depends_on' => $dep_bar,
				),
				array(
					'id'         => 'bar_height',
					'type'       => 'number',
					'label'      => __( 'Height (px)', 'wp-ext-rule-pricing' ),
					'default'    => 12,
					'depends_on' => $dep_bar,
				),
				array(
					'id'          => 'bar_display_text',
					'type'        => 'textarea',
					'label'       => __( 'Display', 'wp-ext-rule-pricing' ),
					'description' => '{{counter_bar}}, {{remaining_units}}, {{campaign_start_date}}, {{campaign_end_date}}',
					'default'     => 'Hurry up! Just <span>{{remaining_units}}</span> items left in stock',
					'rows'        => 3,
					'depends_on'  => $dep_bar,
				),
				array(
					'id'         => 'bar_border_style',
					'type'       => 'select',
					'label'      => __( 'Border Style', 'wp-ext-rule-pricing' ),
					'default'    => 'none',
					'options'    => array(
						array( 'value' => 'none', 'label' => __( 'None', 'wp-ext-rule-pricing' ) ),
						array( 'value' => 'solid', 'label' => __( 'Solid', 'wp-ext-rule-pricing' ) ),
					),
					'depends_on' => $dep_bar,
				),
				array(
					'id'         => 'bar_border_width',
					'type'       => 'number',
					'label'      => __( 'Border Width (px)', 'wp-ext-rule-pricing' ),
					'default'    => 0,
					'depends_on' => $dep_bar,
				),
				array(
					'id'         => 'bar_border_color',
					'type'       => 'color',
					'label'      => __( 'Border Color', 'wp-ext-rule-pricing' ),
					'default'    => '#cccccc',
					'depends_on' => $dep_bar,
				),
			),
		),
		array(
			'id'     => 'product_cta',
			'title'  => __( 'Product page extras', 'wp-ext-rule-pricing' ),
			'fields' => array(
				array(
					'id'          => 'product_description',
					'type'        => 'textarea',
					'label'       => __( 'Product description', 'wp-ext-rule-pricing' ),
					'description' => __( 'Optional text shown near the campaign elements.', 'wp-ext-rule-pricing' ),
					'default'     => '',
					'rows'        => 4,
				),
				array(
					'id'          => 'product_button_label',
					'type'        => 'text',
					'label'       => __( 'Button label', 'wp-ext-rule-pricing' ),
					'default'     => __( 'Shop now', 'wp-ext-rule-pricing' ),
				),
				array(
					'id'          => 'product_button_link',
					'type'        => 'url',
					'label'       => __( 'Button link', 'wp-ext-rule-pricing' ),
					'placeholder' => 'https://',
					'default'     => '',
				),
			),
		),
	);
}
