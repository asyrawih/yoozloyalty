<template>
    <div style="height: 100%">
        <v-container
            v-if="loading"
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
                    color="primary"
                    indeterminate
                    class="ma-5"
                />
            </v-layout>
        </v-container>

        <v-card v-else>
            <v-toolbar
                flat
                color="transparent"
            >
                <v-toolbar-title>{{ pageOptions.title }}</v-toolbar-title>

                <v-spacer></v-spacer>

                <v-text-field
                    v-if="useSearch"
                    id="input-search"
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
                <v-snackbar
                    v-model="snackbarOptions.value"
                    bottom
                    left
                    :color="snackbarOptions.status"
                >
                    {{ snackbarOptions.message }}
                </v-snackbar>

                <slot v-bind:state="state"></slot>
            </v-card-text>

            <v-card-text
                v-if="useCreate"
                style="height: 100px; position: relative"
            >
                <v-fab-transition>
                    <v-btn
                        color="pink"
                        dark
                        absolute
                        top
                        right
                        fab
                        @click="onCreateClick"
                    >
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </v-fab-transition>

                <v-dialog
                    v-model="state.form"
                    persistent
                    :retain-focus="false"
                    :fullscreen="$vuetify.breakpoint.xsOnly"
                    width="480"
                    @keydown.esc="onCloseClick('form')"
                >
                    <slot name="data_form"></slot>
                </v-dialog>

                <v-dialog
                    v-model="state.confirmation"
                    persistent
                    :retain-focus="false"
                    :fullscreen="$vuetify.breakpoint.xsOnly"
                    width="480"
                    @keydown.esc="onCloseClick('confirmation')"
                >
                    <v-card>
                        <v-overlay
                            :value="confirmationOptions.loading"
                        >
                            <v-progress-circular
                                :size="50"
                                color="primary"
                                indeterminate
                                class="ma-5"
                            />
                        </v-overlay>

                        <v-card-title>
                            <span class="text-h5">
                                {{ confirmationOptions.title }}
                            </span>
                        </v-card-title>

                        <v-divider class="grey lighten-2"></v-divider>

                        <v-card-text class="text-center">
                            <br />

                            <strong>
                                {{ confirmationOptions.message }}
                            </strong>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn
                                :color="confirmationOptions.cancelColor"
                                text
                                @click="onCloseClick('confirmation')"
                            >
                                {{ confirmationOptions.cancelText }}
                            </v-btn>

                            <v-btn
                                :color="confirmationOptions.okColor"
                                text
                                @click="onConfirmOKClick"
                            >
                                {{ confirmationOptions.okText }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import { defineComponent, reactive, watch } from '@vue/composition-api';

export default defineComponent({
    name: "page-layout",
    props: {
        loading: {
            type: Boolean,
            default: false,
        },
        useSearch: {
            type: Boolean,
            default: false,
        },
        useCreate: {
            type: Boolean,
            default: false,
        },
        pageOptions: {
            type: Object,
            default: {
                title: 'PAGE_TITLE',
                form: false,
                confirmation: false
            }
        },
        snackbarOptions: {
            type: Object,
            default: {
                value: false,
                status: 'success',
                message: 'SNACKBAR_MESSAGE'
            }
        },
        confirmationOptions: {
            type: Object,
            default: {
                loading: false,
                title: 'Delete Confirmation',
                message: 'Are you sure, you want to delete this data ?',
                cancelText: 'Cancel',
                okText: 'OK',
                cancelColor: 'secondary',
                okColor: 'primary'
            }
        },
    },
    setup(props, context) {
        const state = reactive({
            search: '',
            form: false,
            confirmation: false,
        });

        watch(() => props.pageOptions, (current, old) => {
            state.form = current.form;
            state.confirmation = current.confirmation;
        }, {
            deep: true
        });

        const onCreateClick = () => {
            state.form = true;

            context.emit('onCreate');

            return;
        };

        const onCloseClick = (action) => {
            state[action] = false;

            context.emit('onClose');

            return;
        };

        const onConfirmOKClick = () => context.emit('onConfirmOK');

        return {
            state,
            onCreateClick,
            onCloseClick,
            onConfirmOKClick
        };
    },
})
</script>
