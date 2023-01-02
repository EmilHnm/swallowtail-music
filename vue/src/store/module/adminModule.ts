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
    getAllSongs(
      context: any,
      data: {
        token: string;
        page: number;
        filterText: string;
        filterType: string;
        signal: AbortSignal;
        itemPerPage: number;
      }
    ) {
      return fetch(
        `${environment.api}/admin/songs?page=${data.page}&itemPerPage=${data.itemPerPage}&query=${data.filterText}&type=${data.filterType}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${data.token}`,
            Accept: "application/json",
          },
          signal: data.signal,
        }
      );
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
    getAllAlbums(
      context: any,
      data: {
        token: string;
        page: number;
        filterText: string;
        filterType: string;
        signal: AbortSignal;
        itemPerPage: number;
      }
    ) {
      return fetch(
        `${environment.api}/admin/albums?page=${data.page}&itemPerPage=${data.itemPerPage}&query=${data.filterText}&type=${data.filterType}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${data.token}`,
            Accept: "application/json",
          },
          signal: data.signal,
        }
      );
    },
    getAlbum(
      context: any,
      { token, album_id }: { token: string; album_id: string }
    ) {
      return fetch(`${environment.api}/admin/albums/${album_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
  },
  updateAlbumInfo(
    context: any,
    payload: { userToken: string; albumInfo: FormData }
  ): Promise<Response> {
    return fetch(`${environment.api}/admin/albums/update`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${payload.userToken}`,
        Accept: "application/json",
      },
      body: payload.albumInfo,
    });
  },
  removeAlbumSongs(
    context: any,
    payload: { userToken: string; songId: string }
  ): Promise<Response> {
    return fetch(`${environment.api}/admin/albums/song-remove`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${payload.userToken}`,
        Accept: "application/json",
      },
      body: JSON.stringify({ song_id: payload.songId }),
    });
  },
  deleteAlbum(
    context: any,
    payload: { userToken: string; albumId: string }
  ): Promise<Response> {
    return fetch(`${environment.api}/admin/albums/${payload.albumId}/delete`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${payload.userToken}`,
        Accept: "application/json",
      },
    });
  },
};
