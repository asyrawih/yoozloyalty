<template>
    <v-card>
        <v-card-title>
            {{ title }}
        </v-card-title>
        <v-form
            :model="data"
            @submit.prevent="submitForm()"
            autocomplete="off"
            method="post"
            id="formModel"
            accept-charset="UTF-8"
            enctype="multipart/form-data"
        >
            <v-card-text v-if="data.status === 'pending'">
                <div v-if="loading" class="px-4 py-3">
                    <v-progress-linear
                        :indeterminate="true"
                        :color="app.color_name"
                    ></v-progress-linear>
                </div>
                <div v-if="loading === false">
                    <v-select
                        label="Status"
                        v-model="status"
                        :items="[
                            {
                                value: 'approved',
                                text: 'Approve'
                            },
                            {
                                value: 'rejected',
                                text: 'Reject'
                            }
                        ]"
                        v-validate="required"
                    >
                    </v-select>
                </div>
            </v-card-text>
            <v-card-text v-else>
                <v-chip color="success" v-if="data.status === 'approved'"
                    >Approved</v-chip
                >
                <v-chip color="error" v-if="data.status === 'rejected'"
                    >Rejected</v-chip
                >
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    type="submit"
                    text
                    :loading="btnLoading"
                    v-if="data.status === 'pending'"
                >
                    Save
                </v-btn>
                <v-btn
                    color="secondary"
                    text
                    large
                    :disabled="btnLoading"
                    @click="
                        $emit('data-list-events', {
                            closeDialog: true,
                            reload: false
                        })
                    "
                >
                    {{ $t("close") }}
                </v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</template>

<script>
export default {
    name: "DataDetail",
    props: {
        api: {
            default: "/app/credit-request",
            required: false,
            type: String
        },
        model: {
            default: "",
            required: false,
            type: String
        },
        uuid: {
            default: "",
            required: true,
            type: String
        },
        title: {
            default: "Update Status",
            required: false,
            type: String
        }
    },
    data: () => {
        return {
            table: {
                headers: [],
                items: [],
                noDataText: ""
            },
            data: {},
            status: null,
            links: [],
            loading: true,
            btnLoading: false
        };
    },
    computed: {
        app() {
            return this.$store.getters.app;
        }
    },
    beforeMount() {
        this.getDataFromApi();
    },
    methods: {
        getDataFromApi() {
            axios
                .get(this.api, {
                    params: {
                        locale: this.$i18n.locale,
                        model: this.model,
                        uuid: this.uuid
                    }
                })
                .then(response => {
                    let data = response.data.data;
                    this.data = data;
                    this.status = data.status;
                })
                .catch(err => {
                    console.log(err.response.data);

                    this.$emit("data-list-events", {
                        closeDialog: true,
                        reload: false
                    });
                })
                .finally(() => (this.loading = false));
        },
        submitForm() {
            this.btnLoading = true;
            axios
                .post(this.api, {
                    uuid: this.uuid,
                    status: this.status
                })
                .then(response => {
                    if (response.data.status === "success") {
                        this.$root.$snackbar(response.data.msg);
                        this.$emit("data-list-events", {
                            closeDialog: true,
                            reload: true
                        });
                    }
                })
                .catch(err => {
                    this.$root.$snackbar(
                        "There was an error while submitting data"
                    );
                })
                .finally(() => {
                    this.btnLoading = false;
                });
        }
    }
};
</script>

<style scoped></style>
