<template>
  <v-container fluid flat>
    <template>
      <v-card flat>
        <v-card-title>
          Payment Method
          <v-spacer></v-spacer>
        </v-card-title>
        <!-- form sms service -->
        <v-row justify="center">
          <v-dialog v-model="dialog" persistent max-width="600px">
            <v-form ref="form" :model="editedItem" lazy-validation>
              <v-card>
                <v-card-title>
                  <span class="text-h5">{{updateProcess ? 'Edit' : 'Add'}} Payment Method</span>
                </v-card-title>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" sm="12">
                        <v-select
                          :items="scheme"
                          :rules="formRules"
                          label="Payment Method"
                          v-model="editedItem.service_name"
                          outlined
                          dense
                          @input="getSchemeDetail($event)"
                        >
                        </v-select>
                      </v-col>
                      <template v-if="editedItem.service_name != null">
												<v-col
													cols="12"
													sm="12"
													v-for="(scheme_item, scheme_index) in schemeDetail"
													:key="scheme_index"
												>
													<v-text-field
														:rules="formRules"
														v-model="editedItem.scheme[scheme_item]"
														:label="
															scheme_item.replaceAll('_', ' ')
																| uppercase(true)
														"
														:placeholder="
															scheme_item.replaceAll('_', ' ')
																| uppercase(true)
														"
														outlined
														dense
													/>
												</v-col>
                      </template>
                      <v-col
                        cols="12"
                        sm="12"
                        v-if="editedItem.service_name != null"
                      >
                        <v-select
                          v-model="editedItem.status"
                          :items="options.status"
                          item-value="value"
                          label="Status"
                          outlined
                          dense
                        >
                        </v-select>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="12"
                        v-if="editedItem.service_name != null"
                      >
                        <v-select
                          multiple
                          clearable
                          v-model="editedItem.currency"
                          :items="options.currencies"
                          item-value="value"
                          label="Currency"
                          outlined
                          dense
                        >
                        </v-select>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="darken-1" text @click="dialog = false">
                    Close
                  </v-btn>
                  <v-btn v-if="!updateProcess" color="blue darken-1" text @click="save"> Send </v-btn>
                  <v-btn v-if="updateProcess" color="blue darken-1" text @click="processEdit"> Update </v-btn>
                </v-card-actions>
              </v-card>
            </v-form>
          </v-dialog>
        </v-row>
        <!-- /form sms service -->
        <v-expansion-panels class="mt-4">
          <v-expansion-panel>
            <v-expansion-panel-header disable-icon-rotate>
              Filter Form
              <template v-slot:actions>
                <v-icon color="primary"> search </v-icon>
              </template>
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <v-form ref="filterForm">
                <v-row>
                  <v-col cols="12" sm="4" class="p-0">
                    <v-text-field
                      v-model="filter.service_name"
                      label="Payment Method"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4" class="p-0">
                    <v-select
											clearable
                      v-model="filter.status"
                      :items="options.status"
                      item-value="value"
                      label="Status"
                      outlined
                      dense
                    >
                    </v-select>
                  </v-col>
                  <v-col cols="12" sm="12">
                    <v-btn color="primary" @click="filterForm"> Filter </v-btn>
                    <v-btn color="error" @click="resetSearch"> Clear </v-btn>
                  </v-col>
                </v-row>
              </v-form>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
        <v-data-table
          :loading="loading"
          v-model="selected"
          :headers="headers"
          :items="rowData"
          item-key="id"
          class="mb-10"
          :options.sync="options"
          :server-items-length="totalPage"
          :footer-props="footerProps"
        >
          <template v-slot:[`item.is_active`]="{ item }">
            <v-chip :color="CustomActiveColumn(item.is_active)" dark>
              {{ item.is_active ? "Active" : "Inactive" }}
            </v-chip>
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-icon small class="ml-1 mr-1" @click="editItem(item)"
              >mdi-pencil</v-icon
            >
            <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
          </template>
          <template v-slot:no-data>
            <div v-if="!loading" class="text-xs-center">
              Your search and/or filter found no results.
            </div>
            <div v-if="loading" class="text-xs-center">Loading data</div>
          </template>
        </v-data-table>

        <v-card-text style="height: 100px; position: relative">
          <v-fab-transition>
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  absolute
                  top
                  right
                  fab
                  v-bind="attrs"
                  v-on="on"
                  @click="dialog = !dialog"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </template>
              <span>Add Service</span>
            </v-tooltip>
          </v-fab-transition>
        </v-card-text>
        <v-dialog v-model="warnDialog" max-width="290">
          <v-card>
            <v-card-title class="headline">Delete Data</v-card-title>
            <v-card-text>
              Are you sure, you want to delete this data?
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="darken-1" text @click="onCancelDelete">
                Cancel
              </v-btn>
              <v-btn
                color="red darken-1"
                text
                @click="processingDelete(editedItem)"
              >
                Delete
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-card>
    </template></v-container
  >
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      scheme: [],
      schemeDetail: [],
      loading: true,
      selected: [],
      valid: true,
      filter: {},
      footerProps: {
        "items-per-page-options": [1, 10, 25, 50, 75, 100],
      },
      updateProcess: false,
      dialog: false,
      warnDialog: false,
      formRules: [(v) => !!v || "Required"],
      rules: {
        required: (value) => !!value || "Required.",
        counter: (value) => value.length <= 10 || "Max 10 characters",
      },
      rowData: [],
      totalPage: 0,
      options: {
        page: 1,
        itemsPerPage: 10,
        status: [
          {
            text: "Active",
            value: 1,
          },
          {
            text: "Inactive",
            value: 0,
          },
        ],
        currencies: [],
      },
      editedItem: {
        service_name: null,
        status: null,
        scheme: {},
        currency: [],
        id: null
      },
      headers: [
        { text: "Payment Method", value: "name" },
        {
          text: "Status",
          value: "is_active",
          sortable: false,
        },
        {
          text: "Currency",
          value: "currency",
          sortable: false,
        },
        {
          text: "Action",
          value: "actions",
          sortable: false,
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  filters: {
    uppercase: function (value, onlyFirstCharacter) {
      if (!value) {
        return "";
      }

      value = value.toString();

      if (onlyFirstCharacter) {
        try {
          return value.toLowerCase().replace(/(\?\<\=\s)[^\s]|^./g, (a) => a.toUpperCase());

        } catch(e) {
          return value.toUpperCase();
        }
      } else {
        return value.toUpperCase();
      }
    },
  },
  methods: {
    CustomActiveColumn(active) {
      if (active == 1) return "green";
      else if (active == 0) return "red";
    },
    getDataFromApi() {
      const params = {
        per_page: this.options.itemsPerPage,
        page: this.options.page,
        service_name: this.filter.service_name,
        status: this.filter.status,
      };
      axios
        .get("/admin/payment-method", { params: params })
        .then((r) => {
          this.rowData = r.data.data.data || [];
          this.totalPage = r.data.data.total;
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    getScheme() {
      axios
        .get("/admin/payment-method/create", {
          params: {
            locale: this.$i18n.locale
          }
        })
        .then((r) => {
          this.scheme = r.data.data.schema || [];
          this.options.currencies = r.data.data.currencies
        })
        .catch((err) => {
          console.log(err);
        });
    },
    getSchemeDetail(event) {
      axios
        .get("/admin/payment-method/" + event + "/schema")
        .then((r) => {
          this.schemeDetail = r.data.data || [];
        })
        .catch((err) => {
          console.log(err);
        });
    },
    save() {
      this.$refs.form.validate();
      if (this.$refs.form.validate() === true) {
        let params = {
          service_name: this.editedItem.service_name,
          status: this.editedItem.status,
          scheme: this.editedItem.scheme,
          currency: this.editedItem.currency.join(', '),
        };
        axios
          .post("/admin/payment-method", params)
          .then((r) => {
            this.$root.$snackbar(r.data.message);
            this.dialog = false;
            this.getDataFromApi();
            this.reset();
          })
          .catch((err) => {
            this.$root.$snackbar(err.response.data.message);
            this.dialog = false;
            this.getDataFromApi();
            this.reset();
          });
      }
    },
    editItem(item) {
      this.editedItem = {
        id: item.id,
        service_name: item.blueprint,
        status: item.is_active,
        scheme: JSON.parse(item.schema),
        currency: item.currency ? item.currency.split(', ') : [],
      };
      this.dialog = true;
      this.updateProcess = true;
      this.schemeDetail = this.getSchemeDetail(item.blueprint)
    },
    processEdit() {
      this.$refs.form.validate();
      if (this.$refs.form.validate() === true) {
        let params = {
          service_name: this.editedItem.service_name,
          status: this.editedItem.status,
          scheme: this.editedItem.scheme,
          currency: this.editedItem.currency.join(', ')
        };
        axios
          .put(`/admin/payment-method/${this.editedItem.id}`, params)
          .then((r) => {
            this.$root.$snackbar(r.data.message);
            this.dialog = false;
            this.getDataFromApi();
            this.reset();
          })
          .catch((err) => {
            this.$root.$snackbar(err.response.data.message);
            this.dialog = false;
            this.getDataFromApi();
            this.reset();
          });
        this.updateProcess = false;
      }
    },
    onCancelDelete() {
      this.warnDialog = false;
    },
    deleteItem(item) {
      this.editedItem.id = item.id;
      this.warnDialog = true;
    },
    processingDelete(item) {
      axios
        .delete(`/admin/payment-method/${item.id}`)
        .then((r) => {
          this.$root.$snackbar(r.data.message);
          this.dialog = false;
          this.warnDialog = false;
          this.getDataFromApi();
        })
        .catch((err) => {
          this.$root.$snackbar(err.response);
          this.dialog = false;
          this.getDataFromApi();
        });
    },
    close() {
      this.reset();
      this.dialog = false;
    },
    reset() {
      this.updateProcess = false;
      this.$refs.form.reset();
    },
    resetSearch() {
      this.$refs.filterForm.reset();
      this.getDataFromApi();
    },
    filterForm() {
      this.getDataFromApi();
    },
  },
  created() {
    this.getDataFromApi();
    this.getScheme();
  },
};
</script>

<style scoped>
.v-card.dialogField {
  margin: auto;
  max-width: 750px;
}
</style>