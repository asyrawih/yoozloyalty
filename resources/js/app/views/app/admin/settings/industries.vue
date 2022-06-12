<template>
    <data-table
        model="Platform\Models\Industry"
    >
        <template v-slot:empty-head>
            <v-icon size="72" :color="$store.getters.app.color_name">business</v-icon>
        </template>

        <template v-slot:empty-text>
            <p class="subheading">Business industry.</p>
        </template>

    </data-table>
</template>
<script>
export default {
    data() {
        return {
        }
    },
    methods: {
    }
}
</script>
