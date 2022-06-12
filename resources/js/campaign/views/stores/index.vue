<template>
    <div class="pb-5">
        <v-container grid-list-lg fluid class="pa-0">
            <v-layout row wrap>
                <v-flex class="py-0" xs12
                        :style="{'background-color': campaign.theme.secondaryColor, 'color': campaign.theme.secondaryTextColor}">
                    <v-img
                        :src="campaign.rewards.headerImg || ''"
                        :height="campaign.rewards.headerHeight"
                    >
                        <v-overlay
                            absolute
                            :color="campaign.theme.secondaryColor"
                            :opacity="campaign.rewards.headerOpacity"
                            z-index="1"
                        >
                        </v-overlay>
                        <v-container fill-height>
                            <v-layout row wrap align-center>
                                <v-flex xs12 style="z-index: 2">
                                    <h1 class="display-1 mb-2" v-html="campaign.home.storesTitle"></h1>
                                    <div v-html="campaign.home.storesContent"></div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                        <template v-slot:placeholder>
                            <v-layout
                                fill-height
                                align-center
                                justify-center
                                ma-0
                                v-if="campaign.rewards.headerImg"
                            >
                                <v-progress-circular indeterminate
                                                     :style="{'color': campaign.theme.secondaryTextColor}"></v-progress-circular>
                            </v-layout>
                        </template>
                    </v-img>
                </v-flex>

                <v-flex xs12>
                    <v-container grid-list-xl>
                        <v-layout row wrap>
                            <v-flex xs12 sm6 lg4 xl3 v-for="(store, store_index) in stores"
                                    :key="'stores_' + store_index" class="mt-2">
                                <v-card style="height: 100%">
                                    <v-img v-if="store.main_image !== null"
                                           :src="store.main_image"
                                           :key="'card_store_' + store_index + '_picture'"
                                           :aspect-ratio="campaign.rewards.imageRatio"
                                    >
                                        <template v-slot:placeholder>
                                            <v-layout
                                                fill-height
                                                align-center
                                                justify-center
                                                ma-0
                                            >
                                                <v-progress-circular indeterminate
                                                                     :style="{'color': campaign.theme.textColor}"></v-progress-circular>
                                            </v-layout>
                                        </template>
                                    </v-img>
                                    <v-card-title primary-title class="pt-4">
                                        <div class="title mb-2">{{ store.name }}</div>
                                    </v-card-title>
                                    <v-card-text class="pt-0 mt-0 pb-4">
                                        <div v-html="store.short_description" class="body-1"></div>
                                        <div class="mt-1 text-subtitle-2">{{ store.street1 }}</div>
                                        <div class="text-subtitle-2">{{ store.street2 }}</div>
                                        <div class="text-subtitle-2">{{ `${[store.city, store.state, store.postal_code].filter(Boolean).join(', ')}` }}</div>
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
export default {
    name: "index",
    data() {
        return {
            stores: []
        }
    },
    mounted() {
        axios
            .get('/campaign/stores', {params: {locale: this.$i18n.locale, uuid: this.$store.state.app.campaign.uuid}})
            .then(response => {
                this.stores = response.data.data
            })
    },
    computed: {
        campaign() {
            return this.$store.state.app.campaign
        },
    }
}
</script>

<style scoped>

</style>
