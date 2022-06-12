import Vue from "vue";
import Router from "vue-router";
import store from "./store";
import NProgress from "nprogress";
import VueAnalytics from "vue-analytics";

NProgress.configure({ showSpinner: false });

let campaign_slug = store.state.app.campaign.slug;
let campaign_host = store.state.app.campaign.host;
let campaign_prefix = "/campaign";

let pathname = document.location.pathname;

if (campaign_host !== null) {
    campaign_slug = "";
    campaign_prefix = "";
}

/* Layout */
import CampaignLayout from "./views/layouts/CampaignLayout";

/* Public pages */
import Home from "./views/home/index";
import Legal from "./views/legal/index";
import EarnPoints from "./views/earn/index";
import Rewards from "./views/rewards/index";
import Contact from "./views/contact/index";
import MyPoints from "./views/points/index";
import Stores from "./views/stores/index";
//import CardsList from './views/cards/list'

/* Auth */
import Register from "./views/auth/register";
import Login from "./views/auth/login";
import PasswordCreate from "./views/auth/passwords/create";
import PasswordEmail from "./views/auth/passwords/email";
import ResetEmail from "./views/auth/passwords/reset";
import VerificationSuccess from "./views/auth/verificationSuccess";
import Profile from "./views/profile/index";
import SiteLayout from "../site/views/layouts/SiteLayout";
import PrivacyPolicy from "./views/legal/privacy_policy/index";
import UserAgreement from "./views/legal/user_agreement/index";
import ContactUs from "./views/legal/contact_us/index";
import RefundPolicy from "./views/legal/refund_policy/index";
import JustLoggedOut from "./views/home/JustLoggedOut";

/* Routes */
export const constantRouterMap = [
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/register",
                name: "register",
                components: {
                    primary: Register
                }
            }
        ],
        meta: {
            auth: false
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/login",
                name: "login",
                components: {
                    primary: Login
                }
            }
        ],
        meta: {
            auth: false
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/password/create",
                name: "password.create",
                components: {
                    primary: PasswordCreate
                }
            }
        ],
        meta: {
            auth: false
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/password/reset",
                name: "password.email",
                components: {
                    primary: PasswordEmail
                }
            }
        ],
        meta: {
            auth: false
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/password/reset/:token",
                name: "password.reset",
                components: {
                    primary: ResetEmail
                }
            }
        ],
        meta: {
            auth: false
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/verification/success",
                name: "verification.success",
                components: {
                    primary: VerificationSuccess
                }
            }
        ],
        meta: {
            auth: false
        }
    },
    // Generic routes
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/profile",
                name: "user.profile",
                components: {
                    primary: Profile
                }
            }
        ],
        meta: {
            auth: {
                roles: [1, 2, 3],
                redirect: { name: "login" },
                forbiddenRedirect: "/403"
            }
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/user_agreement",
                name: "user_agreement",
                components: {
                    primary: UserAgreement
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/privacy_policy",
                name: "privacy_policy",
                components: {
                    primary: PrivacyPolicy
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/refund_policy",
                name: "refund_policy",
                components: {
                    primary: RefundPolicy
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/contact_us",
                name: "contact_us",
                components: {
                    primary: ContactUs
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    // Campaign routes
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "",
                name: "home",
                components: {
                    primary: Home
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/earn",
                name: "earn",
                components: {
                    primary: EarnPoints
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/rewards",
                name: "rewards",
                components: {
                    primary: Rewards
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/contact",
                name: "contact",
                components: {
                    primary: Contact
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/stores",
                name: "stores",
                components: {
                    primary: Stores
                }
            }
        ],
        meta: {
            auth: undefined
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/points",
                name: "points",
                components: {
                    primary: MyPoints
                }
            }
        ],
        meta: {
            auth: {
                roles: [1, 2, 3],
                redirect: { name: "login" },
                forbiddenRedirect: "/403"
            }
        }
    },
    {
        path: campaign_prefix,
        component: CampaignLayout,
        children: [
            {
                path: campaign_slug + "/just_logged_out",
                name: "just_logged_out",
                components: {
                    primary: JustLoggedOut
                }
            }
        ],
        meta: {
            auth: undefined
        }
    }
];

const router = new Router({
    mode: 'history',
    scrollBehavior: () => ({ y: 0 }),
    routes: constantRouterMap
});

/*
Vue.use(VueAnalytics, {
  id: 'UA-xxxxxxxxx-x',
  router
})
*/

// This callback runs before every route change, including on page load.
router.beforeEach((to, from, next) => {
    if (to.name === "just_logged_out") {
        next();
    } else if (
        Vue.auth.check() &&
        Vue.auth.user().email_verified_at == null &&
        to.name !== "home"
    ) {
        next({ name: "home" });
    } else {
        if (typeof to.name === "undefined") {
            next({ name: "home" });
        }
        next();
    }
});

router.beforeResolve((to, from, next) => {
    if (to.name) {
        NProgress.start();
    }
    next();
});

router.afterEach((to, from) => {
    NProgress.done();
});

export default router;
