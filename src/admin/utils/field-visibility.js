/**
 * Dependent field visibility (PHP depends_on → show/hide fields).
 */

function matchValue( actual, expected ) {
	if ( Array.isArray( expected ) ) {
		return expected.includes( actual );
	}
	if ( typeof expected === 'boolean' ) {
		return !! actual === expected;
	}
	return String( actual ) === String( expected );
}

function matchRule( rule, values ) {
	if ( ! rule?.field ) {
		return true;
	}
	const actual = values?.[ rule.field ];
	const op = rule.operator || '==';

	if ( op === '!=' ) {
		return ! matchValue( actual, rule.value );
	}
	if ( op === 'in' && Array.isArray( rule.value ) ) {
		return rule.value.includes( actual );
	}

	return matchValue( actual, rule.value );
}

/**
 * @param {object} field Field schema.
 * @param {object} values Flat values for current tab.
 * @returns {boolean}
 */
export function isFieldVisible( field, values ) {
	if ( ! field?.depends_on ) {
		return true;
	}

	const rules = Array.isArray( field.depends_on )
		? field.depends_on
		: [ field.depends_on ];

	return rules.every( ( rule ) => matchRule( rule, values ) );
}
