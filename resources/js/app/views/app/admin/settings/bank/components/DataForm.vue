<template>
    <v-dialog
        v-model="state.open"
        persistent
        :fullscreen="$vuetify.breakpoint.xsOnly"
        width="480"
        @keydown.esc="close"
    >
        <v-card>
            <v-overlay
                :value="state.loading"
            >
                <v-progress-circular
                    class="ma-5"
                    :color="app.color_name"
                    :size="50"
                    indeterminate
                />
            </v-overlay>

            <v-toolbar>
                <v-toolbar-title>
                    {{ state.title }}
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-form
                lazy-validation
                data-vv-scope="form"
                @submit.prevent="onSubmit"
            >
                <v-divider class="grey lighten-2"></v-divider>

                <v-card-text
                    :style="{
                        'height': 'auto',
                        'max-width': '800px',
                        'overflow-y': 'auto'
                    }"
                >
                    <v-text-field
                        id="frmBankName"
                        class="mb-3"
                        label="Bank Name"
                        persistent-hint
                        v-model="form.bank_name"
                        v-validate="'required'"
                        data-vv-name="bank_name"
                        data-vv-as="bank name"
                        :error-messages="errors.collect('form.bank_name')"
                    />

                    <v-text-field
                        id="frmBranchName"
                        class="mb-3"
                        label="Branch Name"
                        persistent-hint
                        v-model="form.branch_name"
                        v-validate="'required'"
                        data-vv-name="branch_name"
                        data-vv-as="branch name"
                        :error-messages="errors.collect('form.branch_name')"
                    />

                    <v-text-field
                        id="frmBranchCode"
                        class="mb-3"
                        label="Branch Code"
                        persistent-hint
                        v-model="form.branch_code"
                        v-validate="'required'"
                        data-vv-name="branch_code"
                        data-vv-as="branch code"
                        :error-messages="errors.collect('form.branch_code')"
                    />

                    <v-text-field
                        id="frmAccountNumber"
                        class="mb-3"
                        label="Account Number"
                        persistent-hint
                        v-model="form.account_number"
                        v-validate="'required'"
                        data-vv-name="account_number"
                        data-vv-as="account number"
                        :error-messages="errors.collect('form.account_number')"
                    />

                    <v-text-field
                        id="frmAccountName"
                        class="mb-3"
                        label="Account Name"
                        persistent-hint
                        v-model="form.account_name"
                        v-validate="'required'"
                        data-vv-name="account_name"
                        data-vv-as="account name"
                        :error-messages="errors.collect('form.account_name')"
                    />

                    <v-select
                        id="frmAccountType"
                        class="mb-3"
                        label="Account Type"
                        v-model="form.account_type_id"
                        :items="state.accoutTypes"
                        v-validate="'required'"
                        data-vv-name="account_type_id"
                        data-vv-as="account type"
                        :error-messages="errors.collect('form.account_type_id')"
                    />

                    <v-select
                        v-model="form.is_active"
                        :items="state.accountStatuses"
                        label="Status"
                        class="mb-3"
                    />
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        color="secondary"
                        text
                        large
                        :disabled="state.loading"
                        @click="close"
                    >
                        Cancel
                    </v-btn>

                    <v-btn
                        type="submit"
                        :color="app.color_name"
                        large
                        text
                        :disabled="state.loading"
                    >
                        Save
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import ServiceBankApi from '../services/BankApi';

export default defineComponent({
    $_veeValidate: {
        validator: "new"
    },
    name: "admin-settings-bank-form",
    setup(props, {root}) {
        const app = computed(() => root.$store.getters.app);

        const state = reactive({
            open: false,
            loading: false,
            resolve: null,
            reject: null,
            identifier: null,
            method: 'CREATE',
            title: 'Create Bank Account',
            accoutTypes: [],
            accountStatuses: [
                { text: 'Active', value: 1 },
                { text: 'Inactive', value: 0 },
            ],
        });

        const form = reactive({
            bank_name: "",
            branch_name: "",
            branch_code: "",
            account_number: "",
            account_name: "",
            account_type_id: null,
            is_active: 1,
        });

        const getDataFromApi = async (id) => {
            state.loading = true;

            const {data} = await ServiceBankApi().showApi(id);

            form.bank_name = data.bank_name;
            form.branch_name = data.branch_name;
            form.branch_code = data.branch_code;
            form.account_number = data.account_number;
            form.account_name = data.account_name;
            form.account_type_id = data.account_type_id;
            form.is_active = data.is_active;

            state.loading = false;
        }

        const getAccountTypes = async () => {
            state.loading = true;

            const response = await ServiceBankApi().getAccountTypesApi();

            state.accoutTypes = response.data.map(function (item) {
                return { text: item.name, value: item.id };
            });

            state.loading = false;
        }

        onMounted(async () => {
            await getAccountTypes();
        });

        return {
            app,
            state,
            form,
            getDataFromApi
        };
    },
    methods: {
        resetComponent() {
            this.state.identifier = null;
            this.state.method = 'CREATE';
            this.state.title = 'Create Bank Account';

            this.form.bank_name = "";
            this.form.branch_name = "";
            this.form.branch_code = "";
            this.form.account_number = "";
            this.form.account_name = "";
            this.form.account_type_id = null;
            this.form.is_active = 1;

            this.$validator.reset();
        },
        async open(id = null) {
            this.state.open = true;

            if (id) {
                this.state.identifier = id;
                this.state.method = 'UPDATE';
                this.state.title = 'Update Bank Account';

                await this.getDataFromApi(id);
            }

            return new Promise((resolve, reject) => {
                this.state.resolve = resolve;
                this.state.reject = reject;
            });
        },
        close() {
            this.resetComponent();
            this.state.open = false;
            this.state.resolve(false);
        },
        async onSubmit() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            let response = null;

            try {
                if (this.state.method === 'CREATE') {
                    response = await ServiceBankApi().storeApi(this.form);
                } else {
                    response = await ServiceBankApi().updateApi(this.state.identifier, this.form);
                }

                this.resetComponent();
                this.state.open = false;
                this.state.resolve(response.message);
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field][0]
                        });
                    }
                } else {
                    this.state.reject(exception.message);
                }
            } finally {
                this.state.loading = false;
            }
        }
    }
})
</script>
