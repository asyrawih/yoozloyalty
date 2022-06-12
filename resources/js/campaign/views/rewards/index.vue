<template>
    <div class="pb-5">
        <v-container grid-list-lg fluid class="pa-0">
            <v-layout row wrap>
                <v-flex
                    class="py-0"
                    xs12
                    :style="{
                        'background-color': campaign.theme.secondaryColor,
                        'color': campaign.theme.secondaryTextColor
                    }"
                >
                    <v-img
                        :src="campaign.rewards.headerImg || ''"
                        :height="campaign.rewards.headerHeight"
                    >
                        <v-overlay
                            absolute
                            :color="campaign.theme.secondaryColor"
                            :opacity="campaign.rewards.headerOpacity"
                            z-index="1"
                        />

                        <v-container fill-height>
                            <v-layout row wrap align-center>
                                <v-flex xs12 style="z-index: 2">
                                    <h1
                                        class="mb-2 display-1"
                                        v-html="campaign.rewards.headerTitle"
                                    />

                                    <div
                                        v-html="campaign.rewards.headerContent"
                                    />

                                    <v-btn
                                        v-if="$auth.check() && user"
                                        color="primary"
                                        elevation="2"
                                        @click="
                                            dialog.redeem.customerNumber = $auth.check();
                                            dialog.authRequired = ! $auth.check();
                                        "
                                    >
                                        Customer Number
                                    </v-btn>

                                    <v-dialog
                                        v-if="user"
                                        v-model="dialog.redeem.customerNumber"
                                        persistent
                                        max-width="320"
                                    >
                                        <v-card>
                                            <v-card-title class="headline">
                                                {{ $t("customer_number") }}
                                            </v-card-title>

                                            <v-card-text>
                                                <p class="body-1">
                                                    {{ $t("give_number_to_merchant") }}
                                                </p>

                                                <v-text-field
                                                    type="text"
                                                    id="txtCustomerNumber"
                                                    class="title"
                                                    outlined
                                                    append-icon="filter_none"
                                                    :value="user.number"
                                                    @click:append="copyToClipboard(user.number)"
                                                    readonly
                                                />
                                            </v-card-text>

                                            <v-card-actions>
                                                <v-spacer></v-spacer>

                                                <v-btn
                                                    color="secondary"
                                                    text
                                                    @click="dialog.redeem.customerNumber = false"
                                                >
                                                    {{ $t("close") }}
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-flex>
                            </v-layout>
                        </v-container>

                        <template v-slot:placeholder>
                            <v-layout
                                v-if="campaign.rewards.headerImg"
                                fill-height
                                align-center
                                justify-center
                                ma-0
                            >
                                <v-progress-circular
                                    indeterminate
                                    :style="{
                                        color: campaign.theme.secondaryTextColor
                                    }"
                                />
                            </v-layout>
                        </template>
                    </v-img>
                </v-flex>

                <vue-gallery
                    :images="gallery_images"
                    :index="selected_gallery_image"
                    @close="selected_gallery_image = null"
                    :ref="'gallery'"
                />

                <v-flex xs12>
                    <v-container grid-list-xl>
                        <v-layout row wrap>
                            <v-flex
                                v-for="(reward, reward_index) in campaignRewardsList"
                                :key="'campaign_rewards_' + reward_index"
                                class="mt-2"
                                xs12
                                sm6
                                lg4
                                xl3
                            >
                                <v-card style="height: 100%">
                                    <v-img
                                        :key="`card_reward_${reward_index}__images_sub_0`"
                                        v-if="reward.images.length > 0"
                                        :src="reward.images[0].thumb"
                                        :aspect-ratio="campaign.rewards.imageRatio"
                                        style="cursor: pointer;"
                                        @click="
                                            gallery_images = reward.images;
                                            selected_gallery_image = 0;
                                        "
                                    >
                                        <template v-slot:placeholder>
                                            <v-layout
                                                fill-height
                                                align-center
                                                justify-center
                                                ma-0
                                            >
                                                <v-progress-circular
                                                    indeterminate
                                                    :style="{
                                                        color: campaign.theme.textColor
                                                    }"
                                                />
                                            </v-layout>
                                        </template>
                                    </v-img>

                                    <v-container
                                        class="pa-3"
                                        grid-list-lg
                                        fluid
                                    >
                                        <v-layout
                                            row
                                            wrap
                                            v-if="reward.images.length > 1"
                                        >
                                            <div
                                                v-for="(image, index) in reward.images"
                                                :key="`campaign_reward_images_sub_${index}`"
                                            >
                                                <v-flex
                                                    v-if="index > 0"
                                                    @click="
                                                        gallery_images = reward.images;
                                                        selected_gallery_image = index;
                                                    "
                                                    xs3
                                                    d-flex
                                                    :class="{
                                                        xs12: reward.image_grid_size === 1,
                                                        xs6: reward.image_grid_size === 2,
                                                        xs4: reward.image_grid_size === 3,
                                                        xs3: reward.image_grid_size === 4,
                                                        xs2: reward.image_grid_size === 6,
                                                    }"
                                                >
                                                    <v-card
                                                        class="d-flex"
                                                        flat
                                                        tile
                                                    >
                                                        <v-img
                                                            :src="image.thumb"
                                                            aspect-ratio="1"
                                                            style="cursor: pointer;"
                                                        >
                                                            <template
                                                                v-slot:placeholder
                                                            >
                                                                <v-layout
                                                                    fill-height
                                                                    align-center
                                                                    justify-center
                                                                    ma-0
                                                                >
                                                                    <v-progress-circular
                                                                        :style="{
                                                                            color: campaign.theme.textColor
                                                                        }"
                                                                        indeterminate
                                                                    />
                                                                </v-layout>
                                                            </template>
                                                        </v-img>
                                                    </v-card>
                                                </v-flex>
                                            </div>
                                        </v-layout>
                                    </v-container>

                                    <v-card-title primary-title class="pt-0">
                                        <div style="width:100%">
                                            <div
                                                class="mb-2 title"
                                                v-html="reward.title"
                                            />

                                            <div
                                                style="opacity:0.75"
                                                class="mb-1 subtitle-2"
                                            >
                                                <div
                                                    class="d-flex justify-space-between"
                                                >
                                                    <div>
                                                        <v-icon
                                                            size="15"
                                                            style="position: relative; top:-0.5px;"
                                                            :style="{
                                                                color: campaign.theme.textColor
                                                            }"
                                                        >
                                                            toll
                                                        </v-icon>

                                                        <span
                                                            v-html="
                                                                $t('n_points', {
                                                                    points: formatNumber(reward.points)
                                                                })
                                                            "
                                                        />
                                                    </div>

                                                    <span>
                                                        {{ campaign.rewards.rewardValueSymbol }}
                                                        {{ numberFormat(reward.reward_value, campaign.rewards.FractionDigits)}}
                                                        {{ campaign.rewards.rewardValueCurrency }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div
                                                style="opacity:0.75"
                                                class="subtitle-2"
                                                v-if="
                                                    typeof reward.expires !== 'undefined' &&
                                                        reward.expires !== null &&
                                                        reward.expiresInMonths <= 1
                                                "
                                            >
                                                <v-icon
                                                    size="15"
                                                    :style="{
                                                        color: campaign.theme.textColor,
                                                        position: 'relative',
                                                        top: '-0.5px',
                                                    }"
                                                >
                                                    date_range
                                                </v-icon>

                                                <span
                                                    v-html="'Expires ' + getDate(reward.expires)"
                                                />
                                            </div>
                                        </div>
                                    </v-card-title>

                                    <v-card-actions
                                        v-if="Object.keys(campaign.redeemOptions).length > 0"
                                        class="mx-2 mb-2"
                                    >
                                        <v-btn
                                            v-if="! $auth.check()"
                                            color="light"
                                            block
                                            large
                                            :to="{ name: 'login' }"
                                        >
                                            {{ $t("log_in_to_redeem") }}
                                        </v-btn>

                                        <v-menu
                                            v-if="$auth.check()"
                                            bottom
                                            transition="slide-y-transition"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-btn
                                                    v-on="on"
                                                    block
                                                    large
                                                    :loading="! points"
                                                    :disabled="(points / reward.points) * 100 < 100"
                                                >
                                                    <span
                                                        v-if="points >= reward.points"
                                                    >
                                                        {{ $t("redeem") }}
                                                    </span>

                                                    <span
                                                        v-if="points < reward.points"
                                                    >
                                                        {{
                                                            $t("you_need_n_more_points", {
                                                                points: formatNumber(reward.points - points)
                                                            })
                                                        }}
                                                    </span>
                                                </v-btn>
                                            </template>

                                            <v-list dense three-line subheader>
                                                <v-subheader>
                                                    {{ $t("redeem_this_reward") }}
                                                </v-subheader>

                                                <div
                                                    :key="`campaign_reward_redeem_${index}`"
                                                    v-for="(item, index) in redeemOptions"
                                                    :class="{
                                                        'mb-2': index === Object.keys(campaign.redeemOptions).length - 1
                                                    }"
                                                >
                                                    <v-list-item
                                                        v-if="item.active"
                                                        three-line
                                                        @click="
                                                            selectedReward = reward.uuid;
                                                            dialog.redeem[item.id] = $auth.check()
                                                            dialog.authRequired = ! $auth.check()
                                                        "
                                                    >
                                                        <v-list-item-avatar>
                                                            <v-icon>
                                                                {{ item.icon }}
                                                            </v-icon>
                                                        </v-list-item-avatar>

                                                        <v-list-item-content>
                                                            <v-list-item-title>
                                                                {{ item.title }}
                                                            </v-list-item-title>

                                                            <v-list-item-subtitle>
                                                                {{ item.description }}
                                                            </v-list-item-subtitle>
                                                        </v-list-item-content>
                                                    </v-list-item>

                                                    <v-divider
                                                        v-if="index < Object.keys(campaign.redeemOptions).length"
                                                        class="my-2 grey lighten-2"
                                                        inset
                                                    />
                                                </div>
                                            </v-list>
                                        </v-menu>
                                    </v-card-actions>

                                    <v-card-text class="pt-0 pb-1 mt-0 mb-0">
                                        <div
                                            class="body-1"
                                            v-html="reward.description"
                                        />
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-flex>
            </v-layout>
        </v-container>

        <!-- Login dialog --------------------------------------------------------------------------------------------------------------------------------- -->

        <v-dialog
            v-model="dialog.authRequired"
            persistent
            max-width="360"
        >
            <v-card>
                <v-card-text class="pt-5 title">
                    {{ $t("log_in_to_use_this_feature") }}
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        color="secondary"
                        text
                        @click="dialog.authRequired = false"
                    >
                        {{ $t("close") }}
                    </v-btn>

                    <v-btn color="primary" :to="{ name: 'login' }">
                        {{ $t("log_in") }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Claim QR --------------------------------------------------------------------------------------------------------------------------------- -->

        <v-dialog
            v-model="dialog.redeem.qr"
            persistent
            max-width="360"
        >
            <v-card>
                <v-card-title class="headline">
                    {{ $t("show_qr_to_merchant") }}
                </v-card-title>

                <v-card-text
                    v-if="
                        dialog.redeem.qrVisible &&
                        ! connectionError &&
                        ! rewardRedeemed
                    "
                >
                    <p class="body-1">
                        {{ $t("keep_dialog_open_until_confirmation") }}
                    </p>

                    <div class="text-center">
                        <qrcode-vue
                            :value="dialog.redeem.qrToken"
                            :size="256"
                            level="H"
                        />
                    </div>
                </v-card-text>

                <v-card-text v-if="rewardRedeemed">
                    <p
                        class="body-1"
                        v-html="
                            $t('reward_successfully_redeemed', {
                                rewardRedeemed: '<strong>' + rewardRedeemed + '</strong>'
                            })
                        "
                    />

                    <p
                        class="body-1"
                        v-html="$t('find_rewards_history_tab')"
                    />
                </v-card-text>

                <v-card-text v-if="connectionError">
                    <p class="body-1">
                        A connection error has occured ({{ connectionError }}).
                        Please close this dialog and try again.
                    </p>
                </v-card-text>

                <v-card-text v-if="! dialog.redeem.qrVisible">
                    <v-layout
                        style="height: 303px"
                        fill-height
                        align-center
                        justify-center
                    >
                        <v-progress-circular
                            :size="64"
                            indeterminate
                            :style="{ color: campaign.theme.textColor }"
                        />
                    </v-layout>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        color="secondary"
                        text
                        @click="closeQrDialog"
                    >
                        {{ $t("close") }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Claim - Merchant Enters Code --------------------------------------------------------------------------------------------------------------------------------- -->

        <v-dialog
            v-model="dialog.redeem.merchant"
            persistent
            max-width="360"
        >
            <v-card>
                <v-card-title class="headline">
                    {{ $t("let_merchant_enter_code") }}
                </v-card-title>

                <v-form
                    v-show="! merchantCode.verfied"
                    data-vv-scope="merchantCode"
                    :model="merchantCode"
                    @submit.prevent="verifyMerchantCode"
                    autocomplete="off"
                    method="post"
                    accept-charset="UTF-8"
                >
                    <v-card-text>
                        <p class="body-1">
                            {{ $t("hand_over_device_to_merchant") }}
                        </p>

                        <v-text-field
                            v-model="merchantCode.code"
                            data-vv-name="code"
                            :data-vv-as="$t('code')"
                            :type="
                                dialog.redeem.showMerchantCode
                                    ? 'text'
                                    : 'password'
                            "
                            :append-icon="
                                dialog.redeem.showMerchantCode
                                    ? 'visibility'
                                    : 'visibility_off'
                            "
                            @click:append="
                                dialog.redeem.showMerchantCode = !dialog.redeem
                                    .showMerchantCode
                            "
                            v-validate="'required|max:64'"
                            :error-messages="
                                errors.collect('merchantCode.code')
                            "
                            outline
                            :label="$t('enter_code_here')"
                            class="title"
                        />
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            type="submit"
                            color="primary"
                            :loading="merchantCode.loading"
                            :disabled="merchantCode.verfied"
                        >
                            {{ $t("verify") }}
                        </v-btn>

                        <v-btn
                            color="secondary"
                            text
                            @click="closeMerchantCode"
                        >
                            {{ $t("close") }}
                        </v-btn>
                    </v-card-actions>
                </v-form>

                <v-form
                    v-if="merchantCode.verfied && !merchantCode.processed"
                    data-vv-scope="merchantCodeVerified"
                    :model="merchantCodeVerified"
                    @submit.prevent="processMerchantCode"
                    autocomplete="off"
                    method="post"
                    accept-charset="UTF-8"
                >
                    <v-card-text>
                        <p
                            class="body-1"
                            v-html="$t('code_correct_select_reward')"
                        />

                        <v-autocomplete
                            v-model="merchantCodeVerified.reward"
                            :items="rewards"
                            item-value="uuid"
                            item-text="title_with_points"
                            :label="$t('reward')"
                            :data-vv-as="$t('reward')"
                            data-vv-name="reward"
                            hide-no-data
                            hide-selected
                            prepend-inner-icon="fas fa-gift"
                            v-validate="'required'"
                            :error-messages="errors.collect('merchantCodeVerified.reward')"
                        />

                        <v-autocomplete
                            v-if="Object.keys(segments).length > 0"
                            v-model="merchantCodeVerified.segments"
                            :items="segments"
                            item-value="0"
                            item-text="1"
                            :label="$t('segments') + ' ' + $t('_optional_')"
                            :data-vv-as="$t('segments')"
                            hide-no-data
                            hide-selected
                            chips
                            multiple
                            prepend-inner-icon="category"
                            deletable-chips
                        />
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            type="submit"
                            color="primary"
                            :loading="merchantCodeVerified.loading"
                            :disabled="merchantCode.processed"
                        >
                            {{ $t("redeem_reward") }}
                        </v-btn>

                        <v-btn
                            color="secondary"
                            text
                            @click="closeMerchantCode"
                        >
                            {{ $t("close") }}
                        </v-btn>
                    </v-card-actions>
                </v-form>

                <div v-if="merchantCode.verfied && merchantCode.processed">
                    <v-card-text>
                        <p
                            class="body-1"
                            v-html="
                                $t('reward_successfully_redeemed', {
                                    rewardRedeemed: '<strong>' + redeemedReward + '</strong>'
                                })
                            "
                        />

                        <p
                            class="body-1"
                            v-html="$t('find_rewards_history_tab')"
                        />
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            color="secondary"
                            text
                            @click="closeMerchantCode"
                        >
                            {{ $t("close") }}
                        </v-btn>
                    </v-card-actions>
                </div>
            </v-card>
        </v-dialog>

        <!-- Claim - Provide Customer Number --------------------------------------------------------------------------------------------------------------------------------- -->

        <v-dialog
            v-if="user && user.number"
            v-model="dialog.redeem.customerNumber"
            persistent
            max-width="360"
        >
            <customer-number :open.sync="dialog.redeem.customerNumber" />
        </v-dialog>

        <!-- Claim - Provide Card Number --------------------------------------------------------------------------------------------------------------------------------- -->

        <v-dialog
            v-if="user && user.card_number"
            v-model="dialog.redeem.customerCard"
            width="600px"
            max-width="600px"
            content-class="rounded-xl"
        >
            <customer-card-number />
        </v-dialog>

        <!-- Claim - Provide OTP confirmation ------------------------------------------------------------------------------------------------------------------------------ -->

        <v-dialog
            v-model="dialog.redeem.otpConfirmation"
            persistent
            max-width="360"
        >
            <v-card>
                <v-card-title class="headline">
                    {{ $t("customer_otp_confirmation")}}
                </v-card-title>

                <v-form
                    v-if="!otp.confirmed"
                    data-vv-scope="otp"
                    :model="otp"
                    @submit.prevent="processOtpCode"
                    autocomplete="off"
                    method="post"
                    accept-charset="UTF-8"
                >
                    <v-card-text>
                        <p class="body-1">
                            {{ $t("check_email_for_confirmation") }}
                        </p>

                        <v-text-field
                            type="number"
                            class="title"
                            outlined
                            id="code"
                            data-vv-name="code"
                            v-model="otp.code"
                            placeholder="Confirmation code"
                            v-validate="'required|min:6|max:6'"
                            :error-messages="errors.collect('otp.code')"
                        />

                        <a
                            class="resend-otp"
                            :class="otp.time !== 60 ? 'disable-resend-otp' : ''"
                            ref="resendOtp"
                            @click="resendOtpCode"
                        >
                            {{ otp.resend_text }}
                        </a>

                        <v-spacer></v-spacer>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            type="submit"
                            color="primary"
                            :loading="otp.loading"
                        >
                            {{ $t("submit") }}
                        </v-btn>

                        <v-btn
                            color="secondary"
                            :disabled="otp.loading"
                            text
                            @click="dialog.redeem.otpConfirmation = false"
                        >
                            {{ $t("close") }}
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import QrcodeVue from 'qrcode.vue';
import CustomerNumber from '../components/ui/CustomerNumber.vue';
import CustomerCardNumber from '../components/ui/CustomerCardNumber.vue';

import { copyElById, copyToClipboard } from "../../utils/helpers";

import Store from "../../../app/views/app/admin/settings/store";

export default {
    components: { Store, QrcodeVue, CustomerNumber, CustomerCardNumber },
    $_veeValidate: {
        validator: "new",
    },
    data() {
        return {
            locale: "en",
            points: null,
            segments: [],
            rewards: [],
            campaignRewardsList: [],
            socket: null,
            connectionError: false,
            selectedReward: null,
            rewardRedeemed: null,
            redeemedReward: null,
            gallery_images: [],
            selected_gallery_image: null,
            dialog: {
                authRequired: false,
                redeem: {
                    qr: false,
                    qrVisible: false,
                    qrUrl: "",
                    qrToken: "",
                    merchant: false,
                    showMerchantCode: false,
                    customerNumber: false,
                    customerCard: false,
                    otpConfirmation: false
                }
            },
            merchantCode: {
                loading: false,
                verfied: false,
                processed: false,
                code: ""
            },
            merchantCodeVerified: {
                loading: false,
                reward: "",
                code: ""
            },
            otp: {
                loading: false,
                code: "",
                confirmed: false,
                time: 60,
                timer: null,
                resend_text: "Resend OTP"
            }
        };
    },
    mounted() {
        let locale = Intl.DateTimeFormat().resolvedOptions().locale || "en";
        locale = this.$auth.check() ? this.$auth.user().locale : locale;
        this.locale = locale;

        moment.locale(this.locale.substr(0, 2));

        axios
            .get("/campaign/points", {
                params: {
                    locale: this.$i18n.locale,
                    uuid: this.$store.state.app.campaign.uuid
                }
            })
            .then(response => {
                this.points = response.data;
            })
            .catch(err => {
                // console.log(err.response);
            });

        axios
            .get("/campaign/rewards", {
                params: {
                    locale: this.$i18n.locale,
                    uuid: this.campaign.uuid,
                    customer_type: this.customerType,
                }
            })
            .then(response => {
                this.campaignRewardsList = response.data.rewards;
            })
            .catch(err => {
                // console.log(err.response);
            });

    },
    computed: {
        user() {
            return this.$auth.user();
        },
        customerType() {
            return this.user ? this.user.role : null;
        },
        campaign() {
            return this.$store.state.app.campaign;
        },
        redeemOptions() {
            let claims = [
                {
                    active: _.indexOf(this.campaign.redeemOptions, "qr") >= 0,
                    id: "qr",
                    icon: "fas fa-qrcode",
                    title: this.$t("qr_code"),
                    description: this.$t("qr_code_info")
                },
                {
                    active: _.indexOf(this.campaign.redeemOptions, "merchant") >= 0,
                    id: "merchant",
                    icon: "fas fa-hand-holding",
                    title: this.$t("merchant_enters_code"),
                    description: this.$t("merchant_enters_code_info")
                },
                {
                    active: _.indexOf(this.campaign.redeemOptions, "customerNumber") >= 0,
                    id: "customerNumber",
                    icon: "card_giftcard",
                    title: this.$t("customer_number"),
                    description: this.$t("customer_number_info")
                },
            ];

            if (this.user && this.user.card_number) {
                claims.push({
                    active: _.indexOf(this.campaign.claimOptions, "customerCard") >= 0,
                    id: "customerCard",
                    icon: "fas fa-credit-card",
                    title: this.$t("customer_card"),
                    description: this.$t("customer_card_points_info")
                });
            }

            return claims;
        }
    },
    watch: {
        "dialog.redeem.qr": function(val) {
            if (val === true) {
                this.dialog.redeem.qrVisible = false;

                this.generateRedeemToken();
            }
        }
    },
    methods: {
        copyElById,
        copyToClipboard,
        separatorCardNumber(value = '') {
            return value.trim().replaceAll('-', ' ').toString();
        },
        formatNumber(number) {
            return new Intl.NumberFormat(this.locale.replace("_", "-")).format(
                number
            );
        },
        getDate(date) {
            return moment(date).format("ll");
        },
        closeQrDialog() {
            this.socket.disconnect();
            this.dialog.redeem.qr = false;
            this.connectionError = false;
        },
        generateRedeemToken() {
            axios
                .post("/campaign/get-redeem-reward-token", {
                    locale: this.$i18n.locale,
                    uuid: this.$store.state.app.campaign.uuid,
                    reward: this.selectedReward
                })
                .then(response => {
                    if (response.data.status === "success") {
                        let that = this;
                        let token = response.data.token;
                        let root = this.campaign.root;
                        let scheme = this.campaign.scheme;
                        let url = scheme + "://" + root + "/staff#/rewards/link?token=" + token;

                        this.dialog.redeem.qrUrl = url;
                        this.dialog.redeem.qrToken = token;
                        this.dialog.redeem.qrVisible = true;

                        if (
                            this.socket === null ||
                            this.socket.connection.state == "disconnected"
                        ) {
                            // Enable pusher logging - don't include this in production
                            //Pusher.logToConsole = true

                            //let channel_id = Math.random().toString(36).substr(2, 9)
                            //let csrfToken = document.head.querySelector('meta[name="csrf-token"]').content

                            this.socket = new Pusher(
                                window.initConfig.pusher.key,
                                {
                                    cluster:
                                        window.initConfig.pusher.options
                                            .cluster,
                                    forceTLS:
                                        window.initConfig.pusher.options
                                            .encrypted
                                }
                            );

                            let channel = this.socket.subscribe(token);

                            this.socket.connection.bind("error", function(err) {
                                this.connectionError = err.error.data.code;
                            });

                            channel.bind("redeemed", function(data) {
                                that.rewardRedeemed = data.reward;
                                that.points = data.points;
                            });
                        }
                    } else {
                        //
                    }
                })
                .catch(error => {
                    // Error
                });
        },
        closeMerchantCode() {
            this.dialog.redeem.merchant = false;
            this.segments = [];
            this.rewards = [];
            this.merchantCode.code = "";
            this.merchantCode.verfied = false;
            this.merchantCode.processed = false;
        },
        verifyMerchantCode() {
            this.merchantCode.loading = true;
            // validation
            this.$validator.validateAll("merchantCode").then(valid => {
                if (!valid) {
                    this.merchantCode.loading = false;
                    return false;
                } else {
                    axios
                        .post("/campaign/reward/verify-merchant-code", {
                            locale: this.$i18n.locale,
                            uuid: this.$store.state.app.campaign.uuid,
                            code: this.merchantCode.code
                        })
                        .then(response => {
                            if (response.data.status === "success") {
                                this.rewards = response.data.rewards;
                                this.segments = _.toPairs(
                                    response.data.segments
                                );
                                this.merchantCode.loading = false;
                                this.merchantCode.verfied = true;
                                this.merchantCodeVerified.code = this.merchantCode.code;
                                this.merchantCodeVerified.reward = this.selectedReward;
                            }
                        })
                        .catch(err => {
                            let errors = err.response.data.errors || {};

                            for (let field in errors) {
                                this.$validator.errors.add({
                                    field: "merchantCode." + field,
                                    msg: errors[field]
                                });
                            }
                            this.merchantCode.loading = false;
                        });
                }
            });
        },
        processMerchantCode() {
            this.merchantCodeVerified.loading = true;
            let reward = this.rewards.find(
                reward => reward.uuid === this.selectedReward
            );
            if (reward) {
                if (reward.requires_validation === 1 && !this.otp.confirmed) {
                    this.otp.loading = true;

                    this.requestOtpCode();
                    return;
                }
            }
            // validation
            this.$validator.validateAll("merchantCodeVerified").then(valid => {
                if (!valid) {
                    this.merchantCodeVerified.loading = false;
                    return false;
                } else {
                    axios
                        .post("/campaign/reward/process-merchant-entry", {
                            locale: this.$i18n.locale,
                            uuid: this.$store.state.app.campaign.uuid,
                            code: this.merchantCodeVerified.code,
                            reward: this.merchantCodeVerified.reward,
                            segments: this.merchantCodeVerified.segments
                        })
                        .then(response => {
                            if (response.data.status === "success") {
                                this.redeemedReward = response.data.reward;
                                this.points = response.data.points;
                                this.merchantCodeVerified.loading = false;
                                this.merchantCode.processed = true;
                                this.otp.confirmed = false;
                            }
                        })
                        .catch(err => {
                            let errors = err.response.data.errors || {};

                            for (let field in errors) {
                                this.$validator.errors.add({
                                    field: "merchantCodeVerified." + field,
                                    msg: errors[field]
                                });
                            }
                            this.merchantCodeVerified.loading = false;
                        });
                }
            });
        },
        resendOtpCode() {
            this.otp.loading = true;
            if (this.otp.time !== 60) {
                return;
            }
            this.requestOtpCode();
        },
        resetOtpTimer() {
            clearInterval(this.otp.timer);
            this.otp.timer = null;
            this.otp.time = 60;
            this.otp.resend_text = "Resend OTP";
        },
        requestOtpCode() {
            axios
                .get("/campaign/auth/otp", {
                    params: {
                        purpose: "reward_redemption",
                        uuid: this.$store.state.app.campaign.uuid
                    }
                })
                .then(() => {
                    this.$root.$snackbar("OTP Sent");
                    this.dialog.redeem.otpConfirmation = true;
                    this.otp.confirmed = false;

                    this.otp.timer = setInterval(() => {
                        this.otp.time--;

                        this.otp.resend_text = `You can request for another OTP in ${this.otp.time} seconds`;
                        if (this.otp.time === 0) {
                            this.resetOtpTimer();
                        }
                    }, 1000);
                })
                .catch(err => {
                    let errors = err.response.data.errors || {};

                    for (let field in errors) {
                        this.$validator.errors.add({
                            field: "otp." + field,
                            msg: errors[field]
                        });
                    }
                })
                .finally(() => {
                    this.otp.loading = false;
                    this.merchantCodeVerified.loading = false;
                });
            return;
        },
        processOtpCode() {
            this.otp.loading = true;
            this.$validator.validateAll("otp").then(valid => {
                if (!valid) {
                    this.otp.loading = false;
                    return false;
                }

                axios
                    .post("/campaign/auth/otp", {
                        purpose: "reward_redemption",
                        code: this.otp.code
                    })
                    .then(() => {
                        this.dialog.redeem.otpConfirmation = false;
                        this.otp.confirmed = true;
                        this.otp.code = "";
                        this.processMerchantCode();
                    })
                    .catch(err => {
                        if (err.response.data.status === 'failed') {
                            this.$validator.errors.add({
                                field: "otp.code",
                                msg: err.response.data.message
                            });
                        } else if (err.response.data.errors) {
                            let errors = err.response.data.errors || {};

                            for (let field in errors) {
                                this.$validator.errors.add({
                                    field: "otp." + field,
                                    msg: errors[field]
                                });
                            }
                        }
                    })
                    .finally(() => {
                        this.otp.loading = false;
                    });
            });
        },
        numberFormat(number, digit) {
            let numberString = number.toString();
            let left = numberString.slice(0, numberString.length - digit);
            let right = numberString.substring(
                numberString.length - digit,
                numberString.length
            );

            return left + "." + right;
        }
    }
};
</script>

<style scoped>
.resend-otp {
    font-size: 10px !important;
}
.disable-resend-otp {
    pointer-events: none;
    color: #aaaaaa !important;
}

@media screen and (max-width: 600px) {
    .credit-card-text {
        font-family: 'Roboto';
        font-size: 18px;
        font-weight: normal;
        color: #DFD5B4;
        line-height: 21px;
        letter-spacing: 0.12em;
    }

    .credit-card-number {
        font-family: 'Roboto';
        font-size: 20px;
        font-weight: normal;
        color: #FFFFFF;
        line-height: 29px;
        letter-spacing: 0.17em;
        cursor: pointer;
    }

    .credit-card-image {
        width: 100%;
        height: 100%;
        max-width: 48px;
        max-height: 48px;
        object-fit: contain;
    }
}

@media screen and (min-width: 601px) {
    .credit-card-text {
        font-family: 'Roboto';
        font-size: 20px;
        font-weight: normal;
        color: #DFD5B4;
        line-height: 23px;
        letter-spacing: 0.12em;
    }

    .credit-card-number {
        font-family: 'Roboto';
        font-size: 40px;
        font-weight: normal;
        color: #FFFFFF;
        line-height: 47px;
        letter-spacing: 0.12em;
        cursor: pointer;
    }

    .credit-card-image {
        width: 100%;
        height: 100%;
        max-width: 104px;
        max-height: 104px;
        object-fit: contain;
    }
}
</style>
