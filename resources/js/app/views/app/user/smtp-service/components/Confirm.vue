<template>
    <v-dialog
        v-model="state.open"
        persistent
        :max-width="state.options.width"
        @keydown.esc="onCancel"
        :style="{zIndex: state.options.zIndex}"
    >
        <v-card>
            <v-card-title class="headline" v-show="state.title">
                {{ state.title }}
            </v-card-title>

            <v-card-text class="body-1">
                {{ state.message }}
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    text
                    color="grey darken-2"
                    @click.native="onCancel"
                >
                    Cancel
                </v-btn>

                <v-btn
                    text
                    color="red darken-2"
                    @click.native="onOk"
                >
                    Ok
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';

export default defineComponent({
    setup() {
        const state = reactive({
            open: false,
            resolve: null,
            reject: null,
            message: 'CONFIRM_MESSAGE',
            title: null,
            options: {
                color: 'primary',
                width: 290,
                zIndex: 200,
            },
        });

        const open = (message = 'CONFIRM_MESSAGE', title = null) => {
            state.title = title;
            state.message = message;
            state.open = true;

            return new Promise((resove, reject) => {
                state.resolve = resove;
                state.reject = reject;
            });
        }

        const onOk = () => {
            state.resolve(true);

            state.open = false;
        }

        const onCancel = () => {
            state.resolve(false);

            state.open = false;
        }

        return {
            state,
            open,
            onOk,
            onCancel,
        };
    },
});
</script>
