import { environment } from "@/environment/environment";
export const artistModule = {
  namespaced: true,
  state() {
    return {};
  },
  getters: {},
  mutations: {},
  actions: {
    getArtistList(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/artist`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
    getTopArtists(context: any, token: string) {
      return fetch(`${environment.api}/artist/top`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getArtist(context: any, payload: { token: string; artist_id: string }) {
      return fetch(`${environment.api}/artist/${payload.artist_id}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
        },
      });
    },
    getArtistBySongId(
      context: any,
      payload: { userToken: string; song_id: string }
    ) {
      return fetch(`${environment.api}/artist/song/${payload.song_id}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
      });
    },
    getArtistTopSongById(
      context: any,
      payload: { token: string; artist_id: string }
    ) {
      return fetch(`${environment.api}/artist/song/top/${payload.artist_id}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      });
    },
    getArtistAlbumById(
      context: any,
      payload: { token: string; artist_id: string }
    ) {
      return fetch(`${environment.api}/artist/album/${payload.artist_id}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      });
    },
  },
};
