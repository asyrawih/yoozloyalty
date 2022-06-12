<template>
    <v-dialog
        v-model="state.open"
        persistent
        :fullscreen="$vuetify.breakpoint.xsOnly"
        width="480"
        @keydown.esc="close"
    >
        <v-card>
            <v-overlay
                :value="state.loading"
            >
                <v-progress-circular
                    class="ma-5"
                    :color="app.color_name"
                    :size="50"
                    indeterminate
                />
            </v-overlay>

            <v-toolbar>
                <v-toolbar-title>
                    {{ state.title }}
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-form
                lazy-validation
                data-vv-scope="form"
                @submit.prevent="onSubmit"
            >
                <v-card-text>
                    <v-select
                        id="frmCreditRequestCampaign"
                        class="mb-3"
                        label="Campaign"
                        v-model="form.uuid"
                        :items="state.campaigns"
                        :loading="state.campaignLoading"
                        clearable
                        v-validate="'required'"
                        data-vv-name="uuid"
                        data-vv-as="campaign"
                        :error-messages="errors.collect('form.uuid')"
                    />

                    <v-text-field
                        id="frmCreditRequestNumber"
                        label="Customer number"
                        outline
                        placeholder="XXX-XXX-XXXX"
                        prepend-inner-icon="person"
                        v-mask="[
                            '###-###-####',
                            '###-###-#####',
                            '###-###-######',
                            '###-###-#######',
                        ]"
                        v-model="form.number"
                        v-validate="'required'"
                        data-vv-name="number"
                        data-vv-as="customer number"
                        :error-messages="errors.collect('form.number')"
                    />

                    <v-text-field
                        type="number"
                        id="frmCreditRequestReceiptNumber"
                        label="Receipt number"
                        outline
                        prepend-inner-icon="credit_card"
                        v-model="form.receipt_number"
                        v-validate="'required|numeric'"
                        data-vv-name="receipt_number"
                        data-vv-as="receipt number"
                        :error-messages="errors.collect('form.receipt_number')"
                    />

                    <v-text-field
                        type="number"
                        id="frmCreditRequestReceiptAmount"
                        label="Receipt amount"
                        outline
                        prepend-inner-icon="receipt"
                        v-model="form.receipt_amount"
                        v-validate="'required|decimal:2'"
                        data-vv-name="receipt_amount"
                        data-vv-as="receipt amount"
                        :error-messages="errors.collect('form.receipt_amount')"
                    />
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        color="secondary"
                        text
                        large
                        @click="close"
                    >
                        Cancel
                    </v-btn>

                    <v-btn
                        type="submit"
                        color="primary"
                        text
                        large
                    >
                        Save
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, onMounted, reactive } from '@vue/composition-api';

import ServiceCreditRequest from '../services/CreditRequestApi';

export default defineComponent({
    name: 'merchant-credit-request-form',
    setup(props, {root}) {
        const app = computed(() => root.$store.getters.app);

        const state = reactive({
            open: false,
            loading: false,
            resolve: null,
            reject: null,
            title: "Create Credit Request",
            campaigns: [],
            campaignLoading: false,
        });

        const form = reactive({
            uuid: null,
            number: null,
            receipt_number: null,
            receipt_amount: null,
        });

        onMounted(async () => {
            await getCampaigns();
        });

        const clearForm = () => {
            form.uuid = null;
            form.number = null;
            form.receipt_number = null;
            form.receipt_amount = null;
        }

        const getCampaigns = async (params = {}) => {
            state.campaignLoading = true;

            const response = await ServiceCreditRequest().campaignsApi(params);

            state.campaigns = response.data;

            state.campaignLoading = false;
        };

        return {
            app,
            state,
            form,
            clearForm,
        };
    },
    methods: {
        clearComponent() {
            this.clearForm();
            this.$validator.reset();
        },
        async open() {
            this.state.open = true;

            return new Promise((resolve, reject) => {
                this.state.resolve = resolve;
                this.state.reject = reject;
            });
        },
        close() {
            this.clearComponent();
            this.state.resolve(false);
            this.state.open = false;
        },
        async onSubmit() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return;
            }

            try {
                const response = await ServiceCreditRequest().storeApi(this.form);

                this.clearComponent();
                this.state.resolve(response.message);
                this.state.open = false;
            } catch(exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
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
        },
    },
});
</script>
