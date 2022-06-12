<template>
    <app-page-layout>
        <template slot-scope="props">
            <v-tabs-items
                v-model="props.state.selectedTab"
                :touchless="false"
                class="mx-2"
            >
                <v-tab-item
                    key="cheque"
                    :value="'cheque'"
                    :eager="true"
                    class="px-3"
                >
                    <v-switch
                        v-model="props.form.is_active_cheque"
                        :label="`Active`"
                    >
                    </v-switch>
                </v-tab-item>

                <v-tab-item
                    key="banktransfer"
                    :value="'banktransfer'"
                    :eager="true"
                    class="px-3"
                >
                    <v-switch
                        v-model="props.form.is_active_bank_transfer"
                        :label="`Active`"
                    >
                    </v-switch>
                </v-tab-item>

                <v-tab-item
                    key="lynx"
                    :value="'lynx'"
                    :eager="true"
                    class="px-3"
                >
                    <v-switch
                        v-model="props.form.is_active_lynx"
                        :label="`Active`"
                    >
                    </v-switch>
                </v-tab-item>

                <v-tab-item
                    key="yoozpg"
                    :value="'yoozpg'"
                    :eager="true"
                    class="px-3"
                >
                    <v-switch
                        v-model="props.form.is_active_yooz_pg"
                        :label="`Active`"
                    >
                    </v-switch>

                    <template v-if="props.form.is_active_yooz_pg">
                        <v-radio-group v-model="props.form.yooz_pg_mode" row>
                            <v-radio
                                label="Live Mode"
                                color="red"
                                value="live"
                            >
                            </v-radio>

                            <v-radio
                                label="Test Mode"
                                color="red"
                                value="test"
                            >
                            </v-radio>
                        </v-radio-group>

                        <v-text-field
                            v-if="props.form.yooz_pg_mode === 'live'"
                            v-model="props.form.yooz_pg_host_live"
                            ref="yooz_pg_host_live"
                            label="Yooz PG Host Live"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="yooz_pg_host_live"
                            data-vv-as="yooz pg host live"
                            :error-messages="
                                errors.collect('form.yooz_pg_host_live')
                            "
                        >
                        </v-text-field>

                        <v-text-field
                            v-if="props.form.yooz_pg_mode === 'test'"
                            v-model="props.form.yooz_pg_host_test"
                            ref="yooz_pg_host_test"
                            label="Yooz PG Host Test"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="yooz_pg_host_test"
                            data-vv-as="yooz pg host test"
                            :error-messages="
                                errors.collect('form.yooz_pg_host_test')
                            "
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="props.form.yooz_pg_merchant_id"
                            ref="yooz_pg_merchant_id"
                            label="Yooz PG Merchant ID Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="yooz_pg_merchant_id"
                            data-vv-as="yooz pg merchant id key"
                            :error-messages="
                                errors.collect('form.yooz_pg_merchant_id')
                            "
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="props.form.yooz_pg_encryption_key"
                            ref="yooz_pg_encryption_key"
                            label="Yooz PG Encryption Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="yooz_pg_encryption_key"
                            data-vv-as="yooz pg encryption key"
                            :error-messages="
                                errors.collect('form.yooz_pg_encryption_key')
                            "
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="props.form.yooz_pg_secret_key"
                            ref="yooz_pg_secret_key"
                            label="Yooz PG secret Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="yooz_pg_secret_key"
                            data-vv-as="yooz pg secret key"
                            :error-messages="errors.collect('form.yooz_pg_secret_key')"
                        >
                        </v-text-field>
                    </template>
                </v-tab-item>

                <v-tab-item
                    key="paypal"
                    :value="'paypal'"
                    :eager="true"
                    class="px-3"
                >
                    <v-switch
                        v-model="props.form.is_active_paypal"
                        :label="`Active`"
                    >
                    </v-switch>

                    <template v-if="props.form.is_active_paypal">
                        <v-radio-group v-model="props.form.paypal_mode" row>
                            <v-radio
                                label="Live Mode"
                                color="red"
                                value="live"
                            >
                            </v-radio>

                            <v-radio
                                label="Sandbox Mode"
                                color="red"
                                value="sandbox"
                            >
                            </v-radio>
                        </v-radio-group>

                        <v-text-field
                            v-model="props.form.paypal_client_id"
                            ref="paypal_client_id"
                            label="PayPal Client ID Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="paypal_client_id"
                            data-vv-as="paypal client id key"
                            :error-messages="
                                errors.collect('form.paypal_client_id')
                            "
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="props.form.paypal_secret_key"
                            ref="paypal_secret_key"
                            label="PayPal secret Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="paypal_secret_key"
                            data-vv-as="paypal secret key"
                            :error-messages="errors.collect('form.paypal_secret_key')"
                        >
                        </v-text-field>
                    </template>
                </v-tab-item>

                <v-tab-item
                    key="stripe"
                    :value="'stripe'"
                    :eager="true"
                    class="px-3"
                >
                    <v-switch
                        v-model="props.form.is_active_stripe"
                        :label="`Active`"
                    >
                    </v-switch>

                    <template v-if="props.form.is_active_stripe">
                        <v-radio-group v-model="props.form.stripe_mode" row>
                            <v-radio
                                label="Live Mode"
                                color="red"
                                value="live"
                            >
                            </v-radio>

                            <v-radio
                                label="Sandbox Mode"
                                color="red"
                                value="sandbox"
                            >
                            </v-radio>
                        </v-radio-group>

                        <v-text-field
                            v-model="props.form.stripe_public_key"
                            ref="stripe_public_key"
                            label="Stripe public Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="stripe_public_key"
                            data-vv-as="stripe public key"
                            :error-messages="errors.collect('form.stripe_public_key')"
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="props.form.stripe_secret_key"
                            ref="stripe_secret_key"
                            label="Stripe secret Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="stripe_secret_key"
                            data-vv-as="stripe secret key"
                            :error-messages="errors.collect('form.stripe_secret_key')"
                        >
                        </v-text-field>
                    </template>
                </v-tab-item>

                <v-tab-item
                    key="twocheckout"
                    :value="'twocheckout'"
                    :eager="true"
                    class="px-3"
                >
                    <v-switch
                        v-model="props.form.is_active_twocheckout"
                        :label="`Active`"
                    >
                    </v-switch>

                    <template v-if="props.form.is_active_twocheckout">
                        <v-radio-group v-model="props.form.twocheckout_mode" row>
                            <v-radio
                                label="Live Mode"
                                color="red"
                                value="live"
                            >
                            </v-radio>

                            <v-radio
                                label="Test Mode"
                                color="red"
                                value="test"
                            >
                            </v-radio>
                        </v-radio-group>

                        <v-text-field
                            v-model="props.form.twocheckout_vendor_id"
                            ref="twocheckout_vendor_id"
                            label="2Checkout Vendor ID"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="twocheckout_vendor_id"
                            data-vv-as="twocheckout vendor id"
                            :error-messages="
                                errors.collect('form.twocheckout_vendor_id')
                            "
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="props.form.twocheckout_secret_key"
                            ref="twocheckout_secret_key"
                            label="2Checkout Secret Key"
                            class="mb-3"
                            persistent-hint
                            v-validate="'required'"
                            data-vv-name="twocheckout_secret_key"
                            data-vv-as="twocheckout secret key"
                            :error-messages="errors.collect('form.twocheckout_secret_key')"
                        >
                        </v-text-field>
                    </template>
                </v-tab-item>
            </v-tabs-items>
        </template>
    </app-page-layout>
</template>

<script>
import { defineComponent } from '@vue/composition-api';

import AppPageLayout from './components/PageLayout.vue';

export default defineComponent({
    name: 'payment-page',
    components: {
        AppPageLayout
    },
    setup() {},
});
</script>
