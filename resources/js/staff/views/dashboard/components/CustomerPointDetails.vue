<template>
    <v-card>
        <v-tabs
            v-model="state.selectedTab"
            slider-color="grey darken-3"
            color="grey darken-3"
        >
            <v-tab href="#points">
                Points
            </v-tab>

            <v-tab href="#history">
                History
            </v-tab>
        </v-tabs>

        <div style="position:absolute; right: 8px; top: 8px;">
            <v-btn icon @click="onClose">
                <v-icon>close</v-icon>
            </v-btn>
        </div>

        <v-divider class="grey lighten-2"></v-divider>

        <v-card-text>
            <v-progress-linear
                v-if="state.loading"
                indeterminate
                color="primary"
                class="ma-5"
            />

            <v-tabs-items
                v-else
                v-model="state.selectedTab"
            >
                <v-tab-item value="points">
                    <div class="mx-2 my-5 display-1">
                        {{ state.customer.points }} Points
                    </div>
                </v-tab-item>

                <v-tab-item value="history">
                    <div class="mx-24 my-5">
                        <v-timeline dense v-if="state.customer.histories.length > 0">
                            <v-timeline-item
                                v-for="(history, index) in state.customer.histories"
                                :key="index"
                                :color="history.color"
                                :icon="history.icon"
                                :small="history.icon_size == 'small'"
                                :large="history.icon_size == 'large'"
                                fill-dot
                            >
                                <strong>
                                    {{ history.points }}
                                    <span v-if="history.points == 1">Point</span>
                                    <span v-else>Points</span>
                                    ,
                                    {{ history.date_received }}
                                </strong>

                                <div
                                    class="caption"
                                    v-if="history.reward_title !== null"
                                >
                                    {{ history.reward_title }}
                                </div>

                                <div class="caption">
                                    {{ history.description }}
                                </div>

                                <div
                                    class="caption"
                                    v-if="history.expiry !== null"
                                >
                                    {{ history.expiry }}
                                </div>
                            </v-timeline-item>
                        </v-timeline>

                        <p v-else>Customer has no points yet.</p>
                    </div>
                </v-tab-item>
            </v-tabs-items>
        </v-card-text>
    </v-card>
</template>

<script>
import { computed, defineComponent, onMounted, reactive, watch } from '@vue/composition-api';
import moment from 'moment';

import ServiceCustomerApi from '../services/CustomerApi';

export default defineComponent({
    props: ['identifier'],
    setup(props, { root, emit }) {
        const state = reactive({
            loading: false,
            dialog: false,
            selectedTab: 'points',
            customer: {
                points: 0,
                histories: [],
            },
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);

        watch(() => props.identifier, async (current, previous) => {
            if (current) {
                await onLoaded(current);
            }
        });

        onMounted(async () => {
            moment().locale(locale.value);

            if (props.identifier) {
                await onLoaded(props.identifier);
            }
        });

        const onLoaded = async (identifier = '') => {
            state.loading = true;

            try {
                const response = await ServiceCustomerApi().show(identifier, {
                    locale: locale.value,
                    uuid: campaign.value.uuid,
                });

                state.customer.points = response.data.points;
                state.customer.histories = response.data.histories;
            } catch (exception) {

            } finally {
                state.loading = false;
            }
        };

        const onClose = () => {
            emit('onClear');
        };

        return {
            state,
            onClose,
        };
    },
});
</script>
<style scoped>
    .text-xl {font-size: 2em;}
</style>
