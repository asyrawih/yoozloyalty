<template>
    <div style="height: 100%">
        <v-container
            v-if="state.loading"
            fluid
            style="height: 100%"
        >
            <v-layout
                row
                justify-center
                align-center
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
            <v-toolbar flat color="transparent">
                <v-toolbar-title>SMTP</v-toolbar-title>

                <v-spacer></v-spacer>

                <v-text-field
                    v-show="state.hasCustomDomain > 0"
                    id="frmSearch"
                    label="Search"
                    append-icon="search"
                    single-line
                    clearable
                    flat
                    solo-inverted
                    hide-details
                    :style="{'max-width': $vuetify.breakpoint.xs ? '135px' : '320px'}"
                    v-model="state.search"
                />
            </v-toolbar>

            <v-card-text class="my-7">
                <v-row>
                    <v-col>
                        <v-row v-if="! state.hasCustomDomain > 0">
                            <v-col class="text-xs-center">
                                <div>
                                    <v-icon size="72" :color="app.color_name">email</v-icon>
                                </div>

                                <h1 class="title my-4">
                                    Looks like you don't have any custom domain.
                                </h1>

                                <div class="mx-5 pb-4">
                                    <p class="subheading">
                                        Please add your custom domain first in order to enable SMTP setting.
                                        You can go to Loyalty Setup menu then open website section.
                                        You can add custom domain by edit or add new the website.
                                    </p>
                                </div>
                            </v-col>
                        </v-row>

                        <v-row v-else>
                            <v-col>
                                <v-snackbar
                                    v-model="state.snackbar.value"
                                    bottom
                                    left
                                    :color="state.snackbar.status"
                                >
                                    {{ state.snackbar.message }}
                                </v-snackbar>

                                <data-table
                                    :reload="state.reload"
                                    :search="state.search"
                                    @onSuccess="onSuccess"
                                    @onError="onError"
                                    @onEdit="onEdit"
                                />
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-card-text>

            <v-card-text
                v-show="state.hasCustomDomain > 0"
                style="height: 100px; position: relative"
            >
                <data-form
                    :identifier="state.identifier"
                    @onSuccess="onSuccess"
                    @onError="onError"
                    @onOpen="onOpen"
                    @onClose="onClose"
                />
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import { computed, defineComponent, onMounted, reactive } from '@vue/composition-api';

import DataTable from './components/DataTable.vue';
import DataForm from './components/DataForm.vue';

import SmtpServiceApi from './services/SmtpServiceApi';

export default defineComponent({
    name: 'user-smtp-service',
    components: {
        DataTable,
        DataForm,
    },
    setup(props, { root }) {
        const state = reactive({
            loading: true,
            search: '',
            reload: false,
            identifier: null,
            hasCustomDomain: 0,
            snackbar: {
                value: false,
                status: 'success',
                message: 'SNACKBAR_MESSAGE',
            },
        });

        const snackbarOptions = reactive({
            value: false,
            status: 'success',
            message: 'SNACKBAR_MESSAGE',
        });

        const app = computed(() => root.$store.getters.app);

        onMounted(async () => {
            await onInitialize();
        });

        const onInitialize = async () => {
            try {
                const response = await SmtpServiceApi().initializeApi({});

                if (response.status === 'success') {
                    state.hasCustomDomain = response.hasCustomDomain;
                }
            } catch(exception) {
                if (exception.message) {
                    onError(exception.message);
                }
            } finally {
                state.loading = false;
            }
        };

        const onSnackbar = (message = 'SNACKBAR_MESSAGE', status = 'success') => {
            let snackbar = state.snackbar;

            snackbar.message = message;
            snackbar.status = status;
            snackbar.value = ! snackbar.value;
        }

        const onClearComponent = () => {
            state.identifier = null;
            state.snackbar.value = false;
        };

        const onSuccess = (message) => {
            state.reload = true;

            onClearComponent();

            onSnackbar(message);

            return true;
        };

        const onError = (message) => {
            state.reload = true;

            onClearComponent();

            onSnackbar(message);

            return true;
        }

        const onOpen = () => {
            state.reload = false;

            return true;
        }

        const onClose = () => {
            state.reload = false;

            onClearComponent();

            return true;
        }

        const onEdit = (identifier) => {
            state.identifier = identifier;
        }

        return {
            state,
            app,
            snackbarOptions,
            onSuccess,
            onError,
            onOpen,
            onClose,
            onEdit,
        };
    },
});
</script>
