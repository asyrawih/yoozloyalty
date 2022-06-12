<template>
    <v-app light>
        <v-navigation-drawer
            fixed
            :clipped="$vuetify.breakpoint.lgAndUp"
            :stateless="!$auth.check()"
            v-model="drawer"
            app
        >
            <v-list dense nav shaped key="navKey" class="pl-0">
                <v-list-item-group color="primary">
                    <template v-for="(item, index) in nav">
                        <v-layout
                            v-if="item.heading"
                            :key="item.heading"
                            row
                            align-center
                        >
                            <v-flex xs6>
                                <v-subheader v-if="item.heading">
                                    {{ item.heading }}
                                </v-subheader>
                            </v-flex>
                        </v-layout>
                        <v-layout
                            row
                            v-else-if="item.search"
                            align-center
                            :key="'nav_' + index"
                        >
                            <v-text-field
                                text
                                solo
                                hide-details
                                prepend-inner-icon="search"
                                :placeholder="item.search"
                                class="input-search"
                            ></v-text-field>
                        </v-layout>

                        <v-list-group
                            v-else-if="item.children"
                            :key="'nav_parent_' + index"
                            :value="item.opened"
                            no-action
                            :sub-group="false"
                            :prepend-icon="item.icon"
                        >
                            <template #activator>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.text }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </template>

                            <v-list-item
                                v-for="(child, i) in item.children"
                                :key="'nav_child_' + i"
                                :to="child.to"
                                exact
                            >
                                <v-list-item-icon v-if="child.icon">
                                    <v-icon>{{ child.icon }}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ child.text }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-group>

                        <v-list-item
                            v-else
                            :key="'nav_sub_' + index"
                            :to="item.to"
                            exact
                        >
                            <v-list-item-icon>
                                <v-icon>{{ item.icon }}</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>
                                {{ item.text }}
                            </v-list-item-title>
                        </v-list-item>
                    </template>
                </v-list-item-group>
            </v-list>
        </v-navigation-drawer>
        <v-app-bar
            fixed
            clipped-left
            app
            flat
            class="darken-3 white--text"
            :color="app.app_color"
            height="64px"
            ___v-if="$auth.check()"
        >
            <v-toolbar-title style="margin-left:-5px">
                <v-app-bar-nav-icon
                    @click.stop="drawer = !drawer"
                    class="white--text"
                    v-if="$auth.check()"
                >
                    <v-icon>menu</v-icon>
                </v-app-bar-nav-icon>

                <span :style="{
                    'position': 'relative',
                }">
                    <div :style="{
                        'display': 'inline-block',
                        'width': app.logo ? '128px' : '80px',
                        'position': 'relative',
                    }">
                        <img
                            :src="app.logo ? app.logo : '/images/logo.svg'"
                            alt="App Logo"
                            :style="{
                                'position': 'absolute',
                                'margin-top': '-20px',
                                'object-fit': 'contain',
                                'width': app.logo ? '128px' : '80px',
                                'background-color': app.logo ? '#fff' : 'transparent',
                            }"
                        />
                    </div>

                    <span :class="app.logo ? 'ml-1' : ''">{{ app.app_name }}</span>
                </span>

                <v-progress-circular
                    v-if="$store.state.app.loading"
                    class="ml-2"
                    color="white"
                    indeterminate
                    :width="2"
                    :size="18"
                />
            </v-toolbar-title>

            <v-spacer></v-spacer>
            <v-btn
                dark
                text
                v-if="!$auth.check()"
                :href="app.app_scheme + '://' + app.app_host"
            >
                <v-icon color="white" size="15" class="mr-1">home</v-icon> Home
            </v-btn>
            <v-menu
                nudge-bottom="10px"
                offset-y
                :close-on-content-click="false"
                v-if="$auth.check()"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon dark v-bind="attrs" v-on="on">
                        <v-badge
                            color="pink accent-2"
                            dot
                            overlap
                            :value="notifUnread.length"
                        >
                            <v-icon>
                                notifications
                            </v-icon>
                        </v-badge>
                    </v-btn>
                </template>

                <v-card
                    class="mx-auto"
                    width="350"
                    tile
                    :loading="markAsReadLoading"
                >
                    <div
                        class="py-2 pl-2 pr-8 d-flex justify-space-between align-center"
                    >
                        <!-- <v-btn text depressed small class="caption"
                            >View All</v-btn
                        > -->
                        <p class="mb-0 ml-3 caption">Notifications</p>
                        <v-btn
                            icon
                            v-if="notifUnread.length > 0"
                            @click="markAsReadNotif()"
                        >
                            <v-icon>
                                mark_email_read
                            </v-icon>
                        </v-btn>
                        <v-btn icon disabled v-else>
                            <v-icon>
                                mark_email_read
                            </v-icon>
                        </v-btn>
                    </div>
                    <v-divider></v-divider>
                    <v-list
                        two-line
                        class="py-0"
                        max-height="400"
                        min-height="250"
                        style="overflow-y:scroll"
                    >
                        <v-list-item-group v-if="notifUnread.length > 0">
                            <v-list-item
                                v-for="(notif, key) in notifUnread"
                                :key="'notif-' + key"
                            >
                                <v-list-item-content
                                    @click="
                                        showNotif(
                                            notif.data.title,
                                            notif.data.content
                                        )
                                    "
                                >
                                    <v-list-item-title>{{
                                        notif.data.title
                                    }}</v-list-item-title>

                                    <v-list-item-subtitle>{{
                                        notif.data.content
                                    }}</v-list-item-subtitle>
                                </v-list-item-content>

                                <v-list-item-action>
                                    <v-list-item-action-text>
                                        {{ notif.created_at }}
                                    </v-list-item-action-text>
                                    <v-btn
                                        icon
                                        @click="markAsReadNotif(notif.id, key)"
                                    >
                                        <v-icon>
                                            mark_email_read
                                        </v-icon>
                                    </v-btn>
                                </v-list-item-action>
                            </v-list-item>
                            <v-divider></v-divider>
                        </v-list-item-group>
                        <div class="text-center" style="padding:80px" v-else>
                            <v-icon color="grey lighten-1"
                                >mark_email_read</v-icon
                            >
                            <p class="mt-3 caption">
                                No Notifications shown
                            </p>
                        </div>
                    </v-list>
                </v-card>
            </v-menu>
            <v-menu offset-y nudge-bottom="10px" v-if="$auth.check()">
                <template v-slot:activator="{ on }">
                    <v-btn icon large v-on="on">
                        <v-avatar v-if="$auth.check()" :tile="false" :size="32">
                            <img :src="$auth.user().avatar" alt="avatar" />
                        </v-avatar>
                    </v-btn>
                </template>
                <v-list>
                    <v-subheader>{{ $auth.user().email }}</v-subheader>
                    <v-divider
                        :inset="false"
                        class="grey lighten-2"
                    ></v-divider>
                    <v-list-item :to="{ name: 'profile' }">
                        <v-list-item-content>
                            <v-list-item-title>Profile</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item
                        v-if="$auth.user().role === 3"
                        :to="{ name: 'user.billing' }"
                    >
                        <v-list-item-content>
                            <v-list-item-title>Billing</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider
                        :inset="false"
                        class="grey lighten-2"
                    />

                    <!-- <v-list-item
                        v-if="$auth.check()"
                        @click="
                            if ($auth.impersonating()) {
                                $auth.unimpersonate({
                                    redirect: { name: 'admin.merchant' }
                                });

                                return true;
                            }

                            $auth.logout();

                            return true;
                        "
                    >
                        <v-list-item-content>
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item> -->

                    <v-list-item
                        @click="$auth.logout()">
                        <v-list-item-content>
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>
        <v-main>
            <router-view name="primary"></router-view>
        </v-main>

        <confirm ref="confirm"></confirm>
        <snackbar ref="snackbar"></snackbar>
    </v-app>
</template>
<script>
export default {
    name: "app",
    data() {
        return {
            socket: null,
            pushNotifService: null,
            notifUnread: [],
            markAsReadLoading: false,
            drawer: true,
            navKey: 1,
            menu: {
                analytics: false,
                merchants: false,
                programs: false,
                settings: false
            }
        };
    },
    mounted() {
        this.$root.$confirm = this.$refs.confirm.open;

        this.$root.$snackbar = this.$refs.snackbar.show;

        if (this.$auth.check()) {
            this.getNotif();
            this.subscribeNotification();
        }
    },
    watch: {
        $route(to, from) {
            this.drawer =
                this.$vuetify.breakpoint.lgAndUp && this.$auth.check()
                    ? true
                    : false;

            if (to.meta.parentMenu) {
                this.menu.analytics = false;
                this.menu.merchants = false;
                this.menu.programs = false;
                this.menu.settings = false;

                this.menu[to.meta.parentMenu] = true;
                this.navKey++;
            }
        }
    },
    created: function() {
            this.loadchatWidgets()
        if (
            this.$auth.check() &&
            typeof this.$auth.user().language === "undefined"
        ) {
            this.$auth.logout();
        }

        if (this.$route.path == "/dashboard" && this.user.role != 3) {
            this.$router.push({ name: this.dashboard });
        }

        this.drawer =
            this.$vuetify.breakpoint.lgAndUp && this.$auth.check()
                ? true
                : false;

        if (
            this.$auth.check() &&
            typeof this.user.language !== "undefined" &&
            this.user.language !== "undefined"
        ) {
            this.$i18n.locale = this.user.language;
        }

        // Open menu
        if (this.$route.meta.parentMenu) {
            this.menu[this.$route.meta.parentMenu] = true;

            this.navKey++;
        }

        // Remove loader
        document.getElementById("app-loader").remove();
    },
    computed: {
        app() {
            return this.$store.getters.app;
        },
        user() {
            return this.$auth.user();
        },
        dashboard() {
            let dashboard = "user.dashboard";

            switch (this.user.role) {
                case 1:
                    dashboard = "admin.dashboard";
                    break;
                case 2:
                    dashboard = "admin.dashboard";
                    break;
            }

            return dashboard;
        },
        nav() {
            // Not logged in
            if (this.$auth.check() === false) {
                return [
                    {
                        text: this.$t("login"),
                        icon: "fas fa-sign-in-alt",
                        route_name: "login",
                        to: { name: "login" }
                    },
                    {
                        text: this.$t("sign_up"),
                        icon: "fas fa-user-plus",
                        route_name: "register",
                        to: { name: "register" }
                    },
                    {
                        text: "Reset password",
                        icon: "fas fa-key",
                        route_name: "password.email",
                        to: { name: "password.email" }
                    }, /*,
                    {
                        name: 'Privacy Policy',
                        icon: 'fas fa-shield-alt',
                        route_name: 'register',
                        to: {name: 'register'}
                    }*/
                ];
            }

            const admin_setting_nav = {
                icon: "settings",
                text: this.$t("settings"),
                opened: this.menu.settings,
                children: [
                    {
                        text: this.$t("profile"),
                        icon: "account_circle",
                        route_name: "profile",
                        to: { name: "profile" }
                    },
                    {
                        text: "Store Setup",
                        icon: "style",
                        route_name: "admin.settings.store",
                        to: { name: "admin.settings.store" }
                    },
                    {
                        text: "Plans",
                        icon: "card_membership",
                        route_name: "admin.settings.plans",
                        to: { name: "admin.settings.plans" }
                    },
                    {
                        text: "Industries",
                        icon: "business",
                        route_name: "admin.settings.industries",
                        to: { name: "admin.settings.industries" }
                    },
                    {
                        text: "Trial Day",
                        icon: "timelapse",
                        route_name: "admin.settings.trial",
                        to: { name: "admin.settings.trial" }
                    },
                    {
                        text: "Store Setting",
                        icon: "image",
                        route_name: "admin.settings.logo",
                        to: { name: "admin.settings.logo" }
                    },
                    {
                        text: "Legal",
                        icon: "policy",
                        route_name: "admin.settings.legal",
                        to: { name: "admin.settings.legal" }
                    },
                    {
                        text: "Email",
                        icon: "email",
                        route_name: "admin.settings.email",
                        to: { name: "admin.settings.email" }
                    },
                    {
                        text: "Bank Accounts",
                        icon: "mdi-bank",
                        route_name: "admin.settings.bankAccounts",
                        to: { name: "admin.settings.bankAccounts" }
                    },
                    {
                        text: "Bank Account Types",
                        icon: "mdi-bank",
                        route_name: "admin.settings.bankAccountTypes",
                        to: { name: "admin.settings.bankAccountTypes" }
                    },
                    {
                        text: "Payment",
                        icon: "credit_card",
                        route_name: "admin.settings.payment",
                        to: { name: "admin.settings.payment" }
                    },
                    {
                        text: "Plan Orders",
                        icon: "mdi-file",
                        route_name: "admin.settings.planOrders",
                        to: { name: "admin.settings.planOrders" }
                    },
                    {
                        text: "Push Notification",
                        icon: "notifications",
                        route_name: "admin.settings.pushNotif",
                        to: { name: "admin.settings.pushNotif" }
                    },
                    {
                        text: "Notification Template",
                        icon: "notifications",
                        route_name: "admin.settings.notifTemplate",
                        to: { name: "admin.settings.notifTemplate" }
                    },
                    {
                        text: "Custom Domain Guide",
                        icon: "article",
                        route_name: "admin.settings.customDomainGuide",
                        to: { name: "admin.settings.customDomainGuide" }
                    },
                    {
                        text: "SMS",
                        icon: "message",
                        route_name: "admin.settings.smsService",
                        to: {
                            name: "admin.settings.smsService"
                        }
                    },
                    {
                        text: "SMS Template",
                        icon: "message",
                        route_name: "admin.settings.smsTemplate",
                        to: {
                            name: "admin.settings.smsTemplate"
                        }
                    },
                    {
                        text: "Staff Roles",
                        icon: "mdi-account-key",
                        route_name: "admin.settings.staffRoles",
                        to: {
                            name: "admin.settings.staffRoles"
                        }
                    }
                ]
            };

            // Master Admin
            if (this.user.role == 1) {
                return [
                    {
                        text: this.$t("dashboard"),
                        icon: "dashboard",
                        route_name: this.dashboard,
                        to: { name: this.dashboard }
                    },
                    { heading: "Admin" },
                    {
                        text: "Admin",
                        icon: "people",
                        route_name: "admin.user",
                        to: { name: "admin.user" }
                    },
                    {
                        text: "Merchants",
                        icon: "people",
                        route_name: "admin.merchant",
                        to: { name: "admin.merchant" }
                    },
                    {
                        text: "Broadcast Notification",
                        icon: "podcasts",
                        route_name: "admin.broadcast-notification",
                        to: {
                            name: "admin.broadcast-notification"
                        }
                    },
                    admin_setting_nav
                ];
            }

            // Normal Admin
            if (this.user.role == 2) {
                return [
                    {
                        text: this.$t("dashboard"),
                        icon: "dashboard",
                        route_name: this.dashboard,
                        to: { name: this.dashboard }
                    },
                    { heading: "Admin" },
                    {
                        text: "Merchants",
                        icon: "people",
                        route_name: "admin.merchant",
                        to: { name: "admin.merchant" }
                    },
                    admin_setting_nav
                ];
            }

            // User
            if (this.user.role == 3) {
                return [
                    /*{ search: 'Search customers' },*/
                    {
                        text: this.$t("home"),
                        icon: "home",
                        route_name: this.dashboard,
                        to: { name: this.dashboard }
                    },
                    { heading: this.$t("insights") },
                    {
                        text: this.$t("customers"),
                        icon: "contacts",
                        route_name: "user.customers",
                        to: { name: "user.customers" }
                    },
                    {
                        icon: "timeline",
                        text: this.$t("analytics"),
                        opened: this.menu.analytics,
                        children: [
                            {
                                text: "Credit",
                                icon: "toll",
                                route_name: "user.analytics.credit",
                                to: { name: "user.analytics.credit" }
                            },
                            {
                                text: "Redemption",
                                icon: "fas fa-gift",
                                route_name: "user.analytics.redemption",
                                to: { name: "user.analytics.redemption" }
                            }
                        ]
                    },
                    { heading: "Management" },
                    {
                        icon: "store",
                        text: "Store Setup",
                        opened: this.menu.merchants,
                        children: [
                            {
                                text: "Stores",
                                icon: "business",
                                route_name: "user.businesses",
                                to: { name: "user.businesses" }
                            },
                            {
                                text: this.$t("staff_members"),
                                icon: "work",
                                route_name: "user.staff",
                                to: { name: "user.staff" }
                            },
                            {
                                text: this.$t("segments"),
                                icon: "category",
                                route_name: "user.segments",
                                to: { name: "user.segments" }
                            }
                        ]
                    },
                    {
                        icon: "loyalty",
                        text: "Loyalty Setup",
                        opened: this.menu.programs,
                        children: [
                            {
                                text: "Websites",
                                icon: "record_voice_over",
                                route_name: "user.websites",
                                to: { name: "user.websites" }
                            },
                            {
                                text: "Reward Offer",
                                icon: "fas fa-gift",
                                route_name: "user.offer",
                                to: { name: "user.offer" }
                            },
                            {
                                text: "Redemption Multiplier",
                                icon: "fas fa-donate",
                                route_name: "user.redeem-transaction",
                                to: { name: "user.redeem-transaction" }
                            }
                        ]
                    },
                    {
                        icon: "settings",
                        text: this.$t("settings"),
                        opened: this.menu.settings,
                        children: [
                            {
                                text: this.$t("profile"),
                                icon: "account_circle",
                                route_name: "profile",
                                to: { name: "profile" }
                            },
                            {
                                text: this.$t("billing"),
                                icon: "credit_card",
                                route_name: "user.billing",
                                to: { name: "user.billing" }
                            },
                            {
                                text: this.$t("plan_change_request_history"),
                                icon: "credit_card",
                                route_name: "user.planChangeRequestHistory",
                                to: { name: "user.planChangeRequestHistory" }
                            },
                            {
                                text: "Email template",
                                icon: "email",
                                route_name: "user.emailTemplate",
                                to: { name: "user.emailTemplate" }
                            },
                            {
                                text: this.$t("legal"),
                                icon: "policy",
                                route_name: "user.legal",
                                to: { name: "user.legal" }
                            },
                            {
                                text: "SMS Template",
                                icon: "message",
                                route_name: "user.sms-template",
                                to: { name: "user.sms-template" }
                            },
                            {
                                text: "SMTP Service",
                                icon: "email",
                                route_name: "user.smtp-service",
                                to: { name: "user.smtp-service" }
                            },
                            {
                                text: "Points Expiry",
                                icon: "event",
                                route_name: "user.points-expiry",
                                to: { name: "user.points-expiry" }
                            },
                        ],
                    },
                    {
                        text: "Points Credit Request",
                        icon: "request_page",
                        route_name: "user.credit_request",
                        to: { name: "user.credit_request" }
                    },
                    {
                        text: "Points Transaction History",
                        icon: "request_page",
                        route_name: "user.transaction_history",
                        to: { name: "user.transaction_history" }
                    },
                    {
                        text: "Broadcast Notification",
                        icon: "podcasts",
                        route_name: "user.broadcast-notification",
                        to: {
                            name: "user.broadcast-notification"
                        }
                    }
                ];
            }
        }
    },
    methods: {
        loadchatWidgets() {
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
            (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/54d34cdcb37d8bc7b1c0239d/1fuoh7io3';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
            })();
        },
        getNotif() {
            const url =
                [1, 2].includes(this.user.role)
                    ? "/admin/notifUnread"
                    : "/user/notifUnread";
            axios
                .get(url)
                .then(res => {
                    this.notifUnread = res.data;
                })
                .catch(err => console.log(err));
        },
        showNotif(title, content) {
            this.$root.$snackbar(title + ", " + content, {
                y: "top",
                x: "",
                mode: "multi-line"
            });
        },
        markAsReadNotif(id = "all", index = null) {
            this.markAsReadLoading = true;

            const url =
                [1, 2].includes(this.user.role)
                    ? "/admin/maskAsRead"
                    : "/user/maskAsRead";
            axios
                .post(url, {
                    id
                })
                .then(res => {
                    if (id === "all") {
                        this.notifUnread = [];
                    } else {
                        this.notifUnread.splice(index, 1);
                    }

                    this.markAsReadLoading = false;

                    this.$root.$snackbar("Marked as read");
                })
                .catch(err => console.log(err));
        },
        subscribeNotification() {
            // Pusher.logToConsole = true;
            axios
                .get('/notification-service/active')
                .then(response => {
                    const data = response.data.data;
                    data.map(item => {
                        const schema = JSON.parse(item.schema);
                        /*  
                        ** Pusher notification
                        */
                        if (item.blueprint === 'pusher') {
                            if (
                                this.socket === null ||
                                this.socket.connection.state == "disconnected"
                            ) {
                                this.socket = new Pusher(schema.app_key, {
                                    cluster: schema.cluster,
                                    forceTLS: true
                                });
        
                                let channel = this.socket.subscribe(this.$auth.user().uuid);
        
                                this.socket.connection.bind("error", function(err) {
                                    this.connectionError = err.error.data.code;
                                });
        
                                channel.bind("adminNotif", data => {
                                    this.notifUnread = JSON.parse(data);
                                });
                            }
                        } 
                        
                        /*  
                        ** Onesignal notification
                        */
                        if (item.blueprint === 'onesignal') {
                            if (this.pushNotifService === null) {
                                this.$OneSignal
                                    .init(
                                        {
                                            appId: schema.app_id,
                                            AllowLocalhostAsSecureOrigin: true,
                                        }
                                    )
                                    .then(async () => {
                                        this.pushNotifService = item.blueprint;
                                        this.$OneSignal.setExternalUserId(this.$auth.user().uuid);
                                    });
                            }
                        }                          
                    })
                })
        }
    }
};
</script>

<style>
.v-application--is-ltr
    .v-list--dense.v-list--nav
    .v-list-group--no-action
    > .v-list-group__items
    > div
    > .v-list-item {
    padding-left: 48px;
}
.v-navigation-drawer
    .v-list-item__icon:not(.v-list-group__header__append-icon) {
    margin-right: 15px !important;
}
.v-list__group__header .v-list__group__header__prepend-icon {
    padding-right: 0px;
}
.v-navigation-drawer .v-subheader {
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.8px;
    padding: 16px 0 8px 24px;
    color: rgba(0, 0, 0, 0.54);
    font-weight: 500;
    /*  letter-spacing: -0.06px;*/
}
.input-search input[type="text"] {
    font-size: 13px;
}
.input-search.v-text-field--solo .v-input__slot {
    padding-top: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}
.input-search .v-input__icon {
    min-width: 34px;
}
.input-search .v-icon {
    color: #777 !important;
    font-size: 22px !important;
}
.input-search input::-webkit-input-placeholder {
    /* Chrome/Opera/Safari */
    color: #777;
}
.input-search input::-moz-placeholder {
    /* Firefox 19+ */
    color: #777;
}
.input-search input:-ms-input-placeholder {
    /* IE 10+ */
    color: #777;
}
.v-list-group .v-list-item__icon.v-list-group__header__append-icon {
    min-width: 32px;
}
.v-list--nav .v-list-group .v-list-group__header .v-list-item {
    padding: 0 8px 0 0 !important;
}
</style>
