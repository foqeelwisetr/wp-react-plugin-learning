/**
 * Rule group helpers (Finale wcct_rule shape).
 */

export function createRuleId() {
	return `rule${ Math.random().toString( 36 ).slice( 2, 10 ) }`;
}

export function createGroupId( index ) {
	return `group${ index }`;
}

export function defaultRuleGroups() {
	const ruleId = createRuleId();
	return {
		group0: {
			[ ruleId ]: {
				rule_type: 'general_always',
				operator: '==',
				condition: '',
			},
		},
	};
}

export function normalizeGroups( rules ) {
	if ( ! rules || typeof rules !== 'object' || Array.isArray( rules ) ) {
		return defaultRuleGroups();
	}
	return rules;
}

export function getRuleSummary( ruleType, rule, definitions ) {
	const def = definitions?.[ ruleType ];
	if ( ! def?.summary ) {
		return '';
	}
	if ( typeof def.summary === 'function' ) {
		return def.summary( rule );
	}
	return def.summary;
}
