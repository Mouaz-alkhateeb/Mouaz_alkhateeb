<template>
  <v-card class="auto" max-width="1200">
    <v-img
      class="align-end text-white"
      height="400"
      src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
      cover
    >
      <v-card-title>{{ post.title }}</v-card-title>
    </v-img>
    <div class="ml-4 mt-5 justify-end">
      <v-btn
        color="blue"
        class="mr-5"
        style="float: right !important"
        @click.prevent="this.dialog = true"
      >
        Update Post</v-btn
      >
    </div>
    <v-card-text>
      <div>{{ post.content }}</div>
    </v-card-text>
    <div class="mx-4">
      <v-row>
        <v-col v-for="card in cards" :key="card" cols="12">
          <v-card>
            <v-list lines="two">
              <v-list-subheader>{{ card }}</v-list-subheader>
              <template v-for="(comment, index) in comments" :key="index">
                <v-list-item>
                  <template v-slot:prepend>
                    <v-avatar color="grey-darken-1"></v-avatar>
                  </template>

                  <v-list-item-title> {{ comment.username }}</v-list-item-title>

                  <v-list-item-subtitle>
                    {{ comment.content }}
                  </v-list-item-subtitle>
                </v-list-item>

                <v-divider
                  v-if="n !== 3"
                  :key="`divider-${n}`"
                  inset
                ></v-divider>
              </template>
            </v-list>
          </v-card>
          <div class="my-2">
            <v-text-field
              v-model="commentContent"
              label="Add Comment"
              required
            ></v-text-field>
          </div>
        </v-col>
      </v-row>
    </div>
    <v-card-actions>
      <v-btn color="green" @click.prevent="addComment()"> Add Comment</v-btn>
    </v-card-actions>
  </v-card>
  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <v-card-title>
        <span class="text-h5">Update Post</span>
      </v-card-title>
      <v-card-text>
        <v-form class="px-3" ref="form">
          <v-text-field
            v-model="post.title"
            label=" Title"
            required
            prepend-icon="mdi-rename-box"
          ></v-text-field>

          <v-text-field
            v-model="post.content"
            label="Content"
            required
            prepend-icon=" mdi-city"
          ></v-text-field>

          <!-- <v-file-input
            v-model="post.image"
            label="Upload Image"
            prepend-icon=" mdi-file"
          ></v-file-input> -->

          <br />
          <v-btn
            color="blue darken-1"
            class="mr-2"
            text
            @click.prevent="updatepost()"
            rounded
          >
            update Now
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
  mounted() {
    this.post_id = this.$route.params.id;
    this.getPostDetails();
  },
  data: () => ({
    post_id: null,
    cards: ["All Comments"],
    dialog: false,
    comments: [],
    commentContent: "",
    post: {
      title: "",
      content: "",
      image: null,
    },
  }),
  computed: {
    getToken() {
      return this.$store.getters.getToken;
    },
  },
  methods: {
    getPostDetails() {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        id: this.post_id,
      };
      return new Promise((resolve, reject) => {
        axios
          .post("http://127.0.0.1:8000/api/post/show", data, {
            headers: headers,
          })
          .then((res) => {
            this.post = res.data.data;
            this.comments = res.data.data.comments;
            resolve(res);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    addComment() {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        post_id: this.post_id,
        content: this.commentContent,
      };
      return new Promise((resolve, reject) => {
        axios
          .post("http://127.0.0.1:8000/api/post/add_comment", data, {
            headers: headers,
          })
          .then((res) => {
            this.commentContent = "";
            location.reload();
            resolve(res);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    updatepost() {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        id: this.post_id,
        title: this.post.title,
        content: this.post.content,
        image: this.post.image,
      };
      return new Promise((resolve, reject) => {
        axios
          .post("http://127.0.0.1:8000/api/post/update", data, {
            headers: headers,
          })
          .then((res) => {
            // this.$router.push("/posts");
            location.reload();
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

<style>
</style>
