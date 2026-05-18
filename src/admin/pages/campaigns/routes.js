/**
 * Campaign routes: /campaigns, /campaigns/:id
 */
import { AtrcRoute, AtrcRoutes } from 'atrc';
import CampaignsList from './list';
import CampaignSingle from './single';

export default function CampaignRoutes() {
	return (
		<AtrcRoutes>
			<AtrcRoute index element={ <CampaignsList /> } />
			<AtrcRoute path=":id" element={ <CampaignSingle /> } />
		</AtrcRoutes>
	);
}
