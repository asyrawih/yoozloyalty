<template>
    <div style="height: 100%">
        <v-container fluid v-if="loaded" style="height: 100%">
            <v-layout
                align-center
                justify-center
                row
                fill-height
                class="text-xs-center"
                style="height: 100%"
            >
                <v-progress-circular
                    :size="50"
                    :color="app.color_name"
                    indeterminate
                    class="ma-5"
                ></v-progress-circular>
            </v-layout>
        </v-container>

        <v-card v-show="!loaded">
            <v-toolbar tabs flat>
                <v-toolbar-title>Redemption Multiplier</v-toolbar-title>
            </v-toolbar>
            <v-form
                data-vv-scope="form1"
                lazy-validation
                @submit.prevent="submitForm('form1')"
            >
                <v-divider class="grey lighten-2"></v-divider>

                <v-card-text>
                    <v-alert :value="form1.success" type="success" class="mb-4">
                        {{ $t("update_success") }}
                    </v-alert>

                    <v-text-field
                        type="number"
                        class="mb-3"
                        label="Points Exchange 1 Points = X Money"
                        placeholder="1 points = x money"
                        v-model="form1.value"
                        v-validate="'required'"
                        data-vv-name="value"
                        :error-messages="errors.collect('form1.value')"
                    />

                    <v-text-field
                        type="number"
                        class="mb-3"
                        label="Minimum Points"
                        placeholder="Minimum points to redeem transaction discounts."
                        v-model="form1.minimum_points"
                        v-validate="'required|numeric'"
                        data-vv-name="minimum_points"
                        :error-messages="errors.collect('form1.minimum_points')"
                    />

                    <v-text-field
                        type="number"
                        class="mb-3"
                        label="Maximum Redemption"
                        placeholder="Maximum limit in point redemption in a day."
                        v-model="form1.maximum_redeem"
                        v-validate="'required|numeric'"
                        data-vv-name="maximum_redeem"
                        :error-messages="errors.collect('form1.maximum_redeem')"
                    />
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
    </div>
</template>
<script>
import axios from 'axios';
export default {
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {
            loaded: true,
            form1: {
                loading: false,
                has_error: false,
                error: null,
                uuid: null,
                value: 0,
                minimum_points: 0,
                maximum_redeem: 0,
                errors: {},
                success: false
            },
        };
    },
    computed: {
        app() {
            return this.$store.getters.app;
        }
    },
    async mounted() {
        await this.getDataFromApi();
    },
    methods: {
        async getDataFromApi() {
            try {
                const response = await axios.get('/user/setting/redeem-transaction');

                if (response.data.status === 'success') {
                    let data = response.data.data;

                    this.form1.value = data.value;
                    this.form1.uuid = data.uuid;
                    this.form1.minimum_points = data.minimum_points ?? 0;
                    this.form1.maximum_redeem = data.maximum_redeem ?? 0;

                    this.loaded = false;
                }
            } catch (exception) {
                if (exception.response.data) {

                }

                this.loaded = false;
            }

        },
        submitForm(formName) {
            var app = this[formName];
            app.success = false;
            app.has_error = false;
            app.loading = true;

            this.$validator.validateAll(formName).then(valid => {
                if (valid) {
                    axios
                        .post("/user/setting/redeem-transaction", {
                            locale: this.$i18n.locale,
                            uuid: app.uuid,
                            value: app.value,
                            minimum_points: app.minimum_points,
                            maximum_redeem: app.maximum_redeem,
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
                                        el !== null
                                            ? el.$parent.$vnode.key
                                            : null;
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
                } else {
                    app.loading = false;
                    return false;
                }
            });
        }
    }
};
</script>
