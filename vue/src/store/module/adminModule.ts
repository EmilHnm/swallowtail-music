import { environment } from "@/environment/environment";

export const adminModule = {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    getAllUsers(context: any, token: string) {
      return fetch(`${environment.api}/admin/users`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getUser(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getUserUploadSong(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/songs`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getUserUploadAlbum(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/albums`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    deleteUser(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/delete`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    changeUserRole(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/role/update`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getAllSongs(context: any, token: string) {
      return fetch(`${environment.api}/admin/songs`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getSong(
      context: any,
      { token, song_id }: { token: string; song_id: string }
    ) {
      return fetch(`${environment.api}/admin/songs/${song_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    updateSong(
      context: any,
      data: { token: string; songForm: FormData }
    ): Promise<Response> {
      return fetch(`${environment.api}/admin/songs/update`, {
        method: "POST",
        headers: {
          Accept: "multipart/form-data",
          Authorization: `Bearer ${data.token}`,
        },
        body: data.songForm,
      });
    },
    deleteSong(
      context: any,
      { token, song_id }: { token: string; song_id: string }
    ) {
      return fetch(`${environment.api}/admin/songs/${song_id}/delete`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getAllAlbums(context: any, token: string) {
      return fetch(`${environment.api}/admin/albums`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
  },
};
