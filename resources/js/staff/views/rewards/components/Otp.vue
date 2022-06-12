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
                Confirmation needed
            </v-card-title>

            <v-form
                data-vv-scope="form"
                lazy-validation
                @submit.prevent="onSubmit"
            >
                <v-card-text>
                    <p class="body-1">
                        Please ask the customer for the confirmation code.
                    </p>

                    <v-text-field
                        ref="frmOtpCode"
                        type="number"
                        id="frmOtpCode"
                        class="title"
                        placeholder="Confirmation code"
                        outlined
                        v-model="form.code"
                        v-validate="'required|min:6|max:6'"
                        data-vv-name="code"
                        :error-messages="errors.collect('form.code')"
                    />

                    <a
                        ref="resendOtp"
                        class="resend-otp"
                        :class="state.time === 60 ? '' : 'disable-resend-otp'"
                        @click="resendOtpCode"
                    >
                        {{ state.resendText }}
                    </a>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="state.loading"
                    >
                        {{ $t("submit") }}
                    </v-btn>

                    <v-btn
                        color="secondary"
                        text
                        :disabled="state.loading"
                        @click="close"
                    >
                        {{ $t("close") }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, reactive, ref, watch } from '@vue/composition-api';

import ServiceRewardApi from '../services/RewardApi';

export default defineComponent({
    $_veeValidate: {
        validator: 'new',
    },
    name: 'reward-otp',
    setup(_, { root, refs }) {
        const state = reactive({
            open: false,
            loading: false,
            time: 60,
            timer: null,
            resendText: 'Resend OTP',
            resolve: null,
            reject: null
        });

        const form = reactive({
            number: null,
            code: null,
            mode: null,
        });

        const campaign = computed(() => root.$store.state.app.campaign);

        watch(() => state.open, async (current, previous) => {
            if (current) {
                requestOtpCode(form.number, form.mode);
            }
        });

        const clearForm = () => {
            form.number = null;
            form.code = null;
        }

        const resetOtpTimer = () => {
            clearInterval(state.timer);

            state.time = 60;
            state.timer = null;
            state.resendText = 'Resend OTP';
        };

        const open = (number = '', mode = '') => {
            form.number = number;
            form.mode = mode;

            state.open = true;

            return new Promise((resolve, reject) => {
                state.resolve = resolve;
                state.reject = reject;
            });
        }

        const requestOtpCode = async (number = '', mode = '') => {
            state.loading = true;

            resetOtpTimer();

            try {
                const response = await ServiceRewardApi().requestOtpCodeApi({
                    purpose: 'reward_redemption',
                    uuid: campaign.value.uuid,
                    customer: number,
                    mode,
                });

                if (response.status === 'success') {
                    root.$root.$snackbar(response.message);

                    form.number = number;

                    state.timer = setInterval(() => {
                        state.time--;
                        state.resendText = `You can request for another OTP in ${state.time} seconds.`;

                        if (state.time === 0) {
                            resetOtpTimer();
                        }
                    }, 1000);
                }
            } catch (exception) {
                throw exception;
            } finally {
                state.loading = false;
            }
        };

        const resendOtpCode = async () => {
            if (state.time !== 60) {
                return false;
            }

            await requestOtpCode(form.number, form.mode);
        }

        return {
            state,
            form,
            clearForm,
            open,
            resetOtpTimer,
            resendOtpCode,
        };
    },
    methods: {
        resetComponent() {
            this.state.loading = false;
            this.state.time = 60;
            this.state.timer = null;

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
                const response = await ServiceRewardApi().verifyOtpCodeApi({
                    purpose: 'reward_redemption',
                    code: this.form.code,
                    customer: this.form.number,
                    mode: this.form.mode
                });

                if (response.status === 'success') {
                    this.state.resolve(true);
                }
            } catch (exception) {
                if (exception.status === 'failed') {
                    this.$validator.errors.add({
                        field: `form.code`,
                        msg: exception.message
                    });
                } else {
                    this.$root.$snackbar(exception.message);
                }
            } finally {
                this.state.loading = false;
            }
        },
        close() {
            this.resetComponent();
            this.resetOtpTimer();
            this.state.resolve(false);
            this.state.open = false;
        }
    },
});
</script>

<style scoped>
.resend-otp {
    font-size: 10px !important;
}
.disable-resend-otp {
    pointer-events: none;
    color: #aaaaaa !important;
}
</style>
