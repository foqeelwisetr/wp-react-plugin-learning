/**
 * Whether Pro features are unlocked (PHP filter wp_ext_rule_pricing_is_pro).
 */
export function isPro() {
	return (
		typeof wpextrulepricingLocalize !== 'undefined' &&
		!! wpextrulepricingLocalize.is_pro
	);
}
