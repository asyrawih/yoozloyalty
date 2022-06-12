<template>
    <v-container fluid fill-height>
        <v-layout align-center justify-center row fill-height wrap>
            <v-flex xs10 sm8 md6 lg4 xl3>
                <v-card class="elevation-18">
                    <v-card-text
                        v-show="state.loading"
                    >
                        <v-progress-linear
                            indeterminate
                            color="primary"
                        />
                    </v-card-text>

                    <div v-show="! state.loading && ! state.isValidToken">
                        <v-card-text>
                            <p class="title">
                                Token is not valid or has expired.
                            </p>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn
                                color="primary"
                                to="{name: 'dashboard'}"
                            >
                                Dashboard
                            </v-btn>
                        </v-card-actions>
                    </div>

                    <div
                        v-show="! state.loading && state.customer && state.isValidToken && ! state.redeemed"
                    >
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
                                    />

                                    <v-list-item-subtitle
                                        v-html="state.customer.number"
                                    />

                                    <v-list-item-subtitle>
                                        <v-icon size="17">toll</v-icon>

                                        <span
                                            v-html="customer_points_formatted(state.customer.points)"
                                        />
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>

                        <v-form
                            ref="form"
                            lazy-validation
                            data-vv-scope="form"
                            @submit.prevent="creditCustomer"
                        >
                            <v-card-text>
                                <p class="body-1">
                                    You can redeem the reward below.
                                </p>

                                <v-autocomplete
                                    ref="reward"
                                    id="frmRewards"
                                    label="Reward"
                                    prepend-inner-icon="redeem"
                                    :items="state.rewards"
                                    hide-no-data
                                    hide-selected
                                    item-value="uuid"
                                    item-text="title_with_points"
                                    v-model="form.reward"
                                    v-validate="'required'"
                                    data-vv-name="reward"
                                    data-vv-as="reward"
                                    :error-messages="errors.collect('form.reward')"
                                />

                                <v-autocomplete
                                    v-if="Object.keys(state.segments).length > 0"
                                    ref="segments"
                                    id="frmSegments"
                                    label="Segments (optional)"
                                    prepend-inner-icon="category"
                                    :items="state.segments"
                                    hide-no-data
                                    hide-selected
                                    item-value="0"
                                    item-text="1"
                                    chips
                                    multiple
                                    deletable-chips
                                    v-model="form.segments"
                                />
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    type="submit"
                                    color="primary"
                                    :disabled="state.otpConfirmation"
                                >
                                    Redeem reward
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </div>

                    <div v-show="! state.loading && state.redeemed">
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
                                                <v-img :src="state.customer.avatar"></v-img>
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
                                                    v-html="customer_points_formatted(state.customer.points)"
                                                />
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </v-list-item>
                        </v-layout>

                        <v-card-text>
                            <p class="body-1">The reward has been redeemed.</p>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn
                                color="primary"
                                to="{name: 'dashboard'}"
                            >
                                Dashboard
                            </v-btn>
                        </v-card-actions>
                    </div>
                </v-card>
            </v-flex>
        </v-layout>

        <!-- Reward - Provide OTP confirmation -->

        <v-dialog
            v-model="state.otpOptions.dialog"
            persistent
            max-width="320"
        >
            <v-card>
                <v-card-title class="headline">Confirmation needed</v-card-title>

                <v-form
                    ref="otp"
                    data-vv-scope="otp"
                    @submit.prevent="onProcessOtp"
                >
                    <v-card-text>
                        <p class="body-1">
                            Please ask the customer for the confirmation code.
                        </p>

                        <v-text-field
                            type="number"
                            ref="code"
                            id="frmCode"
                            class="title"
                            outlined
                            placeholder="Confirmation code"
                            v-model="otp.code"
                            v-validate="'required|numeric|min:6|max:6'"
                            data-vv-name="code"
                            data-vv-as="confirmation code"
                            :error-messages="errors.collect('otp.code')"
                        />

                        <p
                            v-show="! state.otpOptions.canResend"
                            class="caption"
                        >
                            You can request for another OTP in {{ state.otpOptions.time }} seconds
                        </p>

                        <v-btn
                            v-show="state.otpOptions.canResend"
                            text
                            color="primary"
                            @click="resendOtpCode"
                            small
                        >
                            Resend OTP
                        </v-btn>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            type="submit"
                            color="primary"
                            text
                        >
                            {{ $t("submit") }}
                        </v-btn>

                        <v-btn
                            color="secondary"
                            text
                            @click="state.otpOptions.dialog = false"
                        >
                            {{ $t("close") }}
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
import { computed, defineComponent, onBeforeMount, onMounted, reactive } from '@vue/composition-api';

import ServiceRewardApi from './services/RewardApi';

export default defineComponent({
    $_veeValidate: {
        validator: "new"
    },
    name: 'reward-link',
    setup(props, { root }) {
        const state = reactive({
            loading: true,
            user: {},
            isValidToken: false,
            customer: {},
            rewards: [],
            segments: [],
            redeemed: false,
            otpOptions: {
                dialog: false,
                confirmed: false,
                time: 60,
                timer: null,
                canResend: false,
            },
        });

        const form = reactive({
            locale: 'en',
            uuid: '',
            token: '',
            reward: {},
            segments: [],
        });

        const otp = reactive({
            purpose: 'reward_redemption',
            code: '',
            customer: '',
        });

        const campaign = computed(() => root.$store.state.app.campaign);
        const user = computed(() => root.$auth.user());

        onBeforeMount(() => {
            if (! root.$can('reward')) {
                root.$router.push({ name: 'dashboard' });
            }
        });

        onMounted(async () => {
            const token = root.$route.query.token;

            const {customer, reward, tokenIsValid} = await ServiceRewardApi().validateLinkTokenApi({
                uuid: campaign.value.uuid,
                token
            });

            if (tokenIsValid) {
                const rewards = await ServiceRewardApi().getRewardListApi({
                    locale: root.$i18n.locale,
                    uuid: campaign.value.uuid
                });

                const segments = await ServiceRewardApi().getSegementListApi({
                    locale: root.$i18n.locale,
                    uuid: campaign.value.uuid
                });

                state.rewards = rewards;
                state.segments = segments;
                state.customer = customer ? customer : {};
                state.isValidToken = tokenIsValid;

                form.locale = root.$i18n.locale;
                form.uuid = campaign.value.uuid;
                form.token = token;
                form.reward = reward ? reward : {};
            }

            state.loading = false;
        });

        const resetForm = () => {
            form.uuid = '';
            form.token = '';
            form.reward = {};
            form.segments = [];

            root.$validator.reset();

            return;
        }

        const resetOtpTimer = () => {
            clearInterval(state.otpOptions.timer);

            state.otpOptions.timer = null;
            state.otpOptions.time = 60;
        }

        const customer_points_formatted = (customer_points) => new Intl
            .NumberFormat(user.value.locale.replace('_', '-'))
            .format(customer_points);

        const requestOtpCode = async () => {
            try {
                const response = await ServiceRewardApi().requestOtpCodeApi({
                    uuid: campaign.value.uuid,
                    purpose: 'reward_redemption',
                    customer: state.customer.customer_number
                });

                if (response.status === 'success') {
                    root.$root.$snackbar(response.message);

                    otp.customer = state.customer.customer_number;

                    state.otpOptions.dialog = true;
                    state.otpOptions.confirmed = false;
                    state.otpOptions.canResend = false;

                    state.otpOptions.timer = setInterval(() => {
                        state.otpOptions.time--;

                        if (! state.otpOptions.time) {
                            state.otpOptions.canResend = true;

                            resetOtpTimer();
                        }
                    }, 1000);

                    return true;
                }
            } catch (exception) {
                throw exception;
            }

            return;
        }

        const resendOtpCode = async () => {
            if (! state.otpOptions.timer) {
                state.otpOptions.dialog = false;

                await requestOtpCode();
            }
        }

        return {
            state,
            form,
            otp,
            resetForm,
            customer_points_formatted,
            requestOtpCode,
            resendOtpCode,
            resetOtpTimer,
        };
    },
    methods: {
        async creditCustomer() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            try {
                let reward = this.state.rewards.find(reward => reward.uuid === this.form.reward);

                if (reward) {
                    if (reward.requires_validation && ! this.state.otpOptions.confirmed) {
                        await this.requestOtpCode();

                        return true;
                    }
                }

                const response = await ServiceRewardApi().creditCustomerApi(this.form);

                if (response.status === 'success') {
                    this.state.customer.points = response.points;
                    this.state.redeemed = true;
                    this.state.otpOptions.confirmed = false;
                }

                this.state.loading = false;

                return true;
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field]
                        });
                    }
                } else {
                    this.$root.$snackbar(exception.message);
                }

                this.state.redeemed = false;
                this.state.loading = false;
                this.state.otpOptions.confirmed = false;

                return false;
            }
        },
        async onProcessOtp() {
            this.state.otpOptions.dialog = false;

            const validate = await this.$validator.validateAll('otp');

            if (! validate) {
                this.state.otpOptions.dialog = true;

                return false;
            }

            try {
                const response = await ServiceRewardApi().verifyOtpCodeApi(this.otp);

                if (response.status === 'success') {
                    this.state.otpOptions.dialog = false;
                    this.state.otpOptions.confirmed = true;
                    this.otp.code = '';
                    this.resetOtpTimer();
                    this.$validator.reset();

                    await this.creditCustomer();
                }

                return true;
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `otp.${field}`,
                            msg: exception.errors[field][0]
                        });
                    }
                } else if (exception.status === 'failed') {
                    this.$validator.errors.add({
                        field: `otp.code`,
                        msg: exception.message
                    });
                } else {
                    this.$root.$snackbar(exception.message);
                }

                this.state.otpOptions.dialog = true;
                this.state.otpOptions.computed = false;

                return false;
            }
        }
    },
});
</script>
