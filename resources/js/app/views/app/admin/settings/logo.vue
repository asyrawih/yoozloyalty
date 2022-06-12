<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Store Setting</v-toolbar-title>
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
                <v-alert
                    :value="form1.has_error && !form1.success"
                    type="error"
                    class="mb-4"
                >
                    <span
                        v-if="form1.error === 'registration_validation_error'"
                        >{{ $t("server_error") }}</span
                    >
                    <span v-else-if="form1.error === 'demo'"
                        >This is a demo user. You can't update or delete
                        anything this account. If you want to test all user
                        features, sign up with a new account.</span
                    >
                    <span v-else>{{ $t("correct_errors") }}</span>
                </v-alert>

                <v-alert :value="form1.success" type="success" class="mb-4">
                    {{ $t("update_success") }}
                </v-alert>
                <h4 class="my-3">Logo</h4>
                <v-btn
                    v-if="showDeleteLogo"
                    small
                    fab
                    color="ma-0 red darken-2 white--text"
                    @click="
                        form1.logo_media_url = defaultImage;
                        form1.logo_media_changed = true;
                        showDeleteLogo = false;
                    "
                    style="position: absolute; z-index: 9"
                    ><v-icon small>delete</v-icon></v-btn
                >
                <v-btn
                    v-else
                    small
                    fab
                    color="ma-0 blue darken-2 white--text"
                    @click="pickFile('logo')"
                    style="position: absolute; z-index: 9"
                    ><v-icon small>add</v-icon></v-btn
                >
                <v-img
                    :src="form1.logo_media_url"
                    class="mb-3 img-rounded"
                    style="width: 96px; height: 96px;"
                    @click="pickFile('logo')"
                ></v-img>

                <input
                    type="file"
                    style="display: none"
                    id="logo"
                    name="logo"
                    accept=".png, .jpg"
                    @change="onFilePicked"
                />

                <p>Allowed file extensions: .JPG and .PNG</p>

                <h4 class="my-3">Favicon</h4>
                <v-btn
                    v-if="showDeleteFavicon"
                    small
                    fab
                    color="ma-0 red darken-2 white--text"
                    @click="
                        form1.favicon_media_url = defaultImage;
                        form1.favicon_media_changed = true;
                        showDeleteFavicon = false;
                    "
                    style="position: absolute; z-index: 9"
                    ><v-icon small>delete</v-icon></v-btn
                >
                <v-btn
                    v-else
                    small
                    fab
                    color="ma-0 blue darken-2 white--text"
                    @click="pickFile('favicon')"
                    style="position: absolute; z-index: 9"
                    ><v-icon small>add</v-icon></v-btn
                >
                <v-img
                    :src="form1.favicon_media_url"
                    class="mb-3 img-rounded"
                    style="width: 96px; height: 96px"
                    @click="pickFile('favicon')"
                ></v-img>

                <input
                    type="file"
                    style="display: none"
                    id="favicon"
                    name="favicon"
                    accept=".ico"
                    @change="onFilePicked"
                />

                <p>
                    Allowed file extension: .ICO. Visit <a href="https://www.favicon-generator.org/" target="_blank">Favicon.ico & App Icon Generator</a>
                    to create a favicon file.
                </p>
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
            activeFilePickerId: null,
            showDeleteLogo: false,
            showDeleteFavicon: false,
            defaultImage:
                "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAA9JREFUeNpiuHbtGkCAAQAFCAKDZcGh3gAAAABJRU5ErkJggg==",
            form1: {
                loading: false,
                logo_media_url: "",
                logo_media_changed: false,
                favicon_media_url: "",
                favicon_media_changed: false,
                has_error: false,
                error: null,
                errors: {},
                success: false
            }
        };
    },
    mounted() {},
    created() {
        axios
            .get("/admin/logo", { params: { locale: this.$i18n.locale } })
            .then(response => {
                this.form1.logo_media_url = response.data.logo
                    ? response.data.logo
                    : this.defaultImage;
                this.form1.favicon_media_url = response.data.favicon
                    ? response.data.favicon
                    : this.defaultImage;
                this.showDeleteLogo = _.startsWith(
                    this.form1.logo_media_url,
                    "data:image/png;base64"
                )
                    ? false
                    : true;
                this.showDeleteFavicon = _.startsWith(
                    this.form1.favicon_media_url,
                    "data:image/png;base64"
                )
                    ? false
                    : true;

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
                    this.updateLogoFav(formName);
                } else {
                    this[formName].loading = false;
                    return false;
                }
            });
        },
        updateLogoFav(formName) {
            var app = this[formName];

            let settings = {
                headers: { "content-type": "multipart/form-data" }
            };

            // Remove image urls
            let formModel = Object.assign({}, this.form1);
            formModel.logo_media_url = null;
            formModel.favicon_media_url = null;

            let formData = new FormData(document.getElementById("form1"));

            for (let field in formModel) {
                formData.append(field, formModel[field]);
            }

            axios
                .post(
                    "/admin/logo",
                    formData,
                    settings
                ) /*, {
            locale: this.$i18n.locale,
            name: app.name,
            email: app.email,
            locale: app.locale,
            timezone: app.timezone,
            new_password: app.new_password,
            current_password: app.current_password
          })*/
                .then(response => {
                    if (response.data.status === "success") {
                        app.success = true;
                        app.new_password = null;
                        app.current_password = null;
                        this.$nextTick(() => this.$validator.reset());

                        this.form1.logo_media_url = response.data.logo
                            ? response.data.logo
                            : this.defaultImage;
                        this.form1.favicon_media_url = response.data.favicon
                            ? response.data.favicon
                            : this.defaultImage;
                    }
                })
                .catch(error => {
                    app.has_error = true;

                    if (error.response.data.status === "error") {
                        if (typeof error.response.data.error !== "undefined")
                            app.error = error.response.data.error;
                        this.errorMsg = error.response.data.error;
                        app.errors = error.response.data.errors || {};

                        for (let field in app.errors) {
                            this.$validator.errors.add({
                                field: formName + "." + field,
                                msg: app.errors[field][0]
                            });
                        }
                    }
                })
                .finally(() => {
                    app.loading = false;
                });
        },
        pickFile(id) {
            this.activeFilePickerId = id;
            document.getElementById(id).click();
        },
        onFilePicked(e) {
            const elementId = e.target.attributes.id.nodeValue;
            const files = e.target.files;
            if (files[0] !== undefined) {
                if (files[0].name.lastIndexOf(".") <= 0) {
                    return;
                }
                const fr = new FileReader();
                fr.readAsDataURL(files[0]);
                fr.addEventListener("load", () => {
                    this.form1[this.activeFilePickerId + "_media_url"] =
                        fr.result;
                    this.form1[this.activeFilePickerId + "_media_file"] =
                        files[0]; // this is an image file that can be sent to server...
                    this.form1[
                        this.activeFilePickerId + "_media_changed"
                    ] = true;
                    if (elementId === "logo") {
                        this.showDeleteLogo = true;
                    } else if (elementId === "favicon") {
                        this.showDeleteFavicon = true;
                    }
                });
            } else {
                this.form1[this.activeFilePickerId + "_media_file"] = "";
                this.form1[this.activeFilePickerId + "_media_url"] = "";
                this.form1[this.activeFilePickerId + "_media_changed"] = true;
            }
        }
    },
    computed: {
        app() {
            return this.$store.getters.app;
        }
    }
};
</script>
<style>
.v-image__image--cover {
    background-size: contain;
}
</style>
