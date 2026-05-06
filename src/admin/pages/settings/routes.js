/* WordPress */
import { __ } from '@wordpress/i18n';
import { useContext } from '@wordpress/element';

/* Library */
import { isEmpty } from 'lodash';

/*Atrc*/
import {
    AtrcRoute,
    AtrcRoutes,
    AtrcNavigate,
    AtrcNav,
    AtrcWireFrameSidebarContent,
} from 'atrc';

/*Inbuilt*/
import { Settings1, Settings2, Advanced } from './pages';
import { AtrcReduxContextData } from '../../routes';
import { SaveSettings } from '../../components/atoms';

/*Local*/
const SettingsRouters = () => {
    return (
        <>
            <AtrcRoutes>
                <AtrcRoute
                    path='setting1'
                    element={<Settings1 />}
                />
                <AtrcRoute
                    path='setting2'
                    element={<Settings2 />}
                />
                <AtrcRoute
                    path='advanced'
                    element={<Advanced />}
                />
                <AtrcRoute
                    index
                    element={
                        <AtrcNavigate
                            to='/settings/setting1'
                            replace
                        />
                    }
                />
            </AtrcRoutes>
            <SaveSettings />
        </>
    );
};

const InitSettings = () => {
    const data = useContext(AtrcReduxContextData);
    const { dbSettings } = data;

    if (isEmpty(dbSettings)) {
        return null;
    }
    return (
        <AtrcWireFrameSidebarContent
            wrapProps={{
                tag: 'div',
                className: 'at-ctnr-fld',
            }}
            rowProps={{}}
            renderSidebar={
                <AtrcNav
                    variant='vertical'
                    navs={[
                        {
                            to: '/settings/setting1',
                            children: __('Settings 1', 'wp-ext-rule-pricing'),
                        },
                        {
                            to: '/settings/setting2',
                            children: __('Settings 2', 'wp-ext-rule-pricing'),
                        },
                        {
                            to: '/settings/advanced',
                            children: __('Advanced', 'wp-ext-rule-pricing'),
                        },
                    ]}
                />
            }
            renderContent={<SettingsRouters />}
            contentProps={{
                tag: 'div',
                contentCol: 'at-col-10',
            }}
            sidebarProps={{
                sidebarCol: 'at-col-2',
            }}
        />
    );
};

export default InitSettings;
