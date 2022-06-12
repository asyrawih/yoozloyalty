<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Push Notification</v-toolbar-title>
            <v-spacer></v-spacer>
            <template v-slot:extension>
                <v-tabs
                    v-model="selected_tab"
                    :slider-color="app.color_name"
                    color="grey darken-3"
                    show-arrows
                >
                    <v-tab :href="'#pusher'">
                        Pusher
                    </v-tab>
                </v-tabs>
            </template>
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
            enctype="multipart/form-data"
        >
            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text>
                <v-alert :value="form1.success" type="success" class="mb-4">
                    {{ $t("update_success") }}
                </v-alert>

                <v-tabs-items
                    v-model="selected_tab"
                    :touchless="false"
                    class="mx-2"
                >
                    <v-tab-item :value="'pusher'">
                        <v-text-field
                            v-model="form1.pusher_app_id"
                            data-vv-name="pusher_app_id"
                            data-vv-as="App ID"
                            ref="pusher_app_id"
                            v-validate="'required'"
                            label="App ID"
                            :error-messages="
                                errors.collect('form1.pusher_app_id')
                            "
                            class="mb-3"
                            persistent-hint
                        ></v-text-field>
                        <v-text-field
                            v-model="form1.pusher_app_key"
                            data-vv-name="pusher_app_key"
                            data-vv-as="App Key"
                            ref="pusher_app_key"
                            v-validate="'required'"
                            label="App Key"
                            :error-messages="
                                errors.collect('form1.pusher_app_key')
                            "
                            class="mb-3"
                            persistent-hint
                        ></v-text-field>
                        <v-text-field
                            v-model="form1.pusher_app_secret"
                            data-vv-name="pusher_app_secret"
                            data-vv-as="App Secret"
                            ref="pusher_app_secret"
                            v-validate="'required'"
                            label="App Secret"
                            :error-messages="
                                errors.collect('form1.pusher_app_secret')
                            "
                            class="mb-3"
                            persistent-hint
                        ></v-text-field>
                        <v-text-field
                            v-model="form1.pusher_app_cluster"
                            data-vv-name="pusher_app_cluster"
                            data-vv-as="App Cluster"
                            ref="pusher_app_cluster"
                            v-validate="'required'"
                            label="App Cluster"
                            :error-messages="
                                errors.collect('form1.pusher_app_cluster')
                            "
                            class="mb-3"
                            persistent-hint
                        ></v-text-field>
                    </v-tab-item>
                </v-tabs-items>

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
            selected_tab: "pusher",
            show_current_password: false,
            form1: {
                loading: false,
                current_password: "",
                pusher_app_id: "",
                pusher_app_key: "",
                pusher_app_secret: "",
                pusher_app_cluster: "",
                has_error: false,
                error: null,
                errors: {},
                success: false
            }
        };
    },
    created() {
        axios
            .get("/admin/push-notif", { params: { locale: this.$i18n.locale } })
            .then(response => {
                this.form1.pusher_app_id = response.data.pusher_app_id;
                this.form1.pusher_app_key = response.data.pusher_app_key;
                this.form1.pusher_app_secret = response.data.pusher_app_secret;
                this.form1.pusher_app_cluster =
                    response.data.pusher_app_cluster;
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
                .post("/admin/push-notif", {
                    locale: this.$i18n.locale,
                    current_password: app.current_password,
                    pusher_app_id: app.pusher_app_id,
                    pusher_app_key: app.pusher_app_key,
                    pusher_app_secret: app.pusher_app_secret,
                    pusher_app_cluster: app.pusher_app_cluster
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
