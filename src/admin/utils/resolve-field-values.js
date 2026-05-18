/**
 * Apply PHP field schema defaults to stored tab values (Autonami-style).
 */

/**
 * @param {Array} sections Tab sections from API.
 * @return {Array}
 */
export function collectFieldsFromSections( sections ) {
	const fields = [];
	( sections || [] ).forEach( ( section ) => {
		( section.fields || [] ).forEach( ( field ) => fields.push( field ) );
	} );
	return fields;
}

/**
 * Merge missing keys from field.default into stored tab values.
 *
 * @param {Array}  sections Tab sections.
 * @param {Object} stored  Saved values for one tab.
 * @return {Object}
 */
export function resolveTabFieldValues( sections, stored = {} ) {
	const out = { ...( stored || {} ) };

	collectFieldsFromSections( sections ).forEach( ( field ) => {
		if ( ! field?.id || field.store === false ) {
			return;
		}
		if ( ! Object.prototype.hasOwnProperty.call( out, field.id ) ) {
			if ( Object.prototype.hasOwnProperty.call( field, 'default' ) ) {
				out[ field.id ] = field.default;
			}
			return;
		}
		const current = out[ field.id ];
		if (
			( current === null || current === '' ) &&
			Object.prototype.hasOwnProperty.call( field, 'default' )
		) {
			out[ field.id ] = field.default;
		}
	} );

	return out;
}

/**
 * Single field value with schema default fallback.
 *
 * @param {Object} field  Field schema.
 * @param {Object} values Tab values (already resolved).
 * @return {*}
 */
export function getResolvedFieldValue( field, values ) {
	if ( field?.id && Object.prototype.hasOwnProperty.call( values || {}, field.id ) ) {
		const current = values[ field.id ];
		if ( current !== null && current !== undefined && current !== '' ) {
			return current;
		}
	}
	if ( Object.prototype.hasOwnProperty.call( field || {}, 'default' ) ) {
		return field.default;
	}
	return '';
}
