import { environment } from "@/environment/environment";

export const albumModule = {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    getAlbum(context: any, payload: { token: string; album_id: string }) {
      return fetch(`${environment.api}/album/${payload.album_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
        },
      });
    },
    getUploadedAlbums(
      context: any,
      payload: {
        userToken: string;
        page: number;
        query: string;
        sort: { column: string; type: string };
        signal: AbortSignal;
      }
    ): Promise<Response> {
      const endpoint = `${environment.api}/account/album/uploaded?page=${payload.page}&query=${payload.query}&sort=${payload.sort.column}&order=${payload.sort.type}`;
      return fetch(endpoint, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        signal: payload.signal,
      });
    },
    searchAlbums(
      context: any,
      data: { token: string; query: string; signal: AbortSignal }
    ) {
      return fetch(`${environment.api}/album/search?query=${data.query}`, {
        method: "GET",
        signal: data.signal,
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
    getAlbumSongs(
      context: any,
      payload: { userToken: string; album_id: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/album/${payload.album_id}/song`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
      });
    },
    removeAlbumSongs(
      context: any,
      payload: { userToken: string; songId: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/account/album/song-remove`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify({ song_id: payload.songId }),
      });
    },
    addAlbumSongs(
      context: any,
      payload: { userToken: string; songId: string; albumId: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/account/album/song-add`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify({
          song_id: payload.songId,
          album_id: payload.albumId,
        }),
      });
    },
    updateAlbumInfo(
      context: any,
      payload: { userToken: string; albumInfo: FormData }
    ): Promise<Response> {
      return fetch(`${environment.api}/account/album/update`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: payload.albumInfo,
      });
    },
    getAddableSong(
      context: any,
      payload: { userToken: string; signal: AbortSignal; query: string }
    ): Promise<Response> {
      return fetch(
        `${environment.api}/account/album/addable?query=${payload.query}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
          signal: payload.signal,
        }
      );
    },
    deleteAlbum(context: any, payload: { userToken: string; albumId: string }) {
      return fetch(
        `${environment.api}/account/album/${payload.albumId}/delete`,
        {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
          },
        }
      );
    },
    getLatestAlbums(context: any, userToken: string): Promise<any> {
      const endpoint = `${environment.api}/album/latest`;
      return new Promise((resolve: (data: any) => any) => {
        const cached = context.rootGetters["cache/data"](endpoint);
        if (cached) {
          resolve(cached);
        } else {
          fetch(endpoint, {
            method: "GET",
            headers: {
              Authorization: `Bearer ${userToken}`,
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
    getTopAlbums(context: any, userToken: string): Promise<any> {
      const endpoint = `${environment.api}/album/top`;
      return new Promise((resolve: (data: any) => any) => {
        const cached = context.rootGetters["cache/data"](endpoint);
        if (cached) {
          resolve(cached);
        } else {
          fetch(endpoint, {
            method: "GET",
            headers: {
              Authorization: `Bearer ${userToken}`,
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
    getAlbumById(
      context: any,
      payload: { userToken: string; albumId: string }
    ) {
      return fetch(`${environment.api}/album/${payload.albumId}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
        },
      });
    },
    getAlbumAvailableTypes(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/album/available-types`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
  },
};
