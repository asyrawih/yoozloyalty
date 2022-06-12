<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Notification Template</v-toolbar-title>
            <v-spacer></v-spacer>
            <template v-slot:extension>
                <v-tabs
                    v-model="selected_tab"
                    :slider-color="app.color_name"
                    color="grey darken-3"
                    show-arrows
                >
                    <v-tab :href="'#admin'">
                        Admin
                    </v-tab>
                    <v-tab :href="'#merchant'">
                        Merchant
                    </v-tab>
                    <v-tab :href="'#customer'">
                        Customer
                    </v-tab>
                </v-tabs>
            </template>
        </v-toolbar>
        <v-card-text>
            <v-tabs-items
                v-model="selected_tab"
                :touchless="false"
                class="mx-2"
            >
                <v-tab-item :value="'admin'">
                    <v-tabs
                        v-model="selected_tab_admin"
                        :slider-color="app.color_name"
                        color="grey darken-3"
                        show-arrows
                    >
                        <v-tab :href="'#merchant_registeration'">
                            Merchant Registeration
                        </v-tab>
                    </v-tabs>
                    <v-tabs-items
                        v-model="selected_tab_admin"
                        :touchless="false"
                        class="mx-2"
                    >
                        <v-tab-item :value="'merchant_registeration'">
                            <AdminMerchantRegisteration
                                :uuid="form1.admin_merchant_registeration.uuid"
                                :subject="
                                    form1.admin_merchant_registeration.subject
                                "
                                :template="
                                    form1.admin_merchant_registeration.template
                                "
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-tab-item>
                <v-tab-item :value="'merchant'">
                    <v-tabs
                        v-model="selected_tab_merchant"
                        :slider-color="app.color_name"
                        color="grey darken-3"
                        show-arrows
                    >
                        <v-tab :href="'#customer_registeration'">
                            Customer Registeration
                        </v-tab>
                        <v-tab :href="'#customer_new_credit_request'">
                            New Credit Request
                        </v-tab>
                        <v-tab :href="'#customer_new_reward_redeem'">
                            New Reward Redeemed
                        </v-tab>
                    </v-tabs>
                    <v-tabs-items
                        v-model="selected_tab_merchant"
                        :touchless="false"
                        class="mx-2"
                    >
                        <v-tab-item :value="'customer_registeration'">
                            <MerchantCustomerRegisteration
                                :uuid="
                                    form1.merchant_customer_registeration.uuid
                                "
                                :subject="
                                    form1.merchant_customer_registeration
                                        .subject
                                "
                                :template="
                                    form1.merchant_customer_registeration
                                        .template
                                "
                            />
                        </v-tab-item>
                        <v-tab-item :value="'customer_new_credit_request'">
                            <MerchantCustomerNewCreditRequest
                                :uuid="
                                    form1.merchant_customer_new_credit_request
                                        .uuid
                                "
                                :subject="
                                    form1.merchant_customer_new_credit_request
                                        .subject
                                "
                                :template="
                                    form1.merchant_customer_new_credit_request
                                        .template
                                "
                            />
                        </v-tab-item>
                        <v-tab-item :value="'customer_new_reward_redeem'">
                            <MerchantCustomerNewRewardRedeem
                                :uuid="
                                    form1.merchant_customer_new_reward_redeem
                                        .uuid
                                "
                                :subject="
                                    form1.merchant_customer_new_reward_redeem
                                        .subject
                                "
                                :template="
                                    form1.merchant_customer_new_reward_redeem
                                        .template
                                "
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-tab-item>
                <v-tab-item :value="'customer'">
                    <v-tabs
                        v-model="selected_tab_customer"
                        :slider-color="app.color_name"
                        color="grey darken-3"
                        show-arrows
                    >
                        <v-tab :href="'#point_credited'">
                            Point Credited
                        </v-tab>
                        <v-tab :href="'#point_redeemed'">
                            Point Redeemed
                        </v-tab>
                        <v-tab :href="'#welcome_message'">
                            Welcome message
                        </v-tab>
                    </v-tabs>
                    <v-tabs-items
                        v-model="selected_tab_customer"
                        :touchless="false"
                        class="mx-2"
                    >
                        <v-tab-item :value="'point_credited'">
                            <CustomerPointCredited
                                :uuid="form1.customer_point_credited.uuid"
                                :subject="form1.customer_point_credited.subject"
                                :template="
                                    form1.customer_point_credited.template
                                "
                            />
                        </v-tab-item>
                        <v-tab-item :value="'point_redeemed'">
                            <CustomerPointRedeemed
                                :uuid="form1.customer_point_redeemed.uuid"
                                :subject="form1.customer_point_redeemed.subject"
                                :template="
                                    form1.customer_point_redeemed.template
                                "
                            />
                        </v-tab-item>
                        <v-tab-item :value="'welcome_message'">
                            <CustomerWelcomeMessage
                                :uuid="form1.customer_welcome_message.uuid"
                                :subject="
                                    form1.customer_welcome_message.subject
                                "
                                :template="
                                    form1.customer_welcome_message.template
                                "
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-tab-item>
            </v-tabs-items>
        </v-card-text>
    </v-card>
</template>

<script>
import AdminMerchantRegisteration from "./admin/merchant-registeration";
import MerchantCustomerRegisteration from "./merchant/customer-registeration";
import MerchantCustomerNewCreditRequest from "./merchant/customer-new-credit-request";
import MerchantCustomerNewRewardRedeem from "./merchant/customer-new-reward-redeem";
import CustomerPointCredited from "./customer/point-credited";
import CustomerPointRedeemed from "./customer/point-redeemed";
import CustomerWelcomeMessage from "./customer/welcome-message";

export default {
    components: {
        AdminMerchantRegisteration,
        MerchantCustomerRegisteration,
        MerchantCustomerNewCreditRequest,
        MerchantCustomerNewRewardRedeem,
        CustomerPointCredited,
        CustomerPointRedeemed,
        CustomerWelcomeMessage
    },
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {
            show_current_password: false,
            selected_tab: "admin",
            selected_tab_admin: "merchant_registeration",
            selected_tab_merchant: "customer_registeration",
            selected_tab_customer: "point_credited",
            focus_element: "",
            form1: {
                loading: false,
                current_password: "",
                admin_merchant_registeration: {
                    uuid: "",
                    subject: "",
                    template: ""
                },
                merchant_customer_registeration: {
                    uuid: "",
                    subject: "",
                    template: ""
                },
                merchant_customer_new_credit_request: {
                    uuid: "",
                    subject: "",
                    template: ""
                },
                merchant_customer_new_reward_redeem: {
                    uuid: "",
                    subject: "",
                    template: ""
                },
                customer_point_credited: {
                    uuid: "",
                    subject: "",
                    template: ""
                },
                customer_point_redeemed: {
                    uuid: "",
                    subject: "",
                    template: ""
                },
                customer_welcome_message: {
                    uuid: "",
                    subject: "",
                    template: ""
                },
                variable: [],
                has_error: false,
                error: null,
                errors: {},
                success: false
            }
        };
    },
    created() {
        axios.get("/admin/notif-template").then(response => {
            // Admin
            this.form1.admin_merchant_registeration =
                response.data.data.admin_merchant_registeration;

            // Merchant
            this.form1.merchant_customer_registeration =
                response.data.data.merchant_customer_registeration;
            this.form1.merchant_customer_new_credit_request =
                response.data.data.merchant_customer_new_credit_request;
            this.form1.merchant_customer_new_reward_redeem =
                response.data.data.merchant_customer_new_reward_redeem;

            // Customer
            this.form1.customer_point_credited =
                response.data.data.customer_point_credited;
            this.form1.customer_point_redeemed =
                response.data.data.customer_point_redeemed;
            this.form1.customer_welcome_message =
                response.data.data.customer_welcome_message;
            this.form1.loading = false;
        });
    },
    methods: {},
    computed: {
        app() {
            return this.$store.getters.app;
        },
        _() {
            return _;
        }
    }
};
</script>
<style scoped></style>
