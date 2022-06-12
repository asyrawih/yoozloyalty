<template>
    <div>
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
                            Customer name
                        </th>
                        <th class="text-left">
                            Customer Number
                        </th>
                        <th class="text-left">
                            Used Time
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="data.length > 0">
                        <tr
                            v-for="(coupun, index) in data"
                            :key="'coupun-' + index"
                        >
                            <td class="text-left">
                                {{ coupun.coupun_code.name }}
                            </td>
                            <td class="text-left">
                                {{ coupun.coupun_code.code }}
                            </td>
                            <td class="text-left">
                                {{ coupun.users.name }}
                            </td>
                            <td class="text-left">
                                +{{ coupun.users.country_isd_code }}
                                {{ coupun.users.customer_number }}
                            </td>
                            <td class="text-left">
                                {{ coupun.created_at }}
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="4">
                            <p class="text-muted mt-2 text-center">
                                No Coupon Used
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
            name: ""
        };
    },
    props: {
        data: {
            type: Array
        }
    },
    computed: {},
    methods: {}
};
</script>
