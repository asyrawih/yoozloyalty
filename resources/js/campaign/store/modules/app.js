import Cookies from "js-cookie";

const app = {
    state: {
        global: window.globalApp,
        campaign: window.initCampaign,
        showCookieConsent:
            Cookies.get("showCookieConsent") === "false" ? false : true || true,
        language:
            Cookies.get("language") ||
            navigator.language ||
            navigator.userLanguage,
        isJustLoggedOut: false
    },
    mutations: {
        SET_LANGUAGE: (state, language) => {
            state.language = language;
            Cookies.set("language", language);
        },
        SET_COOKIE_CONSENT: (state, showCookieConsent) => {
            state.showCookieConsent = showCookieConsent;
            Cookies.set("showCookieConsent", showCookieConsent, {
                expires: 360,
                path: "/"
            });
        },
        UPDATE_CAMPAIGN: (state, campaign) => {
            state.campaign = campaign;
        },
        SET_JUST_LOGGED_OUT: (state, isJustLoggedOut) => {
            state.isJustLoggedOut = isJustLoggedOut;
        }
    },
    actions: {
        setLanguage({ commit }, language) {
            commit("SET_LANGUAGE", language);
        },
        setCookieConsent({ commit }, showCookieConsent) {
            commit("SET_COOKIE_CONSENT", showCookieConsent);
        },
        updateCampaign({ commit }, campaign) {
            commit("UPDATE_CAMPAIGN", campaign);
        },
        setJustLoggedOut({ commit }, isJustLoggedOut) {
            commit("SET_JUST_LOGGED_OUT", isJustLoggedOut)
        }
    }
};

export default app;
