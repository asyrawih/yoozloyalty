<template>
	<v-container fluid fill-height class="px-0 pt-0 pb-5 mb-5">
		<v-layout align-center justify-center row fill-height wrap>
			<v-flex xs11 class="mt-5">
				<v-card class="elevation-18">
					<v-toolbar flat color="transparent">
						<v-toolbar-title></v-toolbar-title>

						<v-spacer></v-spacer>

						<v-text-field
                            v-if="state.selectedTab === 'customers'"
							v-model="state.search"
							append-icon="search"
							label="Search customer number"
							single-line
							clearable
							flat
							solo-inverted
							hide-details
							:style="{
								'max-width': $vuetify.breakpoint.xs ? '135px' : '320px'
							}"
							placeholder="Search customer number"
						/>
					</v-toolbar>

					<v-tabs
						v-model="state.selectedTab"
						slider-color="grey darken-3"
						color="grey darken-3"
					>
						<v-tab href="#customers">
							Customers List
						</v-tab>

						<v-tab href="#history">
							Recent activity
						</v-tab>
					</v-tabs>

					<v-divider class="grey lighten-2"></v-divider>

					<v-tabs-items v-model="state.selectedTab">
						<v-tab-item value="history">
							<v-card-text class="px-0">
								<v-progress-linear
                                    v-if="state.loading"
									indeterminate
									color="primary"
								/>

								<p
                                    v-else-if="! state.loading && ! state.history.items.length"
                                    class="title ma-2"
                                >
                                    There is no activities for this campaign yet.
                                </p>

                                <v-data-table
                                    :headers="state.history.headers"
                                    :items="state.history.items"
                                    :options.sync="state.history.options"
                                    :items-per-page="5"
                                    :loading="state.history.loading"
                                    :server-items-length="state.history.total"
                                    :footer-props="state.history.footerProps"
                                    no-data-text="There is no activities for this campaign yet."
                                >
                                    <template slot="item.segment_details" slot-scope="props">
										<v-chip
											v-for="(segment, index) in props.item.segment_details"
											:key="index"
											small
											class="mr-2 mt-2"
										>
                                            {{ segment.name }}
                                        </v-chip>
									</template>

                                    <template slot="item.created_at" slot-scope="props">
                                        {{ props.item.created_at | formatDate }}
                                    </template>
                                </v-data-table>
							</v-card-text>
						</v-tab-item>

						<v-tab-item value="customers">
							<v-card-text class="px-0">
								<p
                                    v-if="! state.loading && ! state.customer.items.length"
                                    class="title ma-2"
                                >
                                    There is no customers for this campaign yet.
                                </p>

                                <v-data-table
                                    :headers="state.customer.headers"
                                    :items="state.customer.items"
                                    :options.sync="state.customer.options"
                                    :items-per-page="5"
                                    :loading="state.customer.loading"
                                    :server-items-length="state.customer.total"
                                    :footer-props="state.customer.footerProps"
                                    no-data-text="There is no customers for this campaign yet."
                                >
                                    <template slot="item.last_login" slot-scope="props">
                                        {{ props.item.last_login | formatDate }}
                                    </template>

                                    <template slot="item.uuid" slot-scope="props">
                                        <v-btn
                                            class="mr-0 mt-0 mb-0"
                                            dark
                                            color="secondary"
                                            icon
                                            small
                                            title="View customer points"
                                            @click="
                                                state.dialog.points = true;
                                                state.identifier = props.item.uuid;
                                            "
                                        >
                                            <v-icon small>info</v-icon>
                                        </v-btn>
                                    </template>
                                </v-data-table>
							</v-card-text>

                            <v-card-text
                                class="mt-5"
                                style="height: 100px; position: relative"
                            >
                                <customer-data-form
                                    @onSuccess="onSuccess"
                                    @onError="onError"
                                />
                            </v-card-text>
						</v-tab-item>
					</v-tabs-items>
				</v-card>

				<v-dialog
					persistent
					:retain-focus="false"
					:width="480"
					:fullscreen="$vuetify.breakpoint.xsOnly"
					v-model="state.dialog.points"
					@keydown.esc="
                        state.dialog.points = false;
                        state.identifier = null;
                    "
				>
                    <customer-point-details
                        :identifier="state.identifier"
                        @onClear="onClear"
                    />
				</v-dialog>

                <v-snackbar
                    v-model="snackbarOptions.value"
                    top
                    right
                    :color="snackbarOptions.status"
                >
                    {{ snackbarOptions.message }}
                </v-snackbar>
			</v-flex>
		</v-layout>
	</v-container>
</template>


<script>
import { computed, defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import CustomerPointDetails from './components/CustomerPointDetails.vue';
import CustomerDataForm from './components/CustomerDataForm.vue';

import ServiceCustomerApi from './services/CustomerApi';
import ServiceHistoryApi from './services/HistoryApi';

export default defineComponent({
    components: {
        CustomerPointDetails,
        CustomerDataForm,
    },
    setup(_, { root }) {
        const state = reactive({
            loading: true,
            selectedTab: 'customers',
            search: '',
            debounce: null,
            dialog: {
                points: false,
            },
            identifier: null,
            customer: {
                headers: [
                    { text: "Customer Name", value: "name", align: "left" },
                    { text: "Email", value: "email", align: "left" },
                    { text: "Customer Number", value: "number", align: "left" },
                    { text: "Customer Card Number", value: "card", align: "left" },
                    { text: "Last Login", value: "last_login", align: "center" },
                    { text: "Action", value: "uuid", align: "center" },
                ],
                items: [],
                options: {},
                loading: false,
                total: 0,
                footerProps: {
                    "items-per-page-options": [5, 10, 25, 50, 75, 100],
                },
                reload: false,
            },
            history: {
                headers: [
                    { text: "Customer Name", value: "customer_details.name", align: "center" },
                    { text: "Customer Number", value: "customer_details.number", align: "center" },
                    { text: "Reward Offer", value: "reward_title", align: "center" },
                    { text: "Redemption Type", value: "description", align: "center" },
                    { text: "Segment", value: "segment_details", align: "center" },
                    { text: "Points", value: "points", align: "center" },
                    { text: "Timestamp", value: "created_at", align: "center" },
                    { text: "Staff Name", value: "staff_name", align: "center" },
                ],
                items: [],
                options: {},
                loading: false,
                total: 0,
                footerProps: {
                    "items-per-page-options": [5, 10, 25, 50, 75, 100],
                },
                reload: false,
            },
        });

        const snackbarOptions = reactive({
            value: false,
            status: 'success',
            message: 'SNACKBAR_MESSAGE'
        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);

        watch(() => state.customer.options, async (current, previous) => {
            const { page, itemsPerPage } = state.customer.options;

            await onCustomerLoaded(page, itemsPerPage, state.search);
        }, { deep: true });

        watch(() => state.history.options, async (current, previous) => {
            const { page, itemsPerPage } = state.history.options;;

            await onHistoryLoaded(page, itemsPerPage, state.search);
        }, { deep: true });

        watch(() => state.customer.reload, async (current, previous) => {
            if (current) {
                const { page, itemsPerPage } = state.customer.options;

                await onCustomerLoaded(page, itemsPerPage, state.search);
            }
        });

        watch(() => state.search, (current, previous) => {
            if (current === '' || ! current || current.length >= 3) {
                clearTimeout(state.debounce);

                state.debounce = setTimeout(async () => {
                    await onCustomerLoaded(1, 5, state.search);
                }, 600);
            }
        });

        onMounted(async () => {
            const customerOptions = state.customer.options;
            const historyOptions = state.history.options;

            await Promise.all([
                onCustomerLoaded(customerOptions.page, customerOptions.itemsPerPage, state.search),
                onHistoryLoaded(historyOptions.page, historyOptions.itemsPerPage),
            ]);

            state.loading = false;
        });

        const onCustomerLoaded = async (page = 1, perPage = 5, search = '') => {
            state.customer.loading = true;

            try {
                const response = await ServiceCustomerApi().index({
                    locale: locale.value,
                    uuid: campaign.value.uuid,
                    page,
                    perPage,
                    search
                });

                state.customer.items = response.data;
                state.customer.total = response.meta.total;
            } catch (exception) {

            } finally {
                snackbarOptions.value = false;
                snackbarOptions.status = 'success';
                snackbarOptions.message = 'SNACKBAR_MESSAGE';

                state.customer.reload = false;
                state.customer.loading = false;
            }
        };

        const onHistoryLoaded = async (page = 1, perPage = 5) => {
            state.history.loading = true;

            try {
                const response = await ServiceHistoryApi().index({
                    locale: locale.value,
                    uuid: campaign.value.uuid,
                    page,
                    perPage,
                });

                state.history.items = response.data;
                state.history.total = response.meta.total;
            } catch (exception) {

            } finally {
                state.history.loading = false;
            }
        }

        const onClear = () => {
            state.dialog.points = false;
            state.identifier = null;
        }

        const onSuccess = (message) => {
            state.customer.reload = true;
            snackbarOptions.message = message;
            snackbarOptions.value = true;
        }

        const onError = (message) => {
            snackbarOptions.status = 'error';
            snackbarOptions.message = message;
            snackbarOptions.value = true;
        }

        return {
            state,
            snackbarOptions,
            campaign,
            onClear,
            onSuccess,
            onError,
        }
    },
    filters: {
        formatDate: (value = '') => {
            if (value) {
                return moment(value).format("lll");
            }

            return '';
        },
    },
});
// import _ from "lodash";

// export default {
// 	data () {
// 		return {
//             descending: false,
// 			locale: 'en',
// 			loading: true,
// 			search: "",
// 			selectedTab: null,
//             loaded: false,
// 			history: [],
// 			customers: [],
// 			headers: [
// 				{ text: "Customer Name", value: "customer_details.name", align: "center" },
// 				{ text: "Customer Number", value: "customer_details.number", align: "center" },
// 				{ text: "Reward Offer", value: "reward_title", align: "center" },
// 				{ text: "Redemption Type", value: "description", align: "center" },
// 				{ text: "Segment", value: "segment_details", align: "center" },
// 				{ text: "Points", value: "points", align: "center" },
// 				{ text: "Timestamp", value: "created_at", align: "center" },
// 				{ text: "Staff Name", value: "staff_name", align: "center" },
// 			],
// 			customersListHeaders: [
// 				{ text: "Customer Name", value: "name", align: "left" },
// 				{ text: "Email", value: "email", align: "left" },
// 				{ text: "Customer Number", value: "number", align: "left" },
// 				{ text: "Customer Card Number", value: "card", align: "left" },
// 				{ text: "Last Login", value: "last_login", align: "center" },
// 				{ text: "Action", value: "uuid", align: "center" }
// 			],
// 			settings: {
// 				create: true,
// 				import: false
// 			},
// 			dataForm: false,
// 			importForm: false,
// 			dataDetail: false,
// 			customerUuid: "",
// 			creditRequestStatus: false,
// 			model: 'App\Customer',
//             dataListEvents: null,
//             itemsPerPageOptions: [10, 25, 50, 75, 100],
//             watchPagination: false,
//             options: {
//                 page: 1,
//                 itemsPerPage: 10,
//                 filters: []
//             },
//             optionsOld: []
// 		}
// 	},
// 	methods: {
// 		getDate(date) {
// 			return moment(date).format("lll")
// 		},
// 		getDateFrom(date) {
// 			return moment(date).from()
// 		},
//         searchData: _.debounce(function(string) {
//             this.loading = true;
//             this.reloadData();
//         }, 400),
//         getDataFromApi() {
//             this.loading = true;
// 			this.locale = this.$auth.user().locale
// 			moment.locale(this.locale)

// 			axios
// 			.get('/staff/history', { params: { locale: this.$i18n.locale, uuid: this.campaign.uuid }})
// 			.then(response => {
// 				this.history = response.data
// 				this.loading = false
// 				this.loaded = true
//             	this.watchPagination = true;
// 			})

// 			axios
// 			.get('/staff/customers', {
// 				params: {
// 					uuid: this.campaign.uuid,
// 					locale: this.$i18n.locale,
// 					model: this.model,
// 					search: this.search,
// 					page: this.options.page,
// 					itemsPerPage: this.options.itemsPerPage,
// 					sortBy: this.options.sortBy,
// 					descending: this.descending,
// 					filters: this.options.filters
// 				}
// 			})
// 			.then(response => {
// 				this.customers = response.data
// 				this.loading = false
// 				this.loaded = true
//             	this.watchPagination = true;
// 			})
//         },
//         reloadData() {
//             this.watchPagination = false;
//             this.loading = true;
//             this.getDataFromApi();
//         },
//         executeAction(action, uuid) {
//             if (action == "detail") {
//                 this.customerUuid = uuid;
//                 this.dataDetail = true;
//             }
//         },
// 	},
//     watch: {
//         // options: {
//         //     handler(val, old) {
//         //         if (this.watchPagination) {
//         //             let string_val = String(JSON.stringify(val));
//         //             let string_old = String(this.optionsOld);

//         //             if (string_val !== string_old) {
//         //                 this.reloadData();
//         //             }

//         //             this.optionsOld = string_old;
//         //         }
//         //     },
//         //     deep: true
//         // },
//         search() {
//             this.searchData();
//         },
//         // selected() {
//         //     this.disableDeleteSelected =
//         //         this.selected.length > 0 ? false : true;
//         // },
//         dataListEvents() {
//             if (this.dataListEvents.closeDialog) {
//                 this.dataForm = false;
//                 this.importForm = false;
//                 this.dataDetail = false;
//                 this.creditRequestStatus = false;
//             }
//             if (this.dataListEvents.reload) {
//                 this.reloadData();
//             }
//         }
//     },
// 	mounted () {
// 		this.locale = this.$auth.user().locale
// 		moment.locale(this.locale)

// 		this.getDataFromApi()
// 		// axios
// 		// .get('/staff/history', { params: { locale: this.$i18n.locale, uuid: this.campaign.uuid }})
// 		// .then(response => {
// 		// 	this.history = response.data
// 		// 	this.loading = false
// 		// 	that.loaded = true;
// 		// })

// 		// axios
// 		// .get('/staff/customers', { params: { locale: this.$i18n.locale, uuid: this.campaign.uuid }})
// 		// .then(response => {
// 		// 	this.customers = response.data
// 		// 	this.loading = false
// 		// 	that.loaded = true;
// 		// })
// 	},
// 	computed: {
// 		campaign () {
// 			return this.$store.state.app.campaign
// 		}
// 	}
// }
</script>
<style scoped>
	.fab-container{
		float:right;
		margin-top:24px;
		margin-right:24px;
	}
</style>
