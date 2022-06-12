<template>
  <v-container fluid fill-height>
    <v-layout align-center justify-center row fill-height wrap>

      <v-flex xs10 sm7 md5 lg3 xl2>

        <v-form 
          data-vv-scope="form1"
          :model="form1" 
          lazy-validation
          @submit.prevent="submitForm('form1')"
          autocomplete="off"
          method="post"
          >
          <v-card class="elevation-18 my-4">
            <v-toolbar flat color="transparent">
              <v-toolbar-title>Congratulation!</v-toolbar-title>
            </v-toolbar>
            
            <v-card-text>
              <p class="body-1">Success verification email.</p>

            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="primary" large block @click="toLogin" >Login</v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
      </v-flex>
    </v-layout>
  </v-container>
</template>
<script>
  export default {
    data() {
      return {
        errorMsg: '',
        form1: {
          loading: false,
          email: null,
          has_error: false,
        }
      }
    },
    methods: {
      toLogin() {
        this.$router.push({name: 'login'})
      },

    },
    computed: {
      campaign () {
        return this.$store.state.app.campaign
      }
    },
  }
</script>