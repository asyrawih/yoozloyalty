<template>
  <v-card>
    <v-toolbar tabs flat>
      <v-toolbar-title>Broadcast Notification</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>

    <v-form
      data-vv-scope="form1"
      :model="form1"
      id="form1"
      lazy-validation
      @submit.prevent="submitForm('form1')"
      autocomplete="off"
      method="post"
      accept-charset="UTF-8"
    >
      <v-divider class="grey lighten-2"></v-divider>
      <v-card-text>
        <v-alert
          :value="form1.has_error && !form1.success"
          type="error"
          class="mb-4"
        >
            {{form1.respone_message}}
        </v-alert>

        <v-alert :value="form1.success" type="success" class="mb-4">
          {{form1.respone_message}}
        </v-alert>

        <v-select
          v-model="selectedMerchant"
          :items="merchants"
          label="Select Merchant"
          multiple
          data-vv-name="merchants"
          data-vv-as="merchants"
          ref="merchants"
          name="merchants"
          v-validate="'required'"
        >
          <template v-slot:prepend-item>
            <v-list-item ripple @click="toggle">
              <v-list-item-action>
                <v-icon
                  :color="selectedMerchant.length > 0 ? 'indigo darken-4' : ''"
                >
                  {{ icon }}
                </v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title> Select All </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-divider class="mt-2"></v-divider>
          </template>
          <template v-slot:append-item>
            <v-divider class="mb-2"></v-divider>
            <v-list-item disabled>
              <v-list-item-avatar color="grey lighten-3">
                <v-icon> mdi-account </v-icon>
              </v-list-item-avatar>

              <v-list-item-content v-if="likesAllMerchant">
                <v-list-item-title>
                  All Merchant are selected.
                </v-list-item-title>
              </v-list-item-content>

              <v-list-item-content v-else-if="likesSomeMerchant">
                <v-list-item-title> Merchant Count </v-list-item-title>
                <v-list-item-subtitle>
                  {{ selectedMerchant.length }}
                </v-list-item-subtitle>
              </v-list-item-content>

              <v-list-item-content v-else>
                <v-list-item-title>
                  Please select atleast one merchant!
                </v-list-item-title>
                <v-list-item-subtitle>
                  Go ahead, make a selection above!
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </template>
        </v-select>

        <v-text-field
          v-model="form1.title"
          data-vv-name="title"
          data-vv-as="title"
          ref="title"
          type="text"
          label="Title"
          v-validate="'required'"
          :error-messages="errors.collect('form1.title')"
          class="mb-3"
          persistent-hint
        ></v-text-field>
        <v-textarea
          name="message"
          label="Message"
          v-model="form1.message"
          data-vv-name="message"
          data-vv-as="message"
          ref="message"
          v-validate="'required'"
          :error-messages="errors.collect('form1.message')"
        ></v-textarea>
      </v-card-text>

      <v-card-actions class="mx-2">
        <v-spacer></v-spacer>
        <v-btn
          :color="app.color_name"
          large
          :loading="form1.loading"
          type="submit"
          class="mb-2"
          >Send Broadcast</v-btn
        >
      </v-card-actions>
    </v-form>
  </v-card>
</template>
<script>
import _ from 'lodash';
export default {
  $_veeValidate: {
    validator: "new",
  },
  data: () => ({
    merchants: [],
    selectedMerchant: [],
    form1: {
      loading: false,
      title: "",
      message: "",
      has_error: false,
      error: null,
      errors: {},
      success: false,
      merchants: [],
      respone_message:""
    },
  }),

  computed: {
    app() {
      return this.$store.getters.app;
    },
    likesAllMerchant() {
      return this.selectedMerchant.length === this.merchants.length;
    },
    likesSomeMerchant() {
      return this.selectedMerchant.length > 0 && !this.likesAllMerchant;
    },
    icon() {
      if (this.likesAllMerchant) return "mdi-close-box";
      if (this.likesSomeMerchant) return "mdi-minus-box";
      return "mdi-checkbox-blank-outline";
    },
  },
  created() {
    axios.get("/admin/get-dropdown-merchant").then((response) => {
      this.merchants = response.data.data;

      this.loading = false;
    });
  },
  methods: {
    toggle() {
      this.$nextTick(() => {
        if (this.likesAllMerchant) {
          this.selectedMerchant = [];
        } else {
          this.selectedMerchant = _.map(this.merchants, 'value');
        }
      });
    },
    submitForm(formName) {
      this[formName].success = false;
      this[formName].has_error = false;
      this[formName].loading = true;
      this[formName].merchants = this.$refs.merchants.value;

      this.$validator.validateAll(formName).then((valid) => {
        if (valid) {
          this.sendBroadcast(formName);
        } else {
          // Get first error and select tab where error occurs
          let field = this.errors.items[0].field;
          let el =
            typeof this.$refs[field] !== "undefined" ? this.$refs[field] : null;
          let tab = el !== null ? el.$parent.$vnode.key : null;
          if (tab !== null) this.selectedTab = tab;

          this[formName].loading = false;
          return false;
        }
      });
    },
    sendBroadcast(formName) {
      var app = this[formName];

      axios
        .post("/admin/send-broadcast-notication", {
          title: app.title,
          message: app.message,
          merchants: app.merchants,
        })
        .then((response) => {
          if (response.status === 200) {
            app.success = true;
            app.respone_message = response.data.message;
          }else{
            app.error = true
          }
          app.loading = false;
        })
        .catch((err) => {
          let error = err.response.data || {};
          app.error = true;
          app.has_error = true;
          app.respone_message = error.message;
          app.loading = false;
        });
    },
  },
};
</script>
<style scoped></style>
