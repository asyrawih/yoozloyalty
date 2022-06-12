<template>
    <div>
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
        >
            <template slot="item.status" slot-scope="props">
                <div class="d-flex justify-center">
                    <v-switch
                        v-model="props.item.is_active"
                        @change="onStatusChange(props.item)"
                    />
                </div>
            </template>

            <template slot="item.actions" slot-scope="props">
                <v-icon
                    class="mr-5"
                    small
                    @click="onEdit(props.item.id)"
                >
                    mdi-pencil
                </v-icon>

                <v-icon
                    class="mr-5"
                    small
                    @click="onDelete(props.item.id)"
                >
                    mdi-delete
                </v-icon>

                <v-icon
                    small
                    @click="onInfo(props.item.id)"
                >
                    info
                </v-icon>
            </template>

            <!-- <template slot="footer">
                <div class="ma-3">
                    <v-btn
                        depressed
                        color="error"
                        small
                        :disabled="state.disableDeleteSelected"
                    >
                        {{ $t("delete_selected") }}
                    </v-btn>
                </div>
            </template> -->
        </v-data-table>

        <confirm ref="delete_confirm" />

        <link-domain ref="link_domain" />
    </div>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import Confirm from './Confirm.vue';
import LinkDomain from './LinkDomain.vue';

import SmtpServiceApi from '../services/SmtpServiceApi';

export default defineComponent({
    components: {
        Confirm,
        LinkDomain,
    },
    props: {
        search: {
            type: String,
            default: '',
        },
        reload: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, { emit, refs }) {
        const state = reactive({
            loading: false,
            headers: [
                { text: 'SMTP Name', value: 'smtp_name', align: 'center', sortable: false },
                { text: 'Status', value: 'status', align: 'center', sortable: false },
                { text: 'Action', value: 'actions', align: 'center', sortable: false },
            ],
            loadingText: 'Loading smtp...',
            noDataText: 'No smtp service available.',
            options: {},
            items: [],
            selected: [],
            disableDeleteSelected: true,
            totalItems: 0,
            footerProps: {
                "items-per-page-options": [5, 10, 25, 50, 75, 100],
            },
        });

        onMounted(async () => {
            const { page, itemsPerPage } = state.options;

            await onLoaded({ page, itemsPerPage, search: props.search });
        });

        watch(() => props.search, async (current, previous) => {
            if (current === '' || current.length > 3) {
                const { page, itemsPerPage } = state.options;

                await onLoaded({ itemsPerPage, page, search: props.search });
            }
        });

        watch(() => state.options, async (current, previous) => {
            const { page, itemsPerPage } = current;

            await onLoaded({ itemsPerPage, page, search: props.search });
        });

        watch(() => props.reload, async (current, previous) => {
            if (current) {
                const { page, itemsPerPage } = state.options;

                await onLoaded({ itemsPerPage, page, search: props.search });
            }
        });

        watch(() => state.selected, (current, previous) => {
            state.disableDeleteSelected = current.length > 0 ? false : true;
        });

        const onLoaded = async (params) => {
            state.loading = true;

            const response = await SmtpServiceApi().datatableApi(params);

            state.items = response.data;
            state.totalItems = response.total;

            state.loading = false;

            return true;
        }

        const onDeletedSelectedClick = () => {
            if (state.selected.length > 0) {
                let array = _.map(state.selected, function (item) {
                    return item.uuid;
                });

                state.disableDeleteSelected = true;

                // context.emit('onDeletedSelected', array);
            }

            return true;
        }

        const onStatusChange = async (item) => {
            try {
                const response = await SmtpServiceApi()
                    .updateApi({ 'is_active': item.is_active }, item.id);

                if (response.status === 'success') {
                    emit('onSuccess', response.message);
                }
            } catch (exception) {
                if (exception.message) {
                    emit('onError', exception.message);
                }
            }
        };

        const onEdit = (id) => {
            emit('onEdit', id);
        };

        const onDelete = async (id) => {
            const confirm = await refs.delete_confirm.open(
                'Do you want to delete the selected record(s)? This cannot be undone.',
                'Delete'
            );

            if (! confirm) {
                return false;
            }

            try {
                const response = await SmtpServiceApi().deleteApi(id);

                if (response.status === 'success') {
                    emit('onSuccess', response.message);
                }
            } catch (exception) {
                if (exception.message) {
                    emit('onError', exception.message);
                }
            } finally {
                const { page, itemsPerPage } = state.options;

                await onLoaded({ page, itemsPerPage, search: props.search });
            }
        };

        const onInfo = (id) => {
            refs.link_domain.open(id);
        };

        return {
            state,
            onDeletedSelectedClick,
            onStatusChange,
            onEdit,
            onDelete,
            onInfo,
        };
    },
});
</script>
