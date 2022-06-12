<template>
    <div style="height: 100%">
        <v-container
            v-if="state.loading"
            fluid
            style="height: 100%"
        >
            <v-layout
                row
                justify-center
                align-center
                fill-height
                class="text-xs-center"
                style="height: 100%"
            >
                <v-progress-circular
                    :size="50"
                    :color="app.color_name"
                    indeterminate
                    class="ma-5"
                />
            </v-layout>
        </v-container>

        <v-card v-else>
            <v-toolbar color="transparent" flat>
                <v-toolbar-title>{{ title }}</v-toolbar-title>
            </v-toolbar>

            <v-card-text>
                <slot></slot>
            </v-card-text>

            <v-card-actions>
                <slot name="actions"></slot>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';

export default defineComponent({
    name: 'merchant-page-layout',
    props: {
        title: {
            type: String,
            default: 'PAGE_TITLE',
        },
    },
    setup(props, { root }) {
        const state = reactive({
            loading: false,

        });

        return {
            state,
        };
    },
});
</script>
