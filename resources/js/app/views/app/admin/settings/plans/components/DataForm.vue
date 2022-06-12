<template>
    <v-dialog
        v-model="state.open"
        persistent
        :retain-focus="false"
        :fullscreen="$vuetify.breakpoint.xsOnly"
        width="480"
        @keydown.esc="onClose"
    >
        <v-card>
            <v-overlay
                :value="state.loading"
            >
                <v-progress-circular
                    class="ma-5"
                    color="primary"
                    :size="50"
                    indeterminate
                />
            </v-overlay>

            <v-toolbar>
                <v-toolbar-title>{{ state.title }}</v-toolbar-title>

                <v-spacer></v-spacer>

                <v-btn icon @click="onClose">
                    <v-icon>close</v-icon>
                </v-btn>

                <template slot="extension">
                    <v-tabs
                        slot="extension"
                        v-model="state.selectedTab"
                        slider-color="primary"
                        color="grey darken-3"
                        show-arrows
                    >
                        <v-tab
                            key="general_tab"
                            href="#general_content"
                        >
                            General
                        </v-tab>

                        <v-tab
                            key="limitaions_tab"
                            href="#limitaions_content"
                        >
                            Limitations
                        </v-tab>

                        <v-tab
                            key="subscription_id_tab"
                            href="#subscription_id_content"
                        >
                            Subscription ID
                        </v-tab>
                    </v-tabs>
                </template>
            </v-toolbar>

            <v-form
                ref="form"
                lazy-validation
                data-vv-scope="form"
                @submit.prevent="onSubmit"
            >
                <v-divider class="grey lighten-2"></v-divider>

                <v-card-text
                    :style="{
                        'height': 'auto',
                        'max-width': '800px',
                        'overflow-y': 'auto'
                    }"
                >
                    <v-tabs-items
                        v-model="state.selectedTab"
                        touchless
                        class="mx-2"
                    >
                        <v-tab-item
                            key="general_content"
                            value="general_content"
                            class="mb-3 subtitle-3"
                        >
                            <v-text-field
                                id="frmPlanName"
                                class="mb-3"
                                label="Plan Name (required)"
                                persistent-hint
                                v-model="form.name"
                                v-validate="'required'"
                                data-vv-name="name"
                                data-vv-as="plan name"
                                :error-messages="errors.collect('form.name')"
                            />

                            <v-autocomplete
                                id="frmPlanCurrencyCode"
                                class="mb-3"
                                label="Currency Code (required)"
                                persistent-hint
                                item-value="0"
                                item-text="1"
                                :items="currency_codes"
                                v-model="form.currency_code"
                                v-validate="'required'"
                                data-vv-name="currency_code"
                                data-vv-as="currency code"
                                :error-messages="errors.collect('form.currency_code')"
                                @change="onCurrencyChange"
                            />

                            <v-text-field
                                type="number"
                                id="frmPlanPrice"
                                class="mb-3"
                                label="Monthly Price (required)"
                                :prefix="state.prefix_currency"
                                :suffix="state.suffix_currency"
                                persistent-hint
                                v-model="form.price"
                                v-validate="'required|numeric'"
                                data-vv-name="price"
                                data-vv-as="monthly price"
                                :error-messages="errors.collect('form.price')"
                            />
                        </v-tab-item>

                        <v-tab-item
                            key="limitaions_content"
                            value="limitaions_content"
                            class="mb-3 subtitle-3"
                        >
                            <v-checkbox
                                v-model="form.is_unlimited_customers"
                                label="Unlimited customer"
                                @change="onUnlimitedCustomerChecked"
                            />

                            <v-text-field
                                v-show="state.show_limited_customer"
                                type="number"
                                id="frmPlanLimitationsCustomers"
                                class="mb-3"
                                label="Customers (required)"
                                persistent-hint
                                v-model="form.limitations_customers"
                                v-validate="'required_if:is_unlimited_customers,false|numeric'"
                                data-vv-name="limitations_customers"
                                data-vv-as="limitations customers"
                                :error-messages="errors.collect('form.limitations_customers')"
                            />

                            <v-text-field
                                type="number"
                                id="frmPlanLimitationsCampaigns"
                                class="mb-3"
                                label="Website (required)"
                                persistent-hint
                                v-model="form.limitations_campaigns"
                                v-validate="'required|numeric'"
                                data-vv-name="limitations_campaigns"
                                data-vv-as="limitations website"
                                :error-messages="errors.collect('form.limitations_campaigns')"
                            />

                            <v-checkbox
                                v-model="form.is_unlimited_rewards"
                                label="Unlimited Reward Offer"
                                @change="onUnlimitedRewardChecked"
                            />

                            <v-text-field
                                v-show="state.show_limited_reward"
                                type="number"
                                id="frmPlanLimitationsRewards"
                                class="mb-3"
                                label="Reward Offer (required)"
                                persistent-hint
                                v-model="form.limitations_rewards"
                                v-validate="'required_if:is_unlimited_rewards,false|numeric'"
                                data-vv-name="limitations_rewards"
                                data-vv-as="limitations reward offer"
                                :error-messages="errors.collect('form.limitations_rewards')"
                            />

                            <v-checkbox
                                v-model="form.is_unlimited_businesses"
                                label="Unlimited store"
                                @change="onUnlimitedBusinessesChecked"
                            />

                            <v-text-field
                                v-show="state.show_limited_businesses"
                                type="number"
                                id="frmPlanLimitationsBusinesses"
                                class="mb-3"
                                label="Stores (required)"
                                persistent-hint
                                v-model="form.limitations_businesses"
                                v-validate="'required_if:is_unlimited_businesses,false|numeric'"
                                data-vv-name="limitations_businesses"
                                data-vv-as="limitations stores"
                                :error-messages="errors.collect('form.limitations_businesses')"
                            />

                            <v-checkbox
                                v-model="form.is_unlimited_staff"
                                label="Unlimited staff"
                                @change="onUnlimitedStaffChecked"
                            />

                            <v-text-field
                                v-show="state.show_limited_staff"
                                type="number"
                                id="frmPlanLimitationsStaff"
                                class="mb-3"
                                label="Staff (required)"
                                persistent-hint
                                v-model="form.limitations_staff"
                                v-validate="'required_if:is_unlimited_staff,false|numeric'"
                                data-vv-name="limitations_staff"
                                data-vv-as="limitations staff"
                                :error-messages="errors.collect('form.limitations_staff')"
                            />

                            <v-checkbox
                                v-model="form.is_unlimited_segments"
                                label="Unlimited Segment"
                                @change="onUnlimitedSegmentChecked"
                            />

                            <v-text-field
                                v-show="state.show_limited_segments"
                                type="number"
                                id="frmPlanLimitationsSegments"
                                class="mb-3"
                                label="Segments (required)"
                                persistent-hint
                                v-model="form.limitations_segments"
                                v-validate="'required_if:is_unlimited_segments,false|numeric'"
                                data-vv-name="limitations_segments"
                                data-vv-as="limitations segments"
                                :error-messages="errors.collect('form.limitations_segments')"
                            />
                        </v-tab-item>

                        <v-tab-item
                            key="subscription_id_content"
                            value="subscription_id_content"
                            class="mb-3 subtitle-3"
                        >
                            <v-text-field
                                id="frmPlanPaddleSubscriptionID"
                                class="mb-3"
                                label="Paddle Subscription ID (optional)"
                                persistent-hint
                                v-model="form.product_id_paddle"
                            />

                            <v-text-field
                                id="frmPlan2CheckoutSubscriptionID"
                                class="mb-3"
                                label="2Checkout Subscription ID (optional)"
                                persistent-hint
                                v-model="form.product_id_2checkout"
                            />

                            <v-text-field
                                ref="product_id_stripe"
                                id="frmPlanStripeSubscriptionID"
                                class="mb-3"
                                label="Stripe Subscription ID (optional)"
                                persistent-hint
                                v-model="form.product_id_stripe"
                            />

                            <v-text-field
                                id="frmPaypalSubscriptionID"
                                class="mb-3"
                                label="Paypal Subscription ID (optional)"
                                persistent-hint
                                v-model="form.product_id_paypal"
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        color="secondary"
                        text
                        large
                        :disabled="state.loading"
                        @click="onClose"
                    >
                        Cancel
                    </v-btn>

                    <v-btn
                        type="submit"
                        color="primary"
                        large
                        text
                        :disabled="state.loading"
                    >
                        Save
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, reactive } from '@vue/composition-api';

import ServicePlanApi from '../services/PlanApi';

export default defineComponent({
    $_veeValidate: {
        validator: 'new'
    },
    name: 'data-form',
    props: {
        currency_codes: [],
        currency_symbols: [],
    },
    setup(props, { root }) {
        const app = computed(() => root.$store.getters.app);

        const state = reactive({
            open: false,
            loading: false,
            resolve: null,
            reject: null,
            identifier: null,
            method: 'CREATE',
            title: 'Create Plan',
            selectedTab: 'general_tab',
            prefix_currency: 'TTD',
            suffix_currency: 'TTD',
            show_limited_customer: true,
            show_limited_reward: true,
            show_limited_businesses: true,
            show_limited_staff: true,
            show_limited_segments: true,
            previous_limitations_customers: null,
            previous_limitations_rewards: null,
        });

        const form = reactive({
            name: '',
            currency_code: 'TTD',
            price: null,
            is_unlimited_customers: false,
            is_unlimited_rewards: false,
            is_unlimited_businesses: false,
            is_unlimited_staff: false,
            is_unlimited_segments: false,
            limitations_customers: null,
            limitations_campaigns: null,
            limitations_rewards: null,
            limitations_businesses: null,
            limitations_staff: null,
            limitations_segments: null,
            product_id_paddle: null,
            product_id_2checkout: null,
            product_id_stripe: null,
            product_id_paypal: null
        });

        const getDataFromApi = async (id) => {
            state.loading = true;

            try {
                const { data } = await ServicePlanApi().showApi(id);

                const limitations_customers = (checkIfUnlimited(data.limitations_customers))
                    ? null
                    : data.limitations_customers;

                const limitations_rewards = (checkIfUnlimited(data.limitations_rewards))
                    ? null
                    : data.limitations_rewards;

                const limitations_businesses = (checkIfUnlimited(data.limitations_businesses))
                    ? null
                    : data.limitations_businesses;

                const limitations_staff = (checkIfUnlimited(data.limitations_staff))
                    ? null
                    : data.limitations_staff;

                const limitations_segments = (checkIfUnlimited(data.limitations_segments))
                    ? null
                    : data.limitations_segments;

                form.name = data.name;
                form.currency_code = data.price.currency;
                form.price = parseInt(data.price.amount) / 100;
                form.is_unlimited_customers = checkIfUnlimited(data.limitations_customers);
                form.is_unlimited_rewards = checkIfUnlimited(data.limitations_rewards);
                form.is_unlimited_businesses = checkIfUnlimited(data.limitations_businesses);
                form.is_unlimited_staff = checkIfUnlimited(data.limitations_staff);
                form.is_unlimited_segments = checkIfUnlimited(data.limitations_segments);
                form.limitations_customers = limitations_customers;
                form.limitations_campaigns = data.limitations_campaigns;
                form.limitations_rewards = limitations_rewards;
                form.limitations_businesses = limitations_businesses;
                form.limitations_staff = limitations_staff;
                form.limitations_segments = limitations_segments;
                form.product_id_paddle = null;
                form.product_id_2checkout = null;
                form.product_id_stripe = null;
                form.product_id_paypal = null;

                onUnlimitedCustomerChecked(form.is_unlimited_customers);
                onUnlimitedRewardChecked(form.is_unlimited_rewards);
                onUnlimitedBusinessesChecked(form.is_unlimited_businesses);
                onUnlimitedStaffChecked(form.is_unlimited_staff);
                onUnlimitedSegmentChecked(form.is_unlimited_segments);
                onCurrencyChange(data.price.currency);
            } catch(exception) {
                //
            } finally {
                state.loading = false;
            }
        }

        const onCurrencyChange = (event) => {
            state.prefix_currency = props.currency_symbols[event];
            state.suffix_currency = event;
        }

        const onUnlimitedCustomerChecked = (event) => state.show_limited_customer = ! event;
        const onUnlimitedRewardChecked = (event) => state.show_limited_reward = ! event;
        const onUnlimitedBusinessesChecked = (event) => state.show_limited_businesses = ! event;
        const onUnlimitedStaffChecked = (event) => state.show_limited_staff = ! event;
        const onUnlimitedSegmentChecked = (event) => state.show_limited_segments = ! event;


        const checkIfUnlimited = (value = '') => value.toString().toLowerCase() === 'unlimited';

        return {
            app,
            state,
            form,
            onCurrencyChange,
            onUnlimitedCustomerChecked,
            onUnlimitedRewardChecked,
            onUnlimitedBusinessesChecked,
            onUnlimitedStaffChecked,
            onUnlimitedSegmentChecked,
            getDataFromApi,
        };
    },
    methods: {
        async open(id = null) {
            this.state.open = true;

            if (id) {
                this.state.identifier = id;
                this.state.method = 'UPDATE';
                this.state.title = 'Edit Plan';

                await this.getDataFromApi(id);
            }

            return new Promise((resolve, reject) => {
                this.state.resolve = resolve;
                this.state.reject = reject;
            });
        },
        resetForm() {
            this.state.identifier = null;
            this.state.method = 'CREATE';
            this.state.title = 'Create Plan';

            this.form.name = '';
            this.form.currency_code = 'TTD';
            this.form.price = null;

            this.form.is_unlimited_customers = false;
            this.form.is_unlimited_rewards = false;
            this.form.is_unlimited_stores = false;
            this.form.is_unlimited_staff = false;
            this.form.is_unlimited_segments = false;

            this.form.limitations_customers = null;
            this.form.limitations_campaigns = null;
            this.form.limitations_rewards = null;
            this.form.limitations_businesses = null;
            this.form.limitations_staff = null;
            this.form.limitations_segments = null;

            this.form.product_id_paddle = null;
            this.form.product_id_2checkout = null;
            this.form.product_id_stripe = null;
            this.form.product_id_paypal = null;

            this.state.selectedTab = 'general_tab';

            this.onUnlimitedCustomerChecked(false);
            this.onUnlimitedRewardChecked(false);
            this.onUnlimitedBusinessesChecked(false);
            this.onUnlimitedStaffChecked(false);
            this.onUnlimitedSegmentChecked(false);

            this.onCurrencyChange('TTD');

            this.$validator.reset();
        },
        onClose() {
            this.resetForm();

            this.state.open = false;

            this.state.resolve(false);
        },
        async onSubmit() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            let response = null;

            try {
                if (this.state.method === 'UPDATE') {
                    response = await ServicePlanApi().updateApi(this.form, this.state.identifier);
                } else {
                    response = await ServicePlanApi().storeApi(this.form);
                }

                this.resetForm();
                this.state.open = false;
                this.state.resolve(response.message);
            } catch (exception) {
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
                } else {
                    this.state.reject(exception.message);
                }
            } finally {
                this.state.loading = false;
            }
        }
    }
})
</script>
