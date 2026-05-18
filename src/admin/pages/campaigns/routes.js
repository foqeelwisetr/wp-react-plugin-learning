/**
 * Campaign routes: /campaigns, /campaigns/:id, /campaigns/:id/:tabId
 */
import { AtrcRoute, AtrcRoutes } from 'atrc';
import CampaignsList from './list';
import CampaignSingle from './single';

export default function CampaignRoutes() {
	return (
		<AtrcRoutes>
			<AtrcRoute index element={ <CampaignsList /> } />
			<AtrcRoute path=":id" element={ <CampaignSingle /> } />
			<AtrcRoute path=":id/:tabId" element={ <CampaignSingle /> } />
		</AtrcRoutes>
	);
}
