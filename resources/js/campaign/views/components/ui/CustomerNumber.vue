<template>
    <v-card>
        <v-card-title>
            <span>
                {{ $t("customer_number") }}
            </span>

            <v-spacer></v-spacer>

            <v-btn
                icon
                @click="activator = false"
            >
                <v-icon>mdi-close</v-icon>
            </v-btn>
        </v-card-title>

        <v-card-text>
            <v-row class="mx-2 mt-1">
                <v-col class="text-center">
                    <qrcode-vue
                        :value="encrypted(user.number)"
                        :size="170"
                        level="H"
                        class="my-1"
                    />

                    <v-text-field
                        type="text"
                        id="frmCustomerNumber"
                        class="title"
                        outlined
                        append-icon="filter_none"
                        @click:append="copyToClipboard(user.number)"
                        :value="user.number"
                        readonly
                    />
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
    name: 'customer-number',
    components: {
        QrcodeVue,
    },
    props: {
        open: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, { root, emit }) {
        const state = reactive({});

        const user = computed(() => root.$auth.user());
        const campaign = computed(() => root.$store.state.app.campaign);
        const activator = computed({
            get: () => props.open,
            set: (value) => emit('update:open', value),
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
            activator,
            encrypted,
            copyToClipboard,
        }
    },
});
</script>
