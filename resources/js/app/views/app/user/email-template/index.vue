<template>
    <v-card>
        <v-toolbar tabs flat>
            <v-toolbar-title>Email Template</v-toolbar-title>
            <v-spacer></v-spacer>
            <template v-slot:extension>
                <v-tabs
                    v-model="selected_tab"
                    :slider-color="app.color_name"
                    color="grey darken-3"
                    show-arrows
                >
                    <v-tab :href="'#customer'">
                        Customer
                    </v-tab>
                    <v-tab :href="'#staff'">
                        Staff
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
                <v-tab-item :value="'customer'">
                    <v-tabs
                        v-model="selected_tab_customer"
                        :slider-color="app.color_name"
                        color="grey darken-3"
                        show-arrows
                    >
                        <v-tab :href="'#customer_registeration'">
                            Registeration
                        </v-tab>
                        <v-tab :href="'#customer_forgot_password'">
                            Forgot password
                        </v-tab>
                        <v-tab :href="'#credit_point'">
                            Credit point
                        </v-tab>
                        <v-tab :href="'#redeem_point'">
                            Redeem point
                        </v-tab>
                        <v-tab :href="'#otp_confirmation'">
                            OTP confirmation
                        </v-tab>
                        <v-tab :href="'#coupun_delivery'">
                            Coupon Delivery
                        </v-tab>
                    </v-tabs>
                    <v-tabs-items
                        v-model="selected_tab_customer"
                        :touchless="false"
                        class="mx-2"
                    >
                        <v-tab-item :value="'customer_registeration'">
                            <CustomerRegisteration
                                :uuid="
                                    form1.uuid_subject_customer_registeration
                                "
                                :subject="form1.subject_customer_registeration"
                                :template="
                                    form1.template_customer_registeration
                                "
                            />
                        </v-tab-item>
                        <v-tab-item :value="'customer_forgot_password'">
                            <CustomerForgotPassword
                                :uuid="
                                    form1.uuid_subject_customer_forgot_password
                                "
                                :subject="
                                    form1.subject_customer_forgot_password
                                "
                                :template="
                                    form1.template_customer_forgot_password
                                "
                            />
                        </v-tab-item>
                        <v-tab-item :value="'credit_point'">
                            <CustomerCreditPoint
                                :uuid="form1.uuid_subject_customer_credit_point"
                                :subject="form1.subject_customer_credit_point"
                                :template="form1.template_customer_credit_point"
                            />
                        </v-tab-item>
                        <v-tab-item :value="'redeem_point'">
                            <CustomerRedeemPoint
                                :uuid="form1.uuid_subject_customer_redeem_point"
                                :subject="form1.subject_customer_redeem_point"
                                :template="form1.template_customer_redeem_point"
                            />
                        </v-tab-item>
                        <v-tab-item :value="'otp_confirmation'">
                            <OtpCode
                                :uuid="form1.uuid_subject_otp_confirmation"
                                :subject="form1.subject_otp_confirmation"
                                :template="form1.template_otp_confirmation"
                            />
                        </v-tab-item>
                        <v-tab-item :value="'coupun_delivery'">
                            <CustomerCoupunDelivery
                                :uuid="form1.uuid_customer_coupun_delivery"
                                :subject="
                                    form1.subject_customer_coupun_delivery
                                "
                                :template="
                                    form1.template_customer_coupun_delivery
                                "
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-tab-item>
                <v-tab-item :value="'staff'">
                    <v-tabs
                        v-model="selected_tab_staff"
                        :slider-color="app.color_name"
                        color="grey darken-3"
                        show-arrows
                    >
                        <v-tab :href="'#staff_registeration'">
                            Registeration
                        </v-tab>
                        <v-tab :href="'#staff_forgot_password'">
                            Forgot password
                        </v-tab>
                    </v-tabs>
                    <v-tabs-items
                        v-model="selected_tab_staff"
                        :touchless="false"
                        class="mx-2"
                    >
                        <v-tab-item :value="'staff_registeration'">
                            <StaffRegisteration
                                :uuid="form1.uuid_subject_staff_registeration"
                                :subject="form1.subject_staff_registeration"
                                :template="form1.template_staff_registeration"
                            />
                        </v-tab-item>
                        <v-tab-item :value="'staff_forgot_password'">
                            <StaffForgotPassword
                                :uuid="form1.uuid_subject_staff_forgot_password"
                                :subject="form1.subject_staff_forgot_password"
                                :template="form1.template_staff_forgot_password"
                            />
                        </v-tab-item>
                    </v-tabs-items>
                </v-tab-item>
            </v-tabs-items>
        </v-card-text>
    </v-card>
</template>

<script>
import CustomerRegisteration from "./customer/registeration";
import CustomerForgotPassword from "./customer/forgot-password";
import CustomerCreditPoint from "./customer/creditpoint";
import CustomerRedeemPoint from "./customer/redeempoint";
import OtpCode from "./customer/otp-code";
import CustomerCoupunDelivery from "./customer/coupun-delivery";
import StaffRegisteration from "./staff/registeration";
import StaffForgotPassword from "./staff/forgot-password";

export default {
    components: {
        OtpCode,
        CustomerRegisteration,
        CustomerForgotPassword,
        CustomerCreditPoint,
        CustomerRedeemPoint,
        CustomerCoupunDelivery,
        StaffRegisteration,
        StaffForgotPassword
    },
    $_veeValidate: {
        validator: "new"
    },
    data() {
        return {
            show_current_password: false,
            selected_tab: "customer",
            selected_tab_customer: "customer_registeration",
            selected_tab_staff: "staff_registeration",
            focus_element: "",
            form1: {
                loading: false,
                current_password: "",
                // Customer registeration
                uuid_customer_registeration: "",
                subject_customer_registeration: "",
                template_customer_registeration: "",

                // Customer forgot password
                uuid_subject_customer_forgot_password: "",
                subject_customer_forgot_password: "",
                template_customer_forgot_password: "",

                // Customer credit point
                uuid_subject_customer_credit_point: "",
                subject_customer_credit_point: "",
                template_customer_credit_point: "",

                // Customer redeem point
                uuid_subject_customer_redeem_point: "",
                subject_customer_redeem_point: "",
                template_customer_redeem_point: "",

                // Customer OTP confirmation
                uuid_subject_otp_confirmation: "",
                subject_otp_confirmation: "",
                template_otp_confirmation: "",

                // Customer Coupun Delivery
                uuid_customer_coupun_delivery: "",
                subject_customer_coupun_delivery: "",
                template_customer_coupun_delivery: "",

                // Staff forgot password
                uuid_subject_staff_forgot_password: "",
                subject_staff_forgot_password: "",
                template_staff_forgot_password: "",

                // Staff registeration
                uuid_subject_staff_registeration: "",
                subject_staff_registeration: "",
                template_staff_registeration: "",
                variable: [],
                has_error: false,
                error: null,
                errors: {},
                success: false
            }
        };
    },
    created() {
        axios.get("/user/setting/email-template").then(response => {
            // Customer forgot password
            this.form1.uuid_subject_customer_forgot_password =
                response.data.data.customer_forgot_password.uuid;
            this.form1.subject_customer_forgot_password =
                response.data.data.customer_forgot_password.subject;
            this.form1.template_customer_forgot_password =
                response.data.data.customer_forgot_password.template;

            // Customer registeration
            this.form1.uuid_subject_customer_registeration =
                response.data.data.customer_registeration.uuid;
            this.form1.subject_customer_registeration =
                response.data.data.customer_registeration.subject;
            this.form1.template_customer_registeration =
                response.data.data.customer_registeration.template;

            // Customer credit point
            this.form1.uuid_subject_customer_credit_point =
                response.data.data.customer_credit_point.uuid;
            this.form1.subject_customer_credit_point =
                response.data.data.customer_credit_point.subject;
            this.form1.template_customer_credit_point =
                response.data.data.customer_credit_point.template;

            // Customer redeem point
            this.form1.uuid_subject_customer_redeem_point =
                response.data.data.customer_redeem_point.uuid;
            this.form1.subject_customer_redeem_point =
                response.data.data.customer_redeem_point.subject;
            this.form1.template_customer_redeem_point =
                response.data.data.customer_redeem_point.template;

            // Customer otp confirmation
            this.form1.uuid_subject_otp_confirmation =
                response.data.data.otp_confirmation.uuid;
            this.form1.subject_otp_confirmation =
                response.data.data.otp_confirmation.subject;
            this.form1.template_otp_confirmation =
                response.data.data.otp_confirmation.template;

            // Customer Coupun Delivery
            this.form1.uuid_customer_coupun_delivery =
                response.data.data.customer_coupun_delivery.uuid;
            this.form1.subject_customer_coupun_delivery =
                response.data.data.customer_coupun_delivery.subject;
            this.form1.template_customer_coupun_delivery =
                response.data.data.customer_coupun_delivery.template;

            // Staff forgot password
            this.form1.uuid_subject_staff_forgot_password =
                response.data.data.staff_forgot_password.uuid;
            this.form1.subject_staff_forgot_password =
                response.data.data.staff_forgot_password.subject;
            this.form1.template_staff_forgot_password =
                response.data.data.staff_forgot_password.template;

            // Staff registeration
            this.form1.uuid_subject_staff_registeration =
                response.data.data.staff_registeration.uuid;
            this.form1.subject_staff_registeration =
                response.data.data.staff_registeration.subject;
            this.form1.template_staff_registeration =
                response.data.data.staff_registeration.template;
            this.form1.loading = false;
        });
    },
    methods: {
        variableSet(value) {
            const focus_element = this.focus_element;
            const inputField = this.$refs[focus_element];
            if (inputField) {
                inputField.focus();
                const activeElement = document.activeElement;
                const [start, end] = [
                    activeElement.selectionStart,
                    activeElement.selectionEnd
                ];

                this.$nextTick(() => {
                    activeElement.setRangeText(value, start, end, "end");
                    this.form1[focus_element] = activeElement.value;
                });
            }
        }
    },
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
