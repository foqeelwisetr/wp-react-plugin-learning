/**
 * Rules tab (#/rules): static hello + rules list from GET /wp-ext-rule-pricing/v1/rules.
 */
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const RULES_PATH = 'wp-ext-rule-pricing/v1/rules';

let apiFetchReady = false;

function ensureApiFetch() {
	if ( apiFetchReady || typeof wpextrulepricingLocalize === 'undefined' ) {
		return;
	}
	const root = wpextrulepricingLocalize.rest_url || '/wp-json/';
	apiFetch.use( apiFetch.createRootURLMiddleware( root ) );
	apiFetch.use( apiFetch.createNonceMiddleware( wpextrulepricingLocalize.nonce ) );
	apiFetchReady = true;
}

export default function RulesPage() {
	const [ rules, setRules ] = useState( [] );
	const [ error, setError ] = useState( null );
	const [ loading, setLoading ] = useState( true );

	useEffect( () => {
		ensureApiFetch();
		let cancelled = false;
		setLoading( true );
		setError( null );

		apiFetch( { path: RULES_PATH } )
			.then( ( data ) => {
				if ( cancelled ) {
					return;
				}
				const list =
					data &&
					typeof data === 'object' &&
					Array.isArray( data.result )
						? data.result
						: Array.isArray( data )
							? data
							: [];
				setRules( list );
			} )
			.catch( ( err ) => {
				if ( ! cancelled ) {
					setError( err?.message || String( err ) );
					setRules( [] );
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
		<div className="wpextrulepricing-rules">
			<p>hello</p>
			{ loading && <p>Loading rules…</p> }
			{ error && <p role="alert">{ error }</p> }
			{ ! loading && ! error && (
				<ul>
					{ rules.map( ( rule ) => (
						<li key={ rule.id ?? rule.title }>
							{ rule.id }: { rule.title }
						</li>
					) ) }
				</ul>
			) }
		</div>
	);
}
