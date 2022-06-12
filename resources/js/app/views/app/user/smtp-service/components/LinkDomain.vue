<template>
    <v-dialog
        v-model="state.open"
        persistent
        max-width="360"
        @keydown.esc="onClose"
    >
        <v-card>
            <v-overlay
                :value="state.loading"
            >
                <v-progress-circular
                    :size="50"
                    :color="app.color_name"
                    indeterminate
                    class="ma-5"
                />
            </v-overlay>

            <v-toolbar flat color="transparent">
                <v-toolbar-title>
                    SMTP used on website
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-btn icon @click="onClose">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="body-1">
                <div v-if="state.websites">
                    {{ state.websites }}
                </div>

                <div v-else>
                    No website use this service.
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { computed, defineComponent, reactive } from '@vue/composition-api';

import SmtpServiceApi from '../services/SmtpServiceApi';

export default defineComponent({
    setup(props, { root, emit }) {
        const state = reactive({
            loading: false,
            open: false,
            websites: null,
        });

        const app = computed(() => root.$store.getters.app );

        const onGetData = async (id) => {
            state.loading = true;

            try {
                const response = await SmtpServiceApi().websitesApi(id);

                if (response.status === 'success') {
                    state.websites = response.websites;
                }
            } catch (exception) {
                if (exception.message) {
                    emit('onError', exception.message);
                }
            } finally {
                state.loading = false;
            }

        }

        const open = async (identifier = null) => {
            state.open = true;

            await onGetData(identifier);
        };

        const onClose = () => {
            state.open = false;
            state.websites = null;
        };

        return {
            state,
            app,
            open,
            onClose,
        };
    },
});
</script>
