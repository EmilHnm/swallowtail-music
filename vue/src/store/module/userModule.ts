import { environment } from "@/environment/environment";
export const userModule = {
  namespaced: true,
  state() {
    return {};
  },
  mutations: {},
  actions: {
    searchUser(
      context: any,
      data: { token: string; query: string; signal: AbortSignal }
    ) {
      return fetch(`${environment.api}/user/search?query=${data.query}`, {
        method: "GET",
        signal: data.signal,
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
    getUser(context: any, data: { token: string; user_id: string }) {
      return fetch(`${environment.api}/user/${data.user_id}`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
    getTopTracks(context: any, data: { token: string; user_id: string }) {
      return fetch(`${environment.api}/user/${data.user_id}/top-tracks`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
    getTopArtists(context: any, data: { token: string; user_id: string }) {
      return fetch(`${environment.api}/user/${data.user_id}/top-artists`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
    getTopSongs(context: any, data: { token: string; user_id: string }) {
      return fetch(`${environment.api}/user/${data.user_id}/top-songs`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
    getPublicPlaylist(context: any, data: { token: string; user_id: string }) {
      return fetch(`${environment.api}/user/${data.user_id}/public-playlist`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
  },
  getters: {},
};
