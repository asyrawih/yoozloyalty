<template>
    <v-dialog
        v-model="state.open"
        persistent
        width="360"
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

            <v-card-title class="headline">
                Redeem for transaction discount.
            </v-card-title>

            <v-form
                data-vv-scope="form"
                lazy-validation
                @submit.prevent="onSubmit"
            >
                <v-card-text>
                    <p class="body-1">
                        Customer can get discount on receipt amount.
                    </p>

                    <p
                        v-if="state.redeemed"
                        class="body-1"
                    >
                        Point successfully redeemed.
                    </p>

                    <v-simple-table
                        v-if="state.redeemed"
                    >
                        <template v-slot:default>
                            <tbody>
                                <tr>
                                    <td width="50%">Receipt Amount</td>
                                    <td width="10%">:</td>
                                    <td>{{ money_formatter(form.bill_amount) }}</td>
                                </tr>

                                <tr>
                                    <td width="50%">Discount</td>
                                    <td width="10%">:</td>
                                    <td>{{ money_formatter(state.response.discount_point) }}</td>
                                </tr>

                                <tr>
                                    <td width="50%">Amount Return</td>
                                    <td width="10%">:</td>
                                    <td>{{ money_formatter(state.response.amount_return) }}</td>
                                </tr>

                                <tr>
                                    <td width="50%">Amount Left</td>
                                    <td width="10%">:</td>
                                    <td>{{ money_formatter(state.response.amount_left) }}</td>
                                </tr>

                                <tr>
                                    <td width="50%">Point Cost</td>
                                    <td width="10%">:</td>
                                    <td>{{ state.response.point_cost }}</td>
                                </tr>

                                <tr>
                                    <td width="50%">Point Left</td>
                                    <td width="10%">:</td>
                                    <td>{{ state.response.point_left }}</td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>

                    <div class="mb-3">
                        <a
                            v-if="state.redeemed"
                            href="javascript:void(0);"
                            @click="onRepeat"
                        >
                            Redeem another reward
                        </a>
                    </div>

                    <v-text-field
                        v-if="! state.redeemed"
                        id="frmCustomerNumber"
                        label="Customer number"
                        prepend-inner-icon="person"
                        outline
                        placeholder="XXX-XXX-XXXX"
                        v-model="form.number"
                        v-validate="'required'"
                        data-vv-name="number"
                        data-vv-as="customer number"
                        :error-messages="errors.collect('form.number')"
                        v-mask="[
                            '###-###-####',
                            '###-###-#####',
                            '###-###-######',
                            '###-###-#######',
                        ]"
                    />

                    <v-text-field
                        v-if="! state.redeemed"
                        type="number"
                        id="frmReceiptNumber"
                        label="Receipt number"
                        prepend-inner-icon="credit_card"
                        outline
                        v-model="form.bill_number"
                        v-validate="'required|numeric'"
                        data-vv-name="bill_number"
                        data-vv-as="receipt number"
                        :error-messages="errors.collect('form.bill_number')"
                    />

                    <v-text-field
                        v-if="! state.redeemed"
                        type="number"
                        id="frmReceiptAmount"
                        label="Receipt amount"
                        prepend-inner-icon="receipt"
                        outline
                        v-model="form.bill_amount"
                        v-validate="'required|decimal:2|min_value:1|max_value:999999'"
                        data-vv-name="bill_amount"
                        data-vv-as="receipt amount"
                        :error-messages="errors.collect('form.bill_amount')"
                    />

                    <v-text-field
                        v-if="! state.redeemed"
                        type="number"
                        id="frmPartialPoints"
                        label="Amount of point to redeem"
                        prepend-inner-icon="redeem"
                        outline
                        v-model="form.partial_points"
                        v-validate="'numeric'"
                        data-vv-name="partial_points"
                        data-vv-as="amount of point to redeem"
                        :error-messages="errors.collect('form.partial_points')"
                    />

                    <div v-if="! state.redeemed">
                        <v-autocomplete
                            v-if="Object.keys(segments).length > 0"
                            label="Segments (optional)"
                            prepend-inner-icon="category"
                            :items="segments"
                            item-value="0"
                            item-text="1"
                            hide-no-data
                            hide-selected
                            chips
                            multiple
                            deletable-chips
                            v-model="form.segments"
                        />
                    </div>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="state.loading"
                        :disabled="state.redeemed"
                    >
                        Redeem Reward
                    </v-btn>

                    <v-btn
                        color="secondary"
                        text
                        @click="close"
                    >
                        Close
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, reactive, watch } from '@vue/composition-api';
import onScan from 'onscan.js';
import CryptoJS from 'crypto-js';

import ServiceRewardApi from '../services/RewardApi';

export default defineComponent({
    name: 'reward-transaction-discount',
    props: {
        segments: {
            type: Array,
            default: [],
        },
    },
    setup(_, { root }) {
        const state = reactive({
            open: false,
            loading: false,
            redeemed: false,
            response: {
                amount_left: 0,
                amount_return: 0,
                discount_point: 0,
                point_cost: 0,
                point_left: 0
            },
        });

        const form = reactive({
            reward: null,
            number: null,
            bill_amount: null,
            bill_number: null,
            partial_points: null,
            segments: []
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);

        watch(() => state.open, (current, previous) => {
            if (onScan.isAttachedTo(document)) {
                onScan.detachFrom(document);
            }

            if (current) {
                onScan.attachTo(document, {
                    onScan: (sScanned, iQty) => {
                        const decrypted = decryptedText(sScanned);

                        const code = decrypted.replace(/-/g, '');

                        if (code.length >= 9 && code.length <= 15) {
                            form.number = code;
                        }
                    },
                });
            }
        });

        const clearForm = () => {
            form.number = null;
            form.bill_number = null;
            form.bill_amount = null;
            form.partial_points = null;
            form.segments = [];
        };

        const open = () => {
            state.open = true;
        }

        const money_formatter = (money) => {
            const symbol = campaign.value.rewards.rewardValueSymbol;
            const currency = campaign.value.rewards.rewardValueCurrency;

            return `${symbol} ${money} ${currency}`;
        }

        const decryptedText = (value = '') => {
            const encodedWord = CryptoJS.enc.Base64.parse(value);
            const decoded = CryptoJS.enc.Utf8.stringify(encodedWord);

            return decoded;
        }

        return {
            state,
            form,
            locale,
            campaign,
            clearForm,
            open,
            money_formatter
        };
    },
    methods: {
        resetComponent() {
            this.state.loading = false;
            this.state.redeemed = false;
            this.state.response = {
                amount_left: 0,
                amount_return: 0,
                discount_point: 0,
                point_cost: 0,
                point_left: 0
            };
            this.clearForm();
            this.$validator.reset();
        },
        async onSubmit() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            try {
                const response = await ServiceRewardApi().creditCustomer2Api({
                    locale: this.locale,
                    uuid: this.campaign.uuid,
                    reward: {
                        fraction_digits: this.campaign.rewards.FractionDigits,
                        bill_number: this.form.bill_number,
                        bill_amount: this.form.bill_amount,
                        partial_points: this.form.partial_points,
                    },
                    number: this.form.number,
                    segments: this.form.segments,
                    mode: 'redeem_transaction'
                });

                if (response.status === 'success') {
                    this.state.response = response.response;
                    this.state.redeemed = true;
                }
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field]
                        });
                    }
                } else {
                    this.$root.$snackbar(exception.message);
                }
            } finally {
                this.state.loading = false;
            }
        },
        close() {
            this.resetComponent();
            this.state.open = false;
        },
        onRepeat() {
            this.resetComponent();
        }
    },
});
</script>
