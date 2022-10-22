import { environment } from "@/environment/environment";

export const songModule = {
  namespaced: true,
  state() {
    return {};
  },
  getters: {},
  mutations: {},
  actions: {
    getSong(context: any, songId: string) {
      return fetch(`${environment.api}/song/${songId}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      });
    },
    uploadSong(context: any, data: any): Promise<Response> {
      return fetch(`${environment.api}/song/upload`, {
        method: "POST",
        headers: {
          Accept: "multipart/form-data",
          Authorization: `Bearer ${data.token}`,
        },
        body: data.songForm,
      });
    },
    getGenreList(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/genre`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
    getArtistList(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/artist`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
  },
};
