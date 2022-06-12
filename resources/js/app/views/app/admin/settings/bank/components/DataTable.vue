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
    >
        <template slot="item.account_type" slot-scope="props">
            {{ props.item.account_type_formatted }}
        </template>

        <template slot="item.is_active" slot-scope="props">
            <v-icon v-if="props.item.is_active" color="success">check</v-icon>

            <v-icon v-else color="error">close</v-icon>
        </template>

        <template slot="item.actions" slot-scope="props">
            <slot name="actions" v-bind:item="props.item"></slot>
        </template>
    </v-data-table>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import ServiceBankApi from '../services/BankApi';

export default defineComponent({
    name: "admin-settings-bank-table",
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
    setup(props, {root}) {
        const state = reactive({
            loading: false,
            headers: [
                { text: 'Bank Name', align: 'center', value: 'bank_name', sortable: false },
                { text: 'Account Name', align: 'center', value: 'account_name', sortable: false },
                { text: 'Account Number',align: 'center',value: 'account_number', sortable: false },
                { text: 'Account Type', align: 'center', value: 'account_type.name', sortable: false },
                { text: "Status", align: 'center', value: "is_active", sortable: false },
                { text: "Action", align: 'center', value: "actions", sortable: false },
            ],
            loadingText: 'Loading bank accounts...',
            noDataText: 'No bank account available.',
            options: {},
            items: [],
            disableDeleteSelected: true,
            totalItems: 0,
            footerProps: {
                "items-per-page-options": [5, 10, 25, 50, 75, 100],
            },
        });

        watch(() => props.search, async (after, before) => {
            if (after === '' || after.length >= 3) {
                const { sortBy, sortDesc, page, itemsPerPage } = state.options;

                await onLoaded({ itemsPerPage, page, sortBy, sortDesc, search: props.search });
            }
        });

        watch(() => state.options, async ({sortBy, sortDesc, page, itemsPerPage}, before) => {
            await onLoaded({ itemsPerPage, page, sortBy, sortDesc, search: props.search });
        }, { deep: true });

        watch(() => props.refresh, async (after, before) => {
            if (after) {
                const { sortBy, sortDesc, page, itemsPerPage } = state.options;

                await onLoaded({ itemsPerPage, page, sortBy, sortDesc, search: props.search });
            }
        });

        // watch(() => state.selected, (after, before) => {
        //     state.disableDeleteSelected = after.length > 0 ? false : true;
        // });

        onMounted(async () => {
            const { sortBy, sortDesc, page, itemsPerPage } = state.options;

            await onLoaded({ sortBy, sortDesc, page, itemsPerPage, search: props.search });
        });

        const onLoaded = async (params) => {
            state.loading = true;

            const response = await ServiceBankApi().datatableApi(params);

            state.items = response.data;
            state.totalItems = response.meta.total;

            state.loading = false;

            return;
        };

        return {
            state,
        };
    },
})
</script>
