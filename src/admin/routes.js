/*CSS*/
import './admin.scss';

/* WordPress */
import { render, createContext, useContext } from '@wordpress/element';
import { useLocation } from 'react-router-dom';

/* Library */
import { map, isEmpty } from 'lodash';

/*Atrc*/
import {
    AtrcHashRouter,
    AtrcRoute,
    AtrcRoutes,
    AtrcWrap,
    AtrcNotice,
    AtrcWrapFloating,
    AtrcMain
} from 'atrc';

import { AtrcApplyWithSettings } from 'atrc/build/data';

/*Inbuilt*/
import AdminHeader from './components/organisms/admin-header';
import Initlanding from './pages/landing';
import InitSettings from './pages/settings/routes';
import CampaignRoutes from './pages/campaigns/routes';

/* Local */

/* ==============Create Local Storage and Database Settings Context================== */
export const AtrcReduxContextData = createContext();

const isCampaignSingleView = ( pathname ) =>
    /^\/campaigns\/\d+/.test( pathname || '' );

const AppRootWrap = ( { children } ) => {
    const location = useLocation();
    const fullscreen = isCampaignSingleView( location.pathname );

    return (
        <AtrcWrap
            variant="wrp"
            className={
                fullscreen
                    ? 'wpextrulepricing-root--campaign-fs'
                    : 'at-box-szg at-m at-typ'
            }
        >
            { children }
        </AtrcWrap>
    );
};

const AdminRoutes = () => {
    const data = useContext(AtrcReduxContextData);
    const { dbNotices, dbRemoveNotice } = data;
    const location = useLocation();
    const fullscreenCampaign = isCampaignSingleView( location.pathname );

    return (
        <>
            { ! fullscreenCampaign ? <AdminHeader /> : null }
            <AtrcMain
                className={
                    fullscreenCampaign
                        ? 'wpextrulepricing-main--campaign-fs'
                        : ''
                }
            >
                <AtrcRoutes>
                    <AtrcRoute
                        index
                        element={<Initlanding />}
                    />
                    <AtrcRoute
                        exact
                        path='/settings/*'
                        element={<InitSettings />}
                    />
                    <AtrcRoute
                        path='/campaigns/*'
                        element={<CampaignRoutes />}
                    />
                </AtrcRoutes>
                {/*Notice is common for settings*/}
                {!isEmpty(dbNotices) ? (
                    <AtrcWrapFloating>
                        {map(dbNotices, (value, key) => (
                            <AtrcNotice
                                key={key}
                                autoDismiss={5000}
                                onAutoRemove={() => dbRemoveNotice(key)}
                                onRemove={() => dbRemoveNotice(key)}>
                                {value.message}
                            </AtrcNotice>
                        ))}
                    </AtrcWrapFloating>
                ) : null}
            </AtrcMain>
        </>
    );
};

/* Init actual WordPress settings */
const InitDatabaseSettings = (props) => {
    const {
        isLoading,
        canSave,
        settings,
        updateSetting,
        saveSettings,
        notices,
        removeNotice,
        lsSettings,
        lsUpdateSetting,
        lsSaveSettings,
    } = props;

    const dbProps = {
        dbIsLoading: isLoading,
        dbCanSave: canSave,
        dbSettings: settings,
        dbUpdateSetting: updateSetting,
        dbSaveSettings: saveSettings,
        dbNotices: notices,
        dbRemoveNotice: removeNotice,
        lsSettings: lsSettings,
        lsUpdateSetting: lsUpdateSetting,
        lsSaveSettings: lsSaveSettings,
    };
    return (
        <AtrcReduxContextData.Provider value={{ ...dbProps }}>
            <AtrcHashRouter
                basename='/'
                future={ {
                    v7_startTransition: true,
                    v7_relativeSplatPath: true,
                } }
            >
                <AppRootWrap>
                    <AdminRoutes />
                </AppRootWrap>
            </AtrcHashRouter>
        </AtrcReduxContextData.Provider>
    );
};
const InitDataBaseSettingsWithHoc = AtrcApplyWithSettings(InitDatabaseSettings);

/* Init local storage settings */
const InitLocalStorageSettings = (props) => {
    const { settings, updateSetting, saveSettings } = props;
    const defaultSettings = {
        gs1: true /* getting started 1 */,
    };
    return (
        <InitDataBaseSettingsWithHoc
            atrcStore={wpextrulepricingLocalize.store}//store from AtrcRegisterStore
            atrcStoreKey='settings'//key from admin.js
            lsSettings={settings || defaultSettings}
            lsUpdateSetting={updateSetting}
            lsSaveSettings={saveSettings}
        />
    );
};
const InitLocalStorageSettingsWithHoc = AtrcApplyWithSettings(
    InitLocalStorageSettings
);

document.addEventListener('DOMContentLoaded', () => {
    // Check if the root element exists in the DOM
    const rootElement = document.getElementById(wpextrulepricingLocalize.root_id);

    if (rootElement) {
        // Render the component into the root element
        render(
            <InitLocalStorageSettingsWithHoc
                atrcStore={wpextrulepricingLocalize.store} //store from AtrcRegisterStore
                atrcStoreKey='wpextrulepricingLocal'//key from admin.js
            />,
            rootElement
        );
    }
});
