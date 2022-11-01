import type { album } from "./../../model/albumModel";
import { environment } from "@/environment/environment";

export const albumModule = {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    getAlbum(context: any, payload: { userToken: string; albumId: string }) {
      return fetch(`${environment.api}/album/${payload.albumId}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
      });
    },
    getUploadedAlbums(context: any, userToken: string): Promise<Response> {
      // getUploadedAlbums
      return fetch(`${environment.api}/album/uploaded`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
    getAlbumSongs(
      context: any,
      payload: { userToken: string; albumId: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/album/${payload.albumId}/song`, {
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
      return fetch(`${environment.api}/album/song-remove`, {
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
      return fetch(`${environment.api}/album/song-add`, {
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
      return fetch(`${environment.api}/album/update`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: payload.albumInfo,
      });
    },
    getAddableSong(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/album/addable`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
          Accept: "application/json",
        },
      });
    },
    deleteAlbum(context: any, payload: { userToken: string; albumId: string }) {
      return fetch(`${environment.api}/album/${payload.albumId}/delete`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
        },
      });
    },
  },
};
