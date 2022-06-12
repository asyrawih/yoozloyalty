<template>
    <div>
        <v-fab-transition>
            <v-btn
                color="pink"
                dark
                absolute
                top
                right
                fab
                @click="onOpen"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </v-fab-transition>

        <v-dialog
            v-model="state.open"
            persistent
            :retain-focus="false"
            :fullscreen="$vuetify.breakpoint.xsOnly"
            width="480"
            @keydown.esc="onClose"
        >
            <v-card>
                <v-overlay
                    :value="state.loading"
                >
                    <v-progress-circular
                        :size="50"
                        color="primary"
                        indeterminate
                        class="ma-5"
                    />
                </v-overlay>

                <v-toolbar flat color="transparent">
                    <v-toolbar-title>
                        Create new customer
                    </v-toolbar-title>

                    <v-spacer></v-spacer>

                    <v-btn icon @click="onClose">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-toolbar>

                <v-form
                    id="frmCustomer"
                    lazy-validation
                    data-vv-scope="form"
                    @submit.prevent="onSubmit"
                >
                    <v-card-text>
                        <v-text-field
                            id="frmCustomerName"
                            label="Name (Required)"
                            color="purple darken-2"
                            v-model="form.name"
                            v-validate="'required|max:32'"
                            data-vv-name="name"
                            data-vv-as="customer name"
                            :error-messages="errors.collect('form.name')"
                        />

                        <v-text-field
                            id="frmCustomerEmail"
                            label="E-mail Address (Required)"
                            color="purple darken-2"
                            v-model="form.email"
                            v-validate="'required|email|max:64'"
                            data-vv-name="email"
                            data-vv-as="customer email"
                            :error-messages="errors.collect('form.email')"
                        />

                        <input
                            type="hidden"
                            v-model="form.country_code"
                        />

                        <input
                            type="hidden"
                            v-model="form.country_isd_code"
                        />

                        <vue-tel-input-vuetify
                            id="frmCustomerNumber"
                            @input="onInputPhone"
                            @country-changed="onCountryChange(form.country_code, 'phone')"
                            mode="international"
                            :defaultCountry="form.country_code"
                            v-model="form.customer_number"
                            name="customer_number"
                            :error-messages="
                                (state.phoneNumberValid
                                    ? ''
                                    : 'Please input a valid phone number') ||
                                    errors.collect(
                                    'form.customer_number'
                                )
                            "
                        />

                        <v-text-field
                            type="number"
                            id="frmCustomerCardNumber"
                            label="Membership Card Number"
                            v-model="form.card_number"
                            v-validate="'required|digits:16'"
                            data-vv-name="card_number"
                            data-vv-as="membership card number"
                            :error-messages="errors.collect('form.card_number')"
                        />

                        <v-text-field
                            :type="
                                state.passwordShow
                                ? 'text'
                                : 'password'
                            "
                            id="frmCustomerPassword"
                            label="Password (Required)"
                            :append-icon="
                                state.passwordShow
                                ? 'visibility'
                                : 'visibility_off'
                            "
                            @click:append="state.passwordShow = ! state.passwordShow"
                            name="password"
                            v-model="form.password"
                            v-validate="{
                                required: false,
                                min: 8,
                                regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!?])(?=.*[0-9]).*$/
                            }"
                            data-vv-name="password"
                            data-vv-as="password"
                            :error-messages="
                                errors.collect(
                                    'form.password')[0] === 'The password field format is invalid'
                                        ? 'Password should have 1 Capital , 1 Small letter , Number and a special symbol'
                                        : errors.collect('form.password')
                            "
                        />

                        <v-img
                            v-if="form['avatar_media_url']"
                            :src="form['avatar_media_url']"
                            class="mt-3 mb-1 img-rounded my-3"
                            :style="{width: '140px', height: '140px'}"
                            contain
                        />

                        <v-text-field
                            @click="onFilePick('frmCustomerAvatar')"
                            type="text"
                            readonly
                            v-model="form.avatar_media_name"
                            data-vv-name="avatar"
                            data-vv-as="avatar"
                            label="Avatar (Optional)"
                            :error-messages="errors.collect('form.avatar')"
                        >
                            <template slot="append">
                                <v-icon
                                    v-if="form.avatar_media_name != ''"
                                    @click="
                                        form.avatar = null;
                                        form.avatar_media_name = '';
                                        form.avatar_media_url = '';
                                        form.avatar_media_changed = true;
                                    "
                                >
                                    delete
                                </v-icon>
                            </template>
                        </v-text-field>

                        <input
                            type="file"
                            id="frmCustomerAvatar"
                            style="display: none"
                            name="avatar"
                            accept=".png, .jpg"
                            @change="onFilePicked"
                        />

                        <v-checkbox
                            type="checkbox"
                            class="mt-0"
                            label="Active"
                            name="active"
                            v-model="form.active"
                            data-vv-name="active"
                            data-vv-as="active"
                            :error-messages="errors.collect('form.active')"
                        />
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            type="submit"
                            color="primary"
                            large
                            text
                        >
                            {{ $t("save") }}
                        </v-btn>

                        <v-btn
                            color="secondary"
                            text
                            large
                            :disabled="state.loading"
                            @click="onClose"
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
import { computed, defineComponent, reactive } from '@vue/composition-api';

import ServiceCustomerApi from '../services/CustomerApi';

export default defineComponent({
    name: "CustomerDataForm",
    setup(props, { root }) {
        const state = reactive({
            loading: false,
            open: false,
            countryChanged: false,
            passwordShow: false,
            phoneNumberValid: true,
        });

        const form = reactive({
            name: '',
            email: '',
            country_code: '',
            country_isd_code: '',
            customer_number: '',
            card_number: '',
            password: '',
            avatar: null,
            avatar_media_name: '',
            avatar_media_url: '',
            avatar_media_changed: false,
            active: false,
        });

        const campaign = computed(() => root.$store.state.app.campaign);

        const onOpen = () => {
            state.open = true;
        }

        const onClose = () => {
            state.open = false;
        }

        const onInputPhone = (formattedNumber, { number, isValid, country }) => {
            state.phoneNumberValid = isValid;
            form.country_isd_code = country.dialCode;
            form.country_code = country.iso2;
        }

        const onCountryChange = (countryCode, field) => {
            if (countryCode && state.countryChanged) {
                form[field] = "";
            } else {
                form[field] = form[field];
                state.countryChanged = true;
            }
        };

        const onFilePick = (field) => {
            document.getElementById(field).click();
        };

        const onFilePicked = (event) => {
            const files = event.target.files;

            form.avatar = null;
            form.avatar_media_name = '';
            form.avatar_media_url = '';
            form.avatar_media_file = '';
            form.avatar_media_changed = true;

            if (files[0] !== undefined) {
                form.avatar = files[0];
                form.avatar_media_name = files[0].name;

                if (form.avatar_media_name.lastIndexOf('.') <= 0) {
                    return;
                }

                const fileReader = new FileReader();

                fileReader.readAsDataURL(files[0]);

                fileReader.addEventListener('load', () => {
                    form.avatar_media_url = fileReader.result;
                    form.avatar_media_file = files[0]; // this is an image file that can be sent to server...
                    form.avatar_media_changed = true;
                });
            }
        };

        return {
            state,
            form,
            campaign,
            onOpen,
            onClose,
            onInputPhone,
            onCountryChange,
            onFilePick,
            onFilePicked,
        };
    },
    methods: {
        clearForm() {
            let form = this.form;

            this.state.phoneNumberValid = true;

            form.name = '';
            form.email = '';
            form.country_code = '';
            form.country_isd_code = '';
            form.customer_number = '';
            form.card_number = '';
            form.password = '';
            form.avatar = null;
            form.avatar_media_name = '';
            form.avatar_media_url = '';
            form.avatar_media_changed = false;
            form.active = false;

            this.$validator.reset();
        },
        async onSubmit() {
            this.state.loading = true;

            const validate = await this.$validator.validateAll('form');

            if (! validate) {
                this.state.loading = false;

                return false;
            }

            let formData = new FormData();

            if (this.form.avatar) {
                formData.append('avatar', this.form.avatar);
                formData.append('avatar_media_name', this.form.avatar_media_name);
                formData.append('avatar_media_url', this.form.avatar_media_url);
                formData.append('avatar_media_changed', this.form.avatar_media_changed);
            }

            formData.append('locale', this.$i18n.locale);
            formData.append('uuid', this.campaign.uuid);
            formData.append('name', this.form.name);
            formData.append('email', this.form.email);
            formData.append('country_code', this.form.country_code);
            formData.append('country_isd_code', this.form.country_isd_code);
            formData.append('customer_number', this.form.customer_number);
            formData.append('card_number', this.form.card_number);
            formData.append('password', this.form.password);
            formData.append('active', this.form.active);

            try {
                const response = await ServiceCustomerApi().store(formData);

                if (response.status === 'success') {
                    this.clearForm();
                    this.$emit('onSuccess', response.message);
                    this.state.open = false;
                }

            } catch(exception) {
                if (exception.status === 'error' && exception.errors) {
                    for (let field in exception.errors) {
                        this.$validator.errors.add({
                            field: `form.${field}`,
                            msg: exception.errors[field][0]
                        });
                    }
                } else {
                    this.$emit('onError', exception.message);
                }
            } finally {
                this.state.loading = false;
            }
        }
    }
});
</script>

<style scoped>
</style>
