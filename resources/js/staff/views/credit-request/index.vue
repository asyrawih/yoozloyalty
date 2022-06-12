<template>
    <div class="pb-5">
        <v-container class="pa-0" grid-list-lg fluid>
            <v-layout row wrap>
                <v-flex
                    class="py-0"
                    xs12
                    :style="{
                        'background-color': campaign.theme.secondaryColor,
                        'color': campaign.theme.secondaryTextColor
                    }"
                >
                    <v-container fill-height>
                        <v-layout row wrap align-center>
                            <v-flex xs12 style="z-index: 2">
                                <div class="mt-3 mb-7 pa-0">
                                    <h1 class="mb-1 display-1">
                                        Credit Request
                                    </h1>

                                    <div>
                                        Approval credit request from mobile.
                                    </div>
                                </div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-flex>
            </v-layout>
        </v-container>

        <v-container grid-list-lg class="mt-7">
            <v-layout row wrap>
                <v-flex>
                    <app-confirm ref="confirm" />
                    <app-snackbar ref="snackbar" />

                    <v-row>
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
                            <v-btn @click="onRefreshedTable">
                                Refresh
                            </v-btn>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col>
                            <v-data-table
                                :headers="state.headers"
                                :items="state.items"
                                :items-per-page="5"
                                :loading="state.loading"
                                :options.sync="state.options"
                                :server-items-length="state.total"
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
                                    v-if="props.item.status === 'pending'"
                                    slot="item.actions"
                                    slot-scope="props"
                                >
                                    <v-icon
                                        class="mr-5"
                                        color="success"
                                        @click="approved(props.item.id)"
                                    >
                                        done
                                    </v-icon>

                                    <v-icon
                                        color="error"
                                        @click="rejected(props.item.id)"
                                    >
                                        close
                                    </v-icon>
                                </template>
                            </v-data-table>
                        </v-col>
                    </v-row>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
import { computed, defineComponent, onBeforeMount, onMounted, reactive, watch } from '@vue/composition-api';

import AppConfirm from '../components/AppConfirm.vue';
import AppSnackbar from '../components/AppSnackbar.vue';

import ServiceCreditRequestApi from './services/CreditRequestApi';

export default defineComponent({
    components: {
        AppConfirm,
        AppSnackbar,
    },
    setup(_, { root }) {
        const state = reactive({
            loading: false,
            refreshedTable: false,
            headers: [
                { text: 'Website', value: 'campaign_text', align: 'center', sortable: false },
                { text: 'Name', value: 'name', align: 'center', sortable: false },
                { text: 'Email', value: 'email', align: 'center', sortable: false },
                { text: 'Customer Number', value: 'number', align: 'center', sortable: false },
                { text: 'Transaction Date', value: 'created_at', align: 'center', sortable: false },
                { text: 'Receipt Number', value: 'receipt_number', align: 'center', sortable: false },
                { text: 'Receipt Amount', value: 'receipt_amount', align: 'center', sortable: false },
                { text: 'Points', value: 'points', align: 'center', sortable: false },
                { text: 'Status', value: 'status', align: 'center', sortable: false },
                { text: 'Actions', value: 'actions', align: 'center', sortable: false },
            ],
            items: [],
            options: {},
            total: 0,
            filters: {
                status: ''
            },
            select: {
                statuses: [
                    {text: 'All', value: ''},
                    {text: 'Pending', value: 'pending'},
                    {text: 'Approved', value: 'approved'},
                    {text: 'Rejected', value: 'rejected'},
                ]
            }
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);

        onBeforeMount(() => {
            if (root.$auth.check() && ! root.$can('credit-request')) {
                root.$router.push({ name: 'dashboard' });
            }
        });

        onMounted(async () => {
            const { page, itemsPerPage } = state.options;

            await onLoaded(page, itemsPerPage, state.filters);
        });

        watch(() => state.refreshedTable, async (current, previous) => {
            if (current) {
                const { page, itemsPerPage } = state.options;

                await onLoaded(page, itemsPerPage, state.filters);
            }
        });

        watch(() => state.options, async (current, previous) => {
            const { page, itemsPerPage } = state.options;

            await onLoaded(page, itemsPerPage, state.filters);
        }, { deep: true });

        watch(() => state.filters, async (current, previous) => {
            const { page, itemsPerPage } = state.options;

            await onLoaded(page, itemsPerPage, current);
        }, { deep: true });

        const onLoaded = async (page = 1, perPage = 5, filters = {}) => {
            state.loading = true;

            const response = await ServiceCreditRequestApi().index({
                locale: locale.value,
                uuid: campaign.value.uuid,
                page,
                perPage,
                "filters[status]": filters.status,
            });

            state.items = response.data;
            state.total = response.meta.total;

            state.refreshedTable = false;
            state.loading = false;
        };

        const update = async (id, status) => {
            state.loading = true;

            try {
                await ServiceCreditRequestApi().update(id, {
                    locale: locale.value,
                    uuid: campaign.value.uuid,
                    status,
                });

                return true;
            } catch (exception) {
                throw exception;
            } finally {
                state.loading = false;
            }
        }

        return {
            state,
            campaign,
            update,
        };
    },
    methods: {
        onRefreshedTable() {
            this.state.refreshedTable = true;
        },
        approved(id) {
            this.state.refreshedTable = false;

            this.$refs.confirm.open('Are you sure you want to approve this request ?')
                .then((confirm) => {
                    if (confirm) {
                        this.update(id, 'approved')
                            .then(success => {
                                if (success) {
                                    this.state.refreshedTable = true;
                                    this.$refs.snackbar.success('The request has been approved.');
                                }
                            })
                            .catch(error => {
                                if (error.message) {
                                    this.$refs.snackbar.error(error.message);
                                }
                            });
                    }
                });
        },
        rejected(id) {
            this.state.refreshedTable = false;

            this.$refs.confirm.open('Are you sure you want to reject this request?')
                .then((confirm) => {
                    if (confirm) {
                        this.update(id, 'rejected')
                            .then(success => {
                                if (success) {
                                    this.state.refreshedTable = true;
                                    this.$refs.snackbar.success('The request has been rejected.');
                                }
                            })
                            .catch(error => {
                                if (error.message) {
                                    this.$refs.snackbar.error(error.message);
                                }
                            });
                    }
                });
        },
    },
});
</script>
