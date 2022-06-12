<template>
    <merchant-page-layout
        title="Point Transaction History"
    >
        <v-data-table
            :headers="state.headers"
            :items="state.items"
            :items-per-page="10"
            :options.sync="state.options"
            :loading="state.loading"
            :server-items-length="state.total"
            :footer-props="state.footerProps"
        >
            <template slot="item.bill_number" slot-scope="props">
                {{ (props.item.bill_number ? props.item.bill_number : "00000" )}}
            </template>

            <template slot="item.event" slot-scope="props">
                {{ (props.item.reward_title ? props.item.reward_title : props.item.event )}}
            </template>
        </v-data-table>

        <app-snackbar ref="snackbar" />

        <app-confirm ref="confirm" />

        <data-import ref="data_import" />

        <template slot="actions">
            <v-spacer></v-spacer>

            <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="primary"
                        fab
                        dark
                        v-bind="attrs"
                        v-on="on"
                        @click="onImport"
                    >
                        <v-icon>file_upload</v-icon>
                    </v-btn>
                </template>

                <span>Import Transaction History</span>
            </v-tooltip>
        </template>
    </merchant-page-layout>
</template>

<script>
import { defineComponent, onMounted, reactive, ref, watch } from '@vue/composition-api';

import MerchantPageLayout from '../../../components/MerchantPageLayout.vue';
import AppSnackbar from '../../../components/ui/AppSnackbar.vue';
import AppConfirm from '../../../components/ui/AppConfirm.vue';
import DataImport from './components/DataImport.vue';

import ServiceTransactionHistory from './services/TransactionHistoryApi';

export default defineComponent({
    name: 'merchant-transaction-history',
    components: {
        MerchantPageLayout,
        AppSnackbar,
        AppConfirm,
        DataImport,
    },
    setup() {
        const state = reactive({
            loading: false,
            refresh: false,
            headers: [
                { text: 'Website', value: 'campaign.name', align: 'center', sortable: false },
                { text: 'Customer Name', value: 'customer.name', align: 'center', sortable: false },
                { text: 'Transaction Date', value: 'transaction_date', align: 'center', sortable: false },
                { text: 'Identifier', value: 'bill_number', align: 'center', sortable: false },
                { text: 'Points', value: 'points', align: 'center', sortable: false },
                { text: 'Event', value: 'event', align: 'center', sortable: false },
            ],
            items: [],
            options: {},
            total: 0,
            footerProps: {
                'items-per-page-options': [5, 10, 25, 50, 75, 100],
            },
        });

        watch(() => state.refresh, async (current, previous) => {
            if (current) {
                const { page, itemsPerPage } = state.options;

                await getTransactionHistories(page, itemsPerPage);
            }
        });

        watch(() => state.options, async (current, previous) => {
            const { page, itemsPerPage } = state.options;

            await getTransactionHistories(page, itemsPerPage);
        }, { deep: true });

        onMounted(async () => {
            const { page, itemsPerPage } = state.options;

            await getTransactionHistories(page, itemsPerPage);
        });

        const getTransactionHistories = async (page = 1, perPage = 10) => {
            state.loading = true;

            const response = await ServiceTransactionHistory().datatableApi({
                page,
                perPage,
            });

            state.items = response.data;
            state.total = response.meta.total;

            state.refresh = false;
            state.loading = false;
        }

        const snackbar = ref(null);
        const confirm = ref(null);
        const data_import = ref(null);

        return {
            state,
            snackbar,
            confirm,
            data_import,
        };
    },
    methods: {
        onRefresh() {
            this.state.refresh = true;
        },
        onImport() {
            try {
                this.data_import.open()
                    .then(success => {
                        if (success) {
                            this.state.refresh = true;

                            this.snackbar.success(success);
                        }
                    })
                    .catch(error => this.snackbar.error(error));
            } catch (exception) {}
        },
    },
});
</script>
