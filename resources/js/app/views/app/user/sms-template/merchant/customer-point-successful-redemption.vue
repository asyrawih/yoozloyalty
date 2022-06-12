<template>
  <v-form
    data-vv-scope="form1"
    :model="form1"
    id="form1"
    lazy-validation
    @submit.prevent="submitForm('form1')"
    autocomplete="off"
    method="post"
    accept-charset="UTF-8"
    enctype="multipart/form-data"
  >
    <v-divider class="grey lighten-2"></v-divider>
    <v-row>
      <v-col cols="3">
        <v-card outlined>
          <v-card-title class="subtitle-2"> Variables </v-card-title>
          <v-card-text>
            <v-btn
              small
              depressed
              color="primary"
              class="d-block my-2"
              @click="variableSet('{{ user_point }}')"
              >User point</v-btn
            >
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="9">
        <v-card outlined>
          <v-card-text>
            <v-textarea
              outlined
              dense
              counter
              ref="template_form"
              label="Template"
              @focus="focus_element = 'template_form'"
              v-model="form1.template_form"
              data-vv-name="template_form"
              data-vv-as="Template"
              v-validate="'required|max:160'"
              height="100px"
              :error-messages="errors.collect('template_form')"
              :rules="rules"
            >
            </v-textarea>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-text-field
      outlined
      class="mt-4"
      v-model="form1.current_password"
      data-vv-name="current_password"
      :data-vv-as="$t('current_password').toLowerCase()"
      v-validate="'required|min:8|max:24'"
      :label="$t('current_password')"
      :error-messages="errors.collect('form1.current_password')"
      :type="show_current_password ? 'text' : 'password'"
      :append-icon="show_current_password ? 'visibility' : 'visibility_off'"
      @click:append="show_current_password = !show_current_password"
      required
    ></v-text-field>

    <v-alert :value="form1.success" type="success">
      {{ form1.response_message }}
    </v-alert>
    <div class="d-flex">
      <v-spacer></v-spacer>
      <v-btn
        color="primary"
        large
        :loading="form1.loading"
        type="submit"
        class="mb-2"
        >{{ $t("update") }}</v-btn
      >
    </div>
  </v-form>
</template>

<script>
export default {
  props: ["template", "id"],
  data() {
    return {
      show_current_password: false,
      rules: [v => v.length <= 160 || 'Max 160 characters'],
      form1: {
        response_message : '',
        loading: false,
        current_password: "",
        id: "",
        template_form: "",
        has_error: false,
        error: null,
        errors: {},
        success: false,
      },
    };
  },
  watch: {
    template: function (newValue, oldValue) {
      this.form1.template_form = newValue;
    },
    id: function (newValue, oldValue) {
      this.form1.id = newValue;
    },
  },
  mounted() {
    this.form1.template_form = this.template;
    this.form1.id = this.id;
  },
  methods: {
    variableSet(value) {
      const focus_element = this.focus_element;
      const inputField = this.$refs[focus_element];
      if (inputField) {
        inputField.focus();
        const activeElement = document.activeElement;
        const [start, end] = [
          activeElement.selectionStart,
          activeElement.selectionEnd,
        ];

        this.$nextTick(() => {
          activeElement.setRangeText(value, start, end, "end");
          this.form1[focus_element] = activeElement.value;
        });
      }
    },
    submitForm(formName) {
      this[formName].success = false;
      this[formName].has_error = false;
      this[formName].loading = true;

      this.$validator.validateAll(formName).then((valid) => {
        if (valid) {
          this.updateTemplate(formName);
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
    updateTemplate(formName) {
      var app = this[formName];

      axios
        .post("/merchant/sms-template", {
          locale: this.$i18n.locale,
          current_password: app.current_password,
          type: "customer_point_successful_redemption",
          id: app.id,
          template: app.template_form,
        })
        .then((response) => {
          if (response.data.success === true) {
            this.form1.response_message = response.data.message
            app.success = true;
          }
          app.loading = false;
        })
        .catch((err) => {
          let errors = err.response.data.errors || {};
          let i = 0;
          for (let field in errors) {
            if (i == 0) {
              // Get first error and select tab where error occurs
              let el =
                typeof this.$refs[field] !== "undefined"
                  ? this.$refs[field]
                  : null;
              let tab = el !== null ? el.$parent.$vnode.key : null;
              if (tab !== null) this.selectedTab = tab;
            }
            i++;
            this.$validator.errors.add({
              field: formName + "." + field,
              msg: errors[field][0],
            });
          }
          app.loading = false;
        });
    },
  },
};
</script>
