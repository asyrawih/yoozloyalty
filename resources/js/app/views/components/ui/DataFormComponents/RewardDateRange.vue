<template>
    <div>
        <div v-if="campaigns.length == 0">
            Currently there is no Campaign / Website attached to this Reward.<br>
            You can edit reward date range after you assign this reward to any Campaign.
        </div>
        <div v-else>
            <p v-if="campaigns.length > 1">You can set different date range for every campaigns applying this Reward.</p>
            <v-simple-table>
                <template v-slot:default>
                    <thead>
                        <tr>
                            <th class="text-left">
                                Store Name / Campaign
                            </th>
                            <th class="text-left">
                                Timezone
                            </th>
                            <th class="text-center">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="campaigns.length > 0">
                            <tr
                                v-for="(campaign, index) in campaigns"
                                :key="'campaign-' + index"
                            >
                                <td class="text-left">
                                    {{ campaign.store }}<br>
                                    ({{ campaign.name }})
                                </td>
                                <td class="text-left">
                                    {{ campaign.store_timezone }}
                                </td>
                                <td class="text-center">
                                    <v-btn small @click="editDateRange(index)">
                                        Edit Date Range
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="4">
                                <p class="text-muted mt-2 text-center">
                                    Currently there is no Campaign / Website attached to this Reward.<br>
                                    You can edit reward date range after you assign this reward to any Campaign.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </template>
            </v-simple-table>

            <v-dialog v-model="dialogDateRange" width="500" persistent>
                <v-card>
                    <v-card-title>
                        Edit Reward Date Range
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="mt-3">
                        <v-text-field
                            type="text"
                            v-model="formDateRange.name"
                            label="Campaign Name"
                            readonly
                        ></v-text-field>
                        <v-text-field
                            type="text"
                            v-model="formDateRange.store_timezone"
                            label="Timezone"
                            readonly
                        ></v-text-field>
                        <div>
                            <date-time-picker
                                v-model="formDateRange.active_from"
                                :ref="'active_from'"
                                :data-vv-name="'active_from'"
                                :data-vv-as="'expires at'"
                                v-validate="'required'"
                                :label="'Active from (Required)'"
                                :error-messages="errorActiveFrom"
                                :prepend-inner-icon="'calendar_today'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                :format="'LLLL'"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn
                                        :color="'#304ffe'"
                                        text
                                        @click="parent.okHandler"
                                        >OK</v-btn
                                    >
                                    <v-btn
                                        color="red"
                                        text
                                        @click.native="
                                            parent.clearHandler
                                        "
                                        >Clear</v-btn
                                    >
                                    <v-btn
                                        color="secondary"
                                        text
                                        @click.native="
                                            parent.display = false
                                        "
                                        >Close</v-btn
                                    >
                                </template>
                            </date-time-picker>
                        </div>
                        <div>
                            <date-time-picker
                                v-model="formDateRange.expires_at"
                                :ref="'expires_at'"
                                :data-vv-name="'expires_at'"
                                :data-vv-as="'expires at'"
                                v-validate="'required'"
                                :label="'Expires at (Required)'"
                                :error-messages="errorExpiresAt"
                                :prepend-inner-icon="'calendar_today'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                :format="'LLLL'"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn
                                        :color="'#304ffe'"
                                        text
                                        @click="parent.okHandler"
                                        >OK</v-btn
                                    >
                                    <v-btn
                                        color="red"
                                        text
                                        @click.native="
                                            parent.clearHandler
                                        "
                                        >Clear</v-btn
                                    >
                                    <v-btn
                                        color="secondary"
                                        text
                                        @click.native="
                                            parent.display = false
                                        "
                                        >Close</v-btn
                                    >
                                </template>
                            </date-time-picker>
                        </div>
                        <div>
                            <label
                                style="
                                    color:rgba(0, 0, 0, 0.6);
                                    height: 20px;
                                    line-height: 20px;
                                    letter-spacing: normal;
                                "
                            >
                                Active Happy Hour
                            </label>
                        </div>
                        <div class="checkbox-container">
                            <v-checkbox
                                type="checkbox"
                                v-model="formDateRange.active_monday"
                                :ref="'active_monday'"
                                :data-vv-name="'active_monday'"
                                :data-vv-as="'monday'"
                                :label="'Monday'"
                                :error-messages="errors.collect('formDateRange.active_monday')"
                                class="mt-0"
                            ></v-checkbox>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_monday_from"
                                :ref="'active_monday_from'"
                                :data-vv-name="'active_monday_from'"
                                :data-vv-as="'active monday from'"
                                :label="'From Time'"
                                :error-messages="errors.collect('formDateRange.active_monday_from')"
                                :placeholder="'From (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_monday_to"
                                :ref="'active_monday_to'"
                                :data-vv-name="'active_monday_to'"
                                :data-vv-as="'active monday to'"
                                :label="'To Time'"
                                :error-messages="errors.collect('formDateRange.active_monday_to')"
                                :placeholder="'To (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="checkbox-container">
                            <v-checkbox
                                type="checkbox"
                                v-model="formDateRange.active_tuesday"
                                :ref="'active_tuesday'"
                                :data-vv-name="'active_tuesday'"
                                :data-vv-as="'tuesday'"
                                :label="'Tuesday'"
                                :error-messages="errors.collect('formDateRange.active_tuesday')"
                                class="mt-0"
                            ></v-checkbox>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_tuesday_from"
                                :ref="'active_tuesday_from'"
                                :data-vv-name="'active_tuesday_from'"
                                :data-vv-as="'active tuesday from'"
                                :label="'From Time'"
                                :error-messages="errors.collect('formDateRange.active_tuesday_from')"
                                :placeholder="'From (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_tuesday_to"
                                :ref="'active_tuesday_to'"
                                :data-vv-name="'active_tuesday_to'"
                                :data-vv-as="'active tuesday to'"
                                :label="'To Time'"
                                :error-messages="errors.collect('formDateRange.active_tuesday_to')"
                                :placeholder="'To (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="checkbox-container">
                            <v-checkbox
                                type="checkbox"
                                v-model="formDateRange.active_wednesday"
                                :ref="'active_wednesday'"
                                :data-vv-name="'active_wednesday'"
                                :data-vv-as="'wednesday'"
                                :label="'Wednesday'"
                                :error-messages="errors.collect('formDateRange.active_wednesday')"
                                class="mt-0"
                            ></v-checkbox>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_wednesday_from"
                                :ref="'active_wednesday_from'"
                                :data-vv-name="'active_wednesday_from'"
                                :data-vv-as="'active wednesday from'"
                                :label="'From Time'"
                                :error-messages="errors.collect('formDateRange.active_wednesday_from')"
                                :placeholder="'From (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_wednesday_to"
                                :ref="'active_wednesday_to'"
                                :data-vv-name="'active_wednesday_to'"
                                :data-vv-as="'active wednesday to'"
                                :label="'To Time'"
                                :error-messages="errors.collect('formDateRange.active_wednesday_to')"
                                :placeholder="'To (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="checkbox-container">
                            <v-checkbox
                                type="checkbox"
                                v-model="formDateRange.active_thursday"
                                :ref="'active_thursday'"
                                :data-vv-name="'active_thursday'"
                                :data-vv-as="'thursday'"
                                :label="'Thursday'"
                                :error-messages="errors.collect('formDateRange.active_thursday')"
                                class="mt-0"
                            ></v-checkbox>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_thursday_from"
                                :ref="'active_thursday_from'"
                                :data-vv-name="'active_thursday_from'"
                                :data-vv-as="'active thursday from'"
                                :label="'From Time'"
                                :error-messages="errors.collect('formDateRange.active_thursday_from')"
                                :placeholder="'From (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_thursday_to"
                                :ref="'active_thursday_to'"
                                :data-vv-name="'active_thursday_to'"
                                :data-vv-as="'active thursday to'"
                                :label="'To Time'"
                                :error-messages="errors.collect('formDateRange.active_thursday_to')"
                                :placeholder="'To (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="checkbox-container">
                            <v-checkbox
                                type="checkbox"
                                v-model="formDateRange.active_friday"
                                :ref="'active_friday'"
                                :data-vv-name="'active_friday'"
                                :data-vv-as="'friday'"
                                :label="'Friday'"
                                :error-messages="errors.collect('formDateRange.active_friday')"
                                class="mt-0"
                            ></v-checkbox>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_friday_from"
                                :ref="'active_friday_from'"
                                :data-vv-name="'active_friday_from'"
                                :data-vv-as="'active friday from'"
                                :label="'From Time'"
                                :error-messages="errors.collect('formDateRange.active_friday_from')"
                                :placeholder="'From (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_friday_to"
                                :ref="'active_friday_to'"
                                :data-vv-name="'active_friday_to'"
                                :data-vv-as="'active friday to'"
                                :label="'To Time'"
                                :error-messages="errors.collect('formDateRange.active_friday_to')"
                                :placeholder="'To (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div>
                            <label
                                style="
                                    color:rgba(0, 0, 0, 0.6);
                                    height: 20px;
                                    line-height: 20px;
                                    letter-spacing: normal;
                                "
                            >
                                Active on Weekend
                            </label>
                        </div>
                        <div class="checkbox-container">
                            <v-checkbox
                                type="checkbox"
                                v-model="formDateRange.active_saturday"
                                :ref="'active_saturday'"
                                :data-vv-name="'active_saturday'"
                                :data-vv-as="'saturday'"
                                :label="'Saturday'"
                                :error-messages="errors.collect('formDateRange.active_saturday')"
                                class="mt-0"
                            ></v-checkbox>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_saturday_from"
                                :ref="'active_saturday_from'"
                                :data-vv-name="'active_saturday_from'"
                                :data-vv-as="'active saturday from'"
                                :label="'From Time'"
                                :error-messages="errors.collect('formDateRange.active_saturday_from')"
                                :placeholder="'From (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_saturday_to"
                                :ref="'active_saturday_to'"
                                :data-vv-name="'active_saturday_to'"
                                :data-vv-as="'active saturday to'"
                                :label="'To Time'"
                                :error-messages="errors.collect('formDateRange.active_saturday_to')"
                                :placeholder="'To (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="checkbox-container">
                            <v-checkbox
                                type="checkbox"
                                v-model="formDateRange.active_sunday"
                                :ref="'active_sunday'"
                                :data-vv-name="'active_sunday'"
                                :data-vv-as="'sunday'"
                                :label="'Sunday'"
                                :error-messages="errors.collect('formDateRange.active_sunday')"
                                class="mt-0"
                            ></v-checkbox>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_sunday_from"
                                :ref="'active_sunday_from'"
                                :data-vv-name="'active_sunday_from'"
                                :data-vv-as="'active sunday from'"
                                :label="'From Time'"
                                :error-messages="errors.collect('formDateRange.active_sunday_from')"
                                :placeholder="'From (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                        <div class="timepicker-container">
                            <time-picker
                                v-model="formDateRange.active_sunday_to"
                                :ref="'active_sunday_to'"
                                :data-vv-name="'active_sunday_to'"
                                :data-vv-as="'active sunday to'"
                                :label="'To Time'"
                                :error-messages="errors.collect('formDateRange.active_sunday_to')"
                                :placeholder="'To (HH:MM)'"
                                :locale="$auth.user().locale.substring(0, 2)"
                                timePickerFormat="ampm"
                                :persistent-hint="true"
                            >
                                <template
                                    slot="actions"
                                    slot-scope="{ parent }"
                                >
                                    <v-btn color="primary" text @click="parent.okHandler">OK</v-btn>
                                    <v-btn color="red" text @click.native="parent.clearHandler">Clear</v-btn>
                                    <v-btn color="secondary" text @click.native="parent.display = false">Close</v-btn>
                                </template>
                            </time-picker>
                        </div>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            :disabled="disabledBtnSubmit"
                            color="primary"
                            text
                            @click="saveDateRange"
                        >
                            Save
                        </v-btn>
                        <v-btn
                            color="secondary"
                            text
                            @click="dialogDateRangeClose"
                        >
                            Close
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </div>
</template>

<script>
window.moment = require('moment-timezone')
export default {
    $_veeValidate: {
        validator: "new"
    },
    data: () => {
        return {
            validDateRange: true,
            campaigns: [],
            dialogDateRange: false,
            disabledBtnSubmit: false,
            formDateRange: {},
            dateActiveFrom: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().split('T').map(function(date){ return date.split('.')[0];}).join(' '),
            dateExpiresAt: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().split('T').map(function(date){ return date.split('.')[0];}).join(' '),
            errorActiveFrom: "",
            errorExpiresAt: "",
            menu1: false,
            rewardId: 0
        }
    },
    props: {
        api: {
            default: "user/reward",
            type: String
        },
        model: {
            default: "Platform\Models\Reward",
            type: String
        },
        uuid: {
            default: null,
            type: String
        },
        mode: {
            default: "crud",
            type: String
        }
    },
    mounted() {
        // https://loyalty.test/api/user/reward/campaigns?locale=en&uuid=2a358ce1-fd96-446e-b700-5f291a7b77bd
        if(this.uuid !== null) {
            axios.get(this.api + '/campaigns', {
                        params: {
                            locale: this.$i18n.locale,
                            model: this.model,
                            uuid: this.uuid,
                            mode: this.mode
                        }
                    })
                .then(response => {
                    this.campaigns = response.data.campaigns
                    this.rewardId = response.data.reward_id
                })
                .catch(err => {
                    console.log(err.response.data)
                })
        }
    },
    methods: {
        editDateRange: function(index) {
            let campaign = this.campaigns[index]
            let activeFrom = new Date(campaign.active_from)
            let expiresAt = new Date(campaign.expires_at)
            this.formDateRange = campaign
            
            if(campaign.active_from !== null) {
                this.formDateRange.active_from = new Date(campaign.active_from);
            }
            if(campaign.expires_at !== null) {
                this.formDateRange.expires_at = new Date(campaign.expires_at);
            }
            this.dialogDateRange = true
        },
        saveDateRange: function() {
            this.disabledBtnSubmit = true

            
            if(this.formDateRange.active_from === null) {
                this.validDateRange = false
                this.errorActiveFrom = "The active from field is required."
            } else {
                this.errorActiveFrom = ""
            }

            if(this.formDateRange.expires_at === null) {
                this.validDateRange = false
                this.errorExpiresAt = "The expires at field is required."
            } else {
                this.errorExpiresAt = ""
            }

            if(this.formDateRange.active_from !== null && this.formDateRange.expires_at !== null) {
                this.validDateRange = true
                let param = this.formDateRange
                let activeFrom = (new Date(param.active_from - (param.active_from).getTimezoneOffset() * 60000)).toISOString().split('T').map(function(date){ return date.split('.')[0];}).join(' ')
                let expiresAt = (new Date(param.expires_at - (param.expires_at).getTimezoneOffset() * 60000)).toISOString().split('T').map(function(date){ return date.split('.')[0];}).join(' ')
                let reward_id = this.rewardId
                axios
                    .post(this.api + "/save-date-range", {
                        reward_id: reward_id,
                        campaign_id: param.campaign_id,
                        active_from: activeFrom,
                        expires_at: expiresAt,
                        active_monday: param.active_monday,
                        active_tuesday: param.active_tuesday,
                        active_wednesday: param.active_wednesday,
                        active_thursday: param.active_thursday,
                        active_friday: param.active_friday,
                        active_saturday: param.active_saturday,
                        active_sunday: param.active_sunday,
                        active_monday_from: param.active_monday_from,
                        active_tuesday_from: param.active_tuesday_from,
                        active_wednesday_from: param.active_wednesday_from,
                        active_thursday_from: param.active_thursday_from,
                        active_friday_from: param.active_friday_from,
                        active_saturday_from: param.active_saturday_from,
                        active_sunday_from: param.active_sunday_from,
                        active_monday_to: param.active_monday_to,
                        active_tuesday_to: param.active_tuesday_to,
                        active_wednesday_to: param.active_wednesday_to,
                        active_thursday_to: param.active_thursday_to,
                        active_friday_to: param.active_friday_to,
                        active_saturday_to: param.active_saturday_to,
                        active_sunday_to: param.active_sunday_to
                    })
                    .then(response => {
                        if (response.status === 200) {
                            this.$root.$snackbar(
                                "Date range is successfuly saved."
                            );
                            this.dialogDateRange = false
                        } else {
                            this.$root.$snackbar(
                                "There was an error while trying to save the date range."
                            );
                        }
                    })
                    .catch(err => {
                        console.log(err.response)

                        this.$root.$snackbar(
                            "There was an error while trying to save the date range."
                        );
                    })
                    .finally(() => {
                        this.disabledBtnSubmit = false
                    });
            } else {
                this.disabledBtnSubmit = false
            }
        },
        dialogDateRangeClose: function() {
            this.dialogDateRange = false
        },
    },
    computed: {
      computedActiveFrom () {
        return this.dateActiveFrom ? moment(this.dateActiveFrom).format('dddd, MMMM D, YYYY H:mm A') : ''
      },
      computedExpiresAt () {
        return this.dateExpiresAt ? moment(this.dateExpiresAt).format('dddd, MMMM Do YYYY') : ''
      },
    }
}
</script>
<style scoped>
    .checkbox-container{ width: 30%; display: inline-block; }
    .timepicker-container{ width: 30%; display: inline-block; }
</style>