/**
 * Pro upsell state + PHP-loaded copy.
 */
import { useCallback, useMemo, useState } from '@wordpress/element';
import { useEffect } from '@wordpress/element';
import { isPro } from '../utils/is-pro';
import { fetchProUpsells } from './use-campaigns-api';

export function useProUpsell() {
	const [ upsellsMap, setUpsellsMap ] = useState( {} );
	const [ activeUpsellId, setActiveUpsellId ] = useState( null );
	const [ loaded, setLoaded ] = useState( false );

	useEffect( () => {
		fetchProUpsells()
			.then( ( data ) => {
				if ( data?.upsells ) {
					setUpsellsMap( data.upsells );
				}
			} )
			.finally( () => setLoaded( true ) );
	}, [] );

	const openUpsell = useCallback( ( upsellId ) => {
		if ( isPro() ) {
			return false;
		}
		setActiveUpsellId( upsellId || 'default' );
		return true;
	}, [] );

	const closeUpsell = useCallback( () => {
		setActiveUpsellId( null );
	}, [] );

	const activeUpsell = useMemo( () => {
		if ( ! activeUpsellId ) {
			return null;
		}
		return (
			upsellsMap[ activeUpsellId ] ||
			upsellsMap.default ||
			null
		);
	}, [ activeUpsellId, upsellsMap ] );

	const resolveUpsell = useCallback(
		( upsellId ) =>
			upsellsMap[ upsellId ] || upsellsMap.default || null,
		[ upsellsMap ]
	);

	return {
		isPro: isPro(),
		loaded,
		upsellsMap,
		activeUpsell,
		activeUpsellId,
		openUpsell,
		closeUpsell,
		resolveUpsell,
	};
}
