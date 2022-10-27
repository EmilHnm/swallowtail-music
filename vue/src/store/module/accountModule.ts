import { environment } from "@/environment/environment";

export const accountModule = {
  namespaced: true,
  state: {},
  mutations: {},
  actions: {
    updateAccount(context: any, payload: { account: any; token: string }) {
      return fetch(`${environment.api}/account/update`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
        },
        body: JSON.stringify(payload.account),
      });
    },
    updateProfilePhoto(
      context: any,
      payload: { form: FormData; token: string }
    ) {
      return fetch(`${environment.api}/account/profile-image`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
        },
        body: payload.form,
      });
    },
    updatePassword(
      context: any,
      payload: {
        password: {
          current_password: string;
          password: string;
          password_confirmation: string;
        };
        token: string;
      }
    ) {
      return fetch(`${environment.api}/account/password`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
        },
        body: JSON.stringify(payload.password),
      });
    },
    logOutAll(context: any, token: string) {
      return fetch(`${environment.api}/auth/logoutAll`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
  },
  getters: {},
};
