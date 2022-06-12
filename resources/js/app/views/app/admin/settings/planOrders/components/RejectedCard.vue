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
            <span class="text-h5">Rejected Subscription Plan</span>
        </v-card-title>

        <v-divider class="grey lighten-2"></v-divider>

        <v-card-text>
            <table class="my-3 body-1">
                <tbody>
                    <tr>
                        <td>Order ID</td>
                        <td>:</td>
                        <td>{{ content.order_id }}</td>
                    </tr>

                    <tr>
                        <td>Plan</td>
                        <td>:</td>
                        <td>{{ content.plan }}</td>
                    </tr>

                    <tr>
                        <td>Payment Method</td>
                        <td>:</td>
                        <td>{{ content.payment_method }}</td>
                    </tr>

                    <tr>
                        <td>Amount</td>
                        <td>:</td>
                        <td>{{ content.amount }}</td>
                    </tr>
                </tbody>
            </table>
        </v-card-text>

        <div v-if="content.is_confirm">
            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text>
                <p class="subtitle-1">Confirmation Detail</p>

                <table class="my-3 body-1">
                    <tbody>
                        <tr
                            v-if="content.is_cheque || content.is_bank_transfer"
                        >
                            <td>Merchant Bank</td>
                            <td>:</td>
                            <td>{{ content.merchant_bank_name }}</td>
                        </tr>

                        <tr
                            v-if="content.is_cheque"
                        >
                            <td>Cheque Number</td>
                            <td>:</td>
                            <td>{{ content.merchant_identifier }}</td>
                        </tr>

                        <tr
                            v-else-if="content.is_bank_transfer"
                        >
                            <td>Account Number</td>
                            <td>:</td>
                            <td>{{ content.merchant_identifier }}</td>
                        </tr>

                        <tr
                            v-else-if="content.is_lynx"
                        >
                            <td>Lynx Transaction ID</td>
                            <td>:</td>
                            <td>{{ content.merchant_identifier }}</td>
                        </tr>

                        <tr>
                            <td>Date of Deposit</td>
                            <td>:</td>
                            <td>{{ content.paid_at }}</td>
                        </tr>

                        <tr>
                            <td>Amount</td>
                            <td>:</td>
                            <td>{{ content.amount_paid }}</td>
                        </tr>

                        <tr v-if="content.receipt_url">
                            <td>Receipt</td>
                            <td>:</td>
                            <td>
                                <a :href="content.receipt_url" target="_blank">Download Here</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </v-card-text>
        </div>

        <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn
                color="secondary"
                text
                @click="onCloseClick"
            >
                Close
            </v-btn>

            <v-btn
                color="error"
                text
                @click="onOKClick"
            >
                Rejected
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';

import ServicePlanOrders from '../services/PlanOrders';

export default defineComponent({
    name: 'rejected-card',
    props: {
        content: {
            type: Object,
            default: {},
        },
    },
    setup(props, context) {
        const state = reactive({
            loading: false,
            payment_methods: {
                cheque: 'Cheque',
                bank_transfer: 'Bank Transfer',
                lynx: 'Lynx',
                yooz_pge: 'Yooz Payment Gateway'
            },
        });

        const onCloseClick = () => {
            context.emit('onClose');
        };

        const onOKClick = async () => {
            state.loading = true;

            let response = {
                status: 'primary',
                message: 'CONFIRMATION_MESSAGE',
            };

            try {
                if (props.content && props.content.order_id) {
                    response = await ServicePlanOrders().rejectedApi(props.content.order_id);

                    context.emit('onOK', response);
                }
            } catch (exception) {
                response = exception;
            }

            state.loading = false;
        };

        const dateFormatted = (date) => moment(date).format('dddd, DD MMMM YYYY');

        const amountFormatted = (amount) => `$ ${Math.trunc(amount)}`;

        return {
            state,
            onCloseClick,
            onOKClick,
            dateFormatted,
            amountFormatted
        };
    },
})
</script>
