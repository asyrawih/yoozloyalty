<template>
    <data-table
        model="App\Models\BillingInvoice"
    >
        <template v-slot:empty-head>
            <v-icon size="72" :color="$store.getters.app.color_name">business</v-icon>
        </template>

        <template v-slot:empty-text>
            <p class="subheading">Plan change request history.</p>
        </template>

    </data-table>
</template>
<script>
import { defineComponent, reactive } from '@vue/composition-api'

import DataTable from './components/DataTable.vue';

export default defineComponent({
    name: 'plan-orders',
    components: {
        DataTable,
    },
    setup() {
        const state = reactive({
            loading: false,
            snackbar: false,
            snackbar_color: 'success',
            snackbar_text: 'SNACKBAR_MESSAGE',
            tableRefresh: false,
            content: {},
        });

        const dialog = reactive({
            approved: false,
            rejected: false
        });

        const resetState = () => {
            state.snackbar = false;
            state.snackbar_color = 'success';
            state.snackbar_text = 'SNACKBAR_MESSAGE';
            state.tableRefresh = false;
            state.content = {};
        };

        const onOpenClick = (item, action) => {
            resetState();

            dialog[action] = true;

            state.content = item;
        };

        const onCloseClick = (action) => {
            resetState();

            dialog[action] = false;
        };

        const onOKClick = (response, action) => {
            state.content = {};
            state.snackbar_color = response.status;
            state.snackbar_text = response.message;

            dialog[action] = false;

            state.snackbar = true;
            state.tableRefresh = true;
        };

        return {
            state,
            dialog,
            onOpenClick,
            onCloseClick,
            onOKClick
        };
    },
})
</script>
