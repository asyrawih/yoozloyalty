<template>
    <div>
        <div class="text-right">
            <v-dialog v-model="dialogCoupunImport" width="500" persistent>
                <template
                    v-slot:activator="{
                        on,
                        attrs
                    }"
                >
                    <v-btn
                        color="pink white--text"
                        v-bind="attrs"
                        v-on="on"
                        small
                    >
                        Import Csv
                    </v-btn>
                </template>

                <v-card>
                    <v-card-title>
                        Import Csv
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="mt-5">
                        <div class="mb-2">
                            <a href="/sample/coupun-sample.csv">Example Csv</a>
                        </div>
                        <v-alert
                            dense
                            border="left"
                            type="error"
                            v-if="csvError"
                        >
                            Invalid file type / format
                        </v-alert>
                        <v-file-input
                            accept=".csv"
                            label="Import file"
                            @change="csvChange"
                            v-model="csvFile"
                            ref="csv"
                            outlined
                            dense
                        >
                            <template v-slot:selection="{ text }">
                                <v-chip dark color="primary" label small>
                                    {{ text }}
                                </v-chip>
                            </template>
                        </v-file-input>

                        <template v-if="csvData.length > 0">
                            <v-simple-table>
                                <template v-slot:default>
                                    <thead>
                                        <tr>
                                            <th class="text-left">
                                                Name
                                            </th>
                                            <th class="text-left">
                                                Code
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(coupun, index) in csvData"
                                            :key="'coupun-csv-' + index"
                                        >
                                            <td class="text-left">
                                                {{ coupun.name }}
                                            </td>
                                            <td class="text-left">
                                                {{ coupun.code }}
                                            </td>
                                            <td>
                                                <v-btn
                                                    icon
                                                    small
                                                    @click="
                                                        editCoupun(index, true)
                                                    "
                                                >
                                                    <v-icon small>
                                                        edit
                                                    </v-icon>
                                                </v-btn>
                                                <v-btn
                                                    icon
                                                    small
                                                    @click="
                                                        removeCoupunAction(
                                                            index,
                                                            true
                                                        )
                                                    "
                                                >
                                                    <v-icon small>
                                                        delete
                                                    </v-icon>
                                                </v-btn>
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                        </template>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn color="primary" text @click="saveCsv">
                            Submit
                        </v-btn>
                        <v-btn
                            color="secondary"
                            text
                            @click="dialogCoupunImportClose"
                        >
                            Close
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="dialogCoupun" width="500" persistent>
                <template
                    v-slot:activator="{
                        on,
                        attrs
                    }"
                >
                    <v-btn color="primary" v-bind="attrs" v-on="on" small>
                        Add Coupon
                    </v-btn>
                </template>

                <v-card>
                    <v-card-title>
                        {{ dialogCoupunTitle }}
                        Coupon
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="mt-3">
                        <v-text-field
                            type="text"
                            v-model="name"
                            @keyup.enter="handleCoupunEnter"
                            label="Name"
                            :error-messages="
                                name ? '' : 'The coupon name field is required'
                            "
                        ></v-text-field>
                        <v-text-field
                            type="text"
                            v-model="code"
                            @keyup.enter="handleCoupunEnter"
                            label="Code"
                            :error-messages="
                                code ? '' : 'The coupon code field is required'
                            "
                        ></v-text-field>
                        <v-text-field
                            type="number"
                            v-model="qty"
                            label="Qty"
                            @keyup.enter="handleCoupunEnter"
                            v-if="dialogCoupunTitle === 'Add'"
                            :error-messages="
                                qty < 1 ? 'The qty need at least 1' : ''
                            "
                        ></v-text-field>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            v-if="dialogCoupunTitle === 'Add'"
                            :disabled="disabledCoupunBtnSubmit"
                            color="primary"
                            text
                            @click="addCoupun"
                        >
                            Create
                        </v-btn>
                        <v-btn
                            v-if="dialogCoupunTitle === 'Edit'"
                            :disabled="disabledCoupunBtnSubmit"
                            color="primary"
                            text
                            @click="editCoupunAction"
                        >
                            Edit
                        </v-btn>
                        <v-btn
                            color="secondary"
                            text
                            @click="dialogCoupunClose"
                        >
                            Close
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>

        <v-simple-table>
            <template v-slot:default>
                <thead>
                    <tr>
                        <th class="text-left">
                            Name
                        </th>
                        <th class="text-left">
                            Code
                        </th>
                        <th class="text-left">
                            Status
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="data.length > 0">
                        <tr
                            v-for="(coupun, index) in data"
                            :key="'coupun-' + index"
                        >
                            <td class="text-left">
                                {{ coupun.name }}
                            </td>
                            <td class="text-left">
                                {{ coupun.code }}
                            </td>
                            <td class="text-left">
                                <v-chip
                                    class="ma-2"
                                    :color="coupun.status ? 'red' : 'green'"
                                    text-color="white"
                                >
                                    {{ coupun.status ? "Used" : "Available" }}
                                </v-chip>
                            </td>
                            <td>
                                <v-btn icon small @click="editCoupun(index)">
                                    <v-icon small>
                                        edit
                                    </v-icon>
                                </v-btn>
                                <v-btn
                                    icon
                                    small
                                    v-if="!coupun.status"
                                    @click="removeCoupunAction(index)"
                                >
                                    <v-icon small>
                                        delete
                                    </v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="4">
                            <p class="text-muted mt-2 text-center">
                                No Coupon Added
                            </p>
                        </td>
                    </tr>
                </tbody>
            </template>
        </v-simple-table>
    </div>
</template>

<script>
export default {
    data: () => {
        return {
            name: "",
            code: "",
            qty: 1,
            selectedIndex: -1,
            dialogCoupun: false,
            dialogCoupunImport: false,
            dialogCoupunTitle: "Add",
            csvFile: null,
            csvError: false,
            csvData: [],
            csvEdit: false
        };
    },
    props: {
        data: {
            type: Array
        }
    },
    computed: {
        disabledCoupunBtnSubmit() {
            if (this.dialogCoupunTitle === "Add") {
                if (!this.code) return true;
                if (!this.name) return true;
                if (this.qty < 1) return true;
            } else {
                return false;
            }

            if (this.dialogCoupunTitle === "Edit") {
                if (!this.code) return true;
                if (!this.name) return true;
            } else {
                return false;
            }
        }
    },
    methods: {
        dialogCoupunImportClose() {
            this.csvFile = null;
            this.csvError = false;
            this.csvData = [];
            this.csvEdit = false;
            this.dialogCoupunImport = false;
        },
        dialogCoupunClose() {
            this.code = "";
            this.name = "";
            this.dialogCoupunTitle = "Add";
            this.dialogCoupun = false;
        },
        addCoupun() {
            for (let index = 0; index < this.qty; index++) {
                this.data.push({
                    name: this.name,
                    code: this.code,
                    status: 0
                });
            }
            this.dialogCoupunClose();
        },
        handleCoupunEnter() {
            if (this.dialogCoupunTitle === "Add") {
                this.addCoupun();
            } else if (this.dialogCoupunTitle === "Edit") {
                this.editCoupunAction();
            }
        },
        editCoupun(index, csv = false) {
            this.dialogCoupunTitle = "Edit";
            this.dialogCoupun = true;
            this.selectedIndex = index;
            if (csv) {
                this.csvEdit = true;
                this.name = this.csvData[index].name;
                this.code = this.csvData[index].code;
            } else {
                this.name = this.data[index].name;
                this.code = this.data[index].code;
            }
        },
        editCoupunAction() {
            if (this.csvEdit) {
                this.csvData[this.selectedIndex].name = this.name;
                this.csvData[this.selectedIndex].code = this.code;
            } else {
                this.data[this.selectedIndex].name = this.name;
                this.data[this.selectedIndex].code = this.code;
            }
            this.dialogCoupunClose();
        },
        removeCoupunAction(index, csv = false) {
            if (csv) {
                this.csvData.splice(index, 1);
            } else {
                this.data.splice(index, 1);
            }
        },
        csvToArray(str, delimiter = ",") {
            const headers = str.slice(0, str.indexOf("\n")).split(delimiter);
            const rows = str.slice(str.indexOf("\n") + 1).split("\n");
            const arr = rows.map(function(row) {
                const values = row.split(delimiter);
                const el = headers.reduce(function(object, header, index) {
                    object[header] = values[index];
                    return object;
                }, {});
                return el;
            });

            arr.splice(arr.length - 1, 1);

            return arr;
        },
        csvChange() {
            const input = this.$refs.csv;
            const file = input.internalArrayValue[0];
            const reader = new FileReader();

            reader.onload = event => {
                const text = event.target.result;
                const data = this.csvToArray(text);
                if (!data[0].name && !data[0].code) {
                    this.csvError = true;
                    this.csvData = [];
                } else {
                    this.csvData = data;
                }
            };

            reader.readAsText(file);
        },
        saveCsv() {
            this.csvData.forEach(element => {
                this.data.push({
                    name: element.name,
                    code: element.code,
                    status: 0
                });
            });

            this.dialogCoupunImportClose()
            this.dialogCoupunClose();
        }
    }
};
</script>
