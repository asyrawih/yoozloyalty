<template>
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

        <v-card-title>
            <span class="text-h5">Delete Confirmation</span>
        </v-card-title>

        <v-divider class="grey lighten-2"></v-divider>

        <v-card-text class="text-center">
            <br />

            <strong>
                Are you sure, you want to delete this data ?
            </strong>
        </v-card-text>

        <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn
                color="secondary"
                text
                @click="onCloseClick"
            >
                Close
            </v-btn>

            <v-btn
                color="error"
                text
                @click="onOKClick"
            >
                OK
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import { defineComponent, reactive } from '@vue/composition-api';

import ServiceStaffRoles from '../services/StaffRolesApi';

export default defineComponent({
    name: "confirmation-card",
    props: {
        identifier: {
            type: Number,
            default: null,
        },
    },
    setup(props, context) {
        const state = reactive({
            loading: false
        });

        const onCloseClick = () => {
            context.emit('onClose');
        }

        const onOKClick = async () => {
            let response = {
                status: 'primary',
                message: 'CONFIRMATION_MESSAGE',
            };

            try {
                if (props.identifier) {
                    response = await ServiceStaffRoles().deleteApi(props.identifier);
                }
            } catch (exception) {
                response = exception;
            }

            context.emit('onOK', response);
        }

        return {
            state,
            onCloseClick,
            onOKClick
        };
    },
})
</script>
