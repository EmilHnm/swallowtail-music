import { cacheModule } from "./cacheModule";
import { environment } from "@/environment/environment";
export const artistModule = {
  namespaced: true,
  module: {
    cache: cacheModule,
  },
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
    async getTopArtists(context: any, token: string): Promise<any> {
      const endpoint = `${environment.api}/artist/top`;
      return new Promise((resolve: (data: any) => any) => {
        const cached = context.rootGetters["cache/data"](endpoint);
        if (cached) {
          resolve(cached);
        } else {
          fetch(endpoint, {
            method: "GET",
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/json",
            },
          }).then((res: Response) => {
            const data = res.json();
            context.commit(
              "cache/set",
              {
                key: endpoint,
                value: data,
              },
              { root: true }
            );
            resolve(data);
          });
        }
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
    searchArtist(
      context: any,
      payload: { token: string; query: string; signal: AbortSignal }
    ) {
      return fetch(`${environment.api}/artist/search?query=${payload.query}`, {
        method: "GET",
        signal: payload.signal,
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
