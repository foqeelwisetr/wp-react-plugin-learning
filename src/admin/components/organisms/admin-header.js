/* WordPress */
import { __ } from '@wordpress/i18n';

import { useContext } from '@wordpress/element';

/* Library */
import classNames from 'classnames';

/*Atrc*/
import { AtrcButton, AtrcWrap, AtrcHeaderTemplate1 } from 'atrc';

/* Inbuilt */
import { AtrcReduxContextData } from '../../routes';

/*Local*/
const AdminHeader = () => {
    const data = useContext(AtrcReduxContextData);

    const { lsSettings, lsSaveSettings } = data;

    const primaryNav = [
        {
            to: '/',
            children: __('Getting started', 'wp-ext-rule-pricing'),
            end: true,
        },
        {
            to: '/settings',
            children: __('Settings', 'wp-ext-rule-pricing'),
        },
        {
            to: '/rules',
            children: __('Rules', 'wp-ext-rule-pricing'),
        },
    ];

    return (
        <AtrcHeaderTemplate1
            isSticky
            logo={{
                src: wpextrulepricingLocalize.white_label.dashboard.logo,
            }}
            primaryNav={{
                navs: primaryNav,
            }}
            floatingSidebar={() => (
                <AtrcWrap className={classNames()}>
                    <AtrcButton
                        className={classNames()}
                        onClick={() => lsSaveSettings(null)}>
                        {__(
                            'Show all hidden informations, notices and documentations ',
                            'wp-ext-rule-pricing'
                        )}
                    </AtrcButton>
                </AtrcWrap>
            )}
        />
    );
};

export default AdminHeader;
