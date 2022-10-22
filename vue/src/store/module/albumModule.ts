import { environment } from "@/environment/environment";

export const albumModule = {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    getAlbum(context: any, albumId: string) {
      return fetch(`${environment.api}/album/${albumId}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      });
    },
    uploadAlbum(context: any, data: any): Promise<Response> {
      return fetch(`${environment.api}/album/upload`, {
        method: "POST",
        headers: {
          Accept: "multipart/form-data",
          Authorization: `Bearer ${data.token}`,
        },
        body: data.albumForm,
      });
    },
  },
};
