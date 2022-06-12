<template>
    <app-page-layout
        :loading="state.loading"
        useSearch
        :pageOptions="state.pageOptions"
    >
        <template slot-scope="props">
            <data-table
                :search="props.options.search"
                :refresh="state.tableRefesh"
                @onDeletedSelected="onDeletedSelected"
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

            <data-form
                ref="data_form"
                :currency_codes="state.currency_codes"
                :currency_symbols="state.currency_symbols"
            />
        </template>
    </app-page-layout>
</template>

<script>
import { defineComponent, reactive, ref } from '@vue/composition-api';

import AppPageLayout from '../../../../components/AppPageLayout.vue';
import AppSnackbar from '../../../../components/ui/AppSnackbar.vue';
import AppConfirm from '../../../../components/ui/AppConfirm.vue';
import DataTable from './components/DataTable.vue';
import DataForm from './components/DataForm.vue';

import ServicePlanApi from './services/PlanApi';

export default defineComponent({
    name: 'admin-settings-plans',
    components: {
        AppPageLayout,
        AppSnackbar,
        AppConfirm,
        DataTable,
        DataForm,
    },
    setup() {
        const state = reactive({
            loading: true,
            tableRefesh: false,
            currency_codes: [],
            currency_symbols: [],
            pageOptions: {
                title: 'Plans',
            },
        });

        const confirm = ref(null);
        const snackbar = ref(null);
        const data_form = ref(null);

        return {
            state,
            confirm,
            snackbar,
            data_form,
        };
    },
    async mounted() {
        const response = await ServicePlanApi().initializeApi({
            locale: this.$i18n.locale
        });

        this.state.currency_codes = _.toPairs(response.currency_codes);
        this.state.currency_symbols = response.currency_symbols;

        this.state.loading = false;
    },
    methods: {
        onCreate() {
            this.state.tableRefesh = false;

            this.data_form
                .open()
                .then(success => {
                    if (success) {
                        this.state.tableRefesh = true;

                        this.snackbar.success(success);
                    }
                })
                .catch(error => {
                    this.snackbar.error(error);
                });
        },
        onEdit(id) {
            this.state.tableRefesh = false;

            this.data_form
                .open(id)
                .then(success => {
                    if (success) {
                        this.state.tableRefesh = true;

                        this.snackbar.success(success);
                    }
                })
                .catch(error => {
                    this.snackbar.error(error);
                });
        },
        onDelete(id) {
            this.state.tableRefesh = false;

            this.confirm.open('Are you sure, you want to delete this data ?')
                .then(async (confirm) => {
                    if (confirm) {
                       this.state.loading = true;

                        try {
                            const response = await ServicePlanApi().destroyApi(id);

                            return response;
                        } catch (error) {
                            throw error;
                        } finally {
                            this.state.loading = false;
                        }
                    }
                })
                .then(response => {
                    this.snackbar.success(response.message);
                })
                .catch(error => {
                    this.snackbar.error(error.message);
                });
        },
        onDeletedSelected(plans) {
            this.state.tableRefesh = false;

            this.confirm.open('Are you sure, you want to delete this data ?')
                .then(async (confirm) => {
                    if (confirm) {
                       this.state.loading = true;

                        try {
                            const response = await ServicePlanApi().massdeleteApi({ plans });

                            return response;
                        } catch (error) {
                            throw error;
                        } finally {
                            this.state.loading = false;
                        }
                    }
                })
                .then(response => {
                    this.snackbar.success(response.message);
                })
                .catch(error => {
                    this.snackbar.error(error.message);
                });
        },
    }
})
</script>
