<template>
    <v-dialog
        v-model="state.open"
        persistent
        width="360"
        :style="{ zIndex: 200 }"
        @keydown.esc="onCancel"
    >
        <v-card>
            <v-card-title
                v-show="state.title"
                class="headline"
            >
                {{ state.title }}
            </v-card-title>

            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text class="text-center body-1 mt-5">
                {{ state.message }}
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    :color="state.options.okColor"
                    text
                    @click.native="onOk"
                >
                    {{ state.options.okText }}
                </v-btn>

                <v-btn
                    :color="state.options.cancelColor"
                    text
                    @click.native="onCancel"
                >
                    {{ state.options.cancelText }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
/**
 * App Confirm Dialog Component
 *
 * Example:
 *      Html
 *          <app-confirm ref="confirm" />
 *      Javascript
 *          this.$refs.confirm.open('message', 'title', options).then((confirm) => {});
 *      Options
 *          cancelText: 'Cancel',
 *          okText: 'OK',
 *          cancelColor: 'secondary',
 *          okColor: 'primary'
 */
import { computed, defineComponent, reactive } from '@vue/composition-api';

export default defineComponent({
    name: 'app-confirm',
    setup(_, { root }) {
        const app = computed(() => root.$store.getters.app);

        const state = reactive({
            open: false,
            title: null,
            message: '',
            options: {
                cancelText: 'Cancel',
                okText: 'OK',
                cancelColor: 'secondary',
                okColor: 'primary'
            },
            resolve: null,
            reject: null,
        });

        const open = (message = '', title = null, options = {}) => {
            state.open = true;
            state.message = message;
            state.title = title;
            state.options = Object.assign(state.options, options);

            return new Promise((resolve, reject) => {
                state.resolve = resolve;
                state.reject = reject;
            });
        };

        const onOk = () => {
            state.resolve(true);

            state.open = false;
        };

        const onCancel = () => {
            state.resolve(false);

            state.open = false;
        };

        return {
            app,
            state,
            open,
            onOk,
            onCancel
        };
    },
});
</script>
