<template>
    <v-snackbar
        v-model="state.open"
        :color="state.color"
        top
        right
        :timeout="6000"
    >
        {{ state.message }}
    </v-snackbar>
</template>

<script>
/**
 * App Snackbar Component
 *
 * Example:
 *      Html
 *          <app-snackbar ref="snackbar" />
 *      Javascript
 *          this.$refs.snackbar.success('message');
 *          this.$refs.snackbar.error('message');
 */
import { defineComponent, reactive } from '@vue/composition-api';

export default defineComponent({
    setup() {
        const state = reactive({
            open: false,
            color: 'success',
            message: '',
        });

        return {
            state,
        }
    },
    methods: {
        success(message = '') {
            this.state.open = true;
            this.state.color = 'success';
            this.state.message = message;
        },
        error(message = '') {
            this.state.open = true;
            this.state.color = 'error';
            this.state.message = message;
        }
    },
});
</script>
