<template>
    <div style="height: 100%">
        <v-container fluid v-if="state.loading" style="height: 100%">
            <v-layout
                align-center
                justify-center
                row
                fill-height
                class="text-center"
                style="height: 100%"
            >
                <v-progress-circular
                    class="ma-5"
                    indeterminate
                    :color="app.color_name"
                    :size="50"
                />
            </v-layout>
        </v-container>

        <v-card
            v-else
            color="transparent"
            flat
        >
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>Legal</v-toolbar-title>

                <v-spacer></v-spacer>

                <template v-slot:extension>
                    <v-flex>
                        <v-tabs
                            color="grey darken-3"
                            :slider-color="app.color_name"
                            v-model="state.selectedTab"
                            show-arrows
                        >
                            <v-tab
                                v-for="tab in state.tabs"
                                :key="`tab-${tab.name}`"
                            >
                                {{ tab.text }}
                            </v-tab>
                        </v-tabs>
                    </v-flex>
                </template>
            </v-toolbar>

            <v-divider class="grey lighten-2"></v-divider>

            <v-form
                ref="form"
                @submit.prevent="onSubmit"
            >
                <v-card-text>
                    <v-alert
                        :value="state.hasError"
                        type="error"
                        class="mb-4"
                    >
                        <span
                            v-if="state.error == 'registration_validation_error'"
                        >
                            {{ $t("server_error") }}
                        </span>

                        <span v-else>{{ $t("correct_errors") }}</span>
                    </v-alert>

                    <v-alert
                        :value="state.hasSuccess"
                        type="success"
                        class="mb-4"
                    >
                        {{ $t("update_success") }}
                    </v-alert>

                    <v-tabs-items
                        class="mx-2"
                        :touchless="false"
                        v-model="state.selectedTab"
                    >
                        <v-tab-item
                            v-for="tab in state.tabs"
                            :key="`tab-${tab.name}`"
                            eager
                        >
                            <p class="mb-1 caption">Content</p>

                            <editor :content.sync="tab.content" />
                        </v-tab-item>
                    </v-tabs-items>
                </v-card-text>

                <v-card-actions class="mx-2">
                    <v-spacer></v-spacer>

                    <v-btn
                        type="submit"
                        class="mb-2"
                        :color="app.color_name"
                        large
                    >
                        {{ $t("update") }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </div>
</template>

<script>
import { computed, defineComponent, onMounted, reactive } from '@vue/composition-api';

import Editor from './components/Editor.vue';

import ServiceLegalApi from './services/LegalApi';

export default defineComponent({
    components: {
        Editor
    },
    setup(props, { root }) {
        const state = reactive({
            loading: false,
            tabs: [
                {
                    name: "privacy_policy",
                    content: "",
                    text: "Privacy Policy"
                },
                {
                    name: "user_agreement",
                    content: "",
                    text: "User Agreement"
                },
                {
                    name: "contact_us",
                    content: "",
                    text: "Contact Us"
                },
                {
                    name: "refund_policy",
                    content: "",
                    text: "Refund Policy"
                }
            ],
            selectedTab: "privacy_policy",
            error: null,
            hasError: false,
            hasSuccess: false,
        });

        const app = computed(() => root.$store.getters.app);

        onMounted(async () => {
            state.loading = true;

            await onLoaded();

            state.loading = false;
        });

        const onLoaded = async () => {
            const response = await ServiceLegalApi().indexApi({
                locale: root.$i18n.locale,
            });

            response.data.forEach(legal => {
                let tab = state.tabs.find(item => item.name === legal.type);

                if (tab) {
                    if (legal.content) {
                        tab.content = legal.content;
                    }
                }
            });
        }

        const onSubmit = async () => {
            state.loading = true;

            try {
                const response = await ServiceLegalApi().updateApi({
                    locale: root.$i18n.locale,
                    content: state.tabs[state.selectedTab].content,
                    type: state.tabs[state.selectedTab].name
                });

                if (response.status === 'success') {
                    state.hasSuccess = true;
                    state.hasError = false;

                    onLoaded();
                }
            } catch (exception) {
                if (exception.status === 'error') {
                    state.hasSuccess = false;
                    state.hasError = true;
                }
            }

            state.loading = false;
        }

        return {
            state,
            app,
            onSubmit,
        };
    },
});
</script>
