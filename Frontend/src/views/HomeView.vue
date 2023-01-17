<template>
  <div class="text-center">
    <h1>
      All Posts
      <v-btn
        v-if="get_loggedIn"
        variant="outlined"
        rounded="xl"
        class="ml-3"
        color="blue"
        @click="this.dialog = true"
        >Add Post</v-btn
      >
    </h1>
  </div>
  <v-row class="px-3 py-5">
    <v-card
      class="ma-5"
      max-width="350"
      v-for="(post, index) in posts"
      :key="index"
    >
      <v-img
        class="align-end text-white"
        height="150"
        src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
        cover
      >
        <v-card-title>{{ post.title }}</v-card-title>
      </v-img>
      <v-card-text>
        <div>{{ post.content }}</div>
      </v-card-text>
      <v-card-actions class="justify-center"> </v-card-actions>
    </v-card>
  </v-row>
  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <v-card-title>
        <span class="text-h5">Add A New Post</span>
      </v-card-title>
      <v-card-text>
        <v-form class="px-3" ref="form">
          <v-text-field
            v-model="title"
            label=" Title"
            required
            prepend-icon="mdi-rename-box"
          ></v-text-field>

          <v-text-field
            v-model="content"
            label="Content"
            required
            prepend-icon=" mdi-city"
          ></v-text-field>

          <v-file-input
            v-model="image"
            label="Upload Image"
            prepend-icon=" mdi-file"
          ></v-file-input>

          <br />
          <v-btn
            color="blue darken-1"
            class="mr-2"
            text
            @click.prevent="createpost()"
            rounded
          >
            Add Now
          </v-btn>
          <v-btn
            color="red darken-1"
            text
            @click.prevent="this.dialog = false"
            rounded
          >
            close
          </v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from "axios";
export default {
  name: "HomeView",
  mounted() {
    this.$store.dispatch("getAllPosts");
  },
  data: () => ({
    dialog: false,
    content: "",
    title: "",
    image: "",
  }),
  methods: {},
  computed: {
    posts() {
      return this.$store.getters.getAllPosts;
    },
    get_loggedIn() {
      return this.$store.getters.get_loggedIn;
    },
    getToken() {
      return this.$store.getters.getToken;
    },
  },
  methods: {
    createpost() {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        title: this.title,
        content: this.content,
        image: this.image,
      };
      return new Promise((resolve, reject) => {
        axios
          .post("http://127.0.0.1:8000/api/post/create", data, {
            headers: headers,
          })
          .then((res) => {
            this.$router.push("/posts");
            resolve(res);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
  },
};
</script>
