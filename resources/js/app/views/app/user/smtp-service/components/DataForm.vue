<template>
    <div>
        <v-fab-transition>
            <v-btn
                color="pink"
                dark
                absolute
                top
                right
                fab
                @click="onOpen()"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </v-fab-transition>

        <v-dialog
            v-model="state.open"
            persistent
            :retain-focus="false"
            :fullscreen="$vuetify.breakpoint.xsOnly"
            width="480"
            @keydown.esc="onClose()"
        >
            <v-card>
                <v-overlay
                    :value="state.loading"
                >
                    <v-progress-circular
                        :size="50"
                        :color="app.color_name"
                        indeterminate
                        class="ma-5"
                    />
                </v-overlay>

                <v-toolbar flat color="transparent">
                    <v-toolbar-title>
                        {{ state.title }}
                    </v-toolbar-title>

                    <v-spacer></v-spacer>

                    <v-btn icon @click="onClose">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-toolbar>

                <v-form
                    lazy-validation
                    data-vv-scope="form"
                    @submit.prevent="onSubmit"
                >
                    <v-card-text>
                        <v-text-field
                            id="frmMailServiceName"
                            class="mb-3"
                            label="Mail service name"
                            v-model="form.smtp_name"
                            v-validate="'required'"
                            data-vv-name="smtp_name"
                            data-vv-as="mail service name"
                            :error-messages="errors.collect('form.smtp_name')"
                        />

                        <v-text-field
                            id="frmMailFromName"
                            class="mb-3"
                            label="Mail from name"
                            v-model="form.mail_from_name"
                            v-validate="'required'"
                            data-vv-name="mail_from_name"
                            data-vv-as="mail from name"
                            :error-messages="errors.collect('form.mail_from_name')"
                        />

                        <v-text-field
                            id="frmMailFromAddress"
                            class="mb-3"
                            label="Mail from address"
                            v-model="form.mail_from_address"
                            v-validate="'required|email'"
                            data-vv-name="mail_from_address"
                            data-vv-as="mail from address"
                            :error-messages="errors.collect('form.mail_from_address')"
                        />

                        <v-select
                            id="frmMailDriver"
                            label="Mail Driver"
                            :items="state.drivers"
                            v-model="form.mail_driver"
                        />

                        <div v-if="form.mail_driver === 'smtp'">
                            <v-text-field
                                id="frmMailHost"
                                class="mb-3"
                                label="Mail host"
                                v-model="form.mail_host"
                                v-validate="'required'"
                                data-vv-name="mail_host"
                                data-vv-as="mail host"
                                :error-messages="errors.collect('form.mail_host')"
                            />

                            <v-text-field
                                id="frmMailPort"
                                class="mb-3"
                                label="Mail port"
                                v-model="form.mail_port"
                                v-validate="'required'"
                                data-vv-name="mail_port"
                                data-vv-as="mail port"
                                :error-messages="errors.collect('form.mail_port')"
                            />

                            <v-text-field
                                id="frmMailUsername"
                                class="mb-3"
                                label="Mail username"
                                v-model="form.mail_username"
                                v-validate="'required'"
                                data-vv-name="mail_username"
                                data-vv-as="mail username"
                                :error-messages="errors.collect('form.mail_username')"
                            />

                            <v-text-field
                                id="frmMailPassword"
                                class="mb-3"
                                label="Mail password"
                                v-model="form.mail_password"
                                v-validate="'required'"
                                data-vv-name="mail_password"
                                data-vv-as="mail password"
                                :error-messages="errors.collect('form.mail_password')"
                            />

                            <v-text-field
                                id="frmMailEncryption"
                                class="mb-3"
                                label="Mail encryption"
                                v-model="form.mail_encryption"
                                v-validate="'required'"
                                data-vv-name="mail_encryption"
                                data-vv-as="mail encryption"
                                :error-messages="errors.collect('form.mail_encryption')"
                            />
                        </div>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            color="secondary"
                            text
                            large
                            :disabled="state.loading"
                            @click="onClose"
                        >
                            Cancel
                        </v-btn>

                        <v-btn
                            type="submit"
                            :color="app.color_name"
                            large
                            text
                        >
                            Save
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { computed, defineComponent, reactive, watch } from '@vue/composition-api';

import SmtpServiceApi from '../services/SmtpServiceApi';

export default defineComponent({
    props: ['identifier'],
    setup(props, { root, emit }) {
        const state = reactive({
            open: false,
            loading: false,
            title: 'Add a new smtp service.',
            method: 'CREATE',
            drivers: [
                { text: 'smtp', value: 'smtp' },
            ],
        });

        const app = computed(() => root.$store.getters.app);

        const form = reactive({
            smtp_name: null,
            mail_from_name: null,
            mail_from_address: null,
            mail_driver: 'smtp',
            mail_host: null,
            mail_port: null,
            mail_username: null,
            mail_password: null,
            mail_encryption: 'tls',
        });

        watch(() => props.identifier, async (current, previous) => {
            if (current) {
                await onGetData(current);
            }
        });

        const onGetData = async (identifier) => {
            state.loading = true;

            try {
                const response = await SmtpServiceApi().showApi(identifier);

                if (response.status === 'success') {
                    form.smtp_name = response.data.smtp_name;
                    form.mail_from_name = response.data.mail_from_name;
                    form.mail_from_address = response.data.mail_from_address;
                    form.mail_driver = response.data.mail_driver;
                    form.mail_host = response.data.mail_host;
                    form.mail_port = response.data.mail_port;
                    form.mail_username = response.data.mail_username;
                    form.mail_password = response.data.mail_password;
                    form.mail_encryption = response.data.mail_encryption;

                    state.title = 'Edit smtp service';

                    state.method = 'UPDATE';

                    onOpen();
                }
            } catch (exception) {
                if (exception.message) {
                    emit('onError', exception.message);
                }
            } finally {
                state.loading = false;
            }
        };

        const onOpen = () => {
            emit('onOpen');

            state.open = true;

            return true;
        }

        return {
            state,
            app,
            form,
            onOpen,
        };
    },
    methods: {
        onClearComponent() {
            let form = this.form;

            form.smtp_name = null;
            form.mail_from_name = null;
            form.mail_from_address = null;
            form.mail_driver = 'smtp';
            form.mail_host = null;
            form.mail_port = null;
            form.mail_username = null;
            form.mail_password = null;
            form.mail_encryption = 'tls';

            this.state.title = 'Add a new smtp service.';
            this.state.method = 'CREATE';

            this.$validator.reset();
        },
        async onSubmit() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            try {
                let response = null;

                if (this.state.method === 'UPDATE') {
                    response = await SmtpServiceApi().updateApi(this.form, this.identifier);
                } else if (this.state.method === 'CREATE') {
                    response = await SmtpServiceApi().storeApi(this.form);
                }

                if (response.status === 'success') {
                    this.onClearComponent();
                    this.$emit('onSuccess', response.message);
                    this.state.open = false;
                }
            } catch (exception) {
                if (exception.message) {
                    this.$emit('onError', exception.message);
                }
            } finally {
                this.state.loading = false;
            }
        },
        onClose() {
            this.$emit('onClose');

            this.onClearComponent();

            this.state.open = false;

            return true;
        }
    },
});
</script>
