<template>
    <v-card
        class="rounded-xl"
        style="
            background: linear-gradient(180deg, #2A4699 0%, #279CD7 100%);
        "
    >
        <v-card-text>
            <br class="mb-sm-2" />

            <v-row class="mx-sm-2">
                <v-col>
                    <span class="credit-card-text">
                        {{ campaign.business.name }}
                    </span>
                </v-col>
            </v-row>

            <v-row class="mx-sm-2">
                <v-col>
                    <div class="credit-card-barcode">
                        <qrcode-vue
                            :value="encrypted(user.card_number)"
                            :size="qrSize"
                            level="H"
                        />
                    </div>
                </v-col>

                <v-col
                    v-if="! $vuetify.breakpoint.xs"
                    class="text-right"
                    align-self="center"
                >
                    <img
                        v-if="campaign.business.logo"
                        :src="campaign.business.logo"
                        class="credit-card-image"
                    />
                </v-col>
            </v-row>

            <v-row class="mx-sm-2 mt-3">
                <v-col class="text-center">
                    <span
                        class="credit-card-number"
                        @click="copyToClipboard(user.card_number)"
                    >
                        {{ user.card_number | useSeparator }}
                    </span>
                </v-col>
            </v-row>

            <v-row class="mx-sm-2 mb-sm-1">
                <v-col>
                    <span class="credit-card-text">
                        {{ user.name }}
                    </span>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
import { computed, defineComponent, reactive } from '@vue/composition-api';
import CryptoJS from 'crypto-js';
import QrcodeVue from 'qrcode.vue';

import { copyToClipboard } from "../../../utils/helpers";

export default defineComponent({
    name: 'customer-card-number',
    components: {
        QrcodeVue,
    },
    setup(_, { root }) {
        const state = reactive({});

        const user = computed(() => root.$auth.user());
        const campaign = computed(() => root.$store.state.app.campaign);
        const qrSize = computed(() => {
            if (root.$vuetify.breakpoint.xs) {
                return 115;
            } else {
                return 88;
            }
        });
        const encrypted = (value = '') => {
            const encodedWord = CryptoJS.enc.Utf8.parse(value); // encodedWord Array object
            const encoded = CryptoJS.enc.Base64.stringify(encodedWord);

            return encoded;
        };

        return {
            state,
            user,
            campaign,
            qrSize,
            encrypted,
            copyToClipboard
        };
    },
    filters: {
        useSeparator(value = '') {
            return value.trim().replaceAll('-', ' ').toString();
        },
    },
});
</script>

<style scoped>
@media screen and (max-width: 600px) {
    .credit-card-text {
        font-family: 'Roboto';
        font-size: 15px;
        font-weight: normal;
        color: #DFD5B4;
        line-height: 18px;
        letter-spacing: 0.12em;
    }

    .credit-card-number {
        font-family: 'Roboto';
        font-size: 20px;
        font-weight: normal;
        color: #FFFFFF;
        line-height: 29px;
        letter-spacing: 0.17em;
        cursor: pointer;
    }

    .credit-card-image {
        width: 100%;
        height: 100%;
        max-width: 48px;
        max-height: 48px;
        object-fit: contain;
    }

    .credit-card-barcode {
        display: block;
        width: 100%;
        height: 100%;
        max-width: 135px;
        max-height: 135px;
        background: #FFFFFF;
        padding: 10px;
        border-radius: 20px;
        margin-right: auto;
        margin-left: auto;
    }
}

@media screen and (min-width: 601px) {
    .credit-card-text {
        font-family: 'Roboto';
        font-size: 20px;
        font-weight: normal;
        color: #DFD5B4;
        line-height: 23px;
        letter-spacing: 0.12em;
    }

    .credit-card-number {
        font-family: 'Roboto';
        font-size: 40px;
        font-weight: normal;
        color: #FFFFFF;
        line-height: 47px;
        letter-spacing: 0.12em;
        cursor: pointer;
    }

    .credit-card-image {
        width: 100%;
        height: 100%;
        max-width: 104px;
        max-height: 104px;
        object-fit: contain;
    }

    .credit-card-barcode {
        display: block;
        width: 100%;
        height: 100%;
        max-width: 108px;
        max-height: 108px;
        background: #FFFFFF;
        padding: 10px;
        border-radius: 20px;
    }
}
</style>
