/**
 * Campaign list UI config from PHP (bulk actions, categories, …).
 */

export function getCampaignListConfig() {
	if ( typeof wpextrulepricingLocalize !== 'undefined' ) {
		return wpextrulepricingLocalize.campaign_list || {};
	}
	return {};
}
