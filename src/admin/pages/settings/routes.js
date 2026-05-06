/* WordPress */
import { __ } from '@wordpress/i18n';
import { useContext } from '@wordpress/element';

/* Library */
import { isEmpty } from 'lodash';

/* Atrc */
import {
	AtrcRoute,
	AtrcRoutes,
	AtrcNavigate,
	AtrcNav,
	AtrcWireFrameSidebarContent,
} from 'atrc';

/* Inbuilt */
import { AtrcReduxContextData } from '../../routes';
import SchemaSettings, {
	useSettingsUiBootstrap,
} from './schema-settings';

/* PHP-driven settings (sidebar + tabs from REST). */
const SettingsIndexRedirect = ( { firstSlug } ) => {
	if ( ! firstSlug ) {
		return (
			<p className="wpextrulepricing-settings-ui__loading-inline">
				{ __( 'No tabs registered in PHP.', 'wp-ext-rule-pricing' ) }
			</p>
		);
	}
	return (
		<AtrcNavigate
			to={ `/settings/${ firstSlug }` }
			replace
		/>
	);
};

const SettingsRouters = ( { tabs, values, setValues } ) => {
	const firstSlug = tabs[ 0 ]?.slug || '';

	return (
		<AtrcRoutes>
			<AtrcRoute
				index
				element={ <SettingsIndexRedirect firstSlug={ firstSlug } /> }
			/>
			<AtrcRoute
				path=":tabSlug"
				element={
					<SchemaSettings
						tabs={ tabs }
						values={ values }
						setValues={ setValues }
					/>
				}
			/>
		</AtrcRoutes>
	);
};

const InitSettings = () => {
	const data = useContext( AtrcReduxContextData );
	const { dbSettings } = data;
	const { tabs, values, setValues, loading, error } =
		useSettingsUiBootstrap();

	if ( isEmpty( dbSettings ) ) {
		return null;
	}

	if ( loading ) {
		return (
			<div className="wpextrulepricing-settings-ui__loading">
				{ __( 'Loading settings…', 'wp-ext-rule-pricing' ) }
			</div>
		);
	}

	if ( error ) {
		return (
			<div className="wpextrulepricing-settings-ui__error">
				{ error }
			</div>
		);
	}

	const navs = tabs.map( ( tab ) => ( {
		to: `/settings/${ tab.slug }`,
		children: tab.label || tab.slug,
	} ) );

	return (
		<AtrcWireFrameSidebarContent
			wrapProps={ {
				tag: 'div',
				className: 'at-ctnr-fld wpextrulepricing-settings-ui',
			} }
			rowProps={ {} }
			renderSidebar={
				<div className="wpextrulepricing-settings-ui__sidebar-inner">
					<h2 className="wpextrulepricing-settings-ui__sidebar-title">
						{ __( 'Settings', 'wp-ext-rule-pricing' ) }
					</h2>
					<AtrcNav
						variant="vertical"
						navs={ navs }
					/>
				</div>
			}
			renderContent={
				<SettingsRouters
					tabs={ tabs }
					values={ values }
					setValues={ setValues }
				/>
			}
			contentProps={ {
				tag: 'div',
				contentCol: 'at-col-10',
			} }
			sidebarProps={ {
				sidebarCol: 'at-col-2',
			} }
		/>
	);
};

export default InitSettings;
