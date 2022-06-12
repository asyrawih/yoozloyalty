<template>
  <data-table
    model="Platform\Models\Business"
  >
    <template v-slot:empty-head>
      <v-icon size="72" :color="$store.getters.app.color_name">businestoresss</v-icon>
    </template>

    <template v-slot:empty-text>
      <p class="subheading">A stores is an entity to which you can link staff members and campaigns.</p>
      <p class="subheading">Fill in as many details as relevant for your stores. These details will be used for the loyalty campaign website.</p>
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