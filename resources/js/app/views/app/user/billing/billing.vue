<template>
    <div style="height: 100%">
        <v-container fluid v-if="state.loading" style="height: 100%">
            <v-layout
                align-center
                justify-center
                row
                fill-height
                class="text-center"
                style="height: 100%"
            >
                <v-progress-circular
                    :size="50"
                    color="#304FFE"
                    indeterminate
                    class="ma-5"
                />
            </v-layout>
        </v-container>

        <v-card
            v-else
            flat
            color="transparent"
        >
            <v-toolbar
                flat
                color="transparent"
            >
                <v-toolbar-title>Billing</v-toolbar-title>
            </v-toolbar>

            <billing-stat
                :stat="state.stat"
                :options="state.statOptions"
            />

            <v-card-text>
                <data-table
                    :active="state.stat.active_plan"
                    :order="state.selected"
                >
                    <template slot="actions" slot-scope="props">
                        <v-btn
                            v-if="state.canBuy && state.stat.status === 'trial'"
                            color="success"
                            small
                            @click="onBuyNowClick(props.item)"
                        >
                            Buy Now
                        </v-btn>

                        <v-btn
                            v-if="state.canConfirm && state.selected.id == props.item.id"
                            color="warning"
                            small
                            @click="onConfirmPaymentClick"
                        >
                            Confirm Payment
                        </v-btn>

                        <v-btn
                            v-if="
                                state.canChange
                                && state.selected.id !== props.item.id
                            "
                            color="info"
                            small
                            :disabled="
                                state.stat.active_plan
                                && props.item.id === state.stat.active_plan.id
                                || props.item.price < (state.stat.active_plan.price / 100)
                            "
                            @click="onBuyNowClick(props.item, 'change')"
                        >
                            Change Plan
                        </v-btn>

                        <v-btn
                            v-if="state.canUpgrade && props.item.upgradeable"
                            color="primary"
                            small
                            @click="onBuyNowClick(props.item, 'upgrade')"
                        >
                            Upgrade Plan
                        </v-btn>
                    </template>
                </data-table>

                <v-dialog
                    persistent
                    v-model="state.checkout"
                    width="400"
                    :fullscreen="$vuetify.breakpoint.xsOnly"
                >
                    <checkout-form
                        :payment_methods="state.payment_methods"
                        :cheques="state.cheques"
                        :savings="state.savings"
                        :item="state.selected"
                        :action="state.action"
                        @onClose="onCloseEmit($event, 'checkout')"
                        @onCheckout="onCheckoutEmit"
                    ></checkout-form>
                </v-dialog>

                <v-dialog
                    persistent
                    v-model="state.confirm"
                    width="450"
                    :fullscreen="$vuetify.breakpoint.xsOnly"
                >
                    <confirm-form
                        :invoice="state.stat.invoice"
                        @onClose="onCloseEmit($event, 'confirm')"
                        @onCancel="onCancelOrderEmit"
                        @onSendConfirm="onSendConfirmEmit"
                    ></confirm-form>
                </v-dialog>

                <v-snackbar
                    :value="state.snackbar.value"
                    bottom
                    left
                    :color="state.snackbar.status"
                >
                    {{ state.snackbar.message }}
                </v-snackbar>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import { defineComponent, onMounted, reactive } from '@vue/composition-api';

import BillingStat from './components/BillingStat.vue';
import DataTable from './components/DataTable.vue';
import CheckoutForm from './components/CheckoutForm.vue';
import ConfirmForm from './components/ConfirmForm.vue';

import ServicePlanBillingApi from './services/PlanBillingsApi';

export default defineComponent({
    name: 'user-billing',
    components: {
        BillingStat,
        DataTable,
        CheckoutForm,
        ConfirmForm,
    },
    setup() {
        const state = reactive({
            loading: false,
            payment_methods: [],
            cheques: [],
            savings: [],
            stat: {},
            statOptions: {
                loading: false
            },
            selected: {},
            checkout: false,
            confirm: false,
            snackbar: {
                value: false,
                status: 'error',
                message: 'SNACKBAR_MESSAGE'
            },
            action: 'buy',
            canBuy: true,
            canConfirm: false,
            canChange: false,
            canUpgrade: false,
        });

        onMounted(async () => {
            state.loading = true;

            const { payment_methods, cheques, savings } = await ServicePlanBillingApi().initializeApi();
            const stat = await ServicePlanBillingApi().statApi();

            state.payment_methods = payment_methods;
            state.cheques = cheques;
            state.savings = savings;
            state.stat = stat;
            state.selected = stat.order_plan;

            onStatusChange(stat.status);

            state.loading = false;
        });

        const useSnackbar = (message = 'SNACKBAR_MESSAGE', status = 'error') => {
            state.snackbar.value = true;
            state.snackbar.status = status;
            state.snackbar.message = message;

            return;
        }

        const resetSnackbar = () => {
            state.snackbar.value = false;
            state.snackbar.status = 'error';
            state.snackbar.message = 'SNACKBAR_MESSAGE';

            return;
        }

        const resetState = () => {
            state.selected = {};

            resetSnackbar();

            return;
        }

        const changeAction = (
            canBuy = true,
            canConfirm = false,
            canChange = false,
            canUpgrade = false
        ) => {
            state.canBuy = canBuy;
            state.canConfirm = canConfirm;
            state.canChange = canChange;
            state.canUpgrade = canUpgrade;

            return;
        }

        const onStatusChange = (status = 'trial') => {
            switch(status) {
                case 'pending':
                    changeAction(false, true, true, false);
                    break;
                case 'confirm':
                    changeAction(false, false, false, false);
                    break;
                case 'active':
                    changeAction(false, false, false, true);
                    break;
                default:
                    changeAction(true, false, false, false);
            }

            return;
        }

        const onCloseEmit = (status, action) => {
            resetSnackbar();

            onStatusChange(state.stat.status);

            state.selected = state.stat.order_plan;

            state[action] = false;

            return;
        }

        const onBuyNowClick = (item, action = 'buy') => {
            resetState();

            state.checkout = true;
            state.selected = item;
            state.action = action;

            if (action === 'change') {
                changeAction(false, false, true, false);
            }

            return;
        }

        const onCheckoutEmit = (response) => {
            if (response.status && response.status === 'error') {
                useSnackbar(response.message, response.status);
            }

            if (response.data && response.data.redirect) {
                window.open(response.data.redirect, '_self');

                state.loading = true;
            }

            if(response.data && response.data.html) {
                var winUrl = window.open('', '_self');
                winUrl.document.write(response.data.html);

                if (response.data.html.indexOf('autoInitForm') !== -1) {
                    winUrl.document.forms.autoInitForm.submit();
                }
            }

            if (response.data && response.data.stat) {
                state.stat = response.data.stat;

                onStatusChange(response.data.stat.status);
            }

            state.checkout = false;

            return;
        }

        const onConfirmPaymentClick = () => {
            resetSnackbar();

            state.confirm = true;
        }

        const onCancelOrderEmit = (response) => {
            if (response.status === 'success' && response.data && response.data.stat) {
                state.stat = response.data.stat;
                state.selected = {};

                onStatusChange(response.data.stat.status);
            }

            useSnackbar(response.message, response.status);

            state.confirm = false;
        }

        const onSendConfirmEmit = (response) => {
            if (response.status === 'success' && response.data && response.data.stat) {
                state.stat = response.data.stat;

                onStatusChange(response.data.stat.status);
            }

            useSnackbar(response.message, response.status);

            state.confirm = false;
        }

        return {
            state,
            onCloseEmit,
            onBuyNowClick,
            onCheckoutEmit,
            onConfirmPaymentClick,
            onCancelOrderEmit,
            onSendConfirmEmit,
        };
    },
});
</script>
