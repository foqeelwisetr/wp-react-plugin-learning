/**
 * Full-screen campaign editor: body class + cleanup on unmount.
 */
import { useEffect } from '@wordpress/element';

const BODY_CLASS = 'wpextrulepricing-campaign-fullscreen';

export default function useCampaignFullscreen( enabled = true ) {
	useEffect( () => {
		if ( ! enabled ) {
			return undefined;
		}

		document.body.classList.add( BODY_CLASS );

		return () => {
			document.body.classList.remove( BODY_CLASS );
		};
	}, [ enabled ] );
}
