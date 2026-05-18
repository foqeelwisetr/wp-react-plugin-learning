/**
 * Group visible fields into single-row or multi-column rows (PHP `row` + `col`).
 */
import { isFieldVisible } from './field-visibility';

/**
 * @param {Array} fields
 * @param {Object} tabValues
 * @return {Array<{ type: string, fields: Array, rowClass?: string }>}
 */
export function groupFieldsIntoRows( fields, tabValues ) {
	const visible = ( fields || [] ).filter( ( field ) =>
		isFieldVisible( field, tabValues )
	);
	const groups = [];
	let i = 0;

	while ( i < visible.length ) {
		const field = visible[ i ];

		if ( field.row ) {
			const rowId = field.row;
			const groupFields = [ field ];
			let j = i + 1;
			while ( j < visible.length && visible[ j ].row === rowId ) {
				groupFields.push( visible[ j ] );
				j++;
			}
			groups.push( {
				type: 'row',
				fields: groupFields,
				rowClass: groupFields.find( ( f ) => f.row_class )?.row_class || '',
			} );
			i = j;
			continue;
		}

		groups.push( { type: 'single', fields: [ field ] } );
		i++;
	}

	return groups;
}

/**
 * @param {Object} field
 * @return {string}
 */
export function getFieldColClass( field ) {
	const col = field?.col;
	if ( ! col ) {
		return 'wpextrulepricing-campaign-fields__col--1';
	}
	if ( typeof col === 'string' ) {
		if ( col.startsWith( 'col-' ) ) {
			return `wpextrulepricing-campaign-fields__${ col }`;
		}
		const parsed = parseInt( col, 10 );
		if ( ! Number.isNaN( parsed ) ) {
			return `wpextrulepricing-campaign-fields__col--${ parsed }`;
		}
		return `wpextrulepricing-campaign-fields__col--1`;
	}
	return `wpextrulepricing-campaign-fields__col--${ col }`;
}

/**
 * @param {Array} fields
 * @return {string}
 */
export function getRowLabel( fields ) {
	const labeled = fields.find( ( f ) => f.label );
	return labeled?.label || '';
}
