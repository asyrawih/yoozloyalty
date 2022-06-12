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
    >
        <template slot="item.avatar" slot-scope="props">
            <v-avatar
                :tile="false"
                size="32"
                color="grey lighten-4"
            >
                <v-img
                    v-if="props.item.avatar"
                    :src="props.item.avatar"
                ></v-img>
            </v-avatar>
        </template>

        <template slot="item.role" slot-scope="props">
            <span>{{ state.roles[props.item.role] }}</span>
        </template>

        <template slot="item.actions" slot-scope="props">
            <slot name="actions" v-bind:item="props.item"></slot>
        </template>

        <!-- <template slot="footer">
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
        </template> -->
    </v-data-table>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';
import _ from 'lodash';

import ServiceAdminUserApi from '../services/AdminUserApi';

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
                { text: 'Avatar', value: 'avatar', align: 'center', sortable: false },
                { text: 'Admin Name', value: 'name', align: 'center', sortable: true },
                { text: 'E-Mail', value: 'email', align: 'center', sortable: false },
                { text: 'Role', value: 'role', align: 'center', sortable: false },
                { text: '', value: 'actions', align: 'center', sortable: false },
            ],
            loadingText: 'Loading admin users...',
            noDataText: 'No admin user available',
            options: {},
            items: [],
            selected: [],
            disableDeleteSelected: true,
            totalItems: 0,
            footerProps: {
                "items-per-page-options": [5, 10, 25, 50, 75, 100],
            },
            roles: {
                1: 'Master Admin',
                2: 'Normal Admin'
            }
        });

        watch(() => props.search, async (after, before) => {
            if (after === '' || after.length > 3) {
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

            const response = await ServiceAdminUserApi().datatableApi(params);

            state.items = response.data;
            state.totalItems = response.meta.total;

            state.loading = false;

            return;
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
