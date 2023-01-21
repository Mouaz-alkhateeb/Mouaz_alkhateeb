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
        :src="'data:image/jpeg;base64,' + post.file_data"
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
      <div class="text-center">
        <v-progress-circular
          v-if="loader_work"
          :size="70"
          :width="3"
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>
      <v-card-title>
        <span class="text-h5">Add A New Post</span>
      </v-card-title>
      <v-card-text>
        <v-form class="px-3" ref="form" v-model="valid" lazy-validation>
          <v-text-field
            v-model="title"
            label=" Title"
            required
            prepend-icon="mdi-rename-box"
            :rules="titleRules"
          ></v-text-field>

          <v-text-field
            v-model="content"
            label="Content"
            required
            :rules="contentRules"
            prepend-icon=" mdi-city"
          ></v-text-field>
          <v-file-input
            id="image"
            ref="fileInput"
            v-model="image"
            label="Upload Image"
            prepend-icon=" mdi-file"
            @input="onSelectFile"
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
  name: "HomeView",
  mounted() {
    this.$store.dispatch("getAllPosts");
  },
  data: () => ({
    dialog: false,
    content: "",
    image: "",
    valid: true,
    title: "",
    titleRules: [
      (v) => !!v || "Title is required",
      (v) => (v && v.length <= 20) || "Title must be less than 20 characters",
    ],
    contentRules: [(v) => !!v || "Content is required"],
    loader_work: false,
  }),
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
    getApiURL() {
      return this.$store.getters.getApiURL;
    },
  },
  methods: {
    async createpost() {
      const headers = {
        accept: "application/json",
        authorization: "Bearer " + this.getToken,
        "Content-Type": "multipart/form-data",
      };
      let data = {
        title: this.title,
        content: this.content,
        image: this.image,
      };
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        this.loader_work = true;
        return new Promise((resolve, reject) => {
          axios
            .post(this.getApiURL + "/post/create", data, {
              headers: headers,
            })
            .then((res) => {
              this.loader_work = false;
              this.dialog = false;
              this.$router.push("/posts");
              resolve(res);
            })
            .catch((error) => {
              this.loader_work = false;
              reject(error);
            });
        });
      }
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
<style scoped>
.v-progress-circular {
  margin: 1rem;
}
</style>
