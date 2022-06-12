<template>
    <v-data-table
        :headers="state.headers"
        :loading="state.loading"
        :items="state.items"
        :items-per-page="5"
    >
        <template slot="item.permissions" slot-scope="props">
            {{ _.map(props.item.permissions, (permission) => {
                return _.startCase(permission);
            }).join(', ') }}
        </template>

        <template slot="item.actions" slot-scope="props">
            <slot name="actions" v-bind:item="props.item">
                COLUMN_ACTIONS
            </slot>
        </template>
    </v-data-table>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import ServiceStaffRoles from '../services/StaffRolesApi';

export default defineComponent({
    name: "data-table",
    props: {
        refresh: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, context) {
        const state = reactive({
            loading: false,
            headers: [
                { text: 'Role', value: 'name', align: 'center' },
                { text: 'Permissions', value: 'permissions', align: 'center' },
                { value: 'actions', align: 'center' }
            ],
            items: [],
        });

        watch(() => props.refresh, async (after, before) => {
            if (after) {
                state.loading = true;

                try {
                    const response = await ServiceStaffRoles().datatableApi();

                    state.items = response;
                } catch (exception) {
                    console.log(exception);
                }

                state.loading = false;
            }
        });

        onMounted(async () => {
            state.loading = true;

            try {
                const response = await ServiceStaffRoles().datatableApi();

                state.items = response;
            } catch (exception) {
                console.log(exception);
            }

            state.loading = false;
        });

        return {
            state
        };
    },
    computed: {
        _ () { return _; },
    }
})
</script>
