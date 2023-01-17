<template>
  <div class="text-center">
    <h1 class="pt-5">Login Page</h1>
    <div class="px-7 py-4">
      <v-form ref="form" v-model="valid" lazy-validation>
        <v-text-field
          v-model="email"
          :rules="emailRules"
          label="E-mail"
          required
        ></v-text-field>
        <v-text-field
          v-model="password"
          :counter="10"
          :rules="passwordRules"
          label="Password"
          type="password"
          required
        ></v-text-field>

        <v-btn color="success" class="mr-4" @click.prevent="login()">
          Login
        </v-btn>

        <v-btn color="error" class="mr-4" @click="reset"> Reset Form </v-btn>
      </v-form>
    </div>
  </div>
</template>


<script>
export default {
  name: "LoginView",
  data: () => ({
    valid: true,
    isLoading: false,
    password: "",
    passwordRules: [
      (v) => !!v || "password is required",
      (v) =>
        (v && v.length <= 10) || "password must be less than 10 characters",
    ],
    email: "",
    emailRules: [
      (v) => !!v || "E-mail is required",
      (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
    ],
  }),

  methods: {
    reset() {
      this.$refs.form.reset();
    },

    login() {
      this.isLoading = true;
      this.$store
        .dispatch("login", {
          email: this.email,
          password: this.password,
        })
        .then(() => {
          this.isLoading = false;
          this.$router.push("/");
        })
        .catch((error) => {
          this.isLoading = false;
          this.error = " There was error during login process";
          console.log(error.message);
        });
    },
  },
};
</script>
