<template>
    <div style="height: 100%">
        <v-container
            v-if="loading"
            fluid
            style="height: 100%"
        >
            <v-layout
                class="text-xs-center"
                style="height: 100%"
                row
                justify-center
                align-center
                fill-height
            >
                <v-progress-circular
                    class="ma-5"
                    :size="50"
                    :color="app.color_name"
                    indeterminate
                />
            </v-layout>
        </v-container>

        <v-card v-else>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>{{ pageOptions.title }}</v-toolbar-title>

                <v-spacer></v-spacer>

                <v-text-field
                    v-show="useSearch"
                    id="frmSearch"
                    v-model="state.search"
                    label="Search"
                    append-icon="search"
                    single-line
                    clearable
                    flat
                    solo-inverted
                    hide-details
                    :style="{
                        'max-width': $vuetify.breakpoint.xs ? '135px' : '320px'
                    }"
                />
            </v-toolbar>

            <v-divider></v-divider>

            <v-card-text class="my-7">
                <slot
                    v-bind:options="{
                        search: state.search
                    }"
                >
                </slot>
            </v-card-text>

            <v-card-text style="height: 100px; position: relative">
                <slot name="data_form"></slot>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import { computed, defineComponent, reactive } from '@vue/composition-api';

export default defineComponent({
    name: 'app-page-layout',
    props: {
        loading: {
            type: Boolean,
            default: false,
        },
        useSearch: {
            type: Boolean,
            default: false,
        },
        pageOptions: {
            type: Object,
            default: {
                title: 'PAGE_TITLE'
            },
        },
    },
    setup(_, { root }) {
        const app = computed(() => root.$store.getters.app);

        const state = reactive({
            search: '',
        });

        return {
            app,
            state,
        };
    },
});
</script>
