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

            <v-card-title class="headline">
                {{ title }}
            </v-card-title>

            <v-form
                lazy-validation
                data-vv-scope="form"
                @submit.prevent="onSubmit"
            >
                <v-card-text>
                    <p
                        v-if="state.credited"
                        class="body-1"
                    >
                        Customer successfully earn points.
                    </p>

                    <p
                        v-else
                        class="body-1"
                    >
                        {{ caption }}
                    </p>

                    <div class="mb-3">
                        <a
                            v-if="state.credited"
                            href="javascript:void(0);"
                            @click="onRepeat"
                        >
                            Add another points
                        </a>
                    </div>

                    <v-text-field
                        v-if="useCardNumber"
                        id="frmCardNumber"
                        label="Membership card number"
                        prepend-inner-icon="person"
                        outline
                        placeholder="XXXX-XXXX-XXXX-XXXX"
                        v-mask="'####-####-####-####'"
                        :disabled="state.credited"
                        v-model="form.number"
                        v-validate="'required'"
                        data-vv-name="number"
                        data-vv-as="membership card number"
                        :error-messages="errors.collect('form.number')"
                    />

                    <v-text-field
                        v-else
                        id="frmCutomerNumber"
                        label="Customer number"
                        outline
                        placeholder="XXX-XXX-XXXX"
                        prepend-inner-icon="person"
                        v-mask="[
                            '###-###-####',
                            '###-###-#####',
                            '###-###-######',
                            '###-###-#######',
                        ]"
                        v-model="form.number"
                        :disabled="state.credited"
                        v-validate="'required'"
                        data-vv-name="number"
                        data-vv-as="customer number"
                        :error-messages="errors.collect('form.number')"

                    />

                    <v-text-field
                        type="number"
                        id="frmReceiptNumber"
                        label="Receipt number"
                        outline
                        :disabled="state.credited"
                        prepend-inner-icon="credit_card"
                        v-model="form.bill_number"
                        v-validate="'required|numeric'"
                        data-vv-name="bill_number"
                        data-vv-as="receipt number"
                        :error-messages="errors.collect('form.bill_number')"
                    />

                    <v-text-field
                        type="number"
                        id="frmReceiptAmount"
                        label="Receipt amount"
                        outline
                        prepend-inner-icon="receipt"
                        :disabled="state.credited"
                        v-model="form.bill_amount"
                        v-validate="'required|decimal:2'"
                        data-vv-name="bill_amount"
                        data-vv-as="receipt amount"
                        :error-messages="errors.collect('form.bill_amount')"
                    />

                    <v-autocomplete
                        v-if="Object.keys(segments).length > 0"
                        :items="segments"
                        label="Segments (optional)"
                        item-value="0"
                        item-text="1"
                        hide-no-data
                        hide-selected
                        chips
                        multiple
                        deletable-chips
                        prepend-inner-icon="category"
                        v-model="form.segments"
                        :disabled="state.credited"
                    />
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="state.loading"
                        :disabled="state.credited"
                    >
                        Credit customer
                    </v-btn>

                    <v-btn
                        color="secondary"
                        text
                        @click="onCancel"
                    >
                        Close
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, reactive, watch } from '@vue/composition-api';
import onScan from 'onscan.js';
import CryptoJS from 'crypto-js';

import ServiceCreditsApi from '../services/CreditsApi';

export default defineComponent({
    name: 'earn-by-number',
    $_veeValidate: {
        validator: "new"
    },
    props: {
        useCardNumber: {
            type: Boolean,
            default: false,
        },
        segments: {
            type: Array,
            default: [],
        },
    },
    setup(props, { root }) {
        const state = reactive({
            open: false,
            loading: false,
            credited: false,
        });

        const form = reactive({
            number: null,
            bill_number: null,
            bill_amount: null,
            segments: [],
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);
        const title = computed(() => (props.useCardNumber) ? 'Credit a customer with customer card number' : 'Credit a customer with customer number' );
        const caption = computed(() => (props.useCardNumber) ? 'Enter the amount of receipt and a customer card number to credit the customer.' : 'Enter the amount of receipt and a customer number to credit the customer.' );
        const mode = computed(() => (props.useCardNumber) ? 'customerCard' : 'customerNumber');

        watch(() => state.open, (current, previous) => {
            if (onScan.isAttachedTo(document)) {
                onScan.detachFrom(document);
            }

            if (current) {
                onScan.attachTo(document, {
                    onScan: (sScanned, iQty) => {
                        const decrypted = decryptedText(sScanned);

                        const code = decrypted.replace(/-/g, '');

                        if (code.length === 16) {
                            form.number = code;
                        }

                        if (code.length >= 9 && code.length <= 15) {
                            form.number = code;
                        }
                    },
                });
            }
        });

        const clearForm = (number = null) => {
            form.number = number;
            form.bill_number = null;
            form.bill_amount = null;
            form.segments = [];
        }

        const decryptedText = (value = '') => {
            const encodedWord = CryptoJS.enc.Base64.parse(value);
            const decoded = CryptoJS.enc.Utf8.stringify(encodedWord);

            return decoded;
        }

        return {
            state,
            form,
            locale,
            campaign,
            title,
            caption,
            mode,
            clearForm
        };
    },
    methods: {
        resetForm(number = null) {
            this.state.loading = false;
            this.state.credited = false;
            this.clearForm(number);
            this.$validator.reset();
        },
        open(number = null) {
            this.state.open = true;
            this.form.number = number;
        },
        async onSubmit() {
            this.state.loading = true;
            this.state.credited = false;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            try {
                const response = await ServiceCreditsApi().creditCustomerApi({
                    locale: this.locale,
                    uuid: this.campaign.uuid,
                    number: this.form.number,
                    bill_number: this.form.bill_number,
                    bill_amount: this.form.bill_amount,
                    segments: this.form.segments,
                    mode: this.mode
                });

                if (response.status === 'success') {
                    this.state.credited = true;
                }
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field]
                        });
                    }
                }
            } finally {
                this.state.loading = false;
            }
        },
        onCancel() {
            this.resetForm();
            this.state.open = false;
        },
        onRepeat() {
            this.resetForm(this.form.number);
        }
    }
});
</script>
