<template>
    <div style="height: 100%">
        <v-container
            v-if="state.loading"
            fluid
            style="height: 100%"
        >
            <v-layout
                row
                justify-center
                align-center
                fill-height
                class="text-xs-center"
                style="height: 100%"
            >
                <v-progress-circular
                    :size="50"
                    color="primary"
                    indeterminate
                    class="ma-5"
                />
            </v-layout>
        </v-container>

        <v-card v-else>
            <v-toolbar tabs flat>
                <v-toolbar-title>Payment Method</v-toolbar-title>

                <v-spacer></v-spacer>

                <template v-slot:extension>
                    <v-flex>
                        <v-tabs
                            v-model="state.selectedTab"
                            slider-color="primary"
                            color="grey darken-3"
                            show-arrows
                        >
                            <v-tab :href="'#cheque'">
                                Cheque
                            </v-tab>

                            <v-tab :href="'#banktransfer'">
                                Bank Transfer
                            </v-tab>

                            <v-tab :href="'#lynx'">
                                Lynx
                            </v-tab>

                            <v-tab :href="'#yoozpg'">
                                Yooz Payment Gateway
                            </v-tab>

                            <!-- <v-tab :href="'#paypal'">
                                PayPal
                            </v-tab>

                            <v-tab :href="'#stripe'">
                                Stripe
                            </v-tab>

                            <v-tab :href="'#twocheckout'">
                                2Checkout
                            </v-tab> -->
                        </v-tabs>
                    </v-flex>
                </template>
            </v-toolbar>

            <v-divider class="grey lighten-2"></v-divider>

            <v-form
                ref="form"
                lazy-validation
                data-vv-scope="form"
                @submit.prevent="onSubmitClick"
            >
                <v-container>
                    <v-card-text>
                        <v-alert
                            :value="state.hasErrors"
                            type="error"
                            class="mx-2"
                        >
                            <span>{{ $t("correct_errors") }}</span>
                        </v-alert>

                        <v-alert
                            :value="state.hasSuccess"
                            type="success"
                            class="mx-2"
                        >
                            <span>{{ $t("update_success") }}</span>
                        </v-alert>

                        <slot v-bind:form="form" v-bind:state="state"></slot>

                        <v-text-field
                            :type="state.show_current_password ? 'text' : 'password'"
                            class="mx-4"
                            v-model="form.current_password"
                            :label="$t('current_password')"
                            outlined
                            :append-icon="
                                state.show_current_password ? 'visibility' : 'visibility_off'
                            "
                            @click:append="
                                state.show_current_password = !state.show_current_password
                            "
                            v-validate="'required|min:8|max:24'"
                            data-vv-name="current_password"
                            :data-vv-as="$t('current_password').toLowerCase()"
                            :error-messages="errors.collect('form.current_password')"
                        />
                    </v-card-text>

                    <v-card-actions class="mx-2">
                        <v-spacer></v-spacer>

                        <v-btn
                            type="submit"
                            color="primary"
                            large
                            :disabled="state.loading"
                            class="mb-2"
                        >
                            {{ $t("update") }}
                        </v-btn>
                    </v-card-actions>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>

<script>
import { defineComponent, onMounted, reactive } from '@vue/composition-api';

import ServicePaymentApi from '../services/PaymentApi';

export default defineComponent({
    $_veeValidate: {
        validator: "new"
    },
    name: 'page-layout',
    setup() {
        const state = reactive({
            loading: true,
            selectedTab: 'cheque',
            show_current_password: false,
            hasSuccess: false,
            hasErrors: false,
        });

        const form = reactive({
            is_active_cheque: false,
            is_active_bank_transfer: false,
            is_active_lynx: false,
            is_active_yooz_pg: false,
            is_active_paypal: false,
            is_active_stripe: false,
            is_active_twocheckout: false,
            yooz_pg_mode: "test",
            yooz_pg_host_live: "",
            yooz_pg_host_test: "",
            yooz_pg_merchant_id: "",
            yooz_pg_encryption_key: "",
            yooz_pg_secret_key: "",
            paypal_mode: "sandbox",
            paypal_client_id: "",
            paypal_secret_key: "",
            stripe_mode: "sandbox",
            stripe_public_key: "",
            stripe_secret_key: "",
            twocheckout_mode: "sandbox",
            twocheckout_vendor_id: "",
            twocheckout_secret_key: "",
            current_password: "",
        });

        onMounted(async () => {
            const response = await ServicePaymentApi().initializeApi();

            form.is_active_cheque = response.is_active_cheque;
            form.is_active_bank_transfer = response.is_active_bank_transfer;
            form.is_active_lynx = response.is_active_lynx;
            form.is_active_yooz_pg = response.is_active_yooz_pg;
            form.is_active_paypal = response.is_active_paypal;
            form.is_active_stripe = response.is_active_stripe;
            form.is_active_twocheckout = response.is_active_twocheckout;
            form.yooz_pg_mode = response.yooz_pg_mode;
            form.yooz_pg_host_live = response.yooz_pg_host_live;
            form.yooz_pg_host_test = response.yooz_pg_host_test;
            form.yooz_pg_merchant_id = response.yooz_pg_merchant_id;
            form.yooz_pg_encryption_key = response.yooz_pg_encryption_key;
            form.yooz_pg_secret_key = response.yooz_pg_secret_key;
            form.paypal_mode = response.paypal_mode;
            form.paypal_client_id = response.paypal_client_id;
            form.paypal_secret_key = response.paypal_secret_key;
            form.stripe_mode = response.stripe_mode;
            form.stripe_public_key = response.stripe_public_key;
            form.stripe_secret_key = response.stripe_secret_key;
            form.twocheckout_mode = response.twocheckout_mode;
            form.twocheckout_vendor_id = response.twocheckout_vendor_id;
            form.twocheckout_secret_key = response.twocheckout_secret_key;

            state.loading = false;
        });

        return {
            state,
            form,
        };
    },
    methods: {
        async onSubmitClick () {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {

                let field = this.$validator.errors.items[0].field;

                let element = typeof this.$refs[field] !== 'undefined'
                    ? this.$refs[field]
                    : null;

                let tab = element !== null ? element.$parent.$vnode.key : null;

                if (tab !== null) this.state.selectedTab = tab;

                this.state.loading = false;

                return false;
            }

            try {
                await ServicePaymentApi().updateApi(this.form);

                this.state.hasSuccess = true;
                this.state.hasErrors = false;
            } catch (exception) {
                this.state.hasErrors = true;
                this.state.hasSuccess = false;

                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        let element = typeof this.$refs[field] !== 'undefined'
                            ? this.$refs[field]
                            : null;

                        let tab = element !== null ? element.$parent.$vnode.key : null;

                        if (tab !== null) this.state.selectedTab = tab;

                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field][0]
                        });
                    }
                }
            }

            this.form.current_password = "";
            this.state.loading = false;
        }
    },
});
</script>
