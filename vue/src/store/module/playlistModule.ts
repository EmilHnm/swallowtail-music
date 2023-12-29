import type { GetterTree, MutationTree, ActionTree } from "vuex";
import { environment } from "@/environment/environment";
import type { playlist } from "@/model/playlistModel";

type RootState = ReturnType<typeof state>;

type playlistData = playlist & {
  song_count: number | undefined;
};

const state = () => ({
  playlists: [] as playlistData[],
});

const getters: GetterTree<RootState, RootState> = {
  getPlaylists(state) {
    return state.playlists;
  },
};

const mutations: MutationTree<RootState> = {
  setPlaylists(state, payload: playlistData[]) {
    state.playlists = payload;
  },
  deletePlaylist(state, playlist_id) {
    state.playlists = state.playlists.filter(
      (playlist) => playlist.playlist_id !== playlist_id
    );
  },
  addPlaylist(state, payload: playlistData) {
    state.playlists.push(payload);
  },
  updatePlaylist(state, payload: playlistData) {
    state.playlists = state.playlists.map((playlist) => {
      if (playlist.playlist_id === payload.playlist_id) {
        return payload;
      }
      return playlist;
    });
  },
  addSongToPlaylist(state, playlist_id) {
    state.playlists = state.playlists.map((playlist) => {
      if (playlist.playlist_id === playlist_id) {
        playlist.song_count = playlist.song_count ? playlist.song_count + 1 : 1;
      }
      return playlist;
    });
  },
};

const actions: ActionTree<RootState, RootState> = {
  async createPlaylist(context: any, token: string): Promise<string> {
    return new Promise((res, rej) => {
      fetch(`${environment.api}/playlist/create`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
        },
      }).then(async (response: Response) => {
        const data = await response.json();
        context.commit("addPlaylist", data.playlist);
        if (data.status == "success") {
          res(data.playlist.playlist_id);
        } else {
          rej(data.message);
        }
      });
    });
  },
  async getAccountPlaylist({ commit }, token: string) {
    fetch(`${environment.api}/playlist/all`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      },
    }).then(async (response: Response) => {
      const data = await response.json();
      commit("setPlaylists", data.playlist);
    });
  },
  getPlaylist(context: any, data: any): Promise<Response> {
    return fetch(`${environment.api}/playlist/${data.playlist_id}`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${data.token}`,
      },
    });
  },
  async deletePlaylist({ commit }, data: any): Promise<void> {
    return new Promise((res, rej) => {
      const playlist_id = data.playlist_id;
      fetch(`${environment.api}/playlist/${playlist_id}/delete`, {
        method: "DELETE",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      })
        .then(async (response: Response) => {
          const data = await response.json();
          if (data.status === "success") {
            commit("deletePlaylist", playlist_id);
            res();
          } else {
            rej(data.message);
          }
        })
        .catch((error: Error) => {
          rej(error.message);
        });
    });
  },
  getPlaylistSongs(context: any, data: any): Promise<Response> {
    return fetch(`${environment.api}/playlist/${data.playlist_id}/song`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${data.token}`,
      },
    });
  },

  updateDetailsPlaylist(context: any, data: any): Promise<Response> {
    return fetch(`${environment.api}/playlist/update`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${data.token}`,
      },
      body: data.formData,
    });
  },
  setPlaylistType(context: any, data: any): Promise<Response> {
    return fetch(`${environment.api}/playlist/set-type`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${data.token}`,
      },
      body: JSON.stringify(data.type),
    });
  },
  getAddableSongs(
    context: any,
    data: {
      playlist_id: string;
      token: string;
      query: string;
      signal: AbortSignal;
    }
  ): Promise<Response> {
    return fetch(
      `${environment.api}/playlist/${data.playlist_id}/addable/?query=${data.query}`,
      {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
        signal: data.signal,
      }
    );
  },
  addSongToPlaylist({ commit }, data: any): Promise<void> {
    const playlist_id = data.playlist_id;
    return new Promise<void>((resolve, reject) => {
      fetch(`${environment.api}/playlist/song-add`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          song_id: data.song_id,
          playlist_id: data.playlist_id,
        }),
      })
        .then(async (response: Response) => {
          const data = await response.json();
          if (data.status === "success") {
            commit("addSongToPlaylist", playlist_id);
            resolve();
          } else {
            reject(data.message);
          }
        })
        .catch((error: Error) => {
          reject(error.message);
        });
    });
  },

  addAlbumToPlaylist(context: any, data: any): Promise<Response> {
    return fetch(`${environment.api}/playlist/album-add`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${data.token}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        album_id: data.album_id,
        playlist_id: data.playlist_id,
      }),
    });
  },
  addToPlaylist(
    context: any,
    data: { from: string; to: string; token: string }
  ): Promise<Response> {
    return fetch(`${environment.api}/playlist/add-to-playlist`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${data.token}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        from: data.from,
        to: data.to,
      }),
    });
  },
  getLikedSongList(context: any, token: string): Promise<Response> {
    return fetch(`${environment.api}/playlist/collection`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      },
    });
  },
};

export const playlistModule = {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
