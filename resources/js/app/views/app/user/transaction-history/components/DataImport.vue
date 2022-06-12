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
                <v-card-text>
                    <v-select
                        id="importCreditRequestCampaign"
                        class="mb-3"
                        label="Campaign"
                        v-model="form.uuid"
                        :items="state.campaigns"
                        :loading="state.campaignLoading"
                        clearable
                        v-validate="'required'"
                        data-vv-name="uuid"
                        data-vv-as="campaign"
                        :error-messages="errors.collect('form.uuid')"
                    />

                    <v-file-input
                        accept="
                            .csv,
                            application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                            application/vnd.ms-excel
                            application/csv,
                            text/csv
                        "
                        label="Import file"
                        v-model="form.file"
                        v-validate="'required'"
                        data-vv-name="file"
                        data-vv-as="import file"
                        :error-messages="errors.collect('form.file')"
                    />

                    <p>
                        Import credit request sample download <a href="/sample/import-transaction-history-sample.xlsx">here</a>.
                    </p>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        color="secondary"
                        text
                        large
                        @click="close"
                    >
                        Cancel
                    </v-btn>

                    <v-btn
                        type="submit"
                        color="primary"
                        text
                        large
                    >
                        Import
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, onMounted, reactive } from '@vue/composition-api';

import ServiceTransactionHistory from '../services/TransactionHistoryApi';

export default defineComponent({
    name: 'merchant-transaction-history-import',
    setup(props, {root}) {
        const app = computed(() => root.$store.getters.app);

        const state = reactive({
            open: false,
            loading: false,
            resolve: null,
            reject: null,
            title: "Import Transaction History",
            campaigns: [],
            campaignLoading: false,
        });

        const form = reactive({
            uuid: null,
            file: [],
        });

        onMounted(async () => {
            await getCampaigns();
        });

        const clearForm = () => {
            form.uuid = null;
            form.file = [];
        };

        const getCampaigns = async (params = {}) => {
            state.campaignLoading = true;

            const response = await ServiceTransactionHistory().campaignsApi(params);

            state.campaigns = response.data;

            state.campaignLoading = false;
        };

        return {
            app,
            state,
            form,
            clearForm,
        };
    },
    methods: {
        clearComponent() {
            this.clearForm();

            this.$validator.reset();
        },
        open() {
            this.state.open = true;

            return new Promise((resolve, reject) => {
                this.state.resolve = resolve;
                this.state.reject = reject;
            });
        },
        close() {
            this.state.resolve(false);
            this.state.open = false;
        },
        async onSubmit() {
            this.state.loading = true;

            const validate = this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;
                return;
            }

            try {
                const formData = new FormData();

                formData.append('uuid', this.form.uuid);
                formData.append('file', this.form.file);

                const response = await ServiceTransactionHistory().importApi(formData);

                this.clearComponent();
                this.state.resolve(response.message);
                this.state.open = false;
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
    },
});
</script>
