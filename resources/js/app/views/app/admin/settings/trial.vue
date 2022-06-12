<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Trial</v-toolbar-title>
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
                    <span
                        v-if="form1.error == 'registration_validation_error'"
                        >{{ $t("server_error") }}</span
                    >
                    <span v-else>{{ $t("correct_errors") }}</span>
                </v-alert>

                <v-alert :value="form1.success" type="success" class="mb-4">
                    {{ $t("update_success") }}
                </v-alert>

                <v-text-field
                    v-model="form1.trial_days"
                    data-vv-name="trial_days"
                    data-vv-as="trial days"
                    ref="trial_days"
                    type="number"
                    label="Trial (day)"
                    v-validate="'required'"
                    :error-messages="errors.collect('form1.trial_days')"
                    class="mb-3"
                    hint="The value is used to defined trial day expired"
                    persistent-hint
                ></v-text-field>

                <!-- <v-text-field
                    v-model="form1.grace_period_days"
                    data-vv-name="grace_period_days"
                    data-vv-as="grace period days"
                    ref="grace_period_days"
                    type="number"
                    label="Grace period days"
                    v-validate="'required'"
                    :error-messages="errors.collect('form1.grace_period_days')"
                    class="mb-3"
                    hint="A grace period is a set length of time after the due date"
                    persistent-hint
                ></v-text-field> -->

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
                    :append-icon="
                        show_current_password ? 'visibility' : 'visibility_off'
                    "
                    @click:append="
                        show_current_password = !show_current_password
                    "
                    required
                ></v-text-field>
            </v-card-text>

            <v-card-actions class="mx-2">
                <v-spacer></v-spacer>
                <v-btn
                    :color="app.color_name"
                    large
                    :loading="form1.loading"
                    type="submit"
                    class="mb-2"
                    >{{ $t("update") }}</v-btn
                >
            </v-card-actions>
        </v-form>
    </v-card>
</template>
<script>
export default {
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {
            show_current_password: false,
            form1: {
                loading: false,
                current_password: "",
                trial_days: "",
                grace_period_days: "",
                has_error: false,
                error: null,
                errors: {},
                success: false
            }
        };
    },
    created() {
        axios
            .get("/admin/trial", { params: { locale: this.$i18n.locale } })
            .then(response => {
                this.form1.trial_days = response.data.trial_days;
                this.form1.grace_period_days = response.data.grace_period_days;

                this.loading = false;
            });
    },
    methods: {
        submitForm(formName) {
            this[formName].success = false;
            this[formName].has_error = false;
            this[formName].loading = true;

            this.$validator.validateAll(formName).then(valid => {
                if (valid) {
                    this.updateTrial(formName);
                } else {
                    // Get first error and select tab where error occurs
                    let field = this.errors.items[0].field;
                    let el =
                        typeof this.$refs[field] !== "undefined"
                            ? this.$refs[field]
                            : null;
                    let tab = el !== null ? el.$parent.$vnode.key : null;
                    if (tab !== null) this.selectedTab = tab;

                    this[formName].loading = false;
                    return false;
                }
            });
        },
        updateTrial(formName) {
            var app = this[formName];

            axios
                .post("/admin/trial", {
                    locale: this.$i18n.locale,
                    current_password: app.current_password,
                    trial_days: app.trial_days,
                    grace_period_days: app.grace_period_days
                })
                .then(response => {
                    if (response.data.status === "success") {
                        app.success = true;
                    }
                    app.loading = false;
                })
                .catch(err => {
                    let errors = err.response.data.errors || {};
                    let i = 0;
                    for (let field in errors) {
                        if (i == 0) {
                            // Get first error and select tab where error occurs
                            let el =
                                typeof this.$refs[field] !== "undefined"
                                    ? this.$refs[field]
                                    : null;
                            let tab =
                                el !== null ? el.$parent.$vnode.key : null;
                            if (tab !== null) this.selectedTab = tab;
                        }
                        i++;
                        this.$validator.errors.add({
                            field: formName + "." + field,
                            msg: errors[field][0]
                        });
                    }
                    app.loading = false;
                });
        }
    },
    computed: {
        app() {
            return this.$store.getters.app;
        },
        _() {
            return _;
        }
    }
};
</script>
<style scoped></style>
