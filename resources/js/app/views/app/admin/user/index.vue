<template>
    <page-layout
        :loading="state.loading"
        useCreate
        useSearch
        :pageOptions="state.pageOptions"
        :snackbarOptions="state.snackbarOptions"
        :confirmationOptions="state.confirmationOptions"
        @onCreate="onCreateEmit"
        @onClose="onCloseEmit"
        @onConfirmOK="onConfirmOKEmit"
    >
        <template slot-scope="props">
            <data-table
                :search="props.state.search"
                :refresh="state.tableRefesh"
                @onDeletedSelected="onDeletedSelectedEmit"
            >
                <template slot="actions" slot-scope="props">
                    <!-- <v-icon
                        class="mr-5"
                        small
                        :disabled="! props.item.editable"
                        @click="onEditClick(props.item.id)"
                    >
                        mdi-pencil
                    </v-icon> -->

                    <v-btn
                        v-if="props.item.editable"
                        color="primary"
                        small
                        text
                        @click="onEditClick(props.item.id)"
                    >
                        Edit
                    </v-btn>

                    <span v-if="props.item.editable && props.item.deleteable">|</span>

                    <v-btn
                        v-if="props.item.deleteable"
                        color="primary"
                        small
                        text
                        @click="onDeleteClick(props.item.id)"
                    >
                        Delete
                    </v-btn>

                    <!-- <v-icon
                        small
                        :disabled="! props.item.deleteable"
                        @click="onDeleteClick(props.item.id)"
                    >
                        mdi-delete
                    </v-icon> -->
                </template>
            </data-table>
        </template>

        <template slot="data_form">
            <data-form
                :identifier="state.identifier"
                :formOptions="state.formOptions"
                @onClose="onCloseEmit"
                @onSubmit="onSubmitEmit"
            />
        </template>
    </page-layout>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';

import PageLayout from './components/PageLayout.vue';
import DataTable from './components/DataTable.vue';
import DataForm from './components/DataForm.vue';

import ServiceAdminUserApi from './services/AdminUserApi';

export default defineComponent({
    name: "plans",
    components: {
        PageLayout,
        DataTable,
        DataForm
    },
    setup() {
        const state = reactive({
            loading: false,
            tableRefesh: false,
            deleted_id: null,
            massdeleted: [],
            pageOptions: {
                title: 'Admin',
                form: false,
                confirmation: false,
            },
            snackbarOptions: {
                value: false,
                status: 'error',
                message: 'SNACKBAR_MESSAGE'
            },
            formOptions: {
                title: 'Create Admin',
                closeText: 'Close',
                submitText: 'Create',
                method: 'CREATE',
                identifier: null,
            },
            confirmationOptions: {
                loading: false,
                title: 'Delete Confirmation',
                message: 'Are you sure, you want to delete this data ?',
                cancelText: 'Cancel',
                okText: 'OK',
                cancelColor: 'error',
                okColor: 'primary'
            }
        });

        const resetSnackbarOptions = () => {
            state.snackbarOptions.value = false;
            state.snackbarOptions.status = 'error';
            state.snackbarOptions.message = 'SNACKBAR_MESSAGE';

            return;
        };

        const resetFormOptions = () => {
            state.formOptions.title = 'Create Admin';
            state.formOptions.submitText = 'Create';
            state.formOptions.method = 'CREATE';
            state.formOptions.identifier = null;

            return;
        }

        const useSnackbar = (status, message) => {
            state.snackbarOptions.value = true;
            state.snackbarOptions.status = status ?? 'error';
            state.snackbarOptions.message = message;

            return;
        }

        const onCreateEmit = () => {
            state.pageOptions.form = true;
            state.tableRefesh = false;

            resetSnackbarOptions();
            resetFormOptions();
        };

        const onEditClick = (id) => {
            state.tableRefesh = false;

            resetSnackbarOptions();
            resetFormOptions();

            state.formOptions.title = 'Edit Admin';
            state.formOptions.submitText = 'Save';
            state.formOptions.method = 'UPDATE';
            state.formOptions.identifier = id;

            state.pageOptions.form = true;

            return;
        }

        const onDeleteClick = (id) => {
            state.tableRefesh = false;
            state.deleted_id = id;

            resetSnackbarOptions();

            state.pageOptions.confirmation = true;

            return;
        }

        const onCloseEmit = () => {
            state.tableRefesh = false;
            state.deleted_id = null;

            resetSnackbarOptions();
            resetFormOptions();

            state.pageOptions.form = false;
            state.pageOptions.confirmation = false;

            return;
        }

        const onSubmitEmit = (response) => {
            useSnackbar(response.status, response.message);

            if (response.status === 'success') {
                state.pageOptions.form = false;

                state.tableRefesh = true;
            }

            return;
        }

        const onConfirmOKEmit = async () => {
            state.confirmationOptions.loading = true;

            let response = null;

            try {
                if (state.massdeleted.length > 0) {
                    response = await ServiceAdminUserApi().massdeleteApi({ plans: state.massdeleted });
                } else if (state.deleted_id) {
                    response = await ServiceAdminUserApi().destroyApi(state.deleted_id);
                }
            } catch (exception) {
                response = exception;
            }

            useSnackbar(response.status, response.message);

            state.confirmationOptions.loading = false;
            state.pageOptions.confirmation = false;
            state.tableRefesh = true;
            state.deleted_id = null;
            state.massdeleted = [];

            return;
        }

        const onDeletedSelectedEmit = async (payload) => {
            state.tableRefesh = false;
            state.massdeleted = payload;

            resetSnackbarOptions();

            state.pageOptions.confirmation = true;

            return;
        }

        return {
            state,
            onCreateEmit,
            onEditClick,
            onDeleteClick,
            onSubmitEmit,
            onCloseEmit,
            onConfirmOKEmit,
            onDeletedSelectedEmit
        };
    },
    computed: {
        app() {
            return this.$store.getters.app;
        }
    },
})
</script>
