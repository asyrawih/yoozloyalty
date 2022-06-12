<template>
    <v-card>
        <v-overlay :value="state.loading">
            <v-progress-circular
                :size="50"
                color="#304FFE"
                indeterminate
                class="ma-5"
            />
        </v-overlay>

        <v-card-title> Checkout </v-card-title>

        <v-card-text>
            <strong>Price</strong>

            <br />

            <span class="text-h4">{{ state.price_formatted }}</span>
        </v-card-text>

        <v-divider class="grey lighten-2"></v-divider>

        <v-form>
            <v-card-text class="my-1">
                <v-select
                    v-model="form.payment_method"
                    class="mb-3"
                    :items="payment_methods"
                    label="Payment Method List"
                    @change="onChangePaymentMethod"
                />

                <v-select
                    v-if="form.payment_method === 'cheque'"
                    v-model="form.bank_id"
                    class="mb-3"
                    :items="cheques"
                    label="Bank List"
                    @change="onChangeBankDetail"
                />

                <v-select
                    v-else-if="form.payment_method === 'bank_transfer'"
                    v-model="form.bank_id"
                    class="mb-3"
                    :items="savings"
                    label="Bank List"
                    @change="onChangeBankDetail"
                />
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    color="secondary"
                    text
                    @click="onCloseClick"
                    :disabled="state.loading"
                >
                    Close
                </v-btn>

                <v-btn
                    color="primary"
                    text
                    @click="onCheckoutClick"
                    :disabled="!state.canCheckout || state.loading"
                >
                    Checkout
                </v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</template>

<script>
import {
    defineComponent,
    onMounted,
    reactive,
    watch,
} from "@vue/composition-api";

import ServiceBillingPlans from "../services/PlanBillingsApi";

const canCheckoutByPass = [
    "lynx",
    "yooz_pg",
    "paytm",
    "payu",
    "instamojo",
    "paypal",
];

export default defineComponent({
    props: {
        payment_methods: {
            type: Array,
            default: [],
        },
        cheques: {
            type: Array,
            default: [],
        },
        savings: {
            type: Array,
            default: [],
        },
        item: Object,
        action: {
            type: String,
            default: "buy",
        },
    },
    setup(props, context) {
        const state = reactive({
            loading: false,
            price_formatted: "",
            canCheckout: false,
        });

        const form = reactive({
            plan_id: null,
            payment_method: null,
            bank_id: null,
            amount: 0,
            currency: "",
            action: "buy",
        });

        watch(
            () => props.item,
            (current, old) => {
                if (current) {
                    state.price_formatted = current.price_formatted;

                    form.plan_id = current.id;
                    form.amount = current.price;
                    form.currency = current.currency;
                    form.action = props.action;
                }
            },
            { deep: true }
        );

        onMounted(() => {
            if (props.item) {
                state.price_formatted = props.item.price_formatted;

                form.plan_id = props.item.id;
                form.amount = props.item.price;
                form.currency = props.item.currency;
                form.action = props.action;
            }
        });

        const onChangePaymentMethod = () => {
            state.canCheckout = false;

            form.bank_id = null;

            if (canCheckoutByPass.includes(form.payment_method)) {
                state.canCheckout = true;
            }
        };

        const onChangeBankDetail = () => (state.canCheckout = true);

        const resetForm = () => {
            form.plan_id = null;
            form.payment_method = null;
            form.bank_id = null;
            form.amount = 0;
            form.currency = "";
            form.action = "buy";

            state.price_formatted = "";
            state.canCheckout = false;
        };

        const onCloseClick = () => {
            resetForm();

            context.emit("onClose");
        };

        const onCheckoutClick = async () => {
            state.loading = true;

            let response = null;

            try {
                if (props.item) {
                    response = await ServiceBillingPlans().checkoutApi(form);
                }
            } catch (exception) {
                if (exception.status === "error") {
                    response = exception;
                }
            }

            resetForm();

            state.loading = false;

            context.emit("onCheckout", response);
        };

        return {
            state,
            form,
            onChangePaymentMethod,
            onChangeBankDetail,
            onCloseClick,
            onCheckoutClick,
        };
    },
});
</script>
