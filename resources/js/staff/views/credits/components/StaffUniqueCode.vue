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

            <v-form
                lazy-validation
                data-vv-scope="form"
                @submit.prevent="onSubmit"
            >
                <v-card-title class="headline">
                    Generate code to enter on customer's device.
                </v-card-title>

                <v-card-text>
                    <p class="body-1">
                        You can use this code for as many customers as you
                        want, as long as it is not expired.
                    </p>

                    <v-select
                        :items="state.expires"
                        :return-object="false"
                        hide-no-data
                        prepend-inner-icon="calendar_today"
                        v-model="form.expiresSelected"
                        :disabled="state.generated"
                    />

                    <v-text-field
                        v-if="state.generated"
                        :key="state.counter"
                        id="frmGeneratedCode"
                        class="title mt-3"
                        :label="getExpirationFromNow(state.codeExpires, 'Expires ')"
                        append-icon="filter_none"
                        outlined
                        persistent-hint
                        readonly
                        v-model="state.code"
                        @click:append="copyToClipboard(state.code)"
                    />

                    <div v-if="state.generated">
                        <a
                            href="javascript:void(0);"
                            @click="onRepeat"
                        >
                            Generate a new code
                        </a>

                        <br />

                        This will revoke the current code once it's
                        generated.
                    </div>
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
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, reactive, watch } from '@vue/composition-api';
import axios from 'axios';
import moment from 'moment';

import { copyToClipboard } from '../../../utils/helpers';

export default defineComponent({
    name: 'staff-unique-code',
    $_veeValidate: {
        validator: "new"
    },
    setup(_, { root }) {
        const state = reactive({
            open: false,
            loading: false,
            generated: false,
            counter: 1,
            code: null,
            expires: [
                { value: "hour", text: "Expires in one hour" },
                { value: "day", text: "Expires in one day" },
                { value: "week", text: "Expires expires in one week" },
                { value: "month", text: "Expires expires in one month" }
            ],
            codeExpires: null,
        });

        const form = reactive({
            expiresSelected: 'day'
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);

        watch(() => state.open, async (current, previous) => {
            if (current) {
                state.loading = true;

                await getActiveMerchantCode();

                state.loading = false;
            }
        });

        const open = () => {
            state.open = true;
        }

        const clearForm = () => {
            form.expiresSelected = 'day';
        }

        const getExpirationFromNow = (expires, prefix = "")  => {
            if (expires !== null) {
                return prefix + moment(expires).fromNow();
            } else {
                return;
            }
        }

        const getActiveMerchantCode = async () => {
            // Get currently active merchant code
            const response = await axios.get("/staff/points/merchant/active-code", {
                params: {
                    locale: locale.value,
                    campaign: campaign.value.uuid
                }
            });

            if (typeof response.data.code !== "undefined") {
                state.code = response.data.code;
                state.generated = true;
                state.codeExpires = response.data.expires_at;
                state.counter++;
            }
        }

        return {
            state,
            form,
            locale,
            campaign,
            open,
            clearForm,
            copyToClipboard,
            getExpirationFromNow,
            getActiveMerchantCode
        };
    },
    methods: {
        resetComponent() {
            this.state.codeExpires = null;
            this.state.generated = false;
            this.state.code = null;
            this.state.loading = false;
            this.clearForm();
        },
        async onSubmit() {
            // this.merchant.loading = true;
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            try {
                const response = await axios.post("/staff/points/merchant/generate-code", {
                    locale: this.locale,
                    uuid: this.campaign.uuid,
                    expires: this.form.expiresSelected,
                });

                if (response.data.status === "success") {
                    this.state.code = response.data.code;

                    // Get currently active merchant code
                    await this.getActiveMerchantCode();
                }
            } catch (exception) {

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
    }
});
</script>
