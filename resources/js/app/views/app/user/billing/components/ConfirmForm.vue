<template>
    <v-card>
        <v-overlay
            :value="state.loading"
        >
            <v-progress-circular
                :size="50"
                color="primary"
                indeterminate
                class="ma-5"
            />
        </v-overlay>

        <v-card-title>
            <span>Confirm Payment</span>

            <v-spacer></v-spacer>

            <v-btn
                icon
                @click="onCloseClick"
            >
                <v-icon>mdi-close</v-icon>
            </v-btn>
        </v-card-title>

        <v-divider class="grey lighten-2"></v-divider>

        <v-form
            ref="form"
            lazy-validation
            data-vv-scope="form"
        >
            <v-card-text class="my-1">
                <v-text-field
                    v-if="['cheque', 'bank_transfer'].includes(state.payment_method)"
                    id="frmMerchantBankName"
                    ref="merchant_bank_name"
                    class="mb-3"
                    label="Bank Name"
                    v-model="form.merchant_bank_name"
                    v-validate="'required'"
                    data-vv-name="merchant_bank_name"
                    data-vv-as="bank name"
                    :error-messages="errors.collect('form.merchant_bank_name')"
                />

                <v-text-field
                    v-if="state.payment_method === 'cheque'"
                    id="frmMerchantIdentifier"
                    ref="merchant_identifier"
                    class="mb-3"
                    label="Cheque Number"
                    v-model="form.merchant_identifier"
                    v-validate="'required'"
                    data-vv-name="merchant_identifier"
                    data-vv-as="cheque number"
                    :error-messages="errors.collect('form.merchant_identifier')"
                />

                <v-text-field
                    v-else-if="state.payment_method === 'bank_transfer'"
                    id="frmMerchantIdentifier"
                    ref="merchant_identifier"
                    class="mb-3"
                    label="Account Number"
                    v-model="form.merchant_identifier"
                    v-validate="'required'"
                    data-vv-name="merchant_identifier"
                    data-vv-as="account number"
                    :error-messages="errors.collect('form.merchant_identifier')"
                />

                <v-text-field
                    v-else-if="state.payment_method === 'lynx'"
                    id="frmMerchantIdentifier"
                    ref="merchant_identifier"
                    class="mb-3"
                    label="Lynx Transaction ID"
                    v-model="form.merchant_identifier"
                    v-validate="'required'"
                    data-vv-name="merchant_identifier"
                    data-vv-as="lynx transaction id"
                    :error-messages="errors.collect('form.merchant_identifier')"
                />

                <v-text-field
                    type="number"
                    id="frmAmountPaid"
                    ref="amount_paid"
                    class="mb-3"
                    label="Amount"
                    prepend-icon="mdi-currency-usd"
                    v-model="form.amount_paid"
                    v-validate="
                        `required|numeric|min_value:${state.amount}|max_value:${state.amount}`
                    "
                    data-vv-name="amount_paid"
                    data-vv-as="amount"
                    :error-messages="errors.collect('form.amount_paid')"
                />

                <v-dialog
                    ref="paid_at_dialog"
                    v-model="state.paid_at_dialog"
                    :return-value.sync="form.paid_at"
                    persistent
                    width="290px"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                            ref="paid_at"
                            id="frmPaidAt"
                            v-model="date_formatted"
                            label="Date of Payment"
                            prepend-icon="mdi-calendar"
                            placeholder="dd/mm/yyyy"
                            persistent-placeholder
                            readonly
                            v-bind="attrs"
                            v-on="on"
                            v-validate="'required'"
                            data-vv-name="paid_at"
                            data-vv-as="date of payment"
                            :error-messages="errors.collect('form.paid_at')"
                        />
                    </template>

                    <v-date-picker
                        v-model="form.paid_at"
                        scrollable
                    >
                        <v-spacer></v-spacer>

                        <v-btn
                            text
                            color="secondary"
                            @click="state.paid_at_dialog = false"
                        >
                            Close
                        </v-btn>

                        <v-btn
                            text
                            color="primary"
                            @click="$refs.paid_at_dialog.save(form.paid_at)"
                        >
                            Ok
                        </v-btn>
                    </v-date-picker>
                </v-dialog>

                <v-file-input
                    ref="receipt"
                    id="frmReceipt"
                    label="Receipt (optional)"
                    accept="application/pdf"
                    v-model="form.receipt"
                    hint="Allowed file extensions: .PDF"
                    persistent-hint
                    v-validate="'ext:pdf'"
                    data-vv-name="receipt"
                    data-vv-as="receipt"
                    :error-messages="errors.collect('form.receipt')"
                />
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    color="error"
                    text
                    @click="onCancelClick"
                >
                    Cancel Order
                </v-btn>

                <v-btn
                    color="primary"
                    text
                    @click="onSendConfirmClick"
                >
                    Send Confirm
                </v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</template>

<script>
import { computed, defineComponent, onMounted, reactive, watch } from '@vue/composition-api';
import moment from 'moment';

import ServiceBillingPlan from '../services/PlanBillingsApi';

export default defineComponent({
    $_veeValidate: {
        validator: 'new'
    },
    props: {
        invoice: {
            type: Object,
            default: null,
        },
    },
    setup(props, context) {
        const state = reactive({
            loading: false,
            order_id: '',
            payment_method: '',
            amount: 0,
            currency: 'TTD',
            paid_at_dialog: false
        });

        const form = reactive({
            merchant_bank_name: "",
            merchant_identifier: "",
            amount_paid: null,
            paid_at: "",
            receipt: []
        });

        watch(() => props.invoice, (current, old) => {
            if (current) {
                state.order_id = current.order_id;
                state.payment_method = current.payment_method;
                state.amount = number_formatted(current.amount);
                state.currency = current.currency_amount;
            }
        });

        onMounted(() => {
            if (props.invoice) {
                state.order_id = props.invoice.order_id;
                state.payment_method = props.invoice.payment_method;
                state.amount = number_formatted(props.invoice.amount);
                state.currency = props.invoice.currency_amount;
            }

            const dict = {
                custom: {
                    amount_paid: {
                        min_value: (attribute, value) => `The ${attribute} cannot be less than ${state.currency} ${value}.`,
                        max_value: (attribute, value) => `The ${attribute} cannot be greater than ${state.currency} ${value}.`,
                    }
                }
            }

            context.root.$validator.localize('en', dict)
        });

        const date_formatted = computed(() => form.paid_at
            ? moment(form.paid_at).format('DD/MM/YYYY')
            : ''
        );

        const number_formatted = (amount = 0) => Math.trunc(amount);

        return {
            state,
            form,
            date_formatted,
            number_formatted
        };
    },
    methods: {
        resetForm() {
            this.form.merchant_bank_name = "";
            this.form.merchant_identifier = "";
            this.form.amount_paid = null;
            this.form.paid_at = "";
            this.form.receipt = []

            this.$validator.reset();
        },
        onCloseClick() {
            this.resetForm();

            this.$emit('onClose');
        },
        async onCancelClick() {

            this.state.loading = true;

            let response = null

            try {
                response = await ServiceBillingPlan().cancelApi(this.invoice.order_id);
            } catch (exception) {
                response = exception;
            }

            this.resetForm();

            this.state.loading = false;

            this.$emit('onCancel', response);
        },
        async onSendConfirmClick() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            let response = null;

            const formData = new FormData();

            formData.append('order_id', this.state.order_id);
            formData.append('payment_method', this.state.payment_method);
            formData.append('amount', this.state.amount);
            formData.append('merchant_bank_name', this.form.merchant_bank_name);
            formData.append('merchant_identifier',
                ['cheque', 'bank_transfer', 'lynx'].includes(this.state.payment_method) 
                    ? this.form.merchant_identifier
                    : this.state.payment_method
            );
            formData.append('amount_paid', this.form.amount_paid);
            formData.append('paid_at', this.form.paid_at);
            formData.append('receipt', this.form.receipt);

            try {
                response = await ServiceBillingPlan().confirmApi(formData);

                this.resetForm();
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field][0]
                        });
                    }
                } else {
                    response = exception;
                }
            }

            this.$emit('onSendConfirm', response);

            this.state.loading = false;
        }
    }
})
</script>
