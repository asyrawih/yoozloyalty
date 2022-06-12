<template>
    <div style="height: 100%">
        <v-container fluid v-if="state.loading" style="height: 100%">
            <v-layout
                align-center
                justify-center
                row
                fill-height
                class="text-xs-center"
                style="height: 100%"
            >
                <v-progress-circular
                    :size="50"
                    :color="app.color_name"
                    indeterminate
                    class="ma-5"
                />
            </v-layout>
        </v-container>

        <v-card v-else>
            <v-toolbar flat>
                <v-toolbar-title>Plan Order</v-toolbar-title>
            </v-toolbar>

            <v-card-text>
                <v-snackbar
                    :value="state.snackbar"
                    :color="state.snackbar_color"
                    absolute
                    top
                    right
                    text
                >
                    {{ state.snackbar_text }}
                </v-snackbar>

                <data-table
                    :refresh="state.tableRefresh"
                >
                    <template
                        v-if="props.item.is_confirm"
                        slot="actions"
                        slot-scope="props"
                    >
                        <v-btn
                            color="success"
                            small
                            @click="onOpenClick(props.item, 'approved')"
                        >
                            Approved
                        </v-btn>

                        <v-btn
                            color="error"
                            small
                            @click="onOpenClick(props.item, 'rejected')"
                        >
                            Rejected
                        </v-btn>
                    </template>
                </data-table>

                <v-dialog
                    v-model="dialog.approved"
                    width="400"
                    persistent
                    :fullscreen="$vuetify.breakpoint.xsOnly"
                >
                    <approved-card
                        :content="state.content"
                        @onClose="onCloseClick('approved')"
                        @onOK="onOKClick($event, 'approved')"
                    ></approved-card>
                </v-dialog>

                <v-dialog
                    v-model="dialog.rejected"
                    width="400"
                    persistent
                    :fullscreen="$vuetify.breakpoint.xsOnly"
                >
                    <rejected-card
                        :content="state.content"
                        @onClose="onCloseClick('rejected')"
                        @onOK="onOKClick($event, 'rejected')"
                    ></rejected-card>
                </v-dialog>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api'

import DataTable from './components/DataTable.vue';
import ApprovedCard from './components/ApprovedCard.vue';
import RejectedCard from './components/RejectedCard.vue';

export default defineComponent({
    name: 'plan-orders',
    components: {
        DataTable,
        ApprovedCard,
        RejectedCard
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
