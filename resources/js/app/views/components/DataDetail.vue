<template>
    <v-card>
        <v-card-title>
            {{ title }}
        </v-card-title>
        <v-card-text>
            <div v-if="loading" class="px-4 py-3">
                <v-progress-linear
                    :indeterminate="true"
                    :color="app.color_name"
                ></v-progress-linear>
            </div>
            <div v-if="loading === false">
                <v-data-table
                    v-if="type === 'table'"
                    :disable-pagination="true"
                    hide-default-footer
                    :headers="table.headers"
                    :items="table.items"
                    :no-data-text="table.noDataText"
                >
                </v-data-table>
                <template v-else-if="type === 'plan_approval'">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th colspan="2">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><strong>Previous Plan:</strong></td>
                                <td>{{ data.previous_plan.name }}</td>
                            </tr>
                            <tr>
                                <td><strong>New Plan:</strong></td>
                                <td>{{ data.new_plan.name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Requested on:</strong></td>
                                <td>{{ data.created_at }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>{{ data.status }}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </template>
            </div>
        </v-card-text>
        <v-card-actions>
            <v-btn
                color="secondary"
                text
                large
                :loading="btnLoading"
                @click="
                        $emit('data-list-events', {
                            closeDialog: true,
                            reload: false
                        })
                    "
            >
                {{ $t("close") }}
            </v-btn>

            <template v-if="type === 'plan_approval'">
                <v-spacer></v-spacer>
                <v-btn v-for="link in links" :key="link.text"
                    :color="link.color" text @click="executeAction(link.link, link.text)"
                    :loading="btnLoading"
                >
                    {{ link.text }}
                </v-btn>
            </template>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: "DataDetail",
    props: {
        api: {
            default: "/app/data-detail",
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
        type: {
            default: "text",
            required: true,
            type: String
        },
        title: {
            default: "Detail",
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
            links: [],
            loading: true,
            btnLoading: false
        }
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
        getDataFromApi(){
            axios
                .get(this.api, {
                    params: {
                        locale: this.$i18n.locale,
                        model: this.model,
                        uuid: this.uuid
                    }
                })
                .then((response) => {
                    let data = response.data.data
                    if (this.type === 'table') {
                        this.table.headers = data.headers
                        this.table.items = data.items
                        this.table.noDataText = data.noDataText
                    }

                    if (this.type === 'plan_approval') {
                        this.data = data.plan_change_request
                        this.links = data.links
                    }
                })
                .catch(err => {
                    console.log(err.response.data)

                    this.$emit('data-list-events', {
                        closeDialog: true,
                        reload: false
                    })
                })
                .finally(() => (this.loading = false));
        },
        executeAction(link, action){
            this.btnLoading = true
            axios
                .post(link, {
                    uuid: this.uuid,
                    action: action
                })
                .then(response => {
                    if (response.data.status === "success") {
                        this.$root.$snackbar(response.data.msg);
                        this.$emit('data-list-events', {
                            closeDialog: true,
                            reload: true
                        })
                    }
                })
                .catch(err => {
                    this.$root.$snackbar(
                        "There was an error while submitting data"
                    );
                })
                .finally(() => {this.btnLoading = false});
        }
    }
}
</script>

<style scoped>

</style>
