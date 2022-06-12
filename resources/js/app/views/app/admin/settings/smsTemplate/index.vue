<template>
  <v-card>
    <v-toolbar tabs flat>
      <v-toolbar-title>SMS Template</v-toolbar-title>
      <v-spacer></v-spacer>
      <template v-slot:extension>
        <v-tabs
          v-model="selected_tab"
          :slider-color="app.color_name"
          color="grey darken-3"
          show-arrows
        >
          <v-tab :href="'#merchant_registration'">
            Merchant registration
          </v-tab>
          <v-tab :href="'#merchant_payment_confirmation'">
            Merchant Payment Confirmation
          </v-tab>
        </v-tabs>
      </template>
    </v-toolbar>
    <v-card-text>
      <v-tabs-items v-model="selected_tab" :touchless="false" class="mx-2">
        <v-tab-item :value="'merchant_registration'">
          <AdminMerchantregistration
            :id="form1.admin_merchant_registration.id"
            :template="form1.admin_merchant_registration.template"
          />
        </v-tab-item>
        <v-tab-item :value="'merchant_payment_confirmation'">
          <AdminMerchantPaymentConfirmation
            :id="form1.admin_merchant_payment_confirmation.id"
            :template="form1.admin_merchant_payment_confirmation.template"
          />
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
import AdminMerchantregistration from "./admin/merchant-registration";
import AdminMerchantPaymentConfirmation from "./admin/merchant-payment-confirmation";

export default {
  components: {
    AdminMerchantregistration,
    AdminMerchantPaymentConfirmation,
  },
  $_veeValidate: {
    validator: "new",
  },
  data() {
    return {
      show_current_password: false,
      selected_tab: "admin",
      selected_tab_admin: "merchant_registration",
      focus_element: "",
      form1: {
        loading: false,
        current_password: "",
        admin_merchant_registration: {
          id: "",
          template: "",
        },
        admin_merchant_payment_confirmation: {
          id: "",
          template: "",
        },
        variable: [],
        has_error: false,
        error: null,
        errors: {},
        success: false,
      },
    };
  },
  created() {
    axios.get("/admin/sms-template").then((response) => {
      // Admin
      this.form1.admin_merchant_registration =
        response.data.data.merchant_registration;
      this.form1.admin_merchant_payment_confirmation =
        response.data.data.merchant_payment_confirmation;
        
      this.form1.loading = false;
    });
  },
  methods: {},
  computed: {
    app() {
      return this.$store.getters.app;
    },
    _() {
      return _;
    },
  },
};
</script>
<style scoped></style>
