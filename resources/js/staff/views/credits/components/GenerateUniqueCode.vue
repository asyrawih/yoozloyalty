<template>
    <v-dialog
        v-model="state.open"
        persistent
        width="360"
    >
        <v-card>
            <v-overlay
                :value="state.loading"
            >
                <v-progress-circular
                    class="ma-5"
                    color="primary"
                    :size="50"
                    indeterminate
                />
            </v-overlay>

            <v-tabs
                v-model="state.selectedTab"
                slider-color="grey darken-3"
                color="grey darken-3"
            >
                <v-tab :href="'#generate'">
                    Generate code
                </v-tab>

                <v-tab :href="'#active'">
                    Active codes
                </v-tab>
            </v-tabs>

            <v-tabs-items
                v-model="state.selectedTab"
                class="mx-2"
                :touchless="false"
            >
                <v-tab-item :value="'generate'">
                    <v-form
                        data-vv-scope="form"
                        lazy-validation
                        @submit.prevent="onSubmit"
                    >
                        <v-card-text>
                            <p class="body-1">
                                Generate a code you can give to the
                                customer. This code can be used only once.
                            </p>

                            <v-text-field
                                type="number"
                                id="frmCustomerBillNumber"
                                label="Receipt number"
                                prepend-inner-icon="credit_card"
                                outline
                                :disabled="state.generated"
                                v-model="form.bill_number"
                                data-vv-name="bill_number"
                                data-vv-as="receipt number"
                                v-validate="'required|numeric'"
                                :error-messages="errors.collect('form.bill_number')"
                            />

                            <v-text-field
                                type="number"
                                id="frmCustomerBillAmount"
                                label="Receipt amount"
                                prepend-inner-icon="receipt"
                                outline
                                :disabled="state.generated"
                                v-model="form.bill_amount"
                                data-vv-name="bill_amount"
                                data-vv-as="receipt amount"
                                v-validate="'required|decimal:2'"
                                :error-messages="errors.collect('form.bill_amount')"
                            />

                            <v-select
                                :items="state.expires"
                                :return-object="false"
                                hide-no-data
                                prepend-inner-icon="calendar_today"
                                :disabled="state.generated"
                                v-model="form.expiresSelected"
                            />

                            <v-autocomplete
                                v-if="Object.keys(segments).length > 0"
                                :items="segments"
                                item-value="0"
                                item-text="1"
                                label="Segments (optional)"
                                prepend-inner-icon="category"
                                hide-no-data
                                hide-selected
                                chips
                                multiple
                                deletable-chips
                                v-model="form.segments"
                                :disabled="state.generated"
                            />

                            <v-text-field
                                v-if="state.generated"
                                :key="state.counter"
                                class="title mt-3"
                                outlined
                                readonly
                                v-model="state.code"
                                append-icon="filter_none"
                                @click:append="copyToClipboard(state.code)"
                            />

                            <a
                                v-if="state.generated"
                                href="javascript:void(0);"
                                @click="onRepeat"
                            >
                                Generate a new code
                            </a>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn
                                type="submit"
                                color="primary"
                                :loading="state.loading"
                                :disabled="state.generated"

                            >
                                Generate
                            </v-btn>

                            <v-btn
                                color="secondary"
                                text
                                @click="close"
                            >
                                Close
                            </v-btn>
                        </v-card-actions>
                    </v-form>
                </v-tab-item>

                <v-tab-item :value="'active'">
                    <v-card-text
                        v-if="! Object.keys(state.customerActiveCodes).length"
                    >
                        <p class="body-1">There are no active codes.</p>
                    </v-card-text>

                    <v-card-text v-else>
                        <p class="body-1">
                            These codes are not expired and have not been claimed by a customer.
                        </p>

                        <v-simple-table>
                            <thead>
                                <tr>
                                    <th class="text-xs-left">Code</th>
                                    <th class="text-xs-right">Credits</th>
                                    <th class="text-xs-left">Expires</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="item in state.customerActiveCodes"
                                    :key="item.uuid"
                                >
                                    <td>{{ item.formatted_code }}</td>

                                    <td class="text-xs-right">
                                        {{ item.points }}
                                    </td>

                                    <td>
                                        {{ getExpirationFromNow(item.expires_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </v-simple-table>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            color="secondary"
                            text
                            @click="close"
                        >
                            Close
                        </v-btn>
                    </v-card-actions>
                </v-tab-item>
            </v-tabs-items>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, onMounted, reactive } from '@vue/composition-api';
import axios from 'axios';
import moment from 'moment';

import { copyToClipboard } from '../../../utils/helpers';

export default defineComponent({
    name: 'generate-unique-code',
    $_veeValidate: {
        validator: "new"
    },
    props: {
        segments: {
            type: Array,
            default: [],
        },
    },
    setup(props, { root }) {
        const state = reactive({
            open: false,
            loading: false,
            selectedTab: 'generate',
            generated: false,
            code: null,
            expires: [
                { value: "hour", text: "Expires in one hour" },
                { value: "day", text: "Expires in one day" },
                { value: "week", text: "Expires expires in one week" },
                { value: "month", text: "Expires expires in one month" }
            ],
            customerActiveCodes: [],
            counter: 1,
        });

        const form = reactive({
            bill_number: null,
            bill_amount: null,
            expiresSelected: 'week',
            segments: [],
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);

        onMounted(async () => {
            await getCustomerActiveCodes();
        });

        const open = () => {
            state.open = true;
        }

        const clearForm = () => {
            form.bill_number = null;
            form.bill_amount = null;
            form.expiresSelected = 'week';
            form.segments = [];
        }

        const getCustomerActiveCodes = async () => {
            // Get currently active customer codes
            const response = await axios.get("/staff/points/customer/active-codes", {
                params: {
                    locale: locale.value,
                    campaign: campaign.value.uuid
                }
            });

            state.customerActiveCodes = response.data;
        };

        const getExpirationFromNow = (expires, prefix = "")  => {
            if (expires !== null) {
                return prefix + moment(expires).fromNow();
            } else {
                return;
            }
        }

        return {
            state,
            form,
            locale,
            campaign,
            copyToClipboard,
            open,
            clearForm,
            getCustomerActiveCodes,
            getExpirationFromNow,
        };
    },
    methods: {
        resetComponent() {
            this.state.loading = false;
            this.state.generated = false;
            this.state.code = null;
            this.clearForm();
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
                const response = await axios.post("/staff/points/customer/generate-code", {
                    locale: this.locale,
                    uuid: this.campaign.uuid,
                    bill_number: this.form.bill_number,
                    bill_amount: this.form.bill_amount,
                    expires: this.form.expiresSelected,
                    segments: this.form.segments
                });

                if (response.data.status === "success") {
                    this.state.code = response.data.code;
                    this.state.generated = true;

                    await this.getCustomerActiveCodes();
                }
            } catch(exception) {
                let errors = exception.response.data.errors || {};

                for (let field in errors) {
                    this.$validator.errors.add({
                        field: `form.${field}`,
                        msg: errors[field]
                    });
                }
            } finally {
                this.state.loading = false;
            }
        },
        close() {
            this.resetComponent();
            this.state.open = false;
        },
        onRepeat() {
            this.resetComponent();
        }
    },
});
</script>
