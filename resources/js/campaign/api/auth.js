import bearer from '@websanova/vue-auth/drivers/auth/bearer';
import axios from '@websanova/vue-auth/drivers/http/axios.1.x';
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x';
import store from '../store';

let campaign = store.state.app.campaign;;
let campaign_uuid = campaign.uuid;
let campaign_slug = campaign.slug;
let campaign_host = campaign.host;
let campaign_prefix = '/campaign';

let root = (campaign_host !== null) ? '/' : campaign_prefix + '/' + campaign_slug + '/';

// Auth base configuration some of this options
// can be override in method calls
const config = {
    auth: bearer,
    http: axios,
    router: router,
    tokenDefaultKey: `yooz_campaign_${campaign_uuid}`, //'campaign-' + campaign_uuid,
    tokenImpersonateKey: `yooz_campaign_impersonate_${campaign_uuid}`,
    stores: ['storage'],
    rolesKey: 'role',
    registerData: {url: 'campaign/auth/register', method: 'POST', redirect: root + 'login'},
    loginData: {url: 'campaign/auth/login', method: 'POST', redirect: root + 'login', fetchUser: true},
    logoutData: {url: 'campaign/auth/logout', method: 'POST', redirect: root, makeRequest: true},
    fetchData: {url: `campaign/auth/user?uuid=${campaign_uuid}`, method: 'GET', enabled: true},
    refreshData: {url: `campaign/auth/refresh?uuid=${campaign_uuid}`, method: 'GET', enabled: true, interval: 30},
    notFoundRedirect: {path: root}, // https://github.com/websanova/vue-auth/blob/master/docs/Privileges.md
}
export default config;
