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
  },
};
