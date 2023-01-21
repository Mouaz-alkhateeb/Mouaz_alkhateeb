<template>
  <div class="text-center">
    <v-progress-circular
      v-if="loader_work"
      :size="70"
      :width="3"
      indeterminate
      color="primary"
    ></v-progress-circular>
  </div>
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
        :src="'data:image/jpeg;base64,' + post.file_data"
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
  data: () => ({
    loader_work: false,
  }),
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
      this.loader_work = true;
      return new Promise((resolve, reject) => {
        axios
          .post(this.getApiURL + "/post/delete", data, {
            headers: headers,
          })
          .then((res) => {
            this.loader_work = false;
            this.$router.go(0);
            resolve(res);
          })
          .catch((error) => {
            this.loader_work = false;
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
    getApiURL() {
      return this.$store.getters.getApiURL;
    },
  },
};
</script>
