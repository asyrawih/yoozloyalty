<template>
    <v-app :style="{ 'background-color': backgroundColor }">
        <v-navigation-drawer
            v-if="$vuetify.breakpoint.smAndDown"
            fixed
            clipped
            v-model="drawer"
            app
            right
            :style="{
                'background-color': campaign.theme.drawer.backgroundColor,
                color: campaign.theme.drawer.textColor
            }"
        >
            <div v-if="$auth.check() && user">
                <v-flex xs12 align-center justify-center layout text-xs-center>
                    <router-link :to="{ name: 'user.profile' }">
                        <v-avatar :tile="false" :size="80" class="mt-5 mb-4">
                            <img :src="user.avatar" alt="avatar" />
                        </v-avatar>
                    </router-link>
                </v-flex>

                <v-flex xs12 align-center justify-center layout text-xs-center>
                    <router-link
                        :to="{ name: 'user.profile' }"
                        :style="{
                            color: campaign.theme.drawer.textColor,
                            'text-decoration': 'none'
                        }"
                    >
                        <div class="title">{{ user.name }}</div>
                        <div class="subtitle">{{ user.email }}</div>
                    </router-link>
                </v-flex>

                <v-flex xs12 align-center justify-center layout text-xs-center pt-4>
                    <v-btn
                        :style="{ color: campaign.theme.primaryColor }"
                        :to="{ name: 'user.profile' }"
                        @click="doLogout"
                    >
                        Logout
                    </v-btn>
                </v-flex>
            </div>

            <div v-if="! $auth.check()">
                <v-flex
                    v-if="campaign.theme.logo"
                    xs12
                    align-center
                    justify-center
                    layout
                    text-xs-center
                >
                    <router-link :to="{ name: 'home' }">
                        <v-avatar :tile="true" :size="128" class="mt-5 mb-4">
                            <img
                                :src="campaign.theme.logo"
                                :alt="campaign.name"
                            />
                        </v-avatar>
                    </router-link>
                </v-flex>

                <v-flex xs12 align-center justify-center layout text-xs-center>
                    <router-link
                        :to="{ name: 'home' }"
                        :style="{
                            color: campaign.theme.drawer.textColor,
                            'text-decoration': 'none'
                        }"
                    >
                        <div
                            :class="{ 'mt-5': !campaign.theme.logo }"
                            class="title"
                        >
                            {{ campaign.title }}
                        </div>
                    </router-link>
                </v-flex>
            </div>

            <v-list dense nav class="mt-4">
                <template v-for="(item, index) in nav">
                    <v-divider
                        v-if="item.divider"
                        :inset="false"
                        class="grey darken-2"
                        :key="'nav_' + index"
                    />

                    <v-list-item
                        v-else
                        :to="item.to"
                        exact
                        :style="
                            item.route_name === $route.name ? highlighted : {}
                        "
                        :key="'nav_' + index"
                        @click="item.click == 'logout' ? doLogout : null"
                    >
                        <v-list-item-icon v-if="!item.divider">
                            <v-icon
                                :style="{
                                    color:
                                        item.route_name === $route.name
                                            ? highlighted.color
                                            : campaign.theme.drawer.textColor
                                }"
                                class="ml-4 nav-icon"
                                >{{ item.icon }}</v-icon
                            >
                        </v-list-item-icon>

                        <v-list-item-title
                            v-if="!item.divider"
                            :style="{
                                color:
                                    item.route_name === $route.name
                                        ? highlighted.color
                                        : campaign.theme.drawer.textColor
                            }"
                            >{{ item.name }}</v-list-item-title
                        >
                    </v-list-item>
                </template>
            </v-list>
        </v-navigation-drawer>
        <!--
    <v-system-bar app height="36" :style="{'background-color': campaign.theme.primaryColor, 'color': campaign.theme.primaryTextColor}" v-if="Object.keys(campaign.externalUrls).length > 0">
      <span v-for="(item, index) in campaign.externalUrls" :key="'topHeader_' + index">
        <a :href="item.href" :style="{'color': campaign.theme.primaryTextColor}">{{ item.text }}</a>
        <span class="mx-2 bull" v-if="index < Object.keys(campaign.externalUrls).length - 1">&bull;</span>
      </span>
    </v-system-bar>
-->
        <v-app-bar
            :color="campaign.theme.primaryColor"
            :style="{ color: campaign.theme.primaryTextColor }"
            style="z-index: 4;"
            class="header"
            dense
            fixed
            clipped-right
            app
            flat
            height="96px"
        >
            <router-link
                v-if="campaign.theme.logo"
                :to="{ name: 'home' }"
                tag="img"
                :src="campaign.theme.logo"
                style="max-height:70%; max-width: 240px; cursor: pointer;"
                class="ml-2 mr-4"
                :alt="campaign.name"
            ></router-link>
            <v-toolbar-title
                class="mr-5 align-center"
                v-if="campaign.headline || !campaign.theme.logo"
            >
                <v-spacer></v-spacer>
                <span class="title"
                    ><router-link
                        :to="{ name: 'home' }"
                        :style="{
                            color: campaign.theme.primaryTextColor,
                            'text-decoration': 'none'
                        }"
                        >{{ campaign.title }}</router-link
                    ></span
                >
                <div class="subtitle-2" v-if="campaign.headline">
                    {{ campaign.headline }}
                </div>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                <v-btn
                    class="hidden-sm-and-down"
                    text
                    :style="{ color: campaign.theme.primaryTextColor }"
                    v-for="(item, index) in nav"
                    v-if="!item.sideNavOnly"
                    :key="'topNav_' + index"
                    :to="item.to"
                    exact
                    >{{ item.name }}</v-btn
                >
            </v-toolbar-items>
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
                        class="d-flex justify-space-between align-center pl-2 pr-8 py-2"
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
                            <p class="caption mt-3">
                                No Notifications shown
                            </p>
                        </div>
                    </v-list>
                </v-card>
            </v-menu>
            <v-menu
                bottom
                left
                nudge-bottom="60px"
                v-if="$vuetify.breakpoint.mdAndUp && $auth.check() && user"
            >
                <template v-slot:activator="{ on }">
                    <v-btn icon large v-on="on" class="ml-3">
                        <v-avatar :tile="false" :size="32">
                            <img :src="user.avatar" alt="avatar" />
                        </v-avatar>
                    </v-btn>
                </template>
                <v-list>
                    <v-subheader>{{ user.email }}</v-subheader>

                    <v-divider
                        :inset="false"
                        class="grey lighten-2"
                    />

                    <v-list-item
                        @click="$router.push({ name: 'user.profile' })"
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{
                                $t("profile")
                            }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item @click="doLogout">
                        <v-list-item-content>
                            <v-list-item-title>{{
                                $t("logout")
                            }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-menu>

            <v-menu
                bottom
                left
                nudge-bottom="60px"
                v-if="languages !== null && languages.length > 1"
            >
                <template v-slot:activator="{ on }">
                    <v-btn
                        icon
                        large
                        v-on="on"
                        class="ml-3"
                        :style="{ color: campaign.theme.primaryTextColor }"
                    >
                        {{ $t("language_abbr") }}
                    </v-btn>
                </template>

                <v-list>
                    <v-list-item
                        @click="changeLang(item.code)"
                        v-for="(item, index) in languages"
                        :key="'languages_' + index"
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{
                                item.title
                            }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-menu>
            <v-app-bar-nav-icon
                class="hidden-md-and-up mr-2"
                @click.stop="drawer = !drawer"
                :style="{ color: campaign.theme.primaryTextColor }"
                ><v-icon v-if="!drawer">menu</v-icon
                ><v-icon v-if="drawer">close</v-icon></v-app-bar-nav-icon
            >
        </v-app-bar>
        <v-main
            :style="{ 'background-color': campaign.theme.backgroundColor }"
        >
            <router-view name="primary"></router-view>
        </v-main>
        <v-footer
            absolute
            app
            class="pa-0"
            height="auto"
            :style="{
                'background-color': campaign.theme.drawer.backgroundColor,
                color: campaign.theme.drawer.textColor
            }"
        >
            <v-card class="flex" text tile>
                <v-card-title
                    :style="{
                        'background-color': campaign.theme.secondaryColor,
                        color: campaign.theme.secondaryTextColor
                    }"
                    class="pa-2"
                    v-if="Object.keys(campaign.footer.links).length > 0"
                >
                    <strong
                        class="subtitle-1"
                        v-html="campaign.footer.text"
                    ></strong>
                    <v-spacer></v-spacer>
                    <v-btn
                        v-for="item in campaign.footer.links"
                        :key="item.icon"
                        class="mr-2"
                        :href="item.href"
                        target="_blank"
                        :style="{ color: campaign.theme.secondaryTextColor }"
                        icon
                    >
                        <v-icon size="20px">{{ item.icon }}</v-icon>
                    </v-btn>
                </v-card-title>
                <div
                    class="body-2 text-xs-center py-2"
                    :style="{
                        'background-color': campaign.theme.primaryColor,
                        color: campaign.theme.primaryTextColor
                    }"
                >
                    &copy;{{ new Date().getFullYear() }} —
                    {{ campaign.business.name }}
                    <span class="mx-2">&bull;</span>
                    <router-link
                        :to="{ name: 'user_agreement' }"
                        :style="{ color: campaign.theme.primaryTextColor }"
                        >{{ $t("terms_and_conditions") }}</router-link
                    >
                    <span class="mx-2">&bull;</span>
                    <router-link
                        :to="{ name: 'privacy_policy' }"
                        :style="{ color: campaign.theme.primaryTextColor }"
                        >{{ $t("privacy_policy") }}</router-link
                    >
                    <span class="mx-2">&bull;</span>
                    <router-link
                        :to="{ name: 'refund_policy' }"
                        :style="{ color: campaign.theme.primaryTextColor }"
                        >{{ $t("refund_policy") }}</router-link
                    >
                    <span v-if="Object.keys(campaign.externalUrls).length > 0">
                        <span class="mx-2">&bull;</span>
                        <span
                            v-for="(item, index) in campaign.externalUrls"
                            :key="index"
                        >
                            <a
                                :href="item.href"
                                :style="{
                                    color: campaign.theme.primaryTextColor
                                }"
                                >{{ item.text }}</a
                            >
                            <span
                                class="mx-2"
                                v-if="
                                    index <
                                        Object.keys(campaign.externalUrls)
                                            .length -
                                            1
                                "
                                >&bull;</span
                            >
                        </span>
                    </span>
                </div>
            </v-card>
        </v-footer>
        <v-snackbar
            v-model="showCookieConsent"
            :timeout="-1"
            :bottom="true"
            :vertical="false"
            content-class="termsConsent"
            max-width="70%"
            width="100%"
        >
            <v-row>
                <v-col cols="7" md="10" align-self="center">
                    {{ $t("legal_agreement_confirmation") }}
                </v-col>

                <v-col cols="3" md="1" align-self="center">
                    <v-btn color="white" text :to="{ name: 'user_agreement' }">
                        {{ $t("terms") }}
                    </v-btn>
                </v-col>

                <v-col cols="2" md="1" align-self="center">
                    <v-btn
                        color="white"
                        text
                        icon
                        @click="$store.dispatch('setCookieConsent', false)"
                    >
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-col>
            </v-row>
        </v-snackbar>
        <confirm ref="confirm"></confirm>
        <snackbar ref="snackbar"></snackbar>
    </v-app>
</template>
<script>
import { getAvailableLanguages, loadLanguageAsync } from "../../plugins/i18n";

export default {
    name: "app",
    data() {
        return {
            socket: null,
            pushNotifService: null,
            notifUnread: [],
            markAsReadLoading: false,
            drawer: false,
            languages: null
        };
    },
    watch: {
        user: function(val) {
            if (val) {
                this.getNotif();
                this.subscribeNotification();
            }
        },
    },
    mounted() {
        this.$root.$confirm = this.$refs.confirm.open;
        this.$root.$snackbar = this.$refs.snackbar.show;

        let appBarContent = document.querySelector(".v-app-bar .v-toolbar__content");

        appBarContent.classList.add("container");

        /* Log out user when JWT key changed or session expired */
        if (
            this.$auth.check() &&
            this.user &&
            typeof this.user.language === "undefined"
        ) {
            this.doLogout
        }

        /* Get available translations */
        getAvailableLanguages().then(result => (this.languages = result));

        /* Set language */
        let language = this.$route.query.l || null;

        if (language !== null) {
            loadLanguageAsync(language);
        }

        // Remove loader
        document.getElementById("app-loader").remove();
    },
    methods: {
        changeLang(language) {
            loadLanguageAsync(language);
            this.$store.dispatch("setLanguage", language);
        },
        getNotif() {
            axios.get("/campaign/notifUnread")
                .then(response => this.notifUnread = response.data)
                .catch(error => console.log(error));
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

            axios.post("/campaign/maskAsRead", { id })
                .then(response => {
                    if (id === "all") {
                        this.notifUnread = [];
                    } else {
                        this.notifUnread.splice(index, 1);
                    }

                    this.markAsReadLoading = false;

                    this.$root.$snackbar("Marked as read");
                })
                .catch(error => console.log(error));
        },
        subscribeNotification() {
            // if (this.socket === null ||this.socket.connection.state == "disconnected") {

            //     try {
            //         this.socket = new Pusher(window.initConfig.pusher.key, {
            //             cluster: window.initConfig.pusher.options.cluster,
            //             forceTLS: window.initConfig.pusher.options.encrypted
            //         });

            //         let channel = this.socket.subscribe(this.user.uuid);

            //         this.socket.connection.bind("error", function(err) {
            //             this.connectionError = err.error.data.code;
            //         });

            //         channel.bind("customerNotif", data => {
            //             this.notifUnread = JSON.parse(data);
            //         });
            //     } catch(e) {

            //     }
            // }
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
        
                                let channel = this.socket.subscribe(this.user.uuid);
        
                                this.socket.connection.bind("error", function(err) {
                                    this.connectionError = err.error.data.code;
                                });
        
                                channel.bind("customerNotif", data => {
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
                                        this.$OneSignal.setExternalUserId(this.user.uuid);
                                    });
                            }
                        }                          
                    })
                })
        },
        doLogout() {
            if (this.socket) {
                this.socket.unsubscribe(this.user);
            }

            if (this.pushNotifService === 'onesignal') {
                this.$OneSignal.removeExternalUserId();
            }
            
            this.$store.dispatch('setJustLoggedOut', true);
            this.$router.push({ name: 'just_logged_out' });
        }
    },
    computed: {
        showCookieConsent() {
            return this.$store.state.app.showCookieConsent;
        },
        topNav() {
            return [
                {
                    name: this.$t("earn_points"),
                    icon: "card_giftcard",
                    route_name: "earn",
                    to: { name: "earn" }
                },
                {
                    name: this.$t("redeem_points"),
                    icon: "fas fa-gift",
                    route_name: "rewards",
                    to: { name: "rewards" }
                },
                {
                    name: this.$t("contact_us"),
                    icon: "location_on",
                    route_name: "contact_us",
                    to: { name: "contact_us" }
                },
                {
                    name: this.$t("stores"),
                    icon: "toll",
                    route_name: "stores",
                    to: { name: "stores" }
                },
            ];
        },
        nav() {
            let nav = Object.assign([], this.topNav);

            if (this.$auth.check()) {
                nav.push(
                    {
                        name: this.$t("my_points"),
                        icon: "toll",
                        route_name: "points",
                        to: { name: "points" }
                    },
                    { divider: true },
                );
            } else {
                nav.push(
                    { divider: true },
                    {
                        name: this.$t("login"),
                        icon: "exit_to_app",
                        route_name: "login",
                        to: { name: "login" }
                    }
                );
            }

            nav.unshift({
                name: this.$t("home"),
                icon: "home",
                route_name: "home",
                to: { name: "home" }
            });

            return nav;
        },
        backgroundColor() {
            return this.$store.state.app.backgroundColor;
        },
        campaign() {
            return this.$store.state.app.campaign;
        },
        user() {
            return this.$auth.user();
        },
        highlighted() {
            return {
                "background-color": this.campaign.theme.drawer
                    .highlightBackgroundColor,
                color: this.campaign.theme.drawer
                    .highlightTextColor
            };
        }
    }
};
</script>
<style>
.v-overlay--absolute {
    z-index: 2 !important;
}
.v-system-bar {
    white-space: nowrap;
    padding-left: 16px;
}
.v-system-bar a {
    text-decoration: none;
}
.termsConsent .v-snack__content {
    height: auto !important;
}
</style>
