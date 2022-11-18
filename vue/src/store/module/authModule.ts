import { environment } from "@/environment/environment";
export const authModule = {
  namespaced: true,
  state() {
    return {
      user: {
        data: {
          user_id: "",
          username: "",
          email: "",
          profile_photo_url: "",
          role: "",
        },
        token: "",
      },
    };
  },
  getters: {
    userData: (state: any) => state.user.data,
    userToken: (state: any) => state.user.token,
    checkLocalToken: (state: any) => {
      const token = localStorage.getItem("TOKEN");
      if (token) {
        return true;
      }
      return false;
    },
  },
  mutations: {
    setUser(state: any, payload: any) {
      state.user.data.user_id = payload.user.user_id;
      state.user.data.username = payload.user.name;
      state.user.data.email = payload.user.email;
      state.user.data.profile_photo_url = payload.user.profile_photo_url ?? "";
      state.user.data.role = payload.user.role;
      if (payload.token) {
        state.user.token = payload.token;
        localStorage.setItem("TOKEN", payload.token);
      }
    },
    clearUser: (state: any) => {
      state.user.token = "";
      state.user.data = {
        user_id: "",
        username: "",
        email: "",
        profile_photo_url: "",
      };
      localStorage.removeItem("TOKEN");
    },
    checkToken(state: any) {
      const token = localStorage.getItem("TOKEN");
      if (token) {
        state.user.token = token;
      }
    },
  },
  actions: {
    login(context: any, data: { username: string; password: string }) {
      return fetch(`${environment.api}/auth/login`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });
    },
    register(
      context: any,
      data: {
        username: string;
        password: string;
        email: string;
        password_confirmation: string;
      }
    ) {
      return fetch(`${environment.api}/auth/register`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });
    },
    logOut(context: any) {
      return fetch(`${environment.api}/auth/logout`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${context.getters.userToken}`,
        },
      });
    },
    checkAuth(context: any) {
      return fetch(`${environment.api}/auth/user`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${context.getters.userToken}`,
        },
      });
    },
  },
};
