<template>
    <v-data-table
        :headers="state.table_headers"
        :items="state.items"
        :items-per-page="5"
        :mobile-breakpoint="0"
        :item-class="onSelectedPlanClass"
        :loading="state.loading"
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Available plans</v-toolbar-title>
            </v-toolbar>
        </template>

        <template slot="item.price" slot-scope="props">
            <strong>{{ `${props.item.price_formatted}/${props.item.interval}` }}</strong>
        </template>

        <template slot="item.actions" slot-scope="props">
            <slot name="actions" v-bind:item="props.item">
                COLUMN_ACTIONS
            </slot>
        </template>
    </v-data-table>
</template>

<script>
import { defineComponent, onMounted, reactive } from '@vue/composition-api';

import ServicePlanBillingApi from '../services/PlanBillingsApi';

export default defineComponent({
    props: {
        active: Object,
        order: Object,
    },
    setup(props) {
        const state = reactive({
            loading: false,
            table_headers: [
                { text: "Customers", value: "customers", align: "center", sortable: false },
                { text: "Websites", value: "campaigns", align: "center", sortable: false },
                { text: "Reward Offer", value: "rewards", align: "center", sortable: false },
                { text: "Stores", value: "businesses", align: "center", sortable: false },
                { text: "Staff members", value: "staff", align: "center", sortable: false },
                { text: "Segments", value: "segments", align: "center", sortable: false },
                { text: "Price Monthly", value: "price", align: "center", sortable: false },
                { value: "actions", align: "center", sortable: false }
            ],
            items: []
        });

        const onSelectedPlanClass = (item) => {
            if (props.active && props.active.id === item.id) {
                return 'green lighten-1 white--text';
            }

            if (props.order && props.order.id === item.id) {
                return 'yellow lighten-1';
            }

            return '';
        }

        onMounted(async () => {
            state.loading = true;

            state.items = await ServicePlanBillingApi().plansApi();

            state.loading = false;
        });

        return {
            state,
            onSelectedPlanClass
        };
    },
})
</script>
