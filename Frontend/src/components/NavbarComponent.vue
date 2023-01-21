<template>
  <v-app-bar flat>
    <v-container class="fill-height d-flex align-center">
      <v-avatar class="mr-10 ml-4" color="grey-darken-1" size="32"></v-avatar>

      <v-btn to="/" variant="text"> Home </v-btn>
      <v-btn to="/posts" v-if="get_loggedIn" variant="text">My Posts </v-btn>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-btn v-if="!get_loggedIn" to="/login" variant="text"> Login </v-btn>
      <v-btn v-if="!get_loggedIn" to="/register" variant="text">
        Register
      </v-btn>
      <v-btn v-if="get_loggedIn" variant="text" @click.prevent="logout()">
        Logout
      </v-btn>

      <v-spacer></v-spacer>
    </v-container>
  </v-app-bar>
</template>

<script>
export default {
  data() {
    return {
      token: null,
    };
  },
  mounted() {
    this.checkUserStatus();
  },
  methods: {
    checkUserStatus() {
      if (localStorage.getItem("token") != null) {
        this.token = localStorage.getItem("token");
      }
    },
    logout() {
      this.$store
        .dispatch("logout")
        .then(() => {
          this.$router.push("/login");
        })
        .catch((error) => {
          console.log(error.message);
        });
    },
  },
  computed: {
    get_loggedIn() {
      return this.$store.getters.get_loggedIn;
    },
  },
};
</script>

<style>
</style>
