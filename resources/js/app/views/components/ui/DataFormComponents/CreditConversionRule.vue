<template>
    <v-row>
        <v-col>
            <v-row align="center" class="mb-3">
                <v-col cols="12" sm="7">
                    <v-radio-group
                        row
                        v-model="conversion_mode"
                    >
                        <v-radio
                            label="Range Mode"
                            value="range"
                        />

                        <v-radio
                            label="Step Mode"
                            value="step"
                        />
                    </v-radio-group>
                </v-col>

                <v-col cols="12" sm="5" class="text-right">
                    <v-btn
                        color="primary"
                        small
                        @click="onCalculatorClick"
                    >
                        Calculator
                    </v-btn>

                    <v-btn
                        color="primary"
                        small
                        @click="onAddRuleClick"
                    >
                        Add Rule
                    </v-btn>
                </v-col>
            </v-row>

            <v-row class="mb-3">
                <v-col
                    v-if="rules.length > 0"
                >
                    <div v-for="(rule, index) in rules" :key="`rule_${index}`" class="mb-2">
                        <v-row>
                            <v-col>
                                <v-row>
                                    <!-- Range Amount Field range mode -->
                                    <v-col
                                        v-if="rule.mode === 'range'"
                                        cols="12"
                                        :sm="rule.mode === 'range' ? 6 : 4"
                                    >
                                        <v-row>
                                            <!-- Min Amount -->
                                            <v-col>
                                                <v-text-field
                                                    :id="`frmRuleMinAmount${index}`"
                                                    type="number"
                                                    label="Min Amount"
                                                    v-model="rule.min_amount"
                                                    v-validate="`required|numeric`"
                                                    :data-vv-name="`rule.min_amount_${index}`"
                                                    data-vv-as="min amount"
                                                    :error-messages="errors.collect(`rule.min_amount_${index}`)"
                                                />
                                            </v-col>

                                            <!-- Max Amount -->
                                            <v-col>
                                                <v-text-field
                                                    :id="`frmRuleMaxAmount${index}`"
                                                    type="number"
                                                    label="Max Amount"
                                                    v-model="rule.max_amount"
                                                    v-validate="`required|numeric`"
                                                    :data-vv-name="`rule.max_amount_${index}`"
                                                    data-vv-as="max amount"
                                                    :error-messages="errors.collect(`rule.max_amount_${index}`)"
                                                />
                                            </v-col>
                                        </v-row>
                                    </v-col>

                                    <!-- Min amount field step mode -->
                                    <v-col v-if="rule.mode === 'step'" cols="12" sm="3">
                                        <v-text-field
                                            :id="`frmRuleMinAmount${index}`"
                                            type="number"
                                            label="Min Amount"
                                            v-model="rule.min_amount"
                                            v-validate="`required|numeric`"
                                            :data-vv-name="`rule.min_amount_${index}`"
                                            data-vv-as="min amount"
                                            :error-messages="errors.collect(`rule.min_amount_${index}`)"
                                        />
                                    </v-col>

                                    <!-- Rate and Rate Type Field -->
                                    <v-col cols="12" :sm="rule.mode === 'range' ? 6 : 3">
                                        <v-row>
                                            <!-- Rate Field -->
                                            <v-col>
                                                <v-text-field
                                                    :id="`frmRuleRate${index}`"
                                                    type="number"
                                                    :label="rule.mode === 'range' ? 'Rate' : 'Point'"
                                                    v-model="rule.rate"
                                                    v-validate="`required|numeric`"
                                                    :data-vv-name="`rule.rate_${index}`"
                                                    data-vv-as="rate"
                                                    :error-messages="errors.collect(`rule.rate_${index}`)"
                                                />
                                            </v-col>

                                            <!-- Rate Type Field -->
                                            <v-col v-if="rule.mode === 'range'">
                                                <v-select
                                                    label="Rate type"
                                                    :items="state.typeOptions"
                                                    v-model="rule.type"
                                                />
                                            </v-col>
                                        </v-row>
                                    </v-col>

                                    <!-- Step Mode and Step Amount Field step mode -->
                                    <v-col
                                        v-if="rule.mode === 'step'"
                                        cols="12"
                                        sm="6"
                                    >
                                        <v-row>
                                            <!-- Step Mode Field -->
                                            <v-col>
                                                <v-tooltip top>
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-select
                                                            label="Stepping Mode"
                                                            :items="state.stepModeOptions"
                                                            v-model="rule.stepping_mode"
                                                            @change="
                                                                if (! rule.stepping_mode) {
                                                                    rule.step_amount = 0;
                                                                }
                                                            "
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        />
                                                    </template>

                                                    <span>Stepping Mode</span>
                                                </v-tooltip>
                                            </v-col>

                                            <!-- Step Amount Field -->
                                            <v-col>
                                                <v-tooltip top>
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field
                                                            :id="`frmRuleStepAmount${index}`"
                                                            type="number"
                                                            label="Every $ spent"
                                                            :disabled="! rule.stepping_mode"
                                                            v-model="rule.step_amount"
                                                            v-validate="`required|numeric`"
                                                            :data-vv-name="`rule.step_amount_${index}`"
                                                            data-vv-as="step amount"
                                                            :error-messages="errors.collect(`rule.step_amount_${index}`)"
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        />
                                                    </template>

                                                    <span>Step Amount</span>
                                                </v-tooltip>
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                </v-row>
                            </v-col>

                            <v-col align-self="center" cols="2" sm="1">
                                <v-btn color="red" icon text small>
                                    <v-icon @click="onDeleteRuleClick(rule)">
                                        delete
                                    </v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>

                        <span
                            v-if="validate.length > 0 && validate[0]['uuid'] === rule.uuid"
                            class="red--text"
                        >
                            {{ validate[0]['msg'] }}
                        </span>

                        <v-divider></v-divider>
                    </div>
                </v-col>

                <v-col
                    v-else
                    class="text-center"
                >
                    <p>No Rule Added</p>
                </v-col >
            </v-row>

            <confirm ref="confirm" />

            <v-dialog
                v-model="state.dialog.calculator"
                persistent
                width="360px"
            >
                <v-card>
                    <v-overlay
                        :value="state.calculator.loading"
                    >
                        <v-progress-circular
                            :size="50"
                            color="primary"
                            indeterminate
                            class="ma-5"
                        />
                    </v-overlay>

                    <v-card-title>
                        <span>Calculator</span>

                        <v-spacer></v-spacer>

                        <v-btn
                            icon
                            @click="onCalculatorCloseClick"
                        >
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card-title>

                    <v-divider class="grey lighten-2"></v-divider>

                    <v-form
                        @submit.prevent="onCalculatorSubmit"
                        lazy-validation
                        data-vv-scope="calculator"
                    >
                        <v-card-text>
                            <span class="subtitle-1">
                                Verify the conversion rules your added.
                            </span>

                            <v-text-field
                                type="number"
                                id="frmCalculatorAmount"
                                class="mt-3 mb-2"
                                label="Amount"
                                prepend-icon="mdi-currency-usd"
                                v-model="state.calculator.amount"
                                @change="state.calculator.points = 0"
                                v-validate="'required|decimal:2|min_value:0'"
                                data-vv-name="amount"
                                data-vv-as="amount"
                                :error-messages="errors.collect('calculator.amount')"
                            />

                            <v-text-field
                                id="frmCalculatorPoints"
                                label="The Points You Get"
                                prepend-icon="redeem"
                                v-model="state.calculator.points"
                                readonly
                            />
                        </v-card-text>

                        <v-card-actions>
                            <v-btn
                                type="submit"
                                color="primary"
                                block
                            >
                                Test
                            </v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-dialog>
        </v-col>
    </v-row>
</template>

<script>
import { computed, defineComponent, reactive } from '@vue/composition-api';
import axios from 'axios';

export default defineComponent({
    name: 'credit-conversion-rule',
    props: {
        mode: {
            type: String,
            default: 'range',
        },
        data: {
            type: Array,
        },
        validate: {
            type: Array,
        },
        uuid: {
            type: String,
        },
    },
    setup(props, { emit, refs, root }) {
        const state = reactive({
            typeOptions: [
                { text: "Fixed", value: "F" },
                { text: "Percent", value: "P" },
            ],
            stepModeOptions: [
                { text: "Off", value: 0 },
                { text: "On", value: 1 },
            ],
            dialog: {
                calculator: false,
            },
            calculator: {
                amount: 0,
                points: 0,
                loading: false,
            },
        });

        const conversion_mode = computed({
            get: () => props.mode,
            set: value => emit('update:mode', value),
        });

        const rules = computed(() => props.data.filter((item) => item.mode === conversion_mode.value));

        const onAddRuleClick = () => {
            props.data.push({
                min_amount: 0,
                max_amount: 0,
                type: 'F',
                rate: 0,
                step_amount: 0,
                stepping_mode: 0,
                mode: conversion_mode.value,
                uuid: Math.random().toString(36).substring(7),
            });

            return true;
        }

        const onDeleteRuleClick = (rule) => {
            refs.confirm.open(
                'Delete',
                'Do you want to delete the selected record(s)? This cannot be undone.',
                { color: 'red' }
            ).then((confirm) => {
                if (confirm) {
                    props.data.splice(props.data.indexOf(rule), 1);
                }
            });

            return true;
        }

        const onCalculatorClick = () => {
            state.dialog.calculator = true;

            return true;
        }

        return {
            state,
            conversion_mode,
            rules,
            onAddRuleClick,
            onDeleteRuleClick,
            onCalculatorClick,
        };
    },
    methods: {
        onCalculatorCloseClick() {
            this.state.calculator.amount = 0;

            this.state.calculator.points = 0;

            this.state.dialog.calculator = false;

            return true;
        },
        async onCalculatorSubmit() {
            this.state.calculator.loading = true;

            const validate = await this.$validator.validateAll('calculator');

            if (! validate) {
                this.state.calculator.loading = false;

                return false;
            }

            try {
                const response = await axios.post('/user/calculator/credit-points', {
                    amount: this.state.calculator.amount,
                    mode: this.conversion_mode,
                    rules: this.rules,
                });

                if (response.data) {
                    const data = response.data;

                    if (data.status === 'success') {
                        this.state.calculator.points = data.points;
                    }
                }
            } catch (exception) {
                if (exception && exception.response) {
                    const data = exception.response.data;

                    if (data.status === 'error' && data.errors) {
                        for (let field in data.errors) {
                            this.$validator.errors.add({
                                field: `calculator.${field}`,
                                msg: data.errors[field]
                            });
                        }
                    }
                }
            } finally {
                this.state.calculator.loading = false;
            }
        }
    },
});
</script>

