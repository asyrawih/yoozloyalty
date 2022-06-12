<template>
  <v-card>
    <v-toolbar tabs flat>
      <v-toolbar-title>Points Expiry</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-card-text>
        <v-form
            data-vv-scope="form1"
            :model="form1"
            id="form1"
            lazy-validation
            @submit.prevent="submitForm('form1')"
            autocomplete="off"
            method="post"
            accept-charset="UTF-8"
        >
            <v-row>
                <v-col
                    class="pl-3"
					:key="1"
                >
                    <v-text-field
                        outlined
                        dense
                        ref="points_expiry"
                        label="Customer points will be expired in"
                        v-model="form1.points_expiry"
                        @focus="focus_element = 'points_expiry'"
                        data-vv-name="points_expiry"
                        data-vv-as="Points expiry"
                        v-validate="'required'"
                        :error-messages="errors.collect('points_expiry')"
                        suffix="days after given."
                    ></v-text-field>
                </v-col>
				<v-col :key="2"></v-col>
				<v-col :key="3"></v-col>
            </v-row>
            <v-text-field
                outlined
                class="mt-4"
                v-model="form1.current_password"
                data-vv-name="current_password"
                :data-vv-as="$t('current_password').toLowerCase()"
                v-validate="'required|min:8|max:24'"
                :label="$t('current_password')"
                :error-messages="errors.collect('form1.current_password')"
                :type="show_current_password ? 'text' : 'password'"
                :append-icon="show_current_password ? 'visibility' : 'visibility_off'"
                @click:append="show_current_password = !show_current_password"
                required
            ></v-text-field>
			<v-alert :value="form1.success" type="success">
				{{ form1.response_message }}
			</v-alert>
			<div class="d-flex">
				<v-spacer></v-spacer>
				<v-btn
					color="primary"
					large
					:loading="form1.loading"
					type="submit"
					class="mb-2"
				>{{ $t("update") }}</v-btn
				>
			</div>
        </v-form>
    </v-card-text>
  </v-card>
</template>

<script>

export default {
	components: {
	},
	$_veeValidate: {
		validator: "new",
	},
	data() {
		return {
			show_current_password: false,
			focus_element: "points_expiry",
			form1: {
				loading: false,
				current_password: "",
				points_expiry: 0,
				variable: [],
				has_error: false,
				error: null,
				errors: {},
				success: false,
			},
		};
	},
	created() {
		axios.get("/merchant/points-expiry").then((response) => {
			this.form1.points_expiry = response.data.data.points_expiry
			this.form1.loading = false;
		})
		.catch(err => {
			console.log(err.response)
		});
	},
	methods: {
		submitForm(formName) {
			this[formName].success = false;
			this[formName].has_error = false;
			this[formName].loading = true;

			this.$validator.validateAll(formName).then((valid) => {
				if (valid) {
					this.updateExpiry(formName);
				} else {
					// Get first error and select tab where error occurs
					let field = this.errors.items[0].field;
					let el =
						typeof this.$refs[field] !== "undefined" ? this.$refs[field] : null;
					let tab = el !== null ? el.$parent.$vnode.key : null;
					if (tab !== null) this.selectedTab = tab;

					this[formName].loading = false;
					return false;
				}
			});
		},
		updateExpiry(formName) {
			var app = this[formName];

			axios
				.post("/merchant/points-expiry", {
					locale: this.$i18n.locale,
					current_password: app.current_password,
					type: "points_expiry",
					id: app.id,
					points_expiry: app.points_expiry
				})
				.then((response) => {
					if (response.data.success === true) {
						this.form1.response_message = response.data.message
						app.success = true;
					}
					app.loading = false;
				})
				.catch((err) => {
					let errors = err.response.data.errors || {};
					let i = 0;
					for (let field in errors) {
						if (i == 0) {
							// Get first error and select tab where error occurs
							let el =
								typeof this.$refs[field] !== "undefined"
								? this.$refs[field]
								: null;
							let tab = el !== null ? el.$parent.$vnode.key : null;
							if (tab !== null) this.selectedTab = tab;
						}
						i++;
						this.$validator.errors.add({
							field: formName + "." + field,
							msg: errors[field][0],
						});
					}
					app.loading = false;
				});
		},
	},
	computed: {
		app() {
			return this.$store.getters.app;
		},
		_() {
			return _;
		},
	},
};
</script>
<style scoped></style>
