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
          <v-tab :href="'#customer_registration'">
            Customer Registration
          </v-tab>
          <v-tab :href="'#customer_point_credit'">
            Customer Point Credit
          </v-tab>
          <v-tab :href="'#customer_point_redeem'">
            Customer Point Redeem
          </v-tab>
          <v-tab :href="'#customer_point_successful_redemption'">
            Customer Point Successful Redemption
          </v-tab>
          <v-tab :href="'#customer_birthday'">
            Customer Birthday
          </v-tab>
        </v-tabs>
      </template>
    </v-toolbar>
    <v-card-text>
      <v-tabs-items v-model="selected_tab" :touchless="false" class="mx-2">
        <v-tab-item :value="'customer_registration'">
          <CustomerRegistration
            :id="form1.customer_registration.id"
            :template="form1.customer_registration.template"
          />
        </v-tab-item>
        <v-tab-item :value="'customer_point_credit'">
          <CustomerPointCredit
            :id="form1.customer_point_credit.id"
            :template="form1.customer_point_credit.template"
          />
        </v-tab-item>
        <v-tab-item :value="'customer_point_redeem'">
          <CustomerPointRedeem
            :id="form1.customer_point_redeem.id"
            :template="form1.customer_point_redeem.template"
          />
        </v-tab-item>
        <v-tab-item :value="'customer_point_successful_redemption'">
          <CustomerPointSuccessfulRedemption
            :id="form1.customer_point_successful_redemption.id"
            :template="form1.customer_point_successful_redemption.template"
          />
        </v-tab-item>
        <v-tab-item :value="'customer_birthday'">
          <CustomerBirthday
            :id="form1.customer_birthday.id"
            :template="form1.customer_birthday.template"
          />
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>

<script>
import CustomerRegistration from "./merchant/customer-registration";
import CustomerPointCredit from "./merchant/customer-point-credit";
import CustomerPointRedeem from "./merchant/customer-point-redeem";
import CustomerPointSuccessfulRedemption from "./merchant/customer-point-successful-redemption";
import CustomerBirthday from './merchant/customer-birthday';

export default {
  components: {
    CustomerRegistration,
    CustomerPointCredit,
    CustomerPointRedeem,
    CustomerPointSuccessfulRedemption,
    CustomerBirthday
  },
  $_veeValidate: {
    validator: "new",
  },
  data() {
    return {
      show_current_password: false,
      selected_tab: "merchant",
      selected_tab_admin: "customer_registration",
      focus_element: "",
      form1: {
        loading: false,
        current_password: "",
        customer_registration: {
          id: "",
          template: "",
        },
        customer_point_credit: {
          id: "",
          template: "",
        },
        customer_point_redeem: {
          id: "",
          template: "",
        },
        customer_point_successful_redemption: {
          id: "",
          template: "",
        },
        customer_birthday: {
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
    axios.get("/merchant/sms-template").then((response) => {
      this.form1.customer_registration =
        response.data.data.customer_registration;
      this.form1.customer_point_credit =
        response.data.data.customer_point_credit;
      this.form1.customer_point_redeem =
        response.data.data.customer_point_redeem;
      this.form1.customer_point_successful_redemption =
        response.data.data.customer_point_successful_redemption;
        this.form1.customer_birthday = response.data.data.customer_birthday;
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
