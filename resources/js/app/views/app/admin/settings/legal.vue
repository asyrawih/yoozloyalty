<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Legal</v-toolbar-title>
            <v-spacer></v-spacer>
            <template v-slot:extension>
                <v-flex>
                    <v-tabs
                        v-model="selectedTab"
                        :slider-color="app.color_name"
                        color="grey darken-3"
                        show-arrows
                    >
                        <v-tab v-for="tab in tabs" :key="'tab-'+tab.name">
                            {{ tab.text }}
                        </v-tab>
                    </v-tabs>
                </v-flex>
            </template>
        </v-toolbar>

        <v-form
            data-vv-scope="form"
            :model="form"
            id="form"
            lazy-validation
            @submit.prevent="submitForm('form')"
            autocomplete="off"
            method="post"
            accept-charset="UTF-8"
        >
            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text v-if="$auth.user().demo">
                <v-alert :color="app.color_name" class="mb-0 mt-2">
                    This account is in demo mode. You can't save any settings on
                    this page.
                </v-alert>
            </v-card-text>

            <v-card-text>
                <v-alert
                    :value="form.has_error && !form.success"
                    type="error"
                    class="mb-4"
                >
                    <span
                        v-if="form.error == 'registration_validation_error'"
                        >{{ $t("server_error") }}</span
                    >
                    <span v-else>{{ $t("correct_errors") }}</span>
                </v-alert>

                <v-alert :value="form.success" type="success" class="mb-4">
                    {{ $t("update_success") }}
                </v-alert>

                <v-tabs-items
                    v-model="selectedTab"
                    :touchless="false"
                    class="mx-2"
                >
                    <v-tab-item
                        v-for="tab in tabs"
                        :key="'tab-'+tab.name"
                        :eager="true"
                    >
                        <p class="mb-1">Content</p>
                        <editor
                            :content.sync="tab.content"
                            :error-messages="errors.collect('form1.privay_policy.content')"
                        >
                        </editor>
                    </v-tab-item>
                </v-tabs-items>
            </v-card-text>

            <v-card-actions class="mx-2">
                <v-spacer></v-spacer>
                <v-btn
                    :color="app.color_name"
                    large
                    :loading="form.loading"
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
import Editor from "./editor";

export default {
    components: {Editor},
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {
            selectedTab: "privacy_policy",
            tabs: [
                {
                    name: "privacy_policy",
                    content: "",
                    text: "Privacy Policy"
                },
                {
                    name: "user_agreement",
                    content: "",
                    text: "User Agreement"
                },
                {
                    name: "contact_us",
                    content: "",
                    text: "Contact Us"
                },
                {
                    name: "refund_policy",
                    content: "",
                    text: "Refund Policy"
                }
            ],
            account_host: "",
            form: {
                loading: false,
                content: "",
                has_error: false,
                error: null,
                errors: {},
                success: false
            },
            editor: ClassicEditor,
            editorConfig: {
                toolbar: ["bold", "italic"]
            }
        };
    },
    created() {
        this.getLegal()
    },
    methods: {
        submitForm(formName) {
            this[formName].success = false;
            this[formName].has_error = false;
            this[formName].loading = true;

            this.$validator.validateAll(formName).then(valid => {
                if (valid) {
                    this.updateLegal(formName);
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
        updateLegal(formName) {
            let app = this[formName];
            app.content = this.tabs[this.selectedTab].content;
            let type = this.tabs[this.selectedTab].name;

            axios
                .post("/admin/legal", {
                    locale: this.$i18n.locale,
                    content: app.content,
                    type: type
                })
                .then(response => {
                    if (response.data.status === "success") {
                        app.success = true;
                    }
                    app.loading = false;
                    this.getLegal();
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
        getLegal(){
            axios
                .get("/admin/legal", { params: { locale: this.$i18n.locale } })
                .then(response => {
                    response.data.data.forEach(legal => {
                        let tab = this.tabs.find(item => item.name === legal.type)
                        if(tab){
                            tab.content = legal.content
                        }
                    })
                    this.loading = false;
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
