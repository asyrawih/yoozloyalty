<template>
    <v-card>
        <v-overlay
            :value="state.loading"
        >
            <v-progress-circular
                :size="50"
                color="primary"
                indeterminate
                class="ma-5"
            />
        </v-overlay>

        <v-toolbar>
            <v-toolbar-title>
                {{ formOptions.title }}
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn icon @click="onCloseClick">
                <v-icon>close</v-icon>
            </v-btn>
        </v-toolbar>

        <v-form
            ref="form"
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
                <v-radio-group
                    id="frmAdminRole"
                    ref="role"
                    class="mb-3"
                    label="Select Role"
                    row
                    v-model="form.role"
                    v-validate="'required'"
                    data-vv-name="role"
                    data-vv-as="role"
                    :error-messages="errors.collect('role')"
                >
                    <v-radio
                        :value="1"
                        label="Master Admin"
                    />

                    <v-radio
                        :value="2"
                        label="Normal Admin"
                    />
                </v-radio-group>

                <v-text-field
                    id="frmAdminName"
                    ref="name"
                    class="mb-3"
                    label="Name (required)"
                    persistent-hint
                    v-model="form.name"
                    v-validate="'required'"
                    data-vv-name="name"
                    data-vv-as="name"
                    :error-messages="errors.collect('form.name')"
                />

                <v-text-field
                    type="email"
                    id="frmAdminEmail"
                    ref="name"
                    class="mb-3"
                    label="E-mail Address (required)"
                    persistent-hint
                    v-model="form.email"
                    v-validate="'required|email'"
                    data-vv-name="email"
                    data-vv-as="email"
                    :error-messages="errors.collect('form.email')"
                />

                <v-text-field
                    v-if="formOptions.method === 'CREATE'"
                    type="password"
                    id="frmAdminPassword"
                    ref="password"
                    class="mb-3"
                    label="Password (required)"
                    persistent-hint
                    v-model="form.password"
                    v-validate="'required'"
                    data-vv-name="password"
                    data-vv-as="password"
                    :error-messages="errors.collect('form.password')"
                />

                <v-text-field
                    v-else-if="formOptions.method === 'UPDATE'"
                    type="password"
                    id="frmAdminPassword"
                    ref="password"
                    class="mb-3"
                    label="Password"
                    hint="Leave blank to keep current password"
                    persistent-hint
                    v-model="form.password"
                    data-vv-name="password"
                    data-vv-as="password"
                    :error-messages="errors.collect('form.password')"
                />
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    color="secondary"
                    text
                    large
                    :disabled="state.loading"
                    @click="onCloseClick"
                >
                    {{ formOptions.closeText }}
                </v-btn>

                <v-btn
                    type="submit"
                    color="primary"
                    large
                    text
                    :disabled="state.loading"
                >
                    {{ formOptions.submitText }}
                </v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import ServiceAdminUserApi from '../services/AdminUserApi';

export default defineComponent({
    $_veeValidate: {
        validator: "new"
    },
    name: "data-form",
    props: {
        identifier: {
            type: Number,
            default: null,
        },
        formOptions: {
            type: Object,
            default: {
                title: 'Data Form',
                closeText: 'Close',
                submitText: 'Submit',
                method: 'CREATE',
                identifier: null,
            },
        },
    },
    setup(props, context) {
        const state = reactive({
            loading: false,
            showPassword: false,
        });

        const form = reactive({
            role: 1,
            name: '',
            email: '',
            password: ''
        });

        watch(() => props.formOptions, async (after, before) => {
            if (after.identifier) {
                await getDataFromApi(after.identifier);
            }
        }, {
            deep: true
        });

        onMounted(async () => {
            if (props.formOptions.identifier) {
                await getDataFromApi(props.formOptions.identifier);
            }
        });

        const getDataFromApi = async (id) => {
            state.loading = true;

            const response = await ServiceAdminUserApi().showApi(id);

            form.role = response.data.role;
            form.name = response.data.name;
            form.email = response.data.email;

            state.loading = false;

            return;
        }

        return {
            state,
            form,
        };
    },
    methods: {
        resetForm() {
            this.form.role = 1;
            this.form.name = '';
            this.form.email = '';
            this.form.password = '';

            this.$validator.reset();
        },
        onCloseClick() {
            this.resetForm();

            this.$emit('onClose');

            return;
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
                if (this.formOptions.method === 'CREATE') {
                    response = await ServiceAdminUserApi().storeApi(this.form);
                }

                if (this.formOptions.method === 'UPDATE') {
                    response = await ServiceAdminUserApi().updateApi(this.form, this.formOptions.identifier);
                }

                this.resetForm();
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field][0]
                        });
                    }
                } else {
                    response = exception;
                }
            }

            this.state.loading = false;

            this.$emit('onSubmit', response);
        }
    }
})
</script>
