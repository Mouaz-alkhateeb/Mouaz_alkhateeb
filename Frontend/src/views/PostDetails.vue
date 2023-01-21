<template>
  <v-card class="auto" max-width="1200">
    <v-img
      class="align-end text-white"
      height="400"
      :src="'data:image/jpeg;base64,' + post.file_data"
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
              <v-list-subheader
                >{{ post.commentsCount }} Comments
              </v-list-subheader>
              <template v-for="(comment, index) in comments" :key="index">
                <v-list-item>
                  <template v-slot:prepend>
                    <v-avatar color="grey-darken-1"></v-avatar>
                  </template>

                  <v-list-item-title>
                    {{ comment.username }}
                    <v-btn
                      v-if="get_loggedIn"
                      color="red"
                      size="xs"
                      rounded
                      class="px-2 justify-center"
                      @click.prevent="deleteComment(comment.id)"
                    >
                      Delete
                    </v-btn>
                  </v-list-item-title>

                  <v-list-item-subtitle>
                    {{ comment.content }}
                  </v-list-item-subtitle>
                </v-list-item>
                <v-divider v-if="n !== 3" :key="`divider-${n}`" inset>
                </v-divider>
              </template>
            </v-list>
          </v-card>
          <v-form ref="form" v-model="valid" lazy-validation
            ><div class="my-2">
              <v-text-field
                v-model="commentContent"
                label="Add Comment"
                required
                :rules="commentRules"
              ></v-text-field>
            </div>
            <v-card-actions>
              <v-btn
                v-if="commentContent"
                color="green"
                @click.prevent="addComment()"
              >
                Add Comment</v-btn
              >
            </v-card-actions></v-form
          >
        </v-col>
      </v-row>
    </div>
  </v-card>
  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <v-card-title>
        <span class="text-h5">Update Post</span>
      </v-card-title>
      <div class="text-center">
        <v-progress-circular
          v-if="loader_work"
          :size="70"
          :width="3"
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>
      <v-card-text>
        <v-form class="px-3" ref="form" v-model="valid" lazy-validation>
          <v-text-field
            v-model="post.title"
            label=" Title"
            required
            :rules="titleRules"
            prepend-icon="mdi-rename-box"
          ></v-text-field>

          <v-text-field
            v-model="post.content"
            label="Content"
            required
            :rules="contentRules"
            prepend-icon=" mdi-city"
          ></v-text-field>
          <v-img
            class="align-end text-white"
            height="400"
            :src="'data:image/jpeg;base64,' + post.file_data"
            cover
          ></v-img>
          <v-file-input
            id="image"
            ref="fileInput"
            v-model="post.image"
            label="Upload Image"
            prepend-icon=" mdi-file"
            @input="onSelectFile"
          ></v-file-input>

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
          <v-btn color="red darken-1" text @click.prevent="close()" rounded>
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
    loader_work: false,
    valid: true,
    titleRules: [
      (v) => !!v || "Title is required",
      (v) => (v && v.length <= 20) || "Title must be less than 20 characters",
    ],
    contentRules: [(v) => !!v || "Content is required"],

    comments: [],
    commentContent: "",
    commentRules: [(v) => !!v || "Content Comment is required"],
    post: {
      title: "",
      content: "",
      image: "",
      commentsCount: 0,
      file_data: "",
    },
  }),
  computed: {
    getToken() {
      return this.$store.getters.getToken;
    },
    getApiURL() {
      return this.$store.getters.getApiURL;
    },
    get_loggedIn() {
      return this.$store.getters.get_loggedIn;
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
          .post(this.getApiURL + "/post/show", data, {
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
    async addComment() {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        post_id: this.post_id,
        content: this.commentContent,
      };

      //const { valid } = await this.$refs.form.validate();

      return new Promise((resolve, reject) => {
        axios
          .post(this.getApiURL + "/post/add_comment", data, {
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
    async updatepost() {
      const headers = {
        accept: "application/json",
        "Content-Type": "multipart/form-data",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        id: this.post_id,
        title: this.post.title,
        content: this.post.content,
        image: this.post.image,
      };
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        this.loader_work = true;
        return new Promise((resolve, reject) => {
          axios
            .post(this.getApiURL + "/post/update", data, {
              headers: headers,
            })
            .then((res) => {
              this.loader_work = false;
              location.reload();
              resolve(res);
            })
            .catch((error) => {
              this.loader_work = false;
              reject(error);
            });
        });
      }
    },
    deleteComment(comment_id) {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + this.getToken,
      };
      let data = {
        id: comment_id,
      };
      return new Promise((resolve, reject) => {
        axios
          .post(this.getApiURL + "/post/delete_comment", data, {
            headers: headers,
          })
          .then((res) => {
            this.$router.go(0);
            resolve(res);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    onSelectFile() {
      const input = this.$refs.fileInput;
      const { files } = input;

      if (files && files[0]) {
        const reader = new FileReader();

        reader.onload = (e) => {};
        reader.readAsDataURL(files[0]);
        this.image = files[0];
      }
    },
    close() {
      this.dialog = false;
      this.$refs.form.reset();
    },
  },
};
</script>

<style>
</style>
