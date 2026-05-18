<?php
/**
 * Abstract rule type (Finale WCCT_Rule_Base pattern for React admin).
 *
 * @package WP_EXT_RULE_PRICING
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base rule type.
 */
abstract class WP_EXT_RULE_Pricing_Abstract_Rule_Type {

	/**
	 * Unique slug, e.g. general_always.
	 *
	 * @return string
	 */
	abstract public function get_slug();

	/**
	 * Human label.
	 *
	 * @return string
	 */
	abstract public function get_label();

	/**
	 * Optgroup label in rule dropdown.
	 *
	 * @return string
	 */
	abstract public function get_group();

	/**
	 * @return array<int, array<string, string>>
	 */
	public function get_operators() {
		return array(
			array(
				'value' => '==',
				'label' => __( 'is equal to', 'wp-ext-rule-pricing' ),
			),
			array(
				'value' => '!=',
				'label' => __( 'is not equal to', 'wp-ext-rule-pricing' ),
			),
		);
	}

	/**
	 * Extra fields for React (select, text, product-search, …).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_fields() {
		return array();
	}

	/**
	 * Short help shown beside the rule row.
	 *
	 * @param array<string, mixed> $rule Saved rule row.
	 * @return string
	 */
	public function get_summary( $rule = array() ) {
		return '';
	}

	/**
	 * @return bool
	 */
	public function is_locked() {
		return false;
	}

	/**
	 * @return int Lower sorts first in dropdown.
	 */
	public function get_order() {
		return 50;
	}

	/**
	 * Export for REST / React.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array() {
		return array(
			'slug'       => $this->get_slug(),
			'label'      => $this->get_label(),
			'group'      => $this->get_group(),
			'operators'  => $this->get_operators(),
			'fields'     => $this->get_fields(),
			'locked'     => $this->is_locked(),
			'order'      => $this->get_order(),
			'summary'    => $this->get_summary(),
		);
	}
}
