<template>
    <merchant-page-layout
        title="Points Credit Request"
    >
        <v-row class="mb-3">
            <v-col cols="12" :sm="6" :md="2">
                <v-select
                    id="filterStatus"
                    label="Status"
                    dense
                    :items="state.select.statuses"
                    v-model="state.filters.status"
                />
            </v-col>

            <v-col>
                <v-btn
                    @click="onRefreshedTable"
                >
                    Refresh
                </v-btn>
            </v-col>
        </v-row>

        <v-data-table
            :headers="state.headers"
            :items="state.items"
            :items-per-page="10"
            :options.sync="state.options"
            :loading="state.loading"
            :server-items-length="state.total"
            :footer-props="state.footerProps"
            v-model="state.selected"
            show-select
        >
            <template slot="item.status" slot-scope="props">
                <v-chip
                    v-if="props.item.status === 'pending'"
                    class="text-capitalize"
                    small
                >
                    {{ props.item.status }}
                </v-chip>

                <v-chip
                    v-if="props.item.status === 'rejected'"
                    color="red"
                    text-color="white"
                    class="text-capitalize"
                    small
                >
                    {{ props.item.status }}
                </v-chip>

                <v-chip
                    v-if="props.item.status === 'approved'"
                    color="green"
                    text-color="white"
                    class="text-capitalize"
                    small
                >
                    {{ props.item.status }}
                </v-chip>
            </template>

            <template
                slot="item.actions"
                slot-scope="props"
            >
                <v-icon
                    v-if="props.item.status === 'pending'"
                    class="mr-2"
                    color="success"
                    @click="onApproved(props.item.id)"
                >
                    done
                </v-icon>

                <v-icon
                    v-if="props.item.status === 'pending'"
                    class="ml-2"
                    color="error"
                    @click="onRejected(props.item.id)"
                >
                    close
                </v-icon>

                <v-icon
                    class="ml-2"
                    color="error"
                    @click="onDelete(props.item.id)"
                >
                    delete
                </v-icon>
            </template>

            <template slot="footer">
                <div class="ma-3">
                    <v-btn
                        depressed
                        color="success"
                        small
                        :disabled="state.disableApproveSelected"
                        @click="onBulkAction('approved')"
                    >
                        Approve Selected
                    </v-btn>

                    <v-btn
                        depressed
                        color="error"
                        small
                        :disabled="state.disableRejecteSelected"
                        @click="onBulkAction('rejected')"
                    >
                        Reject Selected
                    </v-btn>

                    <v-btn
                        depressed
                        color="error"
                        small
                        :disabled="state.disableDeleteSelected"
                        @click="onBulkAction('deleted')"
                    >
                        {{ $t("delete_selected") }}
                    </v-btn>
                </div>
            </template>
        </v-data-table>

        <app-snackbar ref="snackbar" />

        <app-confirm ref="confirm" />

        <data-import ref="data_import" />

        <data-form ref="data_form" />

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

                <span>Import Credit Request</span>
            </v-tooltip>

            <div class="mx-2"></div>

            <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="pink"
                        fab
                        dark
                        v-bind="attrs"
                        v-on="on"
                        @click="onCreate"
                    >
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>

                <span>Create Credit Request</span>
            </v-tooltip>
        </template>
    </merchant-page-layout>
</template>

<script>
import { defineComponent, onMounted, reactive, ref, watch } from '@vue/composition-api';
import _ from 'lodash';

import MerchantPageLayout from '../../../components/MerchantPageLayout.vue';
import AppSnackbar from '../../../components/ui/AppSnackbar.vue';
import AppConfirm from '../../../components/ui/AppConfirm.vue';
import DataImport from './components/DataImport.vue';
import DataForm from './components/DataForm.vue';

import ServiceCreditRequest from './services/CreditRequestApi';

export default defineComponent({
    name: 'merchant-credit-request',
    components: {
        MerchantPageLayout,
        AppSnackbar,
        AppConfirm,
        DataImport,
        DataForm,
    },
    setup(props, { root }) {
        const state = reactive({
            loading: false,
            refreshedTable: false,
            headers: [
                { text: 'Website', value: 'campaign_text', align: 'center', sortable: false },
                { text: 'Customer Name', value: 'name', align: 'center', sortable: false },
                { text: 'Customer Email', value: 'email', align: 'center', sortable: false },
                { text: 'Customer Number', value: 'number', align: 'center', sortable: false },
                { text: 'Transaction Date', value: 'created_at', align: 'center', sortable: false },
                { text: 'Receipt Number', value: 'receipt_number', align: 'center', sortable: false },
                { text: 'Receipt Amount', value: 'receipt_amount', align: 'center', sortable: false },
                { text: 'Points', value: 'points', align: 'center', sortable: false },
                { text: 'Status', value: 'status', align: 'center', sortable: false },
                { text: 'Actions', value: 'actions', align: 'center', sortable: false },
            ],
            selected: [],
            disableDeleteSelected: true,
            disableApproveSelected: true,
            disableRejecteSelected: true,
            items: [],
            options: {},
            total: 0,
            filters: {
                status: '',
            },
            select: {
                statuses: [
                    { text: 'All', value: '' },
                    { text: 'Pending', value: 'pending' },
                    { text: 'Approved', value: 'approved' },
                    { text: 'Rejected', value: 'rejected' },
                ]
            },
            footerProps: {
                'items-per-page-options': [5, 10, 25, 50, 75, 100],
            },
        });

        onMounted(async () => {
            const { page, itemsPerPage } = state.options;

            await getCreditRequests(page, itemsPerPage);
        });

        watch(() => state.selected, async (current, previous) => {
            if (current.length > 0) {
                state.disableDeleteSelected = false;
                state.disableApproveSelected = false;
                state.disableRejecteSelected = false;
            } else {
                state.disableDeleteSelected = true;
                state.disableApproveSelected = true;
                state.disableRejecteSelected = true;
            }
        });

        watch(() => state.refreshedTable, async (current, previous) => {
            if (current) {
                const { page, itemsPerPage } = state.options;

                await getCreditRequests(page, itemsPerPage);
            }
        });

        watch(() => state.options, async (current, previous) => {
            const { page, itemsPerPage } = state.options;

            await getCreditRequests(page, itemsPerPage);
        }, { deep: true });

        watch(() => state.filters, async (current, previous) => {
            const { page, itemsPerPage } = state.options;

            await getCreditRequests(page, itemsPerPage);
        }, { deep: true });

        const getCreditRequests = async (page = 1, perPage = 10) => {
            state.loading = true;

            const response = await ServiceCreditRequest().datatableApi({
                page,
                perPage,
                'filters[status]': state.filters.status,
            });

            state.items = response.data;
            state.total = response.meta.total;

            state.refreshedTable = false;
            state.loading = false;
        };

        const update = async (id, status) => {
            state.loading = true;

            try {
                await ServiceCreditRequest().updateApi(id, {
                    status,
                });

                return true;
            } catch (exception) {
                throw exception;
            } finally {
                state.loading = false;
            }
        }

        const snackbar = ref(null);
        const confirm = ref(null);
        const data_form = ref(null);
        const data_import = ref(null);

        return {
            state,
            snackbar,
            confirm,
            data_import,
            data_form,
            update,
        };
    },
    methods: {
        onRefreshedTable() {
            this.state.refreshedTable = true;
        },
        onImport() {
            try {
                this.data_import.open()
                    .then(success => {
                        if (success) {
                            this.state.refreshedTable = true;

                            this.snackbar.success(success);
                        }
                    })
                    .catch(error => this.snackbar.error(error));
            } catch (exception) {}
        },
        onCreate() {
            this.state.refreshedTable = false;

            try {
                this.data_form.open()
                    .then(success => {
                        if (success) {
                            this.state.refreshedTable = true;

                            this.snackbar.success(success);
                        }
                    })
                    .catch(error => this.snackbar.error(error));
            } catch (exception) {}
        },
        onApproved(id = null) {
            this.state.refreshedTable = false;

            this.confirm.open('Are you sure you want to approve this request ?', 'Confirm Approval')
                .then((confirm) => {
                    if (confirm) {
                        this.update(id, 'approved')
                            .then(success => {
                                if (success) {
                                    this.state.refreshedTable = true;

                                    this.snackbar.success('The request has been approved.');
                                }
                            })
                            .catch(error => {
                                if (error.message) {
                                    this.snackbar.error(error.message);
                                }
                            });
                    }
                });
        },
        onRejected(id = null) {
            this.state.refreshedTable = false;

            this.confirm.open('Are you sure you want to reject this request ?', 'Confirm Approval')
                .then((confirm) => {
                    if (confirm) {
                        this.update(id, 'rejected')
                            .then(success => {
                                if (success) {
                                    this.state.refreshedTable = true;

                                    this.snackbar.success('The request has been rejected.');
                                }
                            })
                            .catch(error => {
                                if (error.message) {
                                    this.snackbar.error(error.message);
                                }
                            });
                    }
                });
        },
        onDelete(id = null) {
            this.state.refreshedTable = false;

            this.confirm.open(this.$t("confirm_delete"), this.$t("delete"))
                .then((confirm) => {
                    if (confirm) {
                        ServiceCreditRequest()
                            .deleteApi(id)
                            .then((response) => {
                                if (response.status === 'success') {
                                    this.state.refreshedTable = true;

                                    this.snackbar.success(response.message);
                                }
                            })
                            .catch((error) => {
                                this.snackbar.error(error.message);
                            });
                    }
                });
        },
        onBulkAction(action = 'delete') {
            this.state.refreshedTable = false;

            let message = this.$t("confirm_delete");
            let title = this.$t("delete");
            let selected = [];

            switch (action) {
                case 'approved':
                    message = 'Are you sure you want to approve this request ?';
                    title = 'Confirm Approval';
                    break;
                case 'rejected':
                    message = 'Are you sure you want to reject this request ?';
                    title = 'Confirm Approval';
                    break;
            }

            if (this.state.selected.length > 0) {
                selected = _.map(this.state.selected, 'uuid');
            }

            this.confirm.open(message, title)
                .then((confirm) => {
                    if (confirm) {
                        ServiceCreditRequest()
                            .bulkActionsApi({
                                action,
                                selected,
                            })
                            .then((response) => {
                                if (response.status === 'success') {
                                    this.state.refreshedTable = true;
                                    this.state.selected = [];

                                    this.snackbar.success(response.message);
                                }
                            })
                            .catch((error) => {
                                this.snackbar.error(error.message);
                            });
                    }
                });
        }
    },

});
</script>
