/**
 * Finale-style AND / OR rules builder.
 */
import { __ } from '@wordpress/i18n';
import { Button, SelectControl, TextControl } from '@wordpress/components';
import {
	createGroupId,
	createRuleId,
	normalizeGroups,
	getRuleSummary,
} from '../../utils/rules-helpers';

function flattenRuleOptions( groups ) {
	const flat = [];
	( groups || [] ).forEach( ( group ) => {
		( group.options || [] ).forEach( ( opt ) => {
			flat.push( {
				value: opt.value,
				label: opt.locked
					? `${ opt.label } (${ __( 'Pro', 'wp-ext-rule-pricing' ) })`
					: opt.label,
				disabled: !! opt.locked,
			} );
		} );
	} );
	return flat;
}

function RuleRow( {
	groupId,
	ruleId,
	rule,
	ruleOptions,
	definitions,
	onChange,
	onRemove,
	onAddAnd,
	isOnlyRule,
} ) {
	const def = definitions?.[ rule.rule_type ] || {};
	const operators = def.operators || [];
	const fields = def.fields || [];
	const summary =
		getRuleSummary( rule.rule_type, rule, definitions ) || def.summary || '';

	return (
		<tr className="wpextrulepricing-rules-builder__row" data-ruleid={ ruleId }>
			<td className="wpextrulepricing-rules-builder__type">
				<SelectControl
					value={ rule.rule_type }
					options={ [
						{ label: __( 'Select…', 'wp-ext-rule-pricing' ), value: '' },
						...ruleOptions,
					] }
					onChange={ ( rule_type ) =>
						onChange( groupId, ruleId, {
							rule_type,
							operator: '==',
							condition: '',
						} )
					}
					__nextHasNoMarginBottom
				/>
			</td>
			<td className="wpextrulepricing-rules-builder__fields" colSpan={ 2 }>
				{ operators.length > 0 && (
					<SelectControl
						value={ rule.operator || '==' }
						options={ operators.map( ( op ) => ( {
							label: op.label,
							value: op.value,
						} ) ) }
						onChange={ ( operator ) =>
							onChange( groupId, ruleId, { operator } )
						}
						__nextHasNoMarginBottom
					/>
				) }
				{ fields.map( ( field ) => (
					<TextControl
						key={ field.key }
						value={ rule[ field.key ] || '' }
						placeholder={ field.placeholder || '' }
						onChange={ ( value ) =>
							onChange( groupId, ruleId, { [ field.key ]: value } )
						}
						__nextHasNoMarginBottom
					/>
				) ) }
				{ summary ? (
					<p className="wpextrulepricing-rules-builder__summary description">
						{ summary }
					</p>
				) : null }
			</td>
			<td className="wpextrulepricing-rules-builder__and">
				<Button variant="secondary" onClick={ onAddAnd }>
					{ __( 'AND', 'wp-ext-rule-pricing' ) }
				</Button>
			</td>
			<td className="wpextrulepricing-rules-builder__remove">
				{ ! isOnlyRule && (
					<Button
						isDestructive
						variant="link"
						onClick={ () => onRemove( groupId, ruleId ) }
						label={ __( 'Remove condition', 'wp-ext-rule-pricing' ) }
					>
						×
					</Button>
				) }
			</td>
		</tr>
	);
}

export default function RulesBuilder( { value, onChange, ruleTypes } ) {
	const groups = normalizeGroups( value );
	const groupKeys = Object.keys( groups );
	const ruleOptions = flattenRuleOptions( ruleTypes?.groups );
	const definitions = ruleTypes?.definitions || {};

	const updateGroups = ( next ) => {
		onChange( next );
	};

	const handleRuleChange = ( groupId, ruleId, patch ) => {
		const next = { ...groups };
		next[ groupId ] = {
			...next[ groupId ],
			[ ruleId ]: {
				...next[ groupId ][ ruleId ],
				...patch,
			},
		};
		updateGroups( next );
	};

	const addAndRule = ( groupId ) => {
		const ruleId = createRuleId();
		const next = { ...groups };
		next[ groupId ] = {
			...next[ groupId ],
			[ ruleId ]: {
				rule_type: 'general_always',
				operator: '==',
				condition: '',
			},
		};
		updateGroups( next );
	};

	const removeRule = ( groupId, ruleId ) => {
		const next = { ...groups };
		const group = { ...next[ groupId ] };
		delete group[ ruleId ];
		if ( Object.keys( group ).length === 0 ) {
			return;
		}
		next[ groupId ] = group;
		updateGroups( next );
	};

	const addOrGroup = () => {
		const idx = groupKeys.length;
		const groupId = createGroupId( idx );
		const ruleId = createRuleId();
		updateGroups( {
			...groups,
			[ groupId ]: {
				[ ruleId ]: {
					rule_type: 'general_always',
					operator: '==',
					condition: '',
				},
			},
		} );
	};

	const removeGroup = ( groupId ) => {
		if ( groupKeys.length <= 1 ) {
			return;
		}
		const next = { ...groups };
		delete next[ groupId ];
		updateGroups( next );
	};

	return (
		<div className="wpextrulepricing-rules-builder">
			<div className="wpextrulepricing-rules-builder__label">
				<h4>{ __( 'Rules', 'wp-ext-rule-pricing' ) }</h4>
				<p className="description">
					{ __(
						'Create a set of rules to determine when the campaign defined above will be displayed.',
						'wp-ext-rule-pricing'
					) }
				</p>
			</div>
			<div className="wpextrulepricing-rules-builder__groups" id="wpextrulepricing-rules-groups">
				{ groupKeys.map( ( groupId, groupIndex ) => {
					const groupRules = groups[ groupId ] || {};
					const ruleIds = Object.keys( groupRules );
					return (
						<div
							key={ groupId }
							className="wpextrulepricing-rules-builder__group"
							data-groupid={ groupId }
						>
							<div className="wpextrulepricing-rules-builder__group-header">
								<h4>
									{ groupIndex === 0
										? __(
												'Apply this Campaign when these conditions are matched:',
												'wp-ext-rule-pricing'
										  )
										: __( 'or', 'wp-ext-rule-pricing' ) }
								</h4>
								{ groupKeys.length > 1 && (
									<Button
										variant="link"
										isDestructive
										onClick={ () => removeGroup( groupId ) }
									>
										{ __( 'Remove group', 'wp-ext-rule-pricing' ) }
									</Button>
								) }
							</div>
							<table className="wpextrulepricing-rules-builder__table">
								<tbody>
									{ ruleIds.map( ( ruleId ) => (
										<RuleRow
											key={ ruleId }
											groupId={ groupId }
											ruleId={ ruleId }
											rule={ groupRules[ ruleId ] }
											ruleOptions={ ruleOptions }
											definitions={ definitions }
											onChange={ handleRuleChange }
											onRemove={ removeRule }
											onAddAnd={ () => addAndRule( groupId ) }
											isOnlyRule={ ruleIds.length === 1 }
										/>
									) ) }
								</tbody>
							</table>
						</div>
					);
				} ) }
				{ groupKeys.length > 1 && (
					<h4 className="wpextrulepricing-rules-builder__or-label">
						{ __( 'or when these conditions are matched', 'wp-ext-rule-pricing' ) }
					</h4>
				) }
				<Button variant="primary" onClick={ addOrGroup }>
					{ __( 'OR', 'wp-ext-rule-pricing' ) }
				</Button>
				<p className="wpextrulepricing-rules-builder__pro-note description">
					{ __(
						'Unlock all the rules by switching to PRO version.',
						'wp-ext-rule-pricing'
					) }
				</p>
			</div>
		</div>
	);
}
