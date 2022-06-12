<template>
	<v-container fluid fill-height>
		<v-layout align-center justify-center row fill-height wrap>
			<v-flex xs10 sm7 md5 lg3 xl2>

				<v-form
					data-vv-scope="form"
					lazy-validation
					@submit.prevent="onSubmit"
				>
					<v-card class="elevation-18 my-4">
						<v-toolbar flat color="transparent">
							<v-toolbar-title>
								Create a new password
							</v-toolbar-title>
						</v-toolbar>

                        <v-card-text v-if="state.loading">
                            <v-progress-linear
                                indeterminate
                                color="primary"
                            />
                        </v-card-text>

						<v-card-text v-else>
                            <v-alert
                                :value="state.invalidToken"
                                type="error"
                                class="mb-4"
                            >
                                {{ $t('invalid_token') }}
                            </v-alert>

                            <v-alert
                                :value="state.hasError"
                                type="error"
                                class="mb-1"
                                show-icon
                            >
                                <p>{{ $t('correct_errors') }}</p>
                            </v-alert>

							<v-text-field
                                v-show="! state.invalidToken"
								:type="state.show_password ? 'text' : 'password'"
								id="frmCreatePassword"
								:label="$t('new_password')"
								prepend-inner-icon="lock"
								:append-icon="state.show_password ? 'visibility' : 'visibility_off'"
								:placeholder="$t('enter_password')"
								v-model="form.password"
								@click:append="state.show_password = ! state.show_password"
								data-vv-name="password"
								v-validate="'required|min:8|max:24'"
								:data-vv-as="$t('password')"
								:error-messages="errors.collect('form.password')"
							/>
						</v-card-text>

						<v-card-actions v-show="! state.loading && ! state.invalidToken">
							<v-btn
								color="primary"
								large
								block
								type="submit"
							>
								Submit
							</v-btn>
						</v-card-actions>
					</v-card>
				</v-form>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
import { computed, defineComponent, onMounted, reactive } from '@vue/composition-api';
import axios from 'axios';

export default defineComponent({
	setup(_, { root }) {
		const state = reactive({
			loading: false,
			showPassword: false,
            invalidToken: true,
            hasError: false,
            token: null,
		});

		const form = reactive({
			password: '',
		});

        const campaign = computed(() => root.$store.state.app.campaign);

        onMounted(async () => {
            await onValidate();
        });

        const onValidate = async () => {
            state.loading = true;

            try {
                state.token = root.$route.query.code;

                const response = await axios.post("/campaign/auth/password/reset/validate-token", {
                    locale: root.$i18n.locale,
                    token: state.token,
                });

                if (response.data.status === 'success') {
                    state.invalidToken = false
                }
			} catch (exception) {
                state.invalidToken = true;
			} finally {
				state.loading = false;
			}
        }

		const onSubmit = async () => {
			state.loading = true;

			const validate = root.$validator.validateAll(form);

			if (! validate) {
				state.loading = false;

				return;
			}

			try {
                const response = await axios.post("/campaign/auth/password/update", {
                    locale: root.$i18n.locale,
                    uuid: campaign.value.uuid,
                    password: form.password,
                    token: state.token,
                });

                if (response.data.status === 'success') {
                    root.$router.push({
                        name: 'login',
                        params: {successResetUpdateRedirect: true}
                    });
                }
			} catch (exception) {
                state.hasError = true;
			} finally {
				state.loading = false;
			}
		}

		return {
			state,
			form,
            onSubmit,
		}
	},
});
</script>

