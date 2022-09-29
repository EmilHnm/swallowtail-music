import { createStore } from "vuex";

export default createStore({
  state: {
    user: {
      data: {
        user_id: "adc0",
        username: "emil",
        email: "emil@gmail.com",
        imgUrl: "",
      },
      token: "123456789",
    },
  },
  getters: {},
  mutations: {
    logOut: (state) => {
      state.user.token = "";
      state.user.data = {
        user_id: "",
        username: "",
        email: "",
        imgUrl: "",
      };
    },
  },
  actions: {},
  modules: {},
});
