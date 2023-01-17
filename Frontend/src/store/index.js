import { createStore } from 'vuex'
import axios from 'axios'
import VuexPersistence from 'vuex-persist'

export default createStore({

  state: {
    token: null,
    loggedIn: false,
    user: null,
    posts: [],
    user_posts: []
  },
  plugins: [new VuexPersistence().plugin],
  getters: {
    get_loggedIn(state) {
      return state.loggedIn
    },
    get_user(state) {
      return state.user
    },
    getAllPosts(state) {
      return state.posts
    },
    getCurrentUserPosts(state) {
      return state.user_posts
    },
    getToken(state) {
      return state.token
    }
  },
  mutations: {
    set_token(state, payload) {
      state.token = payload
    },
    set_loggedIn(state, payload) {
      state.loggedIn = payload
    },
    set_user(state, payload) {
      state.user = payload
    },
  },
  actions: {
    login({ commit }, payload) {
      return new Promise((resolve, reject) => {
        axios
          .post("http://127.0.0.1:8000/api/auth/login", {
            email: payload.email,
            password: payload.password,
          })
          .then((res) => {
            commit('set_token', res.data.access_token)
            commit('set_loggedIn', true)
            commit('set_user', res.data.user)
            resolve(res)
          })
          .catch((error) => {
            reject(error)
          });
      })
    },
    register({ commit }, payload) {
      return new Promise((resolve, reject) => {
        axios
          .post("http://127.0.0.1:8000/api/auth/register", {
            name: payload.name,
            email: payload.email,
            password: payload.password,
          })
          .then((res) => {
            commit('set_token', res.data.access_token)
            commit('set_loggedIn', true)
            commit('set_user', res.data.user)
            resolve(res)
          })
          .catch((error) => {
            reject(error)
          });
      })
    },
    logout({ commit, state }) {
      return new Promise((resolve, reject) => {
        axios.post("http://127.0.0.1:8000/api/auth/logout", {
          token: state.token
        })
          .then((res) => {
            commit('set_token', null)
            commit('set_loggedIn', false)
            commit('set_user', null)
            localStorage.removeItem("token");
            resolve(res)
          })
          .catch((error) => {
            reject(error)
          });
      })
    },
    getAllPosts({ commit, state }) {
      return new Promise((resolve, reject) => {
        axios
          .get("http://127.0.0.1:8000/api/posts", { token: state.token })
          .then((res) => {
            state.posts = res.data.data;
            resolve(res)
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getCurrentUserPosts({ commit, state }) {
      const headers = {
        accept: "application/json",
        "content-type": "application/json",
        authorization: "Bearer " + state.token,
      };
      return new Promise((resolve, reject) => {
        axios
          .get("http://127.0.0.1:8000/api/user_posts", { headers: headers })
          .then((res) => {
            state.user_posts = res.data.data;
            resolve(res)
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
  },
  modules: {
  }
})
