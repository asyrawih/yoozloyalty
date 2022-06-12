<template>
    <v-card>
        <v-card-title flat>
            <span>Staff Roles</span>

            <v-spacer></v-spacer>
        </v-card-title>

        <v-divider class="grey lighten-2"></v-divider>

        <v-container>
            <v-card-text>
                <v-snackbar
                    :value="snackbar.show"
                    :color="snackbar.color"
                    absolute
                    top
                    right
                    text
                >
                    {{ snackbar.message }}
                </v-snackbar>

                <data-table
                    :refresh="state.tableRefresh"
                >
                    <template slot="actions" slot-scope="props">
                        <v-icon
                            v-if="props.item.editable"
                            class="mr-5"
                            small
                            @click="onEditClick(props.item.id)"
                        >
                            mdi-pencil
                        </v-icon>

                        <v-icon
                            v-if="props.item.editable"
                            small
                            @click="onDeleteClick(props.item.id)"
                        >
                            mdi-delete
                        </v-icon>
                    </template>
                </data-table>
            </v-card-text>
        </v-container>

        <v-fab-transition>
            <v-tooltip left>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="primary"
                        absolute
                        dark
                        bottom
                        right
                        fab
                        v-bind="attrs"
                        v-on="on"
                        @click="onAddClick"
                    >
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>

                <span>Add Role</span>
            </v-tooltip>
        </v-fab-transition>

        <v-dialog
            width="400px"
            persistent
            v-model="dialog.form"
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <data-form
                :identifier="state.identifier"
                @onClose="onCloseClick('form')"
                @onSave="onSaveClick"
            ></data-form>
        </v-dialog>

        <v-dialog
            v-model="dialog.confirmation"
            persistent
            width="400px"
            :fullscreen="$vuetify.breakpoint.xsOnly"
        >
            <confirmation-card
                :identifier="state.identifier"
                @onClose="onCloseClick('confirmation')"
                @onOK="onConfirmationOKClick"
            ></confirmation-card>
        </v-dialog>
    </v-card>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';

import DataTable from './components/DataTable.vue';
import DataForm from './components/DataForm.vue';
import ConfirmationCard from './components/ConfirmationCard.vue';

export default defineComponent({
    name: 'staff-roles',
    components: {
        DataTable,
        DataForm,
        ConfirmationCard
    },
    setup() {
        const state = reactive({
            loading: false,
            tableRefresh: false,
            identifier: null,
        });

        const snackbar = reactive({
            show: false,
            color: 'primary',
            message: 'SNACKBAR_MESSAGE'
        });

        const dialog = reactive({
            form: false,
            confirmation: false,
        });


        const onCloseClick = (action) => {
            state.identifier = null;
            state.tableRefresh = false;

            snackbar.show = false;
            snackbar.color = 'primary';
            snackbar.message = 'SNACKBAR_MESSAGE';

            dialog[action] = false;

            return;
        }

        const onAddClick = () => {
            state.identifier = null;
            state.tableRefresh = false;

            snackbar.show = false;
            snackbar.color = 'primary';
            snackbar.message = 'SNACKBAR_MESSAGE';

            dialog.form = true;

            return;
        };

        const onEditClick = (id) => {
            state.identifier = id;
            state.tableRefresh = false;

            snackbar.show = false;
            snackbar.color = 'primary';
            snackbar.message = 'SNACKBAR_MESSAGE';

            dialog.form = true;

            return;
        };

        const onDeleteClick = (id) => {
            state.identifier = id;
            state.tableRefresh = false;

            snackbar.show = false;
            snackbar.color = 'primary';
            snackbar.message = 'SNACKBAR_MESSAGE';

            dialog.confirmation = true;

            return;
        };

        const onSaveClick = (response) => {
            state.identifier = null;

            snackbar.color = response.status;
            snackbar.message = response.message;

            dialog.form = false;

            snackbar.show = true;

            state.tableRefresh = true;

            return;
        };

        const onConfirmationOKClick = (response) => {
            state.identifier = null;

            snackbar.color = response.status;
            snackbar.message = response.message;

            dialog.confirmation = false;

            snackbar.show = true;

            state.tableRefresh = true;
        };

        return {
            state,
            snackbar,
            dialog,
            onCloseClick,
            onAddClick,
            onEditClick,
            onDeleteClick,
            onSaveClick,
            onConfirmationOKClick
        };
    },
    computed: {
        _ () { return _; },
    }
})
</script>
