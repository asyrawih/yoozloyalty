<template>
    <v-data-table
        :headers="state.table_headers"
        :items="state.items"
        :mobile-breakpoint="0"
        :loading="state.loading"
        :loading-text="state.loadingText"
        :no-data-text="state.noDataText"
        :options.sync="state.options"
        :server-items-length="state.totalItems"
        :footer-props="state.footerProps"
    >
        <template slot="item.status" slot-scope="props">
            <v-chip
                label
                small
                :color="props.item.status.color"
                text-color="white"
            >
                {{ props.item.status.text }}
            </v-chip>
        </template>

        <template slot="item.actions" slot-scope="props">
            <slot name="actions" v-bind:item="props.item">
            </slot>
        </template>
    </v-data-table>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';
import moment from 'moment';

import ServicePlanChangeRequestHistory from "../services/PlanChangeRequestHistory";

export default defineComponent({
    props: {
        refresh: {
            type: Boolean,
            default: false
        }
    },
    setup(props, context) {
        const state = reactive({
            loading: true,
            table_headers: [
                { text: "Order ID", value: "order_id", align: "center", sortable: false },
                { text: "Order Date", value: "created_at", align: "center", sortable: false },
                { text: "Previous Plan", value: "previous_plan", align: "center", sortable: false },
                { text: "Plan", value: "plan", align: "center", sortable: false },
                { text: "Payment Method", value: "payment_method", align: "center", sortable: false },
                { text: "Amount", value: "amount", align: "center", sortable: false },
                { text: "Status", value: "status", align: "center", sortable: false },
                { value: "actions", align: "center", sortable: false },
            ],
            loadingText: 'Loading orders...',
            noDataText: 'No order available',
            options: {},
            items: [],
            totalItems: 0,
            footerProps: {
                "items-per-page-options": [5, 10, 25, 50, 75, 100],
            },
        });

        watch(() => state.options, async ({sortBy, sortDesc, page, itemsPerPage}, before) => {
            await onLoaded({ itemsPerPage, page, sortBy, sortDesc, search: props.search });
        }, { deep: true });

        watch(() => props.refresh, async (after, before) => {
            if (after) {
                const { sortBy, sortDesc, page, itemsPerPage } = state.options;

                await onLoaded({ itemsPerPage, page, sortBy, sortDesc });
            }
        });

        onMounted(async () => {
            const { sortBy, sortDesc, page, itemsPerPage } = state.options;

            await onLoaded({ sortBy, sortDesc, page, itemsPerPage, search: props.search });
        });

        const onLoaded = async (params) => {
            state.loading = true;

            const response = await ServicePlanChangeRequestHistory().datatableApi(params);

            state.items = response.data;
            state.totalItems = response.meta.total;

            state.loading = false;

            return;
        }

        return {
            state,
        };
    },
})
</script>
