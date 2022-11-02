import { environment } from "@/environment/environment";
export const artistModule = {
  namespaced: true,
  state() {
    return {};
  },
  getters: {},
  mutations: {},
  actions: {
    getTopArtists(context: any, token: string) {
      return fetch(`${environment.api}/artist/top`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
  },
};
