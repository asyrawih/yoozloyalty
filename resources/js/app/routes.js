import Vue from "vue"
import Router from "vue-router"
import VueAnalytics from "vue-analytics"

/* Layout */
import AppLayout from "./views/layouts/AppLayout"
import DashboardLayout from "./views/layouts/DashboardLayout"

/* Auth */
import AuthRegister from "./views/auth/register"
import AuthLogin from "./views/auth/login"
import AuthPasswordEmail from "./views/auth/passwords/email"
import AuthResetEmail from "./views/auth/passwords/reset"
import VerificationSuccess from "./views/auth/verificationSuccess"

/* Generic pages */
import AuthProfile from "./views/app/profile/index"

/* Admin pages */
import AdminDashboard from "./views/app/admin/dashboard/index"
import AdminUser from "./views/app/admin/user/index"
import AdminMerchant from "./views/app/admin/merchant/index"
import AdminSettingsStore from "./views/app/admin/settings/store"
import AdminSettingsPlans from "./views/app/admin/settings/plans/index"
import AdminSettingsTrial from "./views/app/admin/settings/trial"
import AdminSettingsLogo from "./views/app/admin/settings/logo"
import AdminSettingsLegal from "./views/app/admin/settings/legal"
import AdminSettingsEmail from "./views/app/admin/settings/email"
import AdminSettingsPayment from "./views/app/admin/settings/payment/index"
import AdminSettingsPlanOrders from "./views/app/admin/settings/planOrders/index"
import AdminSettingsPushNotif from "./views/app/admin/settings/pushNotification/index"
import AdminSettingsNotifTemplate from "./views/app/admin/settings/notifTemplate/index"
import AdminSettingsCustomDomainGuide from "./views/app/admin/settings/customDomainGuide"
import AdminSettingsIndustries from "./views/app/admin/settings/industries"
import AdminSettingsSmsServices from "./views/app/admin/settings/smsService"

import AdminSettingsBanks from "./views/app/admin/settings/bank/index"
import AdminSettingsBankAccountTypes from "./views/app/admin/settings/bankAccountTypes/index"
import AdminSettingsSmsTemplate from "./views/app/admin/settings/smsTemplate/index"

import AdminSettingsStaffRoles from "./views/app/admin/settings/staff-roles/index"

/* User pages */
import UserDashboard from "./views/app/user/dashboard/index"
import UserCustomers from "./views/app/user/customers/index"

import UserAnalyticsCredit from "./views/app/user/analytics/credit"
import UserAnalyticsRedemption from "./views/app/user/analytics/redemption"

import UserBusinesses from "./views/app/user/businesses/index"
import UserSegments from "./views/app/user/segments/index"
import UserStaff from "./views/app/user/staff/index"

import UserWebsite from "./views/app/user/websites/index"
import EditUserWebsiteComponents from "./views/app/user/websites/components/EditWebisteComponent.vue"
import UserBilling from "./views/app/user/billing/billing"
import UserPlanChangeRequestHistory from "./views/app/user/plan-change-request-history/index"
import UserOfferListing from "./views/app/user/offer-listing/index"
import EditOfferComponent from "./views/app/user/offer-listing/EditOfferComponent"
import CreateOfferComponent from "./views/app/user/offer-listing/CreateOfferComponent"
import UserRedeemTransaction from "./views/app/user/redeem-transaction/index"
import UserEmailTemplate from "./views/app/user/email-template/index"
import UserCreditRequest from "./views/app/user/credit-request/index"
import UserSmsTemplate from "./views/app/user/sms-template/index"
import UserPointsExpiry from "./views/app/user/points-expiry/index"
import UserLegal from "./views/app/user/legal/index"
import UserBroadcastNotification from "./views/app/user/broadcast-notification/index"
import AdminBroadcastNotification from "./views/app/admin/broadcast-notification/index"
import UserSmtpService from "./views/app/user/smtp-service/index"
import UserTransactionHistory from "./views/app/user/transaction-history/index"

/* Routes */
export const constantRouterMap = [
    // Generic routes
    { path: "/", redirect: "/login", hidden: true }, // Redirect root to login
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "/register",
                name: "register",
                components: {
                    primary: AuthRegister,
                },
            },
        ],
        meta: {
            auth: false,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "/login",
                name: "login",
                components: {
                    primary: AuthLogin,
                },
            },
        ],
        meta: {
            auth: false,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "/password/reset",
                name: "password.email",
                components: {
                    primary: AuthPasswordEmail,
                },
            },
        ],
        meta: {
            auth: false,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "/password/reset/:token",
                name: "password.reset",
                components: {
                    primary: AuthResetEmail,
                },
            },
        ],
        meta: {
            auth: false,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "/verification/success",
                name: "verification.success",
                components: {
                    primary: VerificationSuccess,
                },
            },
        ],
        meta: {
            auth: false,
        },
    },
    // Generic routes
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "profile",
                name: "profile",
                components: {
                    primary: AuthProfile,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    // User routes
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "dashboard",
                name: "user.dashboard",
                components: {
                    primary: UserDashboard,
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "customers",
                name: "user.customers",
                components: {
                    primary: UserCustomers,
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "analytics/credit",
                name: "user.analytics.credit",
                components: {
                    primary: UserAnalyticsCredit,
                },
                meta: {
                    parentMenu: "analytics",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "analytics/redemption",
                name: "user.analytics.redemption",
                components: {
                    primary: UserAnalyticsRedemption,
                },
                meta: {
                    parentMenu: "analytics",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "businesses",
                name: "user.businesses",
                components: {
                    primary: UserBusinesses,
                },
                meta: {
                    parentMenu: "merchants",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "segments",
                name: "user.segments",
                components: {
                    primary: UserSegments,
                },
                meta: {
                    parentMenu: "merchants",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "staff",
                name: "user.staff",
                components: {
                    primary: UserStaff,
                },
                meta: {
                    parentMenu: "merchants",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "websites",
                name: "user.websites",
                components: {
                    primary: UserWebsite,
                },
                meta: {
                    parentMenu: "programs",
                },
            },
            {
                path: "websites/edit",
                name: "user.websites.edit",
                components: {
                    primary: EditUserWebsiteComponents,
                },
                meta: {
                    parentMenu: "programs",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "offer",
                name: "user.offer",
                components: {
                    primary: UserOfferListing,
                },
                meta: {
                    parentMenu: "programs",
                },
            },
            {
                path: "offer/edit",
                name: "user.offer.edit",
                components: {
                    primary: EditOfferComponent,
                },
                meta: {
                    parentMenu: "programs",
                },
            },
            {
                path: "offer/create",
                name: "user.offer.create",
                components: {
                    primary: CreateOfferComponent,
                },
                meta: {
                    parentMenu: "programs",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "redemption-multiplier",
                name: "user.redeem-transaction",
                components: {
                    primary: UserRedeemTransaction,
                },
                meta: {
                    parentMenu: "programs",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "billing",
                name: "user.billing",
                components: {
                    primary: UserBilling,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "plan-change-request-history",
                name: "user.planChangeRequestHistory",
                components: {
                    primary: UserPlanChangeRequestHistory,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "email-template",
                name: "user.emailTemplate",
                components: {
                    primary: UserEmailTemplate,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "legal",
                name: "user.legal",
                components: {
                    primary: UserLegal,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "credit-request",
                name: "user.credit_request",
                components: {
                    primary: UserCreditRequest,
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "transaction-history",
                name: "user.transaction_history",
                components: {
                    primary: UserTransactionHistory,
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "sms-template",
                name: "user.sms-template",
                components: {
                    primary: UserSmsTemplate,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "smtp-service",
                name: "user.smtp-service",
                components: {
                    primary: UserSmtpService,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "points-expiry",
                name: "user.points-expiry",
                components: {
                    primary: UserPointsExpiry,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
    },
    {
        path: "/",
        component: DashboardLayout,
        children: [
            {
                path: "broadcast-notification",
                name: "user.broadcast-notification",
                components: {
                    primary: UserBroadcastNotification,
                },
            },
        ],
        meta: {
            auth: true,
        },
    },
    // Admin routes
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "dashboard",
                name: "admin.dashboard",
                components: {
                    primary: AdminDashboard,
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "user",
                name: "admin.user",
                components: {
                    primary: AdminUser,
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "merchant",
                name: "admin.merchant",
                components: {
                    primary: AdminMerchant,
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/store",
                name: "admin.settings.store",
                components: {
                    primary: AdminSettingsStore,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/plans",
                name: "admin.settings.plans",
                components: {
                    primary: AdminSettingsPlans,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
            {
                path: "settings/industries",
                name: "admin.settings.industries",
                components: {
                    primary: AdminSettingsIndustries,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/trial",
                name: "admin.settings.trial",
                components: {
                    primary: AdminSettingsTrial,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/logo",
                name: "admin.settings.logo",
                components: {
                    primary: AdminSettingsLogo,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/legal",
                name: "admin.settings.legal",
                components: {
                    primary: AdminSettingsLegal,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/email",
                name: "admin.settings.email",
                components: {
                    primary: AdminSettingsEmail,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/payment",
                name: "admin.settings.payment",
                components: {
                    primary: AdminSettingsPayment,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/plan-orders",
                name: "admin.settings.planOrders",
                components: {
                    primary: AdminSettingsPlanOrders,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/push-notif",
                name: "admin.settings.pushNotif",
                components: {
                    primary: AdminSettingsPushNotif,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/notif-template",
                name: "admin.settings.notifTemplate",
                components: {
                    primary: AdminSettingsNotifTemplate,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/sms-service",
                name: "admin.settings.smsService",
                components: {
                    primary: AdminSettingsSmsServices,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/bank-accounts",
                name: "admin.settings.bankAccounts",
                components: {
                    primary: AdminSettingsBanks,
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/bank-accounts/types",
                name: "admin.settings.bankAccountTypes",
                components: {
                    primary: AdminSettingsBankAccountTypes,
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/sms-template",
                name: "admin.settings.smsTemplate",
                components: {
                    primary: AdminSettingsSmsTemplate,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/staff-roles",
                name: "admin.settings.staffRoles",
                components: {
                    primary: AdminSettingsStaffRoles,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "settings/custom-domain-guide",
                name: "admin.settings.customDomainGuide",
                components: {
                    primary: AdminSettingsCustomDomainGuide,
                },
                meta: {
                    parentMenu: "settings",
                },
            },
        ],
        meta: {
            auth: {
                roles: [1, 2],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    {
        path: "/admin",
        component: DashboardLayout,
        children: [
            {
                path: "broadcast-notification",
                name: "admin.broadcast-notification",
                components: {
                    primary: AdminBroadcastNotification,
                },
            },
        ],
        meta: {
            auth: {
                roles: [1],
                redirect: { name: "login" },
                forbiddenRedirect: "/403",
            },
        },
    },
    { path: "*", redirect: "/login", hidden: true }, // Catch unkown routes
]

const router = new Router({
    //mode: 'history',
    scrollBehavior: () => ({ y: 0 }),
    routes: constantRouterMap,
})

/*
Vue.use(VueAnalytics, {
  id: 'UA-xxxxxxxxx-x',
  router
})
*/

// This callback runs before every route change, including on page load.
router.beforeEach((to, from, next) => {
    let user = Vue.auth.user()

    if (Vue.auth.check() && !user.email_verified_at) {
        if (user.role == 3 && to.name !== "user.dashboard") {
            next("/dashboard")
        } else if (
            (user.role == 1 || user.role == 2) &&
            to.name !== "admin.dashboard"
        ) {
            next("/admin/dashboard")
        }
    }

    next()
})

router.beforeResolve((to, from, next) => {
    if (to.name) {
    }
    next()
})

router.afterEach((to, from) => {})

export default router
