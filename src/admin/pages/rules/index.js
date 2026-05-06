/**
 * Rules (#/rules): list from GET /wp-ext-rule-pricing/v1/rules.
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const RULES_PATH = 'wp-ext-rule-pricing/v1/rules';

let apiMiddlewareReady = false;

function setupRulesApiFetch() {
	if (
		apiMiddlewareReady ||
		typeof wpextrulepricingLocalize === 'undefined'
	) {
		return;
	}
	const root = wpextrulepricingLocalize.rest_url || '/wp-json/';
	apiFetch.use( apiFetch.createRootURLMiddleware( root ) );
	apiFetch.use(
		apiFetch.createNonceMiddleware( wpextrulepricingLocalize.nonce )
	);
	apiMiddlewareReady = true;
}

export default function RulesPage() {
	const [ rules, setRules ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	useEffect( () => {
		setupRulesApiFetch();
		let cancelled = false;
		setLoading( true );
		setError( null );
		apiFetch( { path: RULES_PATH } )
			.then( ( data ) => {
				if ( cancelled ) {
					return;
				}
				const list = Array.isArray( data?.result )
					? data.result
					: [];
				setRules( list );
			} )
			.catch( ( err ) => {
				if ( ! cancelled ) {
					setError(
						err?.message ||
							__(
								'Could not load rules.',
								'wp-ext-rule-pricing'
							)
					);
				}
			} )
			.finally( () => {
				if ( ! cancelled ) {
					setLoading( false );
				}
			} );
		return () => {
			cancelled = true;
		};
	}, [] );

	return (
		<div className="wpextrulepricing-rules at-ctnr-fld">
			<h1 className="wpextrulepricing-rules__title">
				{ __( 'Rules', 'wp-ext-rule-pricing' ) }
			</h1>
			{ loading && (
				<p className="description">
					{ __( 'Loading rules…', 'wp-ext-rule-pricing' ) }
				</p>
			) }
			{ error && (
				<p className="wpextrulepricing-rules__error">{ error }</p>
			) }
			{ ! loading && ! error && rules.length === 0 && (
				<p className="description">
					{ __( 'No rules returned.', 'wp-ext-rule-pricing' ) }
				</p>
			) }
			{ ! loading && rules.length > 0 && (
				<ul className="wpextrulepricing-rules__list">
					{ rules.map( ( rule ) => (
						<li key={ rule.id ?? rule.title }>
							<strong>{ rule.title ?? rule.id }</strong>
							{ rule.id !== undefined ? (
								<span className="description">
									{ ' ' }
									(ID: { String( rule.id ) })
								</span>
							) : null }
						</li>
					) ) }
				</ul>
			) }
		</div>
	);
}
