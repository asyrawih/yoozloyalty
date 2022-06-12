/**
 * Bootstrap JavaScript dependencies that are available in the global scope.
 */

require("./bootstrap");

import 'babel-polyfill';
import Vue from "vue";

/**
 * Import libraries that are used by the application.
 */
import VueCompositionAPI from '@vue/composition-api';
import vuetify from "./plugins/vuetify";
import Cookies from "js-cookie";
import VueAuth from "@websanova/vue-auth";
import VueAxios from "vue-axios";
import VueRouter from "vue-router";
import Vuex from "vuex";
import { i18n } from "./plugins/i18n";
import VeeValidate from "vee-validate";
import VueQRCodeComponent from "vue-qrcode-component";
import VueGallery from "vue-gallery";
import VueGoogleCharts from "vue-google-charts";
import CKEditor from "@ckeditor/ckeditor5-vue2";
import VueTheMask from "vue-the-mask";
import Pusher from "pusher-js";
import OneSignal from "onesignal-vue";

// Store
import store from "./store";

/**
 * Enable Vue libraries.
 */
Vue.use(VueCompositionAPI);
Vue.use(Vuex);
Vue.component("qr-code", VueQRCodeComponent);
Vue.use(VeeValidate);
Vue.component("vue-gallery", VueGallery);
Vue.use(VueGoogleCharts);
Vue.use(CKEditor);
Vue.use(VueTheMask);
Vue.use(OneSignal);

Vue.config.productionTip = false;

// Set Vue router
import router from "./routes.js";

Vue.router = router;
Vue.use(VueRouter);

// Set Vue authentication
import auth from "./api/auth.js";

Vue.use(VueAxios, axios);
Vue.use(VueAuth, auth);

/* Layout */
import App from "./views/layouts/AppLayout.vue";

// Custom components
import Confirm from "./views/components/ui/Confirm.vue";
Vue.component("confirm", Confirm);

// Custom components
import Snackbar from "./views/components/ui/Snackbar.vue";
Vue.component("snackbar", Snackbar);

// import VueTelInputVuetify from "./views/components/ui/VueTelInputVuetify/vue-tel-input-vuetify.vue";
// Vue.component("vue-tel-input-vuetify", VueTelInputVuetify);
import VueTelInputVuetify from "vue-tel-input-vuetify";
Vue.use(VueTelInputVuetify, { vuetify });

import DateTimePicker from "./views/components/ui/DateTimePicker.vue";
Vue.component("date-time-picker", DateTimePicker);

import TimePicker from "./views/components/ui/TimePicker.vue";
Vue.component("time-picker", TimePicker);

import DateRangePicker from "./views/components/ui/DateRangePicker.vue";
Vue.component("date-range-picker", DateRangePicker);

import ColorPicker from "./views/components/ui/ColorPicker.vue";
Vue.component("color-picker", ColorPicker);

import DataTable from "./views/components/DataTable.vue";
Vue.component("data-table", DataTable);

import DataForm from "./views/components/DataForm.vue";
Vue.component("data-form", DataForm);

import DataDetail from "./views/components/DataDetail.vue";
Vue.component("data-detail", DataDetail);

import CreditRequestStatus from "./views/components/ui/CreditRequestStatus.vue";
Vue.component("credit-request-status", CreditRequestStatus);

/**
 * Initialize the app.
 */
const app = new Vue({
    vuetify,
    i18n,
    router,
    store,
    render: h => h(App)
}).$mount("#app");
