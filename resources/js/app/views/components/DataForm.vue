<template>
    <v-card>
        <v-toolbar v-if="! loadingForm" :tabs="tabCount > 1" flat>
            <v-toolbar-title>
                {{
                    uuid === null
                        ? mode === "import"
                        ? translations.import
                        : translations.create_item
                        : translations.edit_item
                }}
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn
                icon
                @click="
                    $emit('data-list-events', {
                        closeDialog: true,
                        reload: false
                    })
                "
            >
                <v-icon>close</v-icon>
            </v-btn>

            <template v-slot:extension v-if="tabCount > 1 && !limitReached">
                <v-tabs
                    slot="extension"
                    v-model="selectedTab"
                    :slider-color="app.color_name"
                    color="grey darken-3"
                    show-arrows
                >
                    <div
                        v-for="(tab, tab_index) in form.items"
                        :key="`tab_${tab_index}`"
                    >
                        <v-tab
                            v-if="tabCount > 1"
                            :href="`#${tab_index}`"
                        >
                            {{ tab.text }}
                        </v-tab>
                    </div>
                </v-tabs>
            </template>
        </v-toolbar>

        <div
            v-if="loadingForm"
            class="px-4 py-3"
        >
            <v-progress-linear
                :color="app.color_name"
                indeterminate
            />
        </div>

        <div v-if="!loadingForm && limitReached">
            <v-card-text class="pt-0">
                <p class="subtitle-1">
                    You have reached the maximum of {{ max }}
                    {{ translations.items_lowercase }}. If you want to create
                    more {{ translations.items_lowercase }}, you have to upgrade
                    your subscription.
                </p>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    large
                    text
                    :color="app.color_name"
                    :to="{ name: 'user.billing' }"
                >
                    Manage subscription
                </v-btn>
            </v-card-actions>
        </div>

        <v-form
            v-if="! loadingForm && ! limitReached"
            id="formModel"
            method="post"
            accept-charset="UTF-8"
            enctype="multipart/form-data"
            autocomplete="on"
            data-vv-scope="formModel"
            :model="formModel"
            @submit.prevent="submitForm('formModel')"
        >
            <v-divider class="grey lighten-2"></v-divider>

            <v-card-text
                :style="{
                    height: settings.dialog_height + 'px' || 'auto',
                    'max-width': '800px',
                    'overflow-y': 'auto'
                }"
            >
                <v-tabs-items
                    class="mx-2"
                    v-model="selectedTab"
                    touchless
                >
                    <v-tab-item
                        v-for="(tab, tab_index) in form.items"
                        :key="tab_index"
                        :value="tab_index"
                        eager
                    >
                        <div
                            v-if="tab.description"
                            class="mb-3 subtitle-1"
                            v-html="tab.description"
                        />

                        <v-card
                            v-if="Object.keys(form.items[tab_index].subs).length > 1"
                            class="mb-3 elevation-1"
                        >
                            <v-tabs
                                v-if="Object.keys(form.items[tab_index].subs).length > 1"
                                v-model="selectedSubTab[tab_index]"
                                hide-slider
                                :color="app.color_name"
                                background-color="grey lighten-4"
                                show-arrows
                            >
                                <v-tab
                                    :key="`sub_tab_${sub_index}`"
                                    :href="`#${sub_index}`"
                                    v-for="(sub, sub_index) in tab.subs"
                                >
                                    {{ sub.text }}
                                </v-tab>
                            </v-tabs>
                        </v-card>

                        <v-tabs-items
                            v-model="selectedSubTab[tab_index]"
                            touchless
                        >
                            <v-tab-item
                                v-for="(sub, sub_index) in tab.subs"
                                :key="sub_index"
                                :value="sub_index"
                                eager
                            >
                                <div
                                    v-if="sub.description"
                                    class="mb-3 subtitle-1"
                                    v-html="sub.description"
                                />

                                <!-- <div v-for="(sub, sub_index) in tab.subs"> -->
                                <!-- <div class="mt-2 mb-3 headline" v-if="sub.text">{{ sub.text }}</div> -->

                                <div
                                    v-for="(form_item, form_index) in sub.items"
                                    :key="form_index"
                                    :style="form_item.style ? form_item.style + (form_index === 0 ? ' padding-top:10px;' : '') : { 'padding-top': (form_index === 0?'10px':'0') }"
                                >
                                    <div v-if="form_item.type == 'description'">
                                        <v-sheet
                                            v-html="form_item.text"
                                            class="mb-3 pa-3 subtitle-1 elevation-1"
                                            color="grey lighten-4"
                                        />
                                    </div>

                                    <div v-if="form_item.type == 'select'">
                                        <v-select
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :items="form_item.select_item"
                                            menu-props="auto"
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-icon="form_item.icon"
                                            :hint="form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text"
                                            persistent-hint
                                            single-line
                                        ></v-select>
                                    </div>

                                    <div v-if="form_item.type == 'multi_select'">
                                        <v-select
                                            v-model="formModel[form_item.column]"
                                            :items="form_item.select_item"
                                            menu-props="auto"
                                            v-validate="form_item.validate"
                                            :label="form_item.required ? form_item.text + $t('_required_') : form_item.text"
                                            :error-messages="errors.collect(`formModel.${form_item.column}`)"
                                            :prepend-icon="form_item.icon"
                                            multiple
                                        />
                                    </div>

                                    <div
                                        v-if="
                                            form_item.type == 'label_only'
                                        "
                                    >
                                        <label
                                            style="
                                                color:rgba(0, 0, 0, 0.6);
                                                height: 20px;
                                                line-height: 20px;
                                                letter-spacing: normal;
                                            "
                                        >
                                            {{ form_item.text }}
                                        </label>
                                    </div>

                                    <div
                                        v-if="
                                            form_item.type == 'text_only'
                                        "
                                    >
                                        <v-text-field
                                            outlined
                                            dense
                                            :type="form_item.type"
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"

                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                            :name="form_item.column+'_loyalty_system'"
                                        ></v-text-field>
                                    </div>

                                    <div
                                        v-if="
                                            form_item.type == 'text' ||
                                                form_item.type == 'email'
                                        "
                                    >
                                        <v-text-field
                                            :type="form_item.type"
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                            :name="`${form_item.column}_loyalty_system`"
                                            :autocomplete="form_item.autocomplete"
                                        ></v-text-field>
                                    </div>

                                    <div v-if="form_item.type == 'password'">
                                        <v-text-field
                                            :type="
                                                formModel[
                                                    form_item.column + '_show'
                                                ]
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            :append-icon="
                                                formModel[
                                                    form_item.column + '_show'
                                                ]
                                                    ? 'visibility'
                                                    : 'visibility_off'
                                            "
                                            @click:append="
                                                formModel[
                                                    form_item.column + '_show'
                                                ] = !formModel[
                                                    form_item.column + '_show'
                                                ]
                                            "
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="{
                                                required: false,
                                                min: 8,
                                                regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!?])(?=.*[0-9]).*$/
                                            }"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )[0] ===
                                                'The password field format is invalid'
                                                    ? 'Password should have 1 Capital , 1 Small letter , Number and a special symbol'
                                                    : errors.collect(
                                                          'formModel.' +
                                                              form_item.column
                                                      )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        ></v-text-field>
                                    </div>

                                    <div
                                        v-if="form_item.type == 'wysiwyg'"
                                        class="mt-4"
                                    >
                                        <div>{{ form_item.text }}</div>
                                        <ckeditor
                                            :editor="ClassicEditor"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :config="form_item.config"
                                        >
                                        </ckeditor>
                                    </div>

                                    <div v-if="form_item.type == 'number'">
                                        <v-text-field
                                            :type="form_item.type"
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.vvalidate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        ></v-text-field>
                                    </div>

                                    <div v-if="form_item.type == 'currency'">
                                        <v-text-field
                                            type="number"
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="'required|decimal:2|min_value:0|max_value:1000000'"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        ></v-text-field>
                                    </div>

                                    <div v-if="form_item.type == 'date_picker'">
                                        <v-dialog
                                            ref="datePickers"
                                            v-model="datePicker"
                                            :return-value.sync="
                                                formModel[form_item.column]
                                            "
                                            persistent
                                            width="290px"
                                        >
                                            <template
                                                v-slot:activator="{ on, attrs }"
                                            >
                                                <v-text-field
                                                    v-model="
                                                        formModel[
                                                            form_item.column
                                                        ]
                                                    "
                                                    :label="
                                                        form_item.required
                                                            ? form_item.text +
                                                              $t('_required_')
                                                            : form_item.text
                                                    "
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                ___autofocus="form_index == 0 && uuid == null"
                                                v-model="
                                                    formModel[form_item.column]
                                                "
                                                :ref="form_item.column"
                                                :data-vv-name="form_item.column"
                                                :data-vv-as="
                                                    form_item.text.toLowerCase()
                                                "
                                                v-validate="form_item.validate"
                                                :error-messages="
                                                    errors.collect(
                                                        'formModel.' +
                                                            form_item.column
                                                    )
                                                "
                                                :prepend-inner-icon="
                                                    form_item.icon
                                                "
                                                :placeholder="
                                                    form_item.placeholder
                                                "
                                                :hint="form_item.hint"
                                                :locale="
                                                    $auth
                                                        .user()
                                                        .locale.substring(0, 2)
                                                "
                                                :format="form_item.format"
                                                :persistent-hint="true"
                                                :prefix="form_item.prefix"
                                                :suffix="form_item.suffix"
                                                scrollable
                                            >
                                                <v-spacer></v-spacer>
                                                <v-btn
                                                    text
                                                    color="primary"
                                                    @click="datePicker = false"
                                                >
                                                    Cancel
                                                </v-btn>
                                                <v-btn
                                                    text
                                                    color="primary"
                                                    @click="
                                                        $refs.datePickers[0].save(
                                                            formModel[
                                                                form_item.column
                                                            ]
                                                        )
                                                    "
                                                >
                                                    OK
                                                </v-btn>
                                            </v-date-picker>
                                        </v-dialog>
                                    </div>
                                    <div v-if="form_item.type == 'time_only'">
                                        <v-time-picker></v-time-picker>
                                    </div>
                                    <div v-if="form_item.type == 'date_time'">
                                        <date-time-picker
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :locale="
                                                $auth
                                                    .user()
                                                    .locale.substring(0, 2)
                                            "
                                            :format="form_item.format"
                                            timePickerFormat="ampm"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        >
                                            <template
                                                slot="actions"
                                                slot-scope="{ parent }"
                                            >
                                                <v-btn
                                                    :color="app.color_name"
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

                                    <div v-if="form_item.type == 'time_picker'">
                                        <time-picker
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :locale="
                                                $auth
                                                    .user()
                                                    .locale.substring(0, 2)
                                            "
                                            :format="form_item.format"
                                            timePickerFormat="ampm"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        >
                                            <template
                                                slot="actions"
                                                slot-scope="{ parent }"
                                            >
                                                <v-btn
                                                    :color="app.color_name"
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
                                        </time-picker>
                                    </div>

                                    <div v-if="form_item.type == 'color'">
                                        <color-picker
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :color="formModel[form_item.column]"
                                            :mode="form_item.mode || 'hexa'"
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        >
                                        </color-picker>
                                    </div>

                                    <div v-if="form_item.type == 'slider'">
                                        <v-slider
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :min="form_item.min"
                                            :max="form_item.max"
                                            :step="form_item.step"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        >
                                            <template #append>
                                                <v-text-field
                                                    v-model="
                                                        formModel[
                                                            form_item.column
                                                        ]
                                                    "
                                                    class="pt-0 mt-0"
                                                    hide-details
                                                    single-line
                                                    type="number"
                                                    style="width: 60px"
                                                ></v-text-field>
                                            </template>
                                        </v-slider>
                                    </div>

                                    <div v-if="form_item.type == 'boolean'">
                                        <v-checkbox
                                            type="checkbox"
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            class="mt-0"
                                        ></v-checkbox>
                                    </div>

                                    <div v-if="form_item.type == 'boolean_only'">
                                        <v-checkbox
                                            type="checkbox"
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            class="mt-0"
                                        ></v-checkbox>
                                    </div>

                                    <div v-if="form_item.type == 'country'">
                                        <input
                                            type="text"
                                            v-model="autocompleteCountry"
                                            autocomplete="country-name"
                                            style="height:1px; width:1px;"
                                        />
                                        <v-select
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :items="country"
                                            menu-props="auto"
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-icon="form_item.icon"
                                            single-line
                                        ></v-select>

                                    </div>
                                    <div v-if="form_item.type == 'city'">
                                        <v-text-field
                                            v-model="formModel[form_item.column]"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            v-validate="form_item.validate"
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                        />
                                        <!-- <input
                                            type="text"
                                            v-model="autocompleteCity"
                                            autocomplete="address-level2"
                                            style="height:1px; width:1px;"
                                        />
                                        <v-select
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :items="city"
                                            menu-props="auto"
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-icon="form_item.icon"
                                            single-line
                                        ></v-select> -->
                                    </div>

                                    <div
                                        v-if="form_item.type == 'custom_domain'"
                                    >
                                        <v-text-field
                                            :type="form_item.type"
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        >
                                        </v-text-field>
                                        <v-btn
                                            depressed
                                            color="primary"
                                            small
                                            @click="getCustomDomain"
                                            class="mt-4 mb-5"
                                            >Custom domain guide</v-btn
                                        >
                                        <v-dialog
                                            max-width="600"
                                            v-model="customDomain.dialog"
                                        >
                                            <v-card
                                                :loading="customDomain.loading"
                                                :disabled="customDomain.loading"
                                            >
                                                <v-toolbar color="primary" dark
                                                    >Custom Domain
                                                    Guide</v-toolbar
                                                >
                                                <v-card-text>
                                                    <div
                                                        class="my-5"
                                                        v-html="
                                                            customDomain.content
                                                        "
                                                    ></div>
                                                </v-card-text>
                                                <v-card-actions
                                                    class="justify-end"
                                                >
                                                    <v-btn
                                                        text
                                                        @click="
                                                            customDomain.dialog = false
                                                        "
                                                        >Close</v-btn
                                                    >
                                                </v-card-actions>
                                            </v-card>
                                        </v-dialog>
                                    </div>

                                    <div
                                        v-if="
                                            form_item.type == 'relation' &&
                                                (form_item.relation.type ==
                                                    'hasOne' ||
                                                    form_item.relation.type ==
                                                        'belongsTo')
                                        "
                                    >
                                        <v-autocomplete
                                            dense
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :placeholder="form_item.placeholder"
                                            :prepend-inner-icon="form_item.icon"
                                            :items="
                                                relations[form_item.column]
                                                    .items
                                            "
                                            :loading="
                                                formModel[
                                                    form_item.column +
                                                        '_loading'
                                                ]
                                            "
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                            item-text="val"
                                            item-value="pk"
                                            hide-no-data
                                            hide-selected
                                            clearable
                                        ></v-autocomplete>
                                    </div>

                                    <div
                                        v-if="
                                            form_item.type == 'relation' &&
                                                form_item.relation.type ==
                                                    'belongsToMany'
                                        "
                                    >
                                        <v-autocomplete
                                            dense
                                            ___autofocus="form_index == 0 && uuid == null"
                                            v-model="
                                                formModel[
                                                    form_item.relation.with
                                                ]
                                            "
                                            :ref="form_item.relation.with"
                                            :data-vv-name="
                                                form_item.relation.with
                                            "
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            v-validate="form_item.validate"
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.relation.with
                                                )
                                            "
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :placeholder="form_item.placeholder"
                                            :prepend-inner-icon="form_item.icon"
                                            :items="
                                                relations[
                                                    form_item.relation.with
                                                ].items
                                            "
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                            item-text="val"
                                            item-value="pk"
                                            hide-no-data
                                            hide-selected
                                            chips
                                            multiple
                                            deletable-chips
                                        ></v-autocomplete>
                                    </div>

                                    <div
                                        v-if="
                                            form_item.type == 'image' ||
                                                form_item.type == 'avatar'
                                        "
                                    >
                                        <v-img
                                            :src="
                                                formModel[
                                                    form_item.column +
                                                        '_media_url'
                                                ]
                                            "
                                            class="mt-3 mb-1"
                                            :class="form_item.class"
                                            contain
                                            :style="{
                                                width:
                                                    form_item.image.thumb_width,
                                                height:
                                                    form_item.image
                                                        .thumb_height,
                                                'max-width':
                                                    form_item.image
                                                        .thumb_max_width,
                                                'max-height':
                                                    form_item.image
                                                        .thumb_max_height
                                            }"
                                            v-if="
                                                formModel[
                                                    form_item.column +
                                                        '_media_url'
                                                ]
                                            "
                                        ></v-img>
                                        <v-text-field
                                            @click="pickFile(form_item.column)"
                                            type="text"
                                            readonly
                                            v-model="
                                                formModel[
                                                    form_item.column +
                                                        '_media_name'
                                                ]
                                            "
                                            :ref="form_item.column"
                                            :data-vv-name="form_item.column"
                                            :data-vv-as="
                                                form_item.text.toLowerCase()
                                            "
                                            :label="
                                                form_item.required
                                                    ? form_item.text +
                                                      $t('_required_')
                                                    : form_item.text
                                            "
                                            :error-messages="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :prepend-inner-icon="form_item.icon"
                                            :placeholder="form_item.placeholder"
                                            :hint="form_item.hint"
                                            :persistent-hint="true"
                                            :prefix="form_item.prefix"
                                            :suffix="form_item.suffix"
                                        >
                                            <template slot="append">
                                                <v-icon
                                                    v-if="
                                                        formModel[
                                                            form_item.column +
                                                                '_media_name'
                                                        ] != ''
                                                    "
                                                    @click="
                                                        formModel[
                                                            form_item.column +
                                                                '_media_name'
                                                        ] = '';
                                                        formModel[
                                                            form_item.column +
                                                                '_media_url'
                                                        ] = '';
                                                        formModel[
                                                            form_item.column +
                                                                '_media_changed'
                                                        ] = true;
                                                    "
                                                    >delete</v-icon
                                                >
                                            </template>
                                        </v-text-field>

                                        <input
                                            type="file"
                                            v-validate="form_item.vvalidate"
                                            style="display: none"
                                            :id="form_item.column"
                                            :name="form_item.column"
                                            accept=".png, .jpg"
                                            @change="onFilePicked"
                                        />
                                    </div>

                                    <div v-if="form_item.type === 'phone'">
                                        <input
                                            type="tel"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            style="height:1px; width:1px;"
                                            autocomplete="tel"
                                        />

                                        <vue-tel-input-vuetify
                                            @input="inputPhone"
                                            @country-changed="
                                                countryChange(
                                                    formModel.country_code,
                                                    form_item.column
                                                )
                                            "
                                            mode="international"
                                            :defaultCountry="formModel.country_code"
                                            v-model="formModel[form_item.column]"
                                            :ref="form_item.column"
                                            :name="form_item.column"
                                            :error-messages="
                                                (phonNumberValid
                                                    ? ''
                                                    : 'Please input a valid phone number') ||
                                                    errors.collect(`formModel.${form_item.column}`)
                                            "
                                            :autocomplete="form_item.autocomplete"
                                        />
                                    </div>

                                    <div v-if="form_item.type === 'hidden'">
                                        <input
                                            type="hidden"
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                        />
                                    </div>

                                    <div v-if="form_item.type === 'import'">
                                        <v-file-input
                                            accept="
                                                .csv,
                                                application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                                                application/vnd.ms-excel
                                                application/csv,
                                                text/csv
                                            "
                                            label="Import file"
                                            outlined
                                            v-model="
                                                formModel[form_item.column]
                                            "
                                            :id="form_item.column"
                                            :name="form_item.column"
                                            @change="onFilePicked"
                                        >
                                            <template
                                                v-slot:selection="{ text }"
                                            >
                                                <v-chip
                                                    dark
                                                    color="primary"
                                                    label
                                                    small
                                                >
                                                    {{ text }}
                                                </v-chip>
                                            </template>
                                        </v-file-input>
                                    </div>

                                    <div
                                        v-if="form_item.type === 'coupun_list'"
                                    >
                                        <coupun-list
                                            :data="formModel[form_item.column]"
                                        ></coupun-list>
                                    </div>
                                    <div
                                        v-if="
                                            form_item.type === 'coupun_history'
                                        "
                                    >
                                        <coupun-history
                                            :data="formModel[form_item.column]"
                                        ></coupun-history>
                                    </div>
                                    <div
                                        v-if="
                                            form_item.type ===
                                                'credit_conversion_rule'
                                        "
                                    >
                                        <credit-conversion-rule
                                            :validate="
                                                errors.collect(
                                                    'formModel.' +
                                                        form_item.column
                                                )
                                            "
                                            :data.sync="formModel[form_item.column]"
                                            :mode.sync="formModel['credit_points_mode']"
                                            :uuid="uuid"
                                        ></credit-conversion-rule>
                                    </div>

                                    <div v-if="form_item.type === 'reward_date_range'">
                                        <reward-date-range
                                            :uuid="uuid"
                                        ></reward-date-range>
                                    </div>

                                    <div v-if="form_item.type === 'static_paragraph'">
                                        <p>{{ form_item.text }}</p>
                                    </div>

                                    <div
                                        class="mt-2"
                                        v-if="
                                            form_item.type ===
                                                'button_reset_password' &&
                                                uuid !== null
                                        "
                                    >
                                        <v-btn
                                            block
                                            color="primary"
                                            @click="sendResetPassword()"
                                            :loading="loading"
                                            >{{ form_item.text }}</v-btn
                                        >
                                    </div>
                                </div>
                            </v-tab-item>
                        </v-tabs-items>

                        <!--              </div>-->
                    </v-tab-item>
                </v-tabs-items>

                <p v-if="mode === 'import'">
                    <span v-show="model == `App\\Customer`">Import customer sample download <a href="/sample/import-customer-sample.xlsx">here</a>.</span>
                    <span v-show="model == `App\\User`">Import merchant sample download <a href="/sample/import-merchant-sample.xlsx">here</a>.</span>
                    <span v-show="model == `Platform\\Models\\Reward`">Import offer listing sample download <a href="/sample/import-reward-sample.xlsx">here</a>.</span>
                </p>
            </v-card-text>

            <v-divider
                v-if="settings.dialog_height"
                class="grey lighten-2"
            ></v-divider>

            <v-card-actions v-if="!loadingForm">
                <v-spacer></v-spacer>

                <v-btn
                    type="submit"
                    class="ml-0"
                    :color="app.color_name"
                    text
                    large
                    :loading="form.loading"
                    :disabled="form.loading"
                >
                    {{
                        mode === "import"
                            ? $t("import")
                            : uuid === null
                            ? $t("create")
                            : $t("save")
                    }}
                </v-btn>

                <v-btn
                    color="secondary"
                    text
                    large
                    :disabled="form.loading"
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

        <v-overlay :value="form.loading" v-if="!loadingForm">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
    </v-card>
</template>

<script>
window.moment = require('moment-timezone')
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import CoupunList from "./ui/DataFormComponents/CoupunList";
import CoupunHistory from "./ui/DataFormComponents/CoupunHistory";
import CreditConversionRule from "./ui/DataFormComponents/CreditConversionRule";
import RewardDateRange from './ui/DataFormComponents/RewardDateRange.vue';

export default {
    $_veeValidate: {
        validator: "new"
    },
    components: {
        CoupunList,
        CoupunHistory,
        CreditConversionRule,
        RewardDateRange
    },
    data: () => {

       return {
            ClassicEditor: ClassicEditor,
            tabCount: 1,
            selectedTab: "tab1",
            selectedSubTab: {
                tab1: "sub1",
                tab2: "sub1",
                tab3: "sub1",
                tab4: "sub1",
                tab5: "sub1",
                tab6: "sub1",
                tab7: "sub1",
                tab8: "sub1",
                tab9: "sub1",
                tab10: "sub1",
                tab11: "sub1",
                tab12: "sub1"
            },
            count: null,
            max: null,
            limitReached: null,
            activeFilePickerColumn: null,
            formModel: {},
            loadingForm: true,
            loading: true,
            datePicker: false,
            country: [],
            city: [],
            settings: [],
            form: [],
            translations: [],
            phonNumberValid: true,
            countryChanged: false,
            relations: [],
            autocompleteCountry: 0,
            autocompleteCity: 0,
            customDomain: {
                dialog: false,
                loading: true,
                content: ""
            }
        };
    },
    watch: {
        autocompleteCountry: function(event) {
            let countryName = event;
            let countryId = this.country.filter(function(item){
                return item.text === countryName;
            })[0].value;

            if(countryId) {
                this.formModel.country_id = countryId;
            }
        },
        autocompleteCity: function(event) {
            let cityName = event;
            let cityId = this.city.filter(function(item){
                return item.text === cityName;
            })[0].value;

            if (cityId) {
                this.formModel.city_id = cityId;
            }
        }
    },
    props: {
        api: {
            default: "/app/data-form",
            required: false,
            type: String
        },
        model: {
            default: "",
            required: false,
            type: String
        },
        uuid: {
            default: null,
            required: false,
            type: String
        },
        mode: {
            default: "crud",
            required: false,
            type: String
        }
    },
    computed: {
        app() {
            return this.$store.getters.app;
        }
    },
    mounted() {
        axios.get("/localization/country").then(response => {
            this.country = response.data.data;
        });
    },
    created() {
        this.$validator.extend("nullable", {
            validate: (value, [testProp]) => {
                return true;
            }
        });

        this.$validator.extend("unique", {
            validate: (value, [testProp]) => {
                return true;
            }
        });

        this.$validator.extend("not_in", {
            validate: (value, [testProp]) => {
                return true;
            }
        });
        this.$validator.extend("time_greater_than", {
            getMessage: field => "Time to should be greater than time from.",
            validate: (time_to, [time_from]) => {
                return time_to > time_from;
            }
        }, {
            hasTarget: true
        });

        moment.locale(this.$auth.user().locale);
    },
    beforeMount() {
        this.getDataFromApi().then(data => {
            this.form = data.form;

            this.tabCount = Object.keys(this.form.items).length;

            if (this.formModel['active_from'] && typeof this.formModel['active_from'] !== 'undefined') {
                let tz_active_from = moment(this.formModel['active_from']).tz('UTC').format().split('Z');

                tz_active_from[1] = moment().format().substr(19); // e.g: +07:00

                this.formModel['active_from'] = new Date(tz_active_from.join(""));
            }

            if (this.formModel['expires_at'] && typeof this.formModel['expires_at'] !== 'undefined') {
                let tz_expires_at = moment(this.formModel['expires_at']).tz('UTC').format().split('Z');

                tz_expires_at[1] = moment().format().substr(19); // e.g: +07:00

                this.formModel['expires_at'] = new Date(tz_expires_at.join(""));
            }
        });
    },
    methods: {
        submitForm(scope) {
            this.form.has_error = false;

            this.form.loading = true;

            this.$validator.errors.clear();

            if (this.tabCount > 1) {
                for (let i = 2; i <= this.tabCount; i++) {}
            }

            this.$validator.validateAll(scope).then(valid => {
                if (valid) {
                    this.saveForm();
                } else {
                    // Get first error and select tab where error occurs
                    let field = this.errors.items[0].field;

                    let el =
                        typeof this.$refs[field] !== "undefined"
                            ? this.$refs[field]
                            : null;
                    let subtab = el !== null ? el[0].$parent.$vnode.key : null;

                    let tab =
                        el !== null
                            ? el[0].$parent.$parent.$parent.$vnode.key
                            : null;

                    if (tab !== null) this.selectedTab = tab;

                    if (tab !== null && subtab !== null)
                        this.selectedSubTab[tab] = subtab;

                    this.form.loading = false;

                    return false;
                }
            });
        },
        saveForm() {
            this.loading = true;

            let that = this;

            let settings = {
                headers: { "content-type": "multipart/form-data" }
            };

            // Remove image urls
            let formModel = Object.assign({}, this.formModel);
            for (let field in this.formModel) {
                if (
                    _.endsWith(field, "_media_url") ||
                    _.endsWith(field, "_media_name") ||
                    _.endsWith(field, "_media_file") ||
                    field == "avatar" ||
                    field == "import"
                ) {
                    formModel[field] = null;
                }

                // console.log(formModel['expires_at']);

                // //Normalize active_from and expires_at to UTC timezone
                if ((field == 'active_from' || field == 'expires_at') && formModel[field]) {
                    formModel[field] = (new Date(formModel[field] - (formModel[field]).getTimezoneOffset() * 60000))
                        .toISOString()
                        .split('T')
                        .map((date) => date.split('.')[0]).join(' ');
                }
            }

            let formData = new FormData(document.getElementById("formModel"));

            formData.append("model", this.model);

            formData.append("formModel", JSON.stringify(formModel));

            if (this.mode === "crud") {
                formData.append("locale", this.$i18n.locale);
                formData.append("uuid", this.uuid);
                axios
                    .post(this.api + "/save", formData, settings)
                    .then(res => {
                        if (res.data.status === "success") {
                            let action =
                                this.uuid === null
                                    ? "item_created"
                                    : "item_saved";
                            this.$root.$snackbar(this.$t(action));
                            this.$emit("data-list-events", {
                                closeDialog: true,
                                reload: true
                            });
                        }
                    })
                    .catch(err => {
                        if (typeof err.response !== "undefined") {
                            if (
                                typeof err.response.status !== "undefined" &&
                                typeof err.response.data.msg !== "undefined" &&
                                err.response.data.msg !== ""
                            ) {
                                if (err.response.status == 422) {
                                    this.$root.$snackbar(err.response.data.msg);
                                    return;
                                }
                            }
                            this.formModel.has_error = true;
                            this.formModel.error = err.response.data.error;
                            this.formModel.errors =
                                err.response.data.errors || {};

                            // Get first error and select tab where error occurs
                            let field = Object.keys(this.formModel.errors)[0];
                            let el =
                                typeof this.$refs[field] !== "undefined"
                                    ? this.$refs[field]
                                    : null;
                            let tab =
                                el !== null ? el[0].$parent.$vnode.key : null;
                            if (tab !== null) this.selectedTab = tab;

                            for (let field in this.formModel.errors) {
                                this.$validator.errors.add({
                                    field: "formModel." + field,
                                    msg: this.formModel.errors[field][0]
                                });
                            }
                        }
                    })
                    .finally(() => {
                        that.loading = false;
                        that.form.loading = false;
                    });
            } else {
                axios
                    .post(this.api + "/import", formData, settings)
                    .then(res => {
                        if (res.data.status === "success") {
                            let action =
                                this.uuid === null
                                    ? "item_created"
                                    : "item_saved";

                            this.$root.$snackbar(this.$t(action));
                        }
                    })
                    .catch(err => {
                        this.$root.$snackbar("Upload failed to process.", { color: 'red' });
                    })
                    .finally(() => {
                        this.$emit("data-list-events", {
                            closeDialog: true,
                            reload: true
                        });
                    });
            }
        },
        getDataFromApi() {
            this.loading = true;
            return new Promise((resolve, reject) => {
                let that = this;
                axios
                    .get(this.api, {
                        params: {
                            locale: this.$i18n.locale,
                            model: this.model,
                            uuid: this.uuid,
                            mode: this.mode
                        }
                    })
                    .then(res => {
                        if (res.data.status === "success") {
                            let form = {};

                            form.items = res.data.form;
                            form.loading = false;
                            form.error = "";
                            form.errors = {};
                            form.has_error = false;
                            form.success = false;

                            that.settings = res.data.settings;
                            that.translations = res.data.translations;
                            that.relations = res.data.relations;
                            if (that.mode === "crud") {
                                that.formModel = res.data.values;
                                // that.relations = res.data.relations;
                                that.count = res.data.count;
                                that.max = res.data.max;
                                that.limitReached = res.data.limitReached;

                                // Dates
                                for (let date of res.data.dates) {
                                    if (that.formModel[date] !== null) {
                                        that.formModel[date] = new Date(
                                            that.formModel[date]
                                        );
                                    }
                                }
                            }
                            that.loading = false;
                            that.loadingForm = false;

                            // Relations
                            /*  for (let relation of res.data.relations) {
                                    this.getRelatedData(relation.column, relation)
                            } */

                            resolve({
                                form
                            });
                        }
                    })
                    .catch(err => console.log(err.response.data))
                    .finally(() => (that.loading = false));
            });
        },
        pickFile(column) {
            this.activeFilePickerColumn = column;
            document.getElementById(column).click();
        },
        onFilePicked(e) {
            if (this.mode === "import") {
                const file = e;
                const fr = new FileReader();
                fr.readAsDataURL(file);
                fr.addEventListener("load", () => {
                    this.formModel["file"] = file;
                });
            } else {
                const files = e.target.files;
                if (files[0] !== undefined) {
                    this.formModel[
                        this.activeFilePickerColumn + "_media_name"
                    ] = files[0].name;
                    if (
                        this.formModel[
                            this.activeFilePickerColumn + "_media_name"
                        ].lastIndexOf(".") <= 0
                    ) {
                        return;
                    }
                    const fr = new FileReader();
                    fr.readAsDataURL(files[0]);
                    fr.addEventListener("load", () => {
                        this.formModel[
                            this.activeFilePickerColumn + "_media_url"
                        ] = fr.result;
                        this.formModel[
                            this.activeFilePickerColumn + "_media_file"
                        ] = files[0]; // this is an image file that can be sent to server...
                        this.formModel[
                            this.activeFilePickerColumn + "_media_changed"
                        ] = true;
                    });
                } else {
                    this.formModel[
                        this.activeFilePickerColumn + "_media_name"
                    ] = "";
                    this.formModel[
                        this.activeFilePickerColumn + "_media_file"
                    ] = "";
                    this.formModel[this.activeFilePickerColumn + "_media_url"] =
                        "";
                    this.formModel[
                        this.activeFilePickerColumn + "_media_changed"
                    ] = true;
                }
            }
        },
        getRelatedData(column, relation) {
            let that = this;
            axios
                .post(this.api + "/relation", {
                    locale: this.$i18n.locale,
                    model: this.model,
                    uuid: this.uuid,
                    relation: relation
                })
                .then(res => {
                    if (res.data.status === "success") {
                        that.formModel[column + "_items"] = res.data.fields;
                        that.formModel[column + "_loading"] = false;
                    }
                })
                .catch(err => console.log(err.response.data));
        },
        sendResetPassword() {
            this.loading = true;
            axios
                .post("/auth/password/reset", {
                    locale: this.$i18n.locale,
                    email: this.formModel.email
                })
                .then(response => {
                    if (response.data.status === "success") {
                        this.$root.$snackbar(response.data.msg);
                    } else if (typeof response.data.error !== "undefined") {
                        this.$root.$snackbar(
                            "There was an error while trying to send the email."
                        );
                    }
                })
                .catch(error => {
                    this.$root.$snackbar(
                        "There was an error while trying to send the email."
                    );
                })
                .finally(() => (this.loading = false));
        },
        inputPhone(formattedNumber, { number, valid, country }) {
            this.phonNumberValid = valid;
            this.formModel.country_isd_code = country.dialCode;
            this.formModel.country_code = country.iso2;
        },
        countryChange(countryCode, model) {
            if (countryCode && this.countryChanged == true) {
                this.formModel[model] = "";
            } else {
                this.formModel[model] = this.formModel[model];
                this.countryChanged = true;
            }
        },
        getCustomDomain() {
            this.customDomain.dialog = true;
            this.customDomain.loading = true;
            axios.get("/admin/domain-guide").then(response => {
                this.customDomain.content = response.data.content;
                this.customDomain.loading = false;
            });
        }
    }
};
</script>
<style scoped></style>
