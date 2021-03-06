import bearer from '@websanova/vue-auth/drivers/auth/bearer';
import axios from '@websanova/vue-auth/drivers/http/axios.1.x';
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x';

// Auth base configuration some of this options
// can be override in method calls
const config = {
    auth: bearer,
    http: axios,
    router: router,
    tokenDefaultKey: 'yooz_architect',
    tokenImpersonateKey: 'yooz_architect_impersonate',
    stores: ['storage'],
    rolesKey: 'role',
    registerData: {url: 'auth/register', method: 'POST', redirect: '/login'},
    loginData: {url: 'auth/login', method: 'POST', redirect: '', fetchUser: true},
    logoutData: {url: 'auth/logout', method: 'POST', redirect: '/', makeRequest: true},
    fetchData: {url: 'auth/user', method: 'GET', enabled: true},
    refreshData: {url: 'auth/refresh', method: 'GET', enabled: true, interval: 30},
    notFoundRedirect: {path: '/dashboard'}, // https://github.com/websanova/vue-auth/blob/master/docs/Privileges.md
    /*parseUserData (data) {
        return data || {}
    }*/
}
export default config
