import { environment } from "@/environment/environment";

export const songModule = {
  namespaced: true,
  state() {
    return {};
  },
  getters: {},
  mutations: {},
  actions: {
    getSong(context: any, payload: { token: string; song_id: string }) {
      return fetch(`${environment.api}/song/${payload.song_id}`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${payload.token}`,
        },
      });
    },
    uploadSong(
      context: any,
      data: { token: string; songForm: FormData }
    ): Promise<Response> {
      return fetch(`${environment.api}/song/upload`, {
        method: "POST",
        headers: {
          Accept: "multipart/form-data",
          Authorization: `Bearer ${data.token}`,
        },
        body: data.songForm,
      });
    },
    updateSong(
      context: any,
      data: { token: string; songForm: FormData }
    ): Promise<Response> {
      return fetch(`${environment.api}/account/song/update`, {
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
    getUploadedSongs(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/account/song/uploaded`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
    deleteSong(context: any, payload: { userToken: string; songId: string }) {
      return fetch(`${environment.api}/account/song/${payload.songId}/delete`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
        },
      });
    },
    getLatestSong(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/song/latest`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
  },
};
