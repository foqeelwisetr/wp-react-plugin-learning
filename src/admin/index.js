/* *********===================== Setup store ======================********* */
import { AtrcApis, AtrcStore, AtrcRegisterStore } from 'atrc/build/data';

AtrcApis.baseUrl({
    //don't change atrc-global-api-base-url
    key: 'atrc-global-api-base-url',
    // eslint-disable-next-line no-undef
    url: wpextrulepricingLocalize.rest_url,
});

/* Settings */
AtrcApis.register({
    key: 'settings',
    path: 'wp-ext-rule-pricing/v1/settings',
    type: 'settings',
});

/* Settings Local for user preferance work with Window: localStorage property */
AtrcStore.register({
    key: 'wpextrulepricingLocal',
    type: 'localStorage',
});

// eslint-disable-next-line no-undef
AtrcApis.xWpNonce(wpextrulepricingLocalize.nonce);
AtrcRegisterStore(wpextrulepricingLocalize.store);

import './routes';

