/**
 * REST helpers for campaigns (Autonami-style admin.php?page=…&path=… via hash router).
 */
import apiFetch from '@wordpress/api-fetch';

const NS = 'wp-ext-rule-pricing/v1';

let apiMiddlewareReady = false;

export function setupCampaignsApiFetch() {
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

function unwrap( response ) {
	return response?.result !== undefined ? response.result : response;
}

export async function fetchCampaigns() {
	setupCampaignsApiFetch();
	const res = await apiFetch( { path: `${ NS }/campaigns` } );
	return unwrap( res ) || [];
}

export async function fetchCampaign( id ) {
	setupCampaignsApiFetch();
	const res = await apiFetch( { path: `${ NS }/campaigns/${ id }` } );
	return unwrap( res );
}

export async function createCampaign( payload = {} ) {
	setupCampaignsApiFetch();
	const res = await apiFetch( {
		path: `${ NS }/campaigns`,
		method: 'POST',
		data: { campaign: payload },
	} );
	return unwrap( res );
}

export async function saveCampaign( id, payload ) {
	setupCampaignsApiFetch();
	const res = await apiFetch( {
		path: `${ NS }/campaigns/${ id }`,
		method: 'PUT',
		data: { campaign: payload },
	} );
	return unwrap( res );
}

export async function deleteCampaign( id ) {
	setupCampaignsApiFetch();
	return apiFetch( {
		path: `${ NS }/campaigns/${ id }`,
		method: 'DELETE',
	} );
}

export async function fetchRuleTypes() {
	setupCampaignsApiFetch();
	const res = await apiFetch( { path: `${ NS }/campaigns/rule-types` } );
	return unwrap( res ) || { groups: [], definitions: {} };
}

export async function fetchCampaignTabs() {
	setupCampaignsApiFetch();
	const res = await apiFetch( { path: `${ NS }/campaigns/tabs` } );
	return unwrap( res ) || [];
}

export async function fetchProUpsells() {
	setupCampaignsApiFetch();
	const res = await apiFetch( { path: `${ NS }/campaigns/pro-upsells` } );
	return unwrap( res ) || { is_pro: false, upsells: {} };
}
