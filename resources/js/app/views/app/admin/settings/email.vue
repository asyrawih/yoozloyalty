<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Email</v-toolbar-title>
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
            <v-card-text class="px-0">
                <div class="px-3">
                    <v-alert
                        :value="form1.has_error && !form1.success"
                        type="error"
                        class="mb-4"
                    >
                        <span
                            v-if="
                                form1.error == 'registration_validation_error'
                            "
                            >{{ $t("server_error") }}</span
                        >
                        <span v-else>{{ $t("correct_errors") }}</span>
                    </v-alert>

                    <v-alert :value="form1.success" type="success" class="mb-4">
                        {{ $t("update_success") }}
                    </v-alert>

                    <v-text-field
                        v-model="form1.mail_from_name"
                        data-vv-name="mail_from_name"
                        data-vv-as="mail from name"
                        ref="mail_from_name"
                        label="Mail from name"
                        v-validate="'required'"
                        :error-messages="errors.collect('form1.mail_from_name')"
                        class="mb-3"
                        hint="Name used for sending automated e-mails to users"
                        persistent-hint
                    ></v-text-field>

                    <v-text-field
                        v-model="form1.mail_from_address"
                        data-vv-name="mail_from_address"
                        data-vv-as="mail from address"
                        ref="mail_from_address"
                        type="email"
                        label="Mail from address"
                        v-validate="'required|max:64|email'"
                        :error-messages="
                            errors.collect('form1.mail_from_address')
                        "
                        class="mb-3"
                        hint="Address used for sending automated e-mails to users"
                        persistent-hint
                    ></v-text-field>

                    <v-text-field
                        v-model="form1.mail_contact"
                        data-vv-name="mail_contact"
                        data-vv-as="mail from contact"
                        ref="mail_contact"
                        type="email"
                        label="Mail contact"
                        v-validate="'required|max:64|email'"
                        :error-messages="errors.collect('form1.mail_contact')"
                        class="mb-3"
                        hint="E-mail address published on website for contact"
                        persistent-hint
                    ></v-text-field>
                </div>
                <v-divider class="mt-3"></v-divider>
                <div class="px-3">
                    <v-select
                        :items="['smtp', 'mailgun']"
                        v-model="form1.mail_driver"
                        data-vv-name="mail_driver"
                        data-vv-as="smtp setting"
                        ref="mail_driver"
                        label="SMTP Setting"
                        v-validate="'required'"
                        :error-messages="errors.collect('form1.mail_driver')"
                        class="mb-3"
                        persistent-hint
                    ></v-select>

                    <v-text-field
                        v-model="form1.mail_host"
                        data-vv-name="mail_host"
                        data-vv-as="mail host"
                        ref="mail_host"
                        label="Mail host"
                        v-validate="'required'"
                        :error-messages="errors.collect('form1.mail_host')"
                        class="mb-3"
                        persistent-hint
                    ></v-text-field>

                    <v-text-field
                        v-model="form1.mail_port"
                        data-vv-name="mail_port"
                        data-vv-as="mail port"
                        ref="mail_port"
                        label="Mail port"
                        v-validate="'required'"
                        :error-messages="errors.collect('form1.mail_port')"
                        class="mb-3"
                        persistent-hint
                    ></v-text-field>

                    <v-text-field
                        v-model="form1.mail_encryption"
                        data-vv-name="mail_encryption"
                        data-vv-as="mail encryption"
                        ref="mail_encryption"
                        label="Mail encryption"
                        v-validate="'required'"
                        :error-messages="
                            errors.collect('form1.mail_encryption')
                        "
                        class="mb-3"
                        persistent-hint
                    ></v-text-field>

                    <v-text-field
                        v-model="form1.mail_username"
                        data-vv-name="mail_username"
                        data-vv-as="mail username"
                        ref="mail_username"
                        label="Mail username"
                        :error-messages="errors.collect('form1.mail_username')"
                        class="mb-3"
                        persistent-hint
                    ></v-text-field>

                    <v-text-field
                        v-model="form1.mail_password"
                        data-vv-name="mail_password"
                        data-vv-as="mail password"
                        ref="mail_password"
                        label="Mail password"
                        :error-messages="errors.collect('form1.mail_password')"
                        class="mb-3"
                        persistent-hint
                    ></v-text-field>

                    <template v-if="form1.mail_driver === 'mailgun'">
                        <v-text-field
                            v-model="form1.mailgun_domain"
                            data-vv-name="mailgun_domain"
                            data-vv-as="mail domain"
                            ref="mailgun_domain"
                            label="Mailgun domain"
                            :error-messages="
                                errors.collect('form1.mailgun_domain')
                            "
                            class="mb-3"
                            persistent-hint
                        ></v-text-field>
                        <v-text-field
                            v-model="form1.mailgun_secret"
                            data-vv-name="mailgun_secret"
                            data-vv-as="mailgun secret"
                            ref="mailgun_secret"
                            label="Mailgun secret"
                            :error-messages="
                                errors.collect('form1.mailgun_secret')
                            "
                            class="mb-3"
                            persistent-hint
                        ></v-text-field>
                        <v-text-field
                            v-model="form1.mailgun_endpoint"
                            data-vv-name="mailgun_endpoint"
                            data-vv-as="mailgun endpoint"
                            ref="mailgun_endpoint"
                            label="Mailgun endpoint"
                            :error-messages="
                                errors.collect('form1.mailgun_endpoint')
                            "
                            class="mb-3"
                            persistent-hint
                        ></v-text-field>
                    </template>

                    <v-text-field
                        outlined
                        class="mt-4"
                        v-model="form1.current_password"
                        data-vv-name="current_password"
                        :data-vv-as="$t('current_password').toLowerCase()"
                        v-validate="'required|min:8|max:24'"
                        :label="$t('current_password')"
                        :error-messages="
                            errors.collect('form1.current_password')
                        "
                        :type="show_current_password ? 'text' : 'password'"
                        :append-icon="
                            show_current_password
                                ? 'visibility'
                                : 'visibility_off'
                        "
                        @click:append="
                            show_current_password = !show_current_password
                        "
                        required
                    ></v-text-field>
                </div>
            </v-card-text>

            <v-card-actions class="mx-2">
                <v-spacer></v-spacer>

                <v-dialog
                    persistent
                    v-model="test_mail.dialog"
                    width="480"
                    :fullscreen="$vuetify.breakpoint.xsOnly"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            :color="app.color_name"
                            large
                            v-bind="attrs"
                            v-on="on"
                            class="mb-2"
                        >
                            Test Email
                        </v-btn>
                    </template>

                    <v-form
                        data-vv-scope="form2"
                        :model="form2"
                        id="form2"
                        lazy-validation
                        @submit.prevent="submitForm('form2')"
                        autocomplete="off"
                        method="post"
                        accept-charset="UTF-8"
                    >
                        <v-card>
                            <v-toolbar flat color="transparent">
                                <v-toolbar-title>Test Email</v-toolbar-title>
                            </v-toolbar>

                            <v-card-text>
                                <v-alert :value="form2.success" type="success" class="mb-4">
                                    {{ form2.successMsg }}
                                </v-alert>

                                <v-text-field
                                    v-model="form2.mail_to_name"
                                    data-vv-name="mail_to_name"
                                    data-vv-as="mail to name"
                                    ref="mail_to_name"
                                    label="Mail to name"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('form2.mail_to_name')"
                                    class="mb-3"
                                    clearable
                                ></v-text-field>

                                <v-text-field
                                    v-model="form2.mail_to_address"
                                    data-vv-name="mail_to_address"
                                    data-vv-as="mail to address"
                                    ref="mail_to_address"
                                    type="email"
                                    label="Mail to address"
                                    v-validate="'required|max:64|email'"
                                    :error-messages="
                                        errors.collect('form2.mail_to_address')
                                    "
                                    clearable
                                    class="mb-3"
                                ></v-text-field>
                            </v-card-text>

                            <v-divider></v-divider>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    :color="app.color_name"
                                    text
                                    v-show="!form2.loading"
                                    @click="dialogTestEmailToggle('form2')"
                                >
                                    Cancel
                                </v-btn>

                                <v-btn
                                    :color="app.color_name"
                                    text
                                    :loading="form2.loading"
                                    @click="submitForm('form2')"
                                >
                                    Send Mail
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-dialog>

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
import axios from 'axios';
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
                mail_from_name: "",
                mail_from_address: "",
                mail_contact: "",
                mail_driver: "",
                mail_host: "",
                mail_port: "",
                mail_encryption: "",
                mail_username: "",
                mail_password: "",
                mailgun_domain: "",
                mailgun_secret: "",
                mailgun_endpoint: "",
                has_error: false,
                error: null,
                errors: {},
                success: false,
            },
            form2: {
                loading: false,
                mail_to_name: "",
                mail_to_address: "",
                has_error: false,
                error: null,
                errors: {},
                success: false,
                successMsg: ""
            },
            test_mail: {
                dialog: false,
            }
        };
    },
    created() {
        axios
            .get("/admin/email", { params: { locale: this.$i18n.locale } })
            .then(response => {
                this.form1.mail_from_name = response.data.mail_from_name;
                this.form1.mail_from_address = response.data.mail_from_address;
                this.form1.mail_contact = response.data.mail_contact;
                this.form1.mail_driver = response.data.mail_driver;
                this.form1.mail_host = response.data.mail_host;
                this.form1.mail_port = response.data.mail_port;
                this.form1.mail_encryption = response.data.mail_encryption;
                this.form1.mail_username = response.data.mail_username;
                this.form1.mail_password = response.data.mail_password;
                this.form1.mailgun_domain = response.data.mailgun_domain;
                this.form1.mailgun_secret = response.data.mailgun_secret;
                this.form1.mailgun_endpoint = response.data.mailgun_endpoint;
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
                    if (formName === 'form1') {
                        this.updateTrial(formName);
                    } else if (formName === 'form2') {
                        this.sendTestEmail(formName);
                    }
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
        dialogTestEmailToggle(formName) {
            var app = this[formName];

            this.$validator.reset();

            app.loading = false;
            app.mail_to_name = "";
            app.mail_to_address = "";
            app.has_error = false;
            app.error = null;
            app.errors = {};
            app.success = false;
            app.successMsg = '';

            this.test_mail.dialog = !this.test_mail.dialog;
        },
        sendTestEmail(formName) {
            var app = this[formName];

            axios
                .post("/admin/testing-email", {
                    mail_to_name: app.mail_to_name,
                    mail_to_address: app.mail_to_address,
                })
                .then(response => {
                    if (response.data.status === "success") {
                        app.success = true;
                        app.successMsg = response.data.msg;
                    }

                    app.loading = false;
                    app.has_error = false;
                    app.error = null;
                    app.errors = {};
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
        },
        updateTrial(formName) {
            var app = this[formName];

            axios
                .post("/admin/email", {
                    locale: this.$i18n.locale,
                    current_password: app.current_password,
                    mail_from_name: app.mail_from_name,
                    mail_from_address: app.mail_from_address,
                    mail_contact: app.mail_contact,
                    mail_driver: app.mail_driver,
                    mail_host: app.mail_host,
                    mail_port: app.mail_port,
                    mail_encryption: app.mail_encryption,
                    mail_username: app.mail_username,
                    mail_password: app.mail_password,
                    mailgun_domain: app.mailgun_domain,
                    mailgun_secret: app.mailgun_secret,
                    mailgun_endpoint: app.mailgun_endpoint
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
