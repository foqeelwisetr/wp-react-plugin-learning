/* *********===================== Setup store ======================********* */
import { AtrcApis, AtrcStore, AtrcRegisterStore } from 'atrc/build/data';

AtrcApis.baseUrl({
    //don't change atrc-global-api-base-url
    key: 'atrc-global-api-base-url',
    // eslint-disable-next-line no-undef
    url: wpextrulepricingLocalize.rest_url,
});

/* Preferences (local only; replaces legacy GET/POST wp-ext-rule-pricing/v1/settings). */
AtrcStore.register({
    key: 'wpextrulepricingLocal',
    type: 'localStorage',
});
AtrcStore.register({
    key: 'settings',
    type: 'localStorage',
});

// eslint-disable-next-line no-undef
AtrcApis.xWpNonce(wpextrulepricingLocalize.nonce);
AtrcRegisterStore(wpextrulepricingLocalize.store);

import './routes';

