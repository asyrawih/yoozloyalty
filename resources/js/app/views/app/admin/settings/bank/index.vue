<template>
    <app-page-layout
        :loading="state.loading"
        :pageOptions="state.pageOptions"
    >
        <template slot-scope="props">
            <data-table
                :search="props.options.search"
                :refresh="state.tableRefeshed"
            >
                <template slot="actions" slot-scope="props">
                    <v-icon
                        class="mr-5"
                        small
                        @click="onEdit(props.item.id)"
                    >
                        mdi-pencil
                    </v-icon>

                    <v-icon
                        small
                        @click="onDelete(props.item.id)"
                    >
                        mdi-delete
                    </v-icon>
                </template>
            </data-table>

            <app-snackbar ref="snackbar" />
            <app-confirm ref="confirm" />
        </template>

        <template slot="data_form">
            <v-fab-transition>
                <v-btn
                    color="pink"
                    dark
                    absolute
                    top
                    right
                    fab
                    @click="onCreate"
                >
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </v-fab-transition>

            <data-form ref="data_form" />
        </template>
    </app-page-layout>
</template>

<script>
import { defineComponent, onMounted, reactive, ref } from '@vue/composition-api';

import AppPageLayout from '../../../../components/AppPageLayout.vue';
import AppSnackbar from '../../../../components/ui/AppSnackbar.vue';
import AppConfirm from '../../../../components/ui/AppConfirm.vue';
import DataTable from './components/DataTable.vue';
import DataForm from './components/DataForm.vue';

import ServiceBankApi from './services/BankApi';

export default defineComponent({
    name: "admin-settings-bank",
    components: {
        AppPageLayout,
        AppSnackbar,
        AppConfirm,
        DataTable,
        DataForm,
    },
    setup() {
        const state = reactive({
            loading: false,
            tableRefeshed: false,
            pageOptions: {
                title: 'Bank Accounts',
            },
        });

        const snackbar = ref(null);
        const confirm = ref(null);
        const data_form = ref(null);

        return {
            state,
            snackbar,
            confirm,
            data_form,
        };
    },
    methods: {
        onCreate() {
            this.state.tableRefeshed = false;

            this.data_form.open()
                .then(success => {
                    if (success) {
                        this.state.tableRefeshed = true;

                        this.snackbar.success(success);
                    }
                })
                .catch(error => this.snackbar.error(error));
        },
        onEdit(id) {
            this.state.tableRefeshed = false;

            this.data_form
                .open(id)
                .then(success => {
                    if (success) {
                        this.state.tableRefeshed = true;

                        this.snackbar.success(success);
                    }
                })
                .catch(error => {
                    this.snackbar.error(error);
                });
        },
        onDelete(id) {
            this.state.tableRefeshed = false;

            this.confirm.open(this.$t("confirm_delete"), this.$t("delete"))
                .then(async (confirm) => {
                    if (confirm) {
                       this.state.loading = true;

                        try {
                            const response = await ServiceBankApi().deleteApi(id);

                            return response;
                        } catch (error) {
                            throw error;
                        } finally {
                            this.state.loading = false;
                        }
                    }
                })
                .then(response => {
                    if (response) {
                        this.snackbar.success(response.message);
                    }
                })
                .catch(error => {
                    this.snackbar.error(error.message);
                });
        },
    },
})
</script>
