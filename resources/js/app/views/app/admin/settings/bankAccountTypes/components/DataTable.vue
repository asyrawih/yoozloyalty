<template>
    <v-data-table
        :headers="state.headers"
        :loading="state.loading"
        :loading-text="state.loadingText"
        :no-data-text="state.noDataText"
        :options.sync="state.options"
        :items="state.items"
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

import ServiceBankAccountTypeApi from '../services/BankAccountTypeApi';

export default defineComponent({
    name: "admin-settings-bank-account-type-table",
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
                { text: 'Account Type', align: 'center', value: 'name', sortable: false },
                { text: "Status", align: 'center', value: "is_active", sortable: false },
                { text: "Action", align: 'center', value: "actions", sortable: false },
            ],
            loadingText: 'Loading bank account types...',
            noDataText: 'No bank account type available.',
            options: {},
            items: [],
            footerProps: {
                "items-per-page-options": [5, 10, 25, 50, 75, 100],
            },
        });

        watch(() => props.search, async (after, before) => {
            if (after === '' || after.length >= 3) {
                await onLoaded({ search: props.search });
            }
        });

        watch(() => props.refresh, async (after, before) => {
            if (after) {
                await onLoaded({ search: props.search });
            }
        });

        onMounted(async () => {
            await onLoaded({ search: props.search });
        });

        const onLoaded = async (params) => {
            state.loading = true;

            const response = await ServiceBankAccountTypeApi().indexApi(params);

            state.items = response.data;

            state.loading = false;

            return;
        };

        return {
            state,
        };
    },
});
</script>
