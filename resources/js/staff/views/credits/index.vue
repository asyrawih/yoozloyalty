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
                                    <h1 class="display-1 mb-1">
                                        Credit Points to Customers
                                    </h1>

                                    <div>
                                        Below are the options that the customer
                                        has to earn credits.
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
                    d-flex
                    xs12
                    sm6
                    lg3
                    v-for="(item, index) in claimOptions.filter(function(option){
                        return option.active;
                    })"

                    :key="index"
                >
                    <v-hover v-if="item.active">
                        <template v-slot:default="{ hover }">
                            <v-card
                                @click="openSection(item.id)"
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
                                            class="title grey--text text--darken-3 mb-2"
                                            v-html="item.title"
                                        ></h3>
                                        <div
                                            class="grey--text text--darken-1 subtitle-1 mb-2"
                                            v-html="item.description"
                                        ></div>
                                    </div>
                                </v-card-title>

                                <v-fade-transition>
                                    <v-overlay
                                        v-if="hover"
                                        absolute
                                        color="#000"
                                    >
                                    </v-overlay>
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

        <!-- Claim - Enter Code --------------------------------------------------------------------------------------------------------------------------------- -->

        <generate-unique-code
            ref="uniqueCode"
            :segments="state.segments"
        />

        <!-- Claim - Merchant Enters Code --------------------------------------------------------------------------------------------------------------------------------- -->

        <staff-unique-code ref="staffCode" />

        <!-- Claim - Provide Customer Number --------------------------------------------------------------------------------------------------------------------------------- -->

        <earn-by-number
            ref="customerNumber"
            :segments="state.segments"
        />

        <!-- Claim - Provide Customer Card Number -->

        <earn-by-number
            ref="customerCard"
            useCardNumber
            :segments="state.segments"
        />
    </div>
</template>

<script>
import _ from 'lodash';
import axios from 'axios';
import CryptoJS from 'crypto-js';

import ScanQrCode from './components/Scanner.vue';
import GenerateUniqueCode from './components/GenerateUniqueCode.vue';
import StaffUniqueCode from './components/StaffUniqueCode.vue';
import EarnByNumber from './components/EarnByNumber.vue';

import { computed, defineComponent, onBeforeMount, onMounted, reactive, ref } from '@vue/composition-api';

export default defineComponent({
    name: 'staff-credit',
    $_veeValidate: {
        validator: 'new',
    },
    components: {
        ScanQrCode,
        GenerateUniqueCode,
        StaffUniqueCode,
        EarnByNumber,
    },
    setup(props, { root, refs }) {
        const state = reactive({
            segments: [],
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);
        const user = computed(() => root.$auth.user());
        const claimOptions = computed(() => [
            {
                active: _.indexOf(campaign.value.claimOptions, "qr") >= 0 ? true : false,
                id: "qrCode",
                icon: "fas fa-qrcode",
                title: "Scan QR Code",
                description: "The customer displays a QR that can be scanned by a staff member."
            },
            {
                active: _.indexOf(campaign.value.claimOptions, "code") >= 0 ? true : false,
                id: "uniqueCode",
                icon: "textsms",
                title: "Generate Unique Code",
                description:
                    "Generate a code that you can give to the customer."
            },
            {
                active: _.indexOf(campaign.value.claimOptions, "merchant") >= 0 ? true : false,
                id: "staffCode",
                icon: "fas fa-hand-holding",
                title: "Staff Unique Code",
                description: "Generate a code that a staff member can enter on the customer's phone."
            },
            {
                active: _.indexOf(campaign.value.claimOptions, "customerNumber") >= 0 ? true : false,
                id: "customerNumber",
                icon: "card_giftcard",
                title: "Customer Mobile Number",
                description: "Add points to a customer account using a customer number."
            },
            {
                active:_.indexOf(campaign.value.claimOptions, "customerCard") >= 0 ? true : false,
                id: "customerCard",
                icon: "fas fa-credit-card",
                title: "Membership Card Number",
                description: "Add points to a customer account using a membership card number."
            }
        ]);

        const qrCode = ref(null);
        const uniqueCode = ref(null);
        const staffCode = ref(null);
        const customerNumber = ref(null);
        const customerCard = ref(null);

        onBeforeMount(() => {
            if (! root.$can('credit')) {
                root.$router.push({ name: 'dashboard' });
            }

            if (user) {
                moment.locale(user.value.locale);
            }
        });

        onMounted(async () => {
            await getSegments();
        });

        const getSegments = async () => {
            try {
                const response = await axios.get("/staff/segments", {
                    params: {
                        locale: locale.value,
                        uuid: campaign.value.uuid,
                    },
                });

                state.segments = _.toPairs(response.data);
            } catch (exception) {}
        }

        const decryptedText = (value = '') => {
            const encodedWord = CryptoJS.enc.Base64.parse(value);
            const decoded = CryptoJS.enc.Utf8.stringify(encodedWord);

            return decoded;
        }

        const openSection = (section = 'qrCode') => refs[section].open();

        const onScanner = (onScan) => {
            onScan.attachTo(document, {
                onScan: (sScanned, iQty) => {
                    if (sScanned.length === 8) {
                        root.$router.push({ name: 'credits.link', query: { token: sScanned } });
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
                },
            });
        }

        return {
            state,
            locale,
            campaign,
            user,
            claimOptions,
            qrCode,
            uniqueCode,
            staffCode,
            customerNumber,
            customerCard,
            openSection,
            onScanner
        };
    },
});
</script>

