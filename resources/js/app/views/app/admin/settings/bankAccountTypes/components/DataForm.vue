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
                        id="frmAccountTypeName"
                        class="mb-3"
                        label="Name"
                        persistent-hint
                        v-model="form.name"
                        v-validate="'required'"
                        data-vv-name="name"
                        data-vv-as="account type name"
                        :error-messages="errors.collect('form.name')"
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
import { computed, defineComponent, reactive } from '@vue/composition-api';

import ServiceBankAccountTypeApi from '../services/BankAccountTypeApi';

export default defineComponent({
    $_veeValidate: {
        validator: "new"
    },
    name: "admin-settings-bank-account-type-form",
    setup(props, {root}) {
        const app = computed(() => root.$store.getters.app);

        const state = reactive({
            open: false,
            loading: false,
            resolve: null,
            reject: null,
            identifier: null,
            method: 'CREATE',
            title: 'Create Bank Account Type',
            accoutTypes: [],
            accountStatuses: [
                { text: 'Active', value: 1 },
                { text: 'Inactive', value: 0 },
            ],
        });

        const form = reactive({
            name: "",
            is_active: 1,
        });

        const getDataFromApi = async (id) => {
            state.loading = true;

            const response = await ServiceBankAccountTypeApi().showApi(id);

            form.name = response.data.name;
            form.is_active = response.data.is_active;

            state.loading = false;
        }

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
            this.state.title = 'Create Bank Account Type';

            this.form.name = "";
            this.form.is_active = 1;

            this.$validator.reset();
        },
        async open(id = null) {
            this.state.open = true;

            if (id) {
                this.state.identifier = id;
                this.state.method = 'UPDATE';
                this.state.title = 'Update Bank Account Type';

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
                    response = await ServiceBankAccountTypeApi().storeApi(this.form);
                } else {
                    response = await ServiceBankAccountTypeApi().updateApi(this.state.identifier, this.form);
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
});
</script>
