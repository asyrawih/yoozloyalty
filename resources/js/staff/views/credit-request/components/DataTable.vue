<template>

</template>

<script>
import { computed, defineComponent, onMounted, reactive, watch } from '@vue/composition-api';

import ServiceCreditRequestApi from '../services/CreditRequestApi';

export default defineComponent({
    props: {
        refreshedTable: {
            type: Boolean,
            default: false,
        }
    },
    setup(props, { root }) {
        const state = reactive({

        });

        const locale = computed(() => root.$i18n.locale);
        const campaign = computed(() => root.$store.state.app.campaign);

        watch(() => props.refreshedTable, async (current, previous) => {
            if (current) {
                const { page, itemsPerPage } = state.options;

                await onLoaded(page, itemsPerPage);
            }
        });

        onMounted(async () => {
            const { page, itemsPerPage } = state.options;

            await onLoaded(page, itemsPerPage);
        });

        const onLoaded = async (page = 1, perPage = 5, search = '') => {
            state.loading = true;

            const response = await ServiceCreditRequestApi().index({
                locale: locale.value,
                uuid: campaign.value.uuid,
                page,
                perPage,
                search
            });

            state.items = response.data;
            state.total = response.meta.total;

            state.loading = false;
        };

        const update = async (id, status) => {
            try {
                const response = await ServiceCreditRequestApi().update(id, {
                    locale: locale.value,
                    uuid: campaign.value.uuid,
                    status,
                });
            } catch (exception) {
                throw exception.message;
            }
        }

        return {
            state,
            update,
        };
    },
    methods: {

    }
});
</script>
