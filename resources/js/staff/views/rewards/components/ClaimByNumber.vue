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
                Redeem reward with {{ title }}
            </v-card-title>

            <v-form
                data-vv-scope="form"
                lazy-validation
                @submit.prevent="onSubmit"
            >
                <v-card-text>
                    <p
                        v-if="! state.redeemed"
                        class="body-1"
                    >
                        <span v-if="! state.redeemed">
                            Select a reward and enter a {{ title }} to redeem a reward for the customer.
                        </span>

                        <span v-else>
                            Reward successfully redeemed.
                        </span>
                    </p>

                    <div class="mb-3">
                        <a
                            v-if="state.redeemed"
                            href="javascript:void(0);"
                            @click="onRepeat"
                        >
                            Redeem another reward
                        </a>
                    </div>

                    <v-autocomplete
                        ref="frmReward"
                        id="frmReward"
                        label="Reward"
                        prepend-inner-icon="fas fa-gift"
                        :items="rewards"
                        item-value="uuid"
                        item-text="title_with_points"
                        hide-no-data
                        hide-selected
                        v-model="form.reward"
                        :disabled="state.redeemed"
                        v-validate="'required'"
                        data-vv-name="reward"
                        :error-messages="errors.collect('form.reward')"
                    />

                    <v-text-field
                        v-if="useCardNumber"
                        ref="frmNumber"
                        id="frmNumber"
                        label="Membership card number"
                        prepend-inner-icon="person"
                        outline
                        placeholder="XXXX-XXXX-XXXX-XXXX"
                        v-mask="'####-####-####-####'"
                        v-model="form.number"
                        :disabled="state.redeemed"
                        v-validate="'required'"
                        data-vv-name="number"
                        data-vv-as="membership card number"
                        :error-messages="errors.collect('form.number')"
                    />

                    <v-text-field
                        v-else
                        ref="frmNumber"
                        id="frmNumber"
                        label="Customer number"
                        prepend-inner-icon="person"
                        outline
                        placeholder="XXX-XXX-XXXX"
                        v-mask="[
                            '###-###-####',
                            '###-###-#####',
                            '###-###-######',
                            '###-###-#######',
                        ]"
                        v-model="form.number"
                        :disabled="state.redeemed"
                        v-validate="'required'"
                        data-vv-name="number"
                        :error-messages="errors.collect('form.number')"
                    />

                    <v-autocomplete
                        v-if="Object.keys(segments).length > 0"
                        ref="frmSegments"
                        id="frmSegments"
                        label="Segments (optional)"
                        prepend-inner-icon="category"
                        :items="segments"
                        item-value="0"
                        item-text="1"
                        hide-no-data
                        hide-selected
                        chips
                        multiple
                        deletable-chips
                        v-model="form.segments"
                        :disabled="state.redeemed"
                    />
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="state.loading"
                        :disabled="state.redeemed"
                    >
                        Redeem Reward
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
import onScan from 'onscan.js';
import CryptoJS from 'crypto-js';

import ServiceCreditsApi from '../services/RewardApi';

export default defineComponent({
    name: 'reward-claim-by-number',
    props: {
        useCardNumber: {
            type: Boolean,
            default: false,
        },
        rewards: {
            type: Array,
            default: [],
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
            redeemed: false,
        });

        const form = reactive({
            reward: null,
            number: null,
            segments: [],
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);
        const title = computed(() => (props.useCardNumber) ? 'customer card number' : 'customer number');
        const mode = computed(() => (props.useCardNumber) ? 'card' : 'number');

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

        const clearForm = () => {
            form.reward = null;
            form.number = null;
            form.segments = [];
        }

        const open = (number = null) => {
            state.open = true;
            form.number = number;
        }

        const redeemReward = async () => {
            try {
                await ServiceCreditsApi().creditCustomer2Api({
                    locale: locale.value,
                    uuid: campaign.value.uuid,
                    reward: form.reward,
                    number: form.number,
                    segments: form.segments,
                    mode: mode.value
                });

                state.redeemed = true;

                return true;
            } catch (exception) {
                throw exception;
            }
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
            mode,
            clearForm,
            open,
            redeemReward,
        };
    },
    methods: {
        resetComponent() {
            this.state.loading = false;
            this.state.redeemed = false;
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
                let reward = this.rewards.find(reward => reward.uuid === this.form.reward);

                if (reward && reward.requires_validation) {
                    this.state.loading = false;

                    this.$root.otpConfirmation.open(this.form.number, this.mode)
                        .then(async (confirm) => {
                            if (confirm) {
                                this.$root.otpConfirmation.close();

                                this.state.loading = true;

                                await this.redeemReward();

                                this.state.loading = false;
                            }
                        })

                    return true;
                }

                await this.redeemReward();
            } catch (exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field],
                        });
                    }
                } else {
                    this.$root.$snackbar(exception.message);
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
