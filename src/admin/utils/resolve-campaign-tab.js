/**
 * Resolve which campaign settings tab should be active on load.
 */

export function isTabAccessible( tab, hasPro ) {
	if ( hasPro ) {
		return true;
	}
	if ( tab.pro ) {
		return false;
	}
	if ( tab.locked && ! tab.sections?.length ) {
		return false;
	}
	return true;
}

/**
 * @param {Array}  tabs       Tab definitions from API.
 * @param {string} preferred  URL or saved tab id.
 * @param {boolean} hasPro    Pro unlocked.
 * @return {string}
 */
export function resolveActiveTab( tabs, preferred, hasPro ) {
	if ( ! tabs?.length ) {
		return preferred || 'schedule';
	}

	const tryId = ( id ) => {
		if ( ! id ) {
			return null;
		}
		const tab = tabs.find( ( t ) => t.id === id );
		if ( tab && isTabAccessible( tab, hasPro ) ) {
			return tab.id;
		}
		return null;
	};

	const fromUrl = tryId( preferred );
	if ( fromUrl ) {
		return fromUrl;
	}

	const defaultTab = tabs.find( ( t ) => t.default );
	const fromDefault = tryId( defaultTab?.id );
	if ( fromDefault ) {
		return fromDefault;
	}

	const firstOpen = tabs.find( ( t ) => isTabAccessible( t, hasPro ) );
	return firstOpen?.id || tabs[ 0 ].id;
}
