<template>
    <v-form
        lazy-validation
        data-vv-scope="form"
        @submit.prevent="onSubmit"
    >
        <v-divider class="grey lighten-2"></v-divider>
        <v-row>
            <v-col cols="3">
                <v-card outlined>
                    <v-card-title class="subtitle-2">Variables</v-card-title>

                    <v-card-text>
                        <v-btn
                            color="primary"
                            class="d-block my-2"
                            small
                            depressed
                            @click="variableSet('{{ name_of_user }}')"
                        >
                            Name of user
                        </v-btn>

                        <v-btn
                            color="primary"
                            class="d-block my-2"
                            small
                            depressed
                            @click="variableSet('{{ website_name }}')"
                        >
                            Website Name
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="9">
                <v-card outlined>
                    <v-card-text>
                        <v-textarea
                            ref="template_form"
                            id="frmSmsBirthdayContent"
                            label="Template"
                            outlined
                            dense
                            counter
                            height="100px"
                            v-model="form.template_form"
                            data-vv-name="template_form"
                            data-vv-as="template"
                            v-validate="'required|max:160'"
                            :error-messages="errors.collect('form.template_form')"
                        />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>


        <v-text-field
            :type="state.show_current_password ? 'text' : 'password'"
            class="mt-4"
            :label="$t('current_password')"
            outlined
            :append-icon="state.show_current_password ? 'visibility' : 'visibility_off'"
            v-model="form.current_password"
            data-vv-name="current_password"
            :data-vv-as="$t('current_password').toLowerCase()"
            v-validate="'required|min:8|max:24'"
            :error-messages="errors.collect('form.current_password')"
            @click:append="state.show_current_password = ! state.show_current_password"
        />

        <v-alert
            type="success"
            :value="state.hasSuccess"
        >
            {{ state.responseMessage }}
        </v-alert>

        <div class="d-flex">
            <v-spacer></v-spacer>

            <v-btn
                type="submit"
                class="mb-2"
                color="primary"
                large
                :disabled="state.loading"
            >
                {{ $t("update") }}
            </v-btn>
        </div>
    </v-form>
</template>

<script>
import { defineComponent, onMounted, reactive, ref, watch } from '@vue/composition-api';
import axios from 'axios';

export default defineComponent({
    props: ['id', 'template'],
    setup(props) {
        const state = reactive({
            loading: false,
            showCurrentPassword: false,
            hasError: false,
            hasSuccess: false,
            responseMessage: '',
        });

        const form = reactive({
            id: '',
            template_form: '',
            current_password: '',
        });

        const template_form = ref(null);

        watch(() => props.id, (current, previous) => {
            form.id = current;
        });

        watch(() => props.template, (current, previous) => {
            form.template_form = current;
        });

        onMounted(() => {
            form.id = props.id;
            form.template_form = props.template;
        });

        return {
            state,
            form,
            template_form,
        };
    },
    methods: {
        variableSet(value = '') {
            const inputField = this.template_form;

            if (inputField) {
                inputField.focus();

                const activeElement = document.activeElement;

                const [start, end] = [activeElement.selectionStart, activeElement.selectionEnd];

                this.$nextTick(() => {
                    activeElement.setRangeText(value, start, end, 'end');
                    this.form.template_form = activeElement.value;
                });
            }
        },
        async onSubmit() {
            this.state.loading = true;
            this.state.hasError = false;
            this.state.hasSuccess = false;
            this.state.responseMessage = '';

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return;
            }

            try {
                const response = await axios.post('/merchant/sms-template', {
                    locale: this.$i18n.locale,
                    current_password: this.form.current_password,
                    type: "customer_birthday",
                    id: this.form.id,
                    template: this.form.template_form,
                });

                if (response.data.success === true) {
                    this.state.responseMessage = response.data.message
                    this.state.hasSuccess = true;
                }
            } catch (exception) {
                let errors = exception.response.data.errors || {};

                for (let field in errors) {
                    this.$validator.errors.add({
                        field: `form.${field}`,
                        msg: errors[field][0],
                    });
                }
            } finally {
                this.state.loading = false;
            }
        },
    },
});
</script>
