<template>
    <v-row>
        <v-col>
            <v-text-field
                id="frmMailFromName"
                class="mb-3"
                label="Mail from name"
                v-model="form.mail_from_name"
                v-validate="'required'"
                data-vv-name="mail_from_name"
                data-vv-as="mail from name"
                :error-messages="errors.collect('smtpservice.mail_from_name')"
            />

            <v-text-field
                id="frmMailFromAddress"
                class="mb-3"
                label="Mail from address"
                v-model="form.mail_from_address"
                v-validate="'required|email'"
                data-vv-name="mail_from_address"
                data-vv-as="mail from address"
                :error-messages="errors.collect('smtpservice.mail_from_address')"
            />

            <v-text-field
                id="frmMailHost"
                class="mb-3"
                label="Mail host"
                v-model="form.mail_host"
                v-validate="'required'"
                data-vv-name="mail_host"
                data-vv-as="mail host"
                :error-messages="errors.collect('smtpservice.mail_host')"
            />

            <v-text-field
                id="frmMailPort"
                class="mb-3"
                label="Mail port"
                v-model="form.mail_port"
                v-validate="'required'"
                data-vv-name="mail_port"
                data-vv-as="mail port"
                :error-messages="errors.collect('smtpservice.mail_port')"
            />

            <v-text-field
                id="frmMailUsername"
                class="mb-3"
                label="Mail username"
                v-model="form.mail_username"
                v-validate="'required'"
                data-vv-name="mail_username"
                data-vv-as="mail username"
                :error-messages="errors.collect('smtpservice.mail_username')"
            />

            <v-text-field
                id="frmMailPassword"
                class="mb-3"
                label="Mail password"
                v-model="form.mail_password"
                v-validate="'required'"
                data-vv-name="mail_password"
                data-vv-as="mail password"
                :error-messages="errors.collect('smtpservice.mail_password')"
            />

            <v-text-field
                id="frmMailEncryption"
                class="mb-3"
                label="Mail encryption"
                v-model="form.mail_encryption"
                v-validate="'required'"
                data-vv-name="mail_encryption"
                data-vv-as="mail encryption"
                :error-messages="errors.collect('smtpservice.mail_encryption')"
            />
        </v-col>
    </v-row>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';

export default defineComponent({
    name: 'smtp-service',
    setup() {
        const state = reactive({});

        const form = reactive({
            mail_from_name: '',
            mail_from_address: '',
            mail_driver: 'smtp',
            mail_host: '',
            mail_port: '',
            mail_username: '',
            mail_password: '',
            mail_encryption: '',
        });

        return {
            state,
            form,
        };
    },
});
</script>
