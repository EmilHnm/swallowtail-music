import { environment } from "@/environment/environment";
import type { playlist } from "@/model/playlistModel";
export const playlistModule = {
  namespaced: true,
  state() {
    return {
      playlists: [],
    };
  },
  getters: {
    getPlaylist: (state: any): playlist[] => {
      return state.playlists;
    },
  },
  mutations: {
    setPlaylists(state: any, payload: string) {
      state.playlists = JSON.parse(JSON.stringify(payload));
    },
    cleanDeletedPlaylist(state: any, payload: any) {
      state.playlists = state.playlists.filter(
        (playlist: playlist) => playlist.playlist_id !== payload
      );
    },
  },
  actions: {
    createPlaylist(context: any, token: string): Promise<Response> {
      return fetch(`${environment.api}/playlist/create`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
        },
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
    deletePlaylist(context: any, data: any): Promise<Response> {
      return fetch(`${environment.api}/playlist/${data.playlist_id}/delete`, {
        method: "DELETE",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
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
    getAccountPlaylist(context: any, token: string) {
      return fetch(`${environment.api}/playlist/all`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
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
    addSongToPlaylist(context: any, data: any): Promise<Response> {
      return fetch(`${environment.api}/playlist/song-add`, {
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
  },
};
