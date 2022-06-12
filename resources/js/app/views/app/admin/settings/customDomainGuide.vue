<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Custom Domain Guide</v-toolbar-title>
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
            enctype="multipart/form-data"
        >
            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text>
                <v-alert :value="form1.success" type="success" class="mb-4">
                    {{ $t("update_success") }}
                </v-alert>

                <ckeditor
                    :editor="ClassicEditor"
                    v-model="form1.content"
                    ref="guide"
                    label="Guide"
                    class="mb-3"
                    persistent-hint
                    :config="ckeditor.config"
                >
                </ckeditor>

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
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {
            ClassicEditor: ClassicEditor,
            show_current_password: false,
            ckeditor: {
                config: {
                    toolbar: ["bold", "italic", "|", "link"]
                }
            },
            form1: {
                loading: false,
                current_password: "",
                id: "",
                content: "",
                has_error: false,
                error: null,
                errors: {},
                success: false
            }
        };
    },
    created() {
        axios
            .get("/admin/domain-guide", {
                params: { locale: this.$i18n.locale }
            })
            .then(response => {
                this.form1.id = response.data.id;
                this.form1.content = response.data.content;
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
                    this.update(formName);
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
        update(formName) {
            var app = this[formName];

            axios
                .post("/admin/domain-guide", {
                    locale: this.$i18n.locale,
                    current_password: app.current_password,
                    id: app.id,
                    content: app.content
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
