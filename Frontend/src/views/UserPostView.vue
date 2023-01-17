<template>
  <div class="text-center">
    <h1>My Posts</h1>
  </div>
  <v-row class="px-3 py-5">
    <v-card
      class="ma-5"
      max-width="350"
      v-for="(post, index) in CurrentUserPosts"
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
      <v-card-actions class="justify-center">
        <v-btn
          color="green"
          rounded="lg"
          :to="{
            name: 'PostDetails',
            params: {
              id: post.id,
            },
          }"
          >show more <v-icon color="green" class="mx-2">mdi-eye</v-icon>
        </v-btn>
        <v-btn
          v-if="get_loggedIn"
          color="red"
          rounded="lg"
          @click.prevent="deletePost(post.id)"
        >
          <v-icon color="red"> mdi-delete-sweep</v-icon>
        </v-btn>
      </v-card-actions>
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
            label=" Title"
            prepend-icon="mdi-rename-box"
          ></v-text-field>

          <v-text-field label="Content" prepend-icon=" mdi-city"></v-text-field>

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
    this.$store.dispatch("getCurrentUserPosts");
  },
  data: () => ({}),
  methods: {
    deletePost(post_id) {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        id: post_id,
      };
      return new Promise((resolve, reject) => {
        axios
          .post("http://127.0.0.1:8000/api/post/delete", data, {
            headers: headers,
          })
          .then((res) => {
            //this.$router.push("/posts");
            location.reload();
            resolve(res);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
  },
  computed: {
    CurrentUserPosts() {
      return this.$store.getters.getCurrentUserPosts;
    },
    get_loggedIn() {
      return this.$store.getters.get_loggedIn;
    },
    getToken() {
      return this.$store.getters.getToken;
    },
  },
};
</script>
