import Vue from 'vue'
import Router from 'vue-router'
import NProgress from 'nprogress'
import VueAnalytics from 'vue-analytics'

NProgress.configure({ showSpinner: false });

/* Layout */
import SiteLayout from './views/layouts/SiteLayout'

/* Public pages */
import Home from './views/home'
import Features from './views/features'
import Pricing from './views/pricing'
import Legal from './views/legal'
import PrivacyPolicy from './views/legal/privacy_policy'
import UserAgreement from './views/legal/user_agreement'
import ContactUs from './views/legal/contact_us'
import RefundPolicy from './views/legal/refund_policy'

/* Routes */
export const constantRouterMap = [
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: '',
      name: 'home',
      components: {
        primary: Home
      }
    }]
  },
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: 'features',
      name: 'features',
      components: {
        primary: Features
      }
    }]
  },
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: 'pricing',
      name: 'pricing',
      components: {
        primary: Pricing
      }
    }]
  },
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: 'legal',
      name: 'legal',
      components: {
        primary: Legal
      }
    }]
  },
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: 'privacy_policy',
      name: 'privacy_policy',
      components: {
        primary: PrivacyPolicy
      }
    }]
  },
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: 'user_agreement',
      name: 'user_agreement',
      components: {
        primary: UserAgreement
      }
    }]
  },
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: 'contact_us',
      name: 'contact_us',
      components: {
        primary: ContactUs
      }
    }]
  },
  {
    path: '/',
    component: SiteLayout,
    children: [{
      path: 'refund_policy',
      name: 'refund_policy',
      components: {
        primary: RefundPolicy
      }
    }]
  },
  { path: '*', redirect: '/', hidden: true } // Catch unkown routes
]

const router = new Router({
  mode: 'history',
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

/*
Vue.use(VueAnalytics, {
  id: 'UA-xxxxxxxxx-x',
  router
})
*/

// This callback runs before every route change, including on page load.
/*
router.beforeEach((to, from, next) => {
  next()
})
*/

router.beforeResolve((to, from, next) => {
  if (to.name) {
      NProgress.start()
  }
  next()
})

router.afterEach((to, from) => {
  NProgress.done()
})

export default router
