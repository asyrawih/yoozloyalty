<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Broadcast Notification</v-toolbar-title>

            <v-spacer></v-spacer>
        </v-toolbar>

        <v-form
            data-vv-scope="form"
            lazy-validation
            @submit.prevent="onSubmit"
        >
            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text>
                <v-alert
                    type="error"
                    class="mb-4"
                    :value="state.hasError"
                >
                    {{ state.responseMessage }}
                </v-alert>

                <v-alert
                    type="success"
                    class="mb-4"
                    :value="state.isValid"
                >
                    {{ state.responseMessage }}
                </v-alert>

                <v-select
                    id="frmBroadcastNotificationCustomers"
                    label="Select Customer"
                    multiple
                    :items="state.customers"
                    v-model="form.customers"
                    data-vv-name="customers"
                    data-vv-as="customers"
                    v-validate="'required'"
                >
                    <template v-slot:prepend-item>
                        <v-list-item ripple @click="toggle">

                            <v-list-item-action>
                                <v-icon
                                    :color="form.customers.length > 0 ? 'indigo darken-4' : ''"
                                >
                                    {{ icon }}
                                </v-icon>
                            </v-list-item-action>

                            <v-list-item-content>
                                <v-list-item-title>Select All</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-divider class="mt-2"></v-divider>
                    </template>

                    <template v-slot:append-item>
                        <v-divider class="mb-2"></v-divider>

                        <v-list-item disabled>
                            <v-list-item-avatar color="grey lighten-3">
                                <v-icon> mdi-account </v-icon>
                            </v-list-item-avatar>

                            <v-list-item-content v-if="likesAllMerchant">
                                <v-list-item-title>
                                    All Merchant are selected.
                                </v-list-item-title>
                            </v-list-item-content>

                            <v-list-item-content v-else-if="likesSomeMerchant">
                                <v-list-item-title> Merchant Count </v-list-item-title>

                                <v-list-item-subtitle>
                                    {{ form.customers.length }}
                                </v-list-item-subtitle>
                            </v-list-item-content>

                            <v-list-item-content v-else>
                                <v-list-item-title>
                                    Please select atleast one customer!
                                </v-list-item-title>

                                <v-list-item-subtitle>
                                    Go ahead, make a selection above!
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-select>

                <v-text-field
                    id="frmBroadcastNotificationTitle"
                    class="mb-3"
                    label="Title"
                    persistent-hint
                    v-model="form.title"
                    v-validate="'required'"
                    data-vv-name="title"
                    data-vv-as="title"
                    :error-messages="errors.collect('form.title')"
                />

                <v-textarea
                    id="frmBroadcastNotificationMessage"
                    label="Message"
                    v-model="form.message"
                    v-validate="'required'"
                    data-vv-name="message"
                    data-vv-as="message"
                    :error-messages="errors.collect('form.message')"
                />
            </v-card-text>

            <v-card-actions class="mx-2">
                <v-spacer></v-spacer>

                <v-btn
                    type="submit"
                    class="mb-2"
                    :color="app.color_name"
                    large
                    :loading="state.loading"
                >
                    Send Broadcast
                </v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</template>

<script>
import { computed, defineComponent, onMounted, reactive, ref } from '@vue/composition-api';
import axios from 'axios';
import _ from 'lodash';

export default defineComponent({
    name: 'merchant-broadcast-notification',
    $_veeValidate: {
        validator: 'new',
    },
    setup(props, { root }) {
        const state = reactive({
            loading: true,
            customers: [],
            hasError: false,
            isValid: false,
            responseMessage: '',
        });

        const form = reactive({
            title: '',
            message: '',
            customers: [],
        });

        const app = computed(() => root.$store.getters.app);
        const likesAllMerchant = computed(() => form.customers.length === state.customers.length);
        const likesSomeMerchant = computed(() => form.customers.length > 0 && ! likesAllMerchant.value);
        const icon = computed(() => {
            if (likesAllMerchant.value) return 'mdi-close-box';
            if (likesSomeMerchant.value) return 'mdi-minus-box';

            return 'mdi-checkbox-blank-outline';
        });

        onMounted(async () => {
            state.loading = true;

            const response = await axios.get('/merchant/get-dropdown-customer');

            state.customers = response.data.data;

            state.loading = false;
        });

        const toggle = () => {
            root.$nextTick(() => {
                if (likesAllMerchant.value) {
                    form.customers = [];
                } else {
                    form.customers = _.map(state.customers, 'value');
                }
            });
        };

        return {
            state,
            form,
            app,
            likesAllMerchant,
            likesSomeMerchant,
            icon,
            toggle,
        };
    },
    methods: {
        async onSubmit() {
            this.state.loading = true;
            this.state.hasError = false;
            this.state.isValid = false;

            const validate = this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            try {
                const response = await axios.post('/merchant/send-broadcast-notication', {
                    title: this.form.title,
                    message: this.form.message,
                    customers: this.form.customers,
                });

                if (response.status === 200) {
                    this.state.isValid = true;
                    this.state.responseMessage = response.data.message;
                } else {
                    this.state.hasError = false;
                }
            } catch (exception) {
                let error = exception.response.data || {};

                this.state.hasError = true;
                this.state.responseMessage = error.message;
            } finally {
                this.state.loading = false;
            }
        }
    },
});
</script>

<style scoped></style>
