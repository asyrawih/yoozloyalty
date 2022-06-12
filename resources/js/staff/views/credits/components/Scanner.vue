<template>
    <v-dialog
        v-model="state.open"
        persistent
        width="360"
    >
        <v-card>
            <v-card-title>
                <span class="headline">Scan Code</span>

                <v-spacer></v-spacer>

                <v-btn icon @click="close">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text class="text-center">
                <img
                    class="mt-3"
                    src="/images/scan-code.svg"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { defineComponent, reactive, watch } from '@vue/composition-api';
import onScan from 'onscan.js';

export default defineComponent({
    name: 'credit-scanner',
    setup(_, { emit }) {
        const state = reactive({
            open: false,
            resolve: null,
            reject: null,
        });

        watch(() => state.open, (current, previous) => {
            if (onScan.isAttachedTo(document)) {
                onScan.detachFrom(document);
            }

            if (current) {
                emit('onScanner', onScan);
            }
        });

        const open = () => state.open = true;

        const close = () => state.open = false;

        return {
            state,
            open,
            close,
        };
    },
});
</script>
