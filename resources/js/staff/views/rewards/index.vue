<template>
    <div class="pb-5">
        <v-container grid-list-lg fluid class="pa-0">
            <v-layout row wrap>
                <v-flex
                    class="py-0"
                    xs12
                    :style="{
                        'background-color': campaign.theme.secondaryColor,
                        color: campaign.theme.secondaryTextColor
                    }"
                >
                    <v-container fill-height>
                        <v-layout row wrap align-center>
                            <v-flex xs12 style="z-index: 2">
                                <div class="mt-3 mb-7 pa-0">
                                    <h1 class="mb-1 display-1">
                                        Redeem rewards
                                    </h1>

                                    <div>
                                        Below are the options the customer has
                                        to redeem a reward.
                                    </div>
                                </div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-flex>
            </v-layout>
        </v-container>

        <v-container grid-list-lg class="mt-7">
            <v-layout row wrap>
                <v-flex
                    v-for="(item, index) in redeemOptions.filter(function(option){
                        return option.active;
                    })"
                    :key="index"
                    d-flex
                    xs12
                    sm6
                    lg3
                >
                    <v-hover v-if="item.active">
                        <template v-slot:default="{ hover }">
                            <v-card
                                @click="onOpen(item.id)"
                                class="w-100 card-link text-xs-center"
                            >
                                <div class="overlay-highlight"></div>

                                <v-icon
                                    size="64"
                                    class="mt-5 grey--text text--darken-3"
                                >
                                    {{ item.icon }}
                                </v-icon>

                                <v-card-title
                                    primary-title
                                    style="display: block"
                                >
                                    <div>
                                        <h3
                                            class="mb-2 title grey--text text--darken-3"
                                            v-html="item.title"
                                        />

                                        <div
                                            class="mb-2 grey--text text--darken-1 subtitle-1"
                                            v-html="item.description"
                                        />
                                    </div>
                                </v-card-title>

                                <v-fade-transition>
                                    <v-overlay
                                        v-if="hover"
                                        absolute
                                        color="#000"
                                    />
                                </v-fade-transition>
                            </v-card>
                        </template>
                    </v-hover>
                </v-flex>
            </v-layout>
        </v-container>

        <!-- Claim QR -->

        <scan-qr-code
            ref="qrCode"
            @onScanner="onScanner"
        />

        <!-- Claim - Merchant Enters Code -->

        <staff-unique-code ref="uniqueCode" />

        <!-- Claim - Provide Customer Number -->

        <claim-by-number
            ref="customerNumber"
            :rewards="state.rewards"
            :segments="state.segments"
        />

        <!-- Claim - Provide Customer Card -->

        <claim-by-number
            ref="customerCard"
            useCardNumber
            :rewards="state.rewards"
            :segments="state.segments"
        />

        <!-- Claim - Transaction Discount -->

        <transaction-discount
            ref="customerCardTransaction"
            :segments="state.segments"
        />

        <!-- Claim - Provide OTP confirmation -->

        <otp ref="otpConfirmation" />
    </div>
</template>

<script>
import { computed, defineComponent, onBeforeMount, onMounted, reactive, ref } from '@vue/composition-api';
import moment from 'moment';
import _ from 'lodash';
import CryptoJS from 'crypto-js';

import ServiceRewardApi from './services/RewardApi';

import ScanQrCode from './components/Scanner.vue';
import StaffUniqueCode from './components/StaffUniqueCode.vue';
import ClaimByNumber from './components/ClaimByNumber.vue';
import TransactionDiscount from './components/TransactionDiscount.vue';
import Otp from './components/Otp.vue';

export default defineComponent({
    name: 'staff-reward',
    $_veeValidate: {
        validator: 'new'
    },
    components: {
        ScanQrCode,
        StaffUniqueCode,
        ClaimByNumber,
        TransactionDiscount,
        Otp,
    },
    setup(props, { root, refs }) {
        const state = reactive({
            segments: [],
            rewards: [],
        });

        const user = computed(() => root.$auth.user());
        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);
        const redeemOptions = computed(() => [
            {
                id: 'qrCode',
                active: _.indexOf(campaign.value.redeemOptions, 'qr') >= 0 ? true : false,
                icon: 'fas fa-qrcode',
                title: 'QR Code',
                description: 'The customer displays a QR that can be scanned by a staff member.'
            },
            {
                id: 'uniqueCode',
                active: _.indexOf(campaign.value.redeemOptions, 'merchant') >= 0 ? true : false,
                icon: 'fas fa-hand-holding',
                title: 'Staff Unique Code',
                description: "Generate a code that a staff member can enter on the customer's phone."
            },
            {
                id: 'customerNumber',
                active: _.indexOf(campaign.value.redeemOptions, 'customerNumber') >= 0 ? true : false,
                icon: 'card_giftcard',
                title: 'Customer Mobile Number',
                description: "Redeem a reward with a customer number."
            },
            {
                id: 'customerCard',
                active: _.indexOf(campaign.value.redeemOptions, 'customerCard') >= 0 ? true : false,
                icon: 'fas fa-credit-card',
                title: 'Membership Card Number',
                description: "Redeem a reward with a membership card number."
            },
            {
                id: 'customerCardTransaction',
                active: true,
                icon: 'fas fa-donate',
                title: 'Redeem Transaction',
                description: "Redeem a reward with a customer number for get discount transaction."
            },
        ]);

        const qrCode = ref(null);
        const uniqueCode = ref(null);
        const customerNumber = ref(null);
        const customerCard = ref(null);
        const customerCardTransaction = ref(null);
        const otpConfirmation = ref(null);

        onBeforeMount(() => {
            if (! root.$can('reward')) {
                root.$router.push({ name: 'dashboard' });
            }

            if (user.value) {
                moment.locales(user.value.locale);
            }
        });

        onMounted(() => {
            root.$root.otpConfirmation = refs.otpConfirmation;

            const params = { locale: locale.value, uuid: campaign.value.uuid };

            Promise.all([
                ServiceRewardApi().getSegementListApi(params),
                ServiceRewardApi().getRewardListApi(params),
            ]).then((responses) => {
                state.segments = responses[0];
                state.rewards = responses[1];
            }).catch(exceptions => {});
        });

        const decryptedText = (value = '') => {
            const encodedWord = CryptoJS.enc.Base64.parse(value);
            const decoded = CryptoJS.enc.Utf8.stringify(encodedWord);

            return decoded;
        }

        const onOpen = (section = 'qrCode') => refs[section].open();

        const onScanner = (onScan) => {
            onScan.attachTo(document, {
                onScan: (sScanned, iQty) => {
                    if (sScanned.length === 8) {
                        root.$router.push({ name: 'rewards.link', query: { token: sScanned } });
                    } else {
                        const decrypted = decryptedText(sScanned);

                        const code = decrypted.replace(/-/g, '');

                        if (code.length === 16) {
                            refs.customerCard.open(code);
                        }

                        if (code.length >= 9 && code.length <= 15) {
                            refs.customerNumber.open(code);
                        }
                    }

                    refs.qrCode.close();
                }
            });
        }

        return {
            state,
            locale,
            campaign,
            redeemOptions,
            qrCode,
            uniqueCode,
            customerNumber,
            customerCard,
            customerCardTransaction,
            otpConfirmation,
            onOpen,
            onScanner,
        };
    },
});
</script>
