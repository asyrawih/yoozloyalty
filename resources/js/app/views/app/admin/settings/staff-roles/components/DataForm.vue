<template>
    <v-form
        ref="form"
        lazy-validation
        data-vv-scope="form"
    >
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

            <v-card-title>
                <span class="text-h5">Staff Roles</span>
            </v-card-title>

            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text>
                <v-text-field
                    ref="name"
                    label="Role Name"
                    class="mb-3"
                    persistent-hint
                    v-model="form.name"
                    v-validate="'required'"
                    data-vv-name="name"
                    data-vv-as="role name"
                    :error-messages="errors.collect('form.name')"
                />

                <v-select
                    ref="permissions"
                    label="Permissions"
                    class="mb-3"
                    persistent-hint
                    multiple
                    v-model="form.permissions"
                    :items="state.permissions"
                    v-validate="'required'"
                    data-vv-name="permissions"
                    data-vv-as="permissions"
                    :error-messages="errors.collect('form.permissions')"
                />
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    color="secondary"
                    text
                    @click="onCloseClick"
                >
                    Close
                </v-btn>

                <v-btn
                    color="primary"
                    text
                    @click="onSaveClick"
                >
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-form>
</template>

<script>
import { defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import ServiceStaffRoles from '../services/StaffRolesApi';

export default defineComponent({
    name: "data-form",
    props: {
        identifier: {
            type: Number,
            default: null
        },
    },
    setup(props, context) {
        const state = reactive({
            loading: false,
            permissions: [
                { text: 'Reward', value: 'reward' },
                { text: 'Credit', value: 'credit' }
            ],
            is_edit: false
        });

        const form = reactive({
            name: "",
            permissions: []
        });

        const getShowApi = async (id) => {
            state.loading = true;

            const response = await ServiceStaffRoles().showApi(id);

            form.name = response.name;
            form.permissions = response.permissions;

            state.is_edit = true;

            state.loading = false;
        }

        watch(() => props.identifier, async (after, before) => {
            if (after) {
                await getShowApi(after);
            }
        });

        onMounted(async () => {
            if (props.identifier) {
                await getShowApi(props.identifier);
            }
        });

        return {
            state,
            form
        };
    },
    methods: {
        resetForm() {
            this.state.is_edit = false;
            this.form.name = "";
            this.form.permissions = [];

            this.$validator.reset();

            return;
        },
        onCloseClick() {
            this.resetForm();

            this.$emit('onClose');

            return;
        },
        async onSaveClick() {
            this.state.loading = true;

            const validated = await this.$validator.validateAll('form');

            if (! validated) {
                this.state.loading = false;

                return false;
            }

            let response = {
                status: 'primary',
                message: 'CONFIRMATION_MESSAGE',
            };

            const payload = {
                name: this.form.name,
                permissions: this.form.permissions
            };

            try {
                if (this.state.is_edit) {
                    response = await ServiceStaffRoles().updateApi(payload, this.identifier);
                } else {
                    response = await ServiceStaffRoles().storeApi(payload);
                }
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field][0]
                        });
                    }
                }

                response = exception;
            }

            this.resetForm();

            this.$emit('onSave', response);

            this.state.loading = false;

            return;
        }
    },
})
</script>
