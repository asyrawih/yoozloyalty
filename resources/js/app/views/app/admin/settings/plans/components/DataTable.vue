<template>
    <v-data-table
        :headers="state.headers"
        :loading="state.loading"
        :loading-text="state.loadingText"
        :no-data-text="state.noDataText"
        :options.sync="state.options"
        :items="state.items"
        :server-items-length="state.totalItems"
        :footer-props="state.footerProps"
        v-model="state.selected"
        show-select
    >
        <template slot="item.actions" slot-scope="props">
            <slot name="actions" :item="props.item"></slot>
        </template>

        <template slot="footer">
            <div class="ma-3">
                <v-btn
                    depressed
                    color="error"
                    small
                    :disabled="state.disableDeleteSelected"
                    @click="onDeletedSelectedClick"
                >
                    {{ $t("delete_selected") }}
                </v-btn>
            </div>
        </template>
    </v-data-table>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';
import _ from 'lodash';

import ServicePlanApi from '../services/PlanApi';

export default defineComponent({
    name: "data-table",
    props: {
        search: {
            type: String,
            default: ''
        },
        refresh: {
            type: Boolean,
            default: false,
        }
    },
    setup(props, context) {
        const state = reactive({
            loading: false,
            headers: [
                { text: 'ID', value: 'id', align: 'center', sortable: false },
                { text: 'Plan Name', value: 'name', align: 'center', sortable: false },
                { text: 'Monthly Price', value: 'price_formatted', align: 'center', sortable: true },
                { text: 'Customers', value: 'limitations_customers', align: 'center', sortable: false },
                { text: 'Website', value: 'limitations_campaigns', align: 'center', sortable: false },
                { text: 'Reward Offer', value: 'limitations_rewards', align: 'center', sortable: false },
                { text: 'Stores', value: 'limitations_businesses', align: 'center', sortable: false },
                { text: 'Staff', value: 'limitations_staff', align: 'center', sortable: false },
                { text: 'Segments', value: 'limitations_segments', align: 'center', sortable: false },
                { text: 'Merchants', value: 'merchants_count', align: 'center', sortable: false },
                { text: '', value: 'actions', align: 'center', sortable: false },
            ],
            loadingText: 'Loading plans...',
            noDataText: 'No plan available',
            options: {},
            items: [],
            selected: [],
            disableDeleteSelected: true,
            totalItems: 0,
            footerProps: {
                "items-per-page-options": [5, 10, 25, 50, 75, 100],
            },
        });

        watch(() => props.search, async (after, before) => {
            if (! after || after === '' || after.length >= 3) {
                const { sortBy, sortDesc, page, itemsPerPage } = state.options;

                await onLoaded({ itemsPerPage, page, sortBy, sortDesc, search: props.search });
            }
        });

        watch(() => state.options, async ({sortBy, sortDesc, page, itemsPerPage}, before) => {
            await onLoaded({ itemsPerPage, page, sortBy, sortDesc, search: props.search });
        });

        watch(() => props.refresh, async (after, before) => {
            if (after) {
                const { sortBy, sortDesc, page, itemsPerPage } = state.options;

                await onLoaded({ itemsPerPage, page, sortBy, sortDesc, search: props.search });
            }
        });

        watch(() => state.selected, (after, before) => {
            state.disableDeleteSelected = after.length > 0 ? false : true;
        });

        onMounted(async () => {
            const { sortBy, sortDesc, page, itemsPerPage } = state.options;

            await onLoaded({ sortBy, sortDesc, page, itemsPerPage, search: props.search });
        });

        const onLoaded = async (params) => {
            state.loading = true;

            const response = await ServicePlanApi().datatableApi(params);

            state.items = response.data;
            state.totalItems = response.meta.total;

            state.loading = false;
        };

        const onDeletedSelectedClick = () => {
            if (state.selected.length > 0) {
                let array = _.map(state.selected, function (item) {
                    return item.uuid;
                });

                state.disableDeleteSelected = true;

                context.emit('onDeletedSelected', array);
            }

            return;
        }

        return {
            state,
            onDeletedSelectedClick
        };
    },
})
</script>
