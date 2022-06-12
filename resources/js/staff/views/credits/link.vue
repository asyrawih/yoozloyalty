<template>
    <v-container fluid fill-height>
        <v-layout align-center justify-center row fill-height wrap>
            <v-flex xs10 sm8 md6 lg4 xl3>
                <v-card class="elevation-18 my-7">
                    <v-card-text v-show="state.loading">
                        <v-progress-linear
                            indeterminate
                            color="primary"
                        />
                    </v-card-text>

                    <div v-show="! state.loading">
                        <div v-show="! state.isValidToken">
                            <v-card-text>
                                <p class="title">
                                    Token is not valid or has expired.
                                </p>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    color="primary"
                                    to="{ name: 'dashboard' }"
                                >
                                    Dashboard
                                </v-btn>
                            </v-card-actions>
                        </div>

                        <div v-show="state.customer">
                            <div v-show="state.isValidToken && ! state.credited">
                                <v-list three-line>
                                    <v-list-item>
                                        <v-list-item-avatar class="mt-6 ml-2">
                                            <v-avatar
                                                class="ma-2"
                                                :size="56"
                                                color="grey"
                                            >
                                                <v-img :src="state.customer.avatar"></v-img>
                                            </v-avatar>
                                        </v-list-item-avatar>

                                        <v-list-item-content>
                                            <v-list-item-title
                                                v-html="state.customer.name"
                                            >
                                            </v-list-item-title>

                                            <v-list-item-subtitle
                                                v-html="state.customer.number"
                                            >
                                            </v-list-item-subtitle>

                                            <v-list-item-subtitle>
                                                <v-icon size="17">toll</v-icon>

                                                <span
                                                    v-html="customer_point_formatted(state.points)"
                                                />
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>

                                <v-form
                                    ref="form"
                                    lazy-validation
                                    data-vv-scope="form"
                                    @submit.prevent="onSubmit"
                                >
                                    <v-card-text>
                                        <p class="body-1">
                                            You can credit the customer's points below.
                                        </p>

                                        <v-text-field
                                            ref="customer_number"
                                            id="frmCustomerNumber"
                                            class="mb-3"
                                            label="Customer Number"
                                            prepend-inner-icon="person"
                                            placeholder="xxxx-xxxx-xxxx"
                                            readonly
                                            v-model="form.customer_number"
                                        />

                                        <v-text-field
                                            ref="receipt_number"
                                            id="frmReceiptNumber"
                                            class="mb-3"
                                            label="Receipt Number"
                                            prepend-inner-icon="receipt"
                                            v-model="form.receipt_number"
                                            v-validate="'required'"
                                            data-vv-name="receipt_number"
                                            data-vv-as="receipt number"
                                            :error-messages="errors.collect('form.receipt_number')"
                                        />

                                        <v-text-field
                                            type="number"
                                            ref="receipt_amount"
                                            id="frmReceiptAmount"
                                            class="mb-3"
                                            label="Receipt Amount"
                                            prepend-inner-icon="mdi-currency-usd"
                                            v-model="form.receipt_amount"
                                            v-validate="'required|numeric|min_value:1'"
                                            data-vv-name="receipt_amount"
                                            data-vv-as="receipt amount"
                                            :error-messages="errors.collect('form.receipt_amount')"
                                        />

                                        <v-autocomplete
                                            ref="segments"
                                            id="frmSegments"
                                            v-model="form.segments"
                                            :items="state.segments"
                                            item-value="0"
                                            item-text="1"
                                            label="Segments (optional)"
                                            hide-no-data
                                            hide-selected
                                            chips
                                            multiple
                                            prepend-inner-icon="category"
                                            deletable-chips
                                        />
                                    </v-card-text>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>

                                        <v-btn
                                            color="primary"
                                            type="submit"
                                        >
                                            Credit points
                                        </v-btn>
                                    </v-card-actions>
                                </v-form>
                            </div>

                            <div v-if="state.credited">
                                <v-layout row align-start>
                                    <v-list-item>
                                        <v-list three-line>
                                            <v-list-item>
                                                <v-list-item-avatar class="mt-6 ml-2">
                                                    <v-avatar
                                                        class="ma-2"
                                                        :size="56"
                                                        color="grey"
                                                    >
                                                        <v-img
                                                            :src="state.customer.avatar"
                                                        ></v-img>
                                                    </v-avatar>
                                                </v-list-item-avatar>

                                                <v-list-item-content>
                                                    <v-list-item-title
                                                        v-html="state.customer.name"
                                                    />

                                                    <v-list-item-subtitle
                                                        v-html="state.customer.number"
                                                    />

                                                    <v-list-item-subtitle>
                                                        <v-icon size="17">toll</v-icon>

                                                        <span
                                                            v-html="customer_point_formatted(state.points)"
                                                        />
                                                    </v-list-item-subtitle>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </v-list>
                                    </v-list-item>
                                </v-layout>

                                <v-card-text>
                                    <p class="body-1">
                                        The customer has been successfully credited.
                                    </p>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn
                                        color="primary"
                                        to="{ name: 'dashboard' }"
                                    >
                                        Dashboard
                                    </v-btn>
                                </v-card-actions>
                            </div>
                        </div>
                    </div>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import { computed, defineComponent, onBeforeMount, onMounted, reactive } from '@vue/composition-api';
import _ from 'lodash';

import ServiceCreditsApi from './services/CreditsApi';

export default defineComponent({
    name: 'credits-link',
    $_veeValidate: {
        validator: "new"
    },
    setup(props, { root }) {
        const state = reactive({
            loading: true,
            isValidToken: false,
            credited: false,
            customer: {},
            segments: [],
            points: 0,
        });

        const form = reactive({
            locale: 'en',
            uuid: '',
            token: '',
            customer_number: '',
            receipt_number: '',
            receipt_amount: null,
            segments : []
        });

        const campaign = computed(() => root.$store.state.app.campaign);
        const user = computed(() => root.$auth.user());

        onBeforeMount(() => {
            if (! root.$can('credit')) {
                root.$router.push({ name: 'dashboard' });
            }
        });

        onMounted(async () => {
            const token = root.$route.query.token;
            const locale = root.$i18n.locale;

            const { tokenIsValid, customer } = await ServiceCreditsApi().validateLinkTokenApi({
                uuid: campaign.value.uuid,
                token
            });

            const segments = await ServiceCreditsApi().getSegmentsApi({
                locale: locale,
                uuid: campaign.value.uuid
            });

            state.segments = _.toPairs(segments);
            state.isValidToken = tokenIsValid;

            form.locale = locale;
            form.uuid = campaign.value.uuid;
            form.token = token;

            if (customer) {
                state.customer = customer;
                state.points = parseInt(customer.points);

                form.customer_number = customer.number;
            }

            state.loading = false;
        });

        const customer_point_formatted = (customer_points) =>
            new Intl.NumberFormat(user.value.locale.replace('_', '-'))
                .format(customer_points);

        const resetForm = () => {
            form.customer_number = '';
            form.receipt_number = '';
            form.receipt_amount = 0;
            form.segments = [];

            root.$validator.reset();

            return true;
        }

        return {
            state,
            form,
            campaign,
            user,
            customer_point_formatted,
            resetForm,
        };
    },
    methods: {
        async onSubmit() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            try {
                const response = await ServiceCreditsApi().creditCustomerByTokenApi(this.form);

                if (response.status === 'success') {
                    this.state.credited = true;
                    this.state.points = this.state.points + parseInt(response.data.points);
                    this.resetForm();
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

                this.state.credited = false;
            }

            this.state.loading = false;

            return true;
        }
    }
});
</script>

