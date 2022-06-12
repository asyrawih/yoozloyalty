<template>
    <v-card-text>
        <div class="text-center" v-if="options.loading">
            <v-progress-circular
                :size="50"
                color="#304FFE"
                indeterminate
                class="ma-6"
            />
        </div>

        <div v-else>
            <v-alert
                v-if="stat.expired"
                dark
                color="error"
            >
                This account has expired. It is not possible to visit other
                pages until this has been resolved.
            </v-alert>

            <div v-if="! stat.expired && stat.status === 'trial'">
                <div class="title">
                    You are currently on the Trial.
                </div>

                <div class="subtitle-1">
                    Expires on {{ stat.subscription_expired_at }}.
                </div>
            </div>

            <div class="mb-3" v-if="state.active_plan && state.active_plan.id">
                <div class="title">
                    You are currently on the {{ state.active_plan.name }} plan.
                </div>

                <div class="subtitle-1 mb-3">
                    Next billing date is {{ stat.subscription_expired_at }}.
                </div>

                <v-alert dark color="info">
                    You can't downgrade plan until expired. You can only upgrade the plan,
                    no data or service will be lost during upgrade.
                </v-alert>
            </div>

            <div v-if="state.order_plan && state.order_plan.id">
                <div class="title">
                    You are currently order on the {{ state.order_plan.name }} plan.
                </div>

                <div class="subtitle-1" v-if="stat.status === 'pending'">
                    Order valid until {{ state.invoice.expired_at_formatted }}.
                </div>

                <div class="body-1 mt-3" v-if="state.bank && stat.status === 'pending'">
                    <span
                        v-if="state.invoice.payment_method === 'bank_transfer'"
                        class="font-weight-bold"
                    >
                        Please transfer funds to:
                    </span>

                    <span
                        v-if="state.invoice.payment_method === 'cheque'"
                        class="font-weight-bold"
                    >
                        Please deposit funds to:
                    </span>
                    <br />
                    <span>Account name - <strong>{{ state.bank.account_name }}</strong></span><br />
                    <span>Account number - <strong>{{ state.bank.account_number }}</strong></span><br />
                    <span>Bank name - <strong>{{ state.bank.bank_name }}</strong></span><br />
                    <span>Bank branch name - <strong>{{ state.bank.branch_name }}</strong></span><br />
                    <span>Bank branch code - <strong>{{ state.bank.branch_code }}</strong></span><br />
                </div>

                <v-alert
                    v-if="stat.status === 'confirm'"
                    dark color="info"
                    class="body-1 mt-3"
                >
                    Please waiting for approval from <strong>Admin</strong>.
                </v-alert>
            </div>
        </div>
    </v-card-text>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

export default defineComponent({
    name: 'billing-stat',
    props: {
        stat: {
            type: Object,
            default: {}
        },
        options: {
            type: Object,
            default: {
                loading: false
            }
        },
    },
    setup(props, context) {
        const state = reactive({
            active_plan: {},
            order_plan: {},
            invoice: {},
            bank: {}
        });

        watch(() => props.stat, (current, old) => {
            state.active_plan = current.active_plan;
            state.order_plan = current.order_plan;
            state.invoice = current.invoice;
            state.bank = current.bank;
        }, { deep: true });

        onMounted(() => {
            if (props.stat) {
                state.active_plan = props.stat.active_plan;
                state.order_plan = props.stat.order_plan;
                state.invoice = props.stat.invoice;
                state.bank = props.stat.bank;
            }
        });

        return {
            state,
        };
    },
});
</script>
