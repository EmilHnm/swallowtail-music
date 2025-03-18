import { environment } from "@/environment/environment";
export const notificationModule = {
  namespaced: true,
  state() {
    return {};
  },
  mutations: {},
  actions: {
    index(
      context: any,
      data: {
        token: string;
        page: number;
        limit: number | null;
        options?: { [key: string]: any };
      }
    ) {
      let endpoint = `${environment.api}/notifications?page=${
        data.page
      }&limit=${data.limit ?? 10}`;
      for (const key in data.options) {
        if (data.options.hasOwnProperty(key)) {
          const value = data.options[key];
          endpoint += `&${key}=${value}`;
        }
      }
      return fetch(endpoint, {
        method: "GET",
        headers: {
          accept: "application/json",
          authorization: `Bearer ${data.token}`,
        },
      });
    },
    hasUnread(context: any, data: { token: string }) {
      return fetch(`${environment.api}/notifications/has-unread`, {
        method: "GET",
        headers: {
          accept: "application/json",
          authorization: `Bearer ${data.token}`,
        },
      });
    },
    markAsRead(context: any, data: { token: string; id: string }) {
      return fetch(`${environment.api}/notifications/mark-as-read`, {
        method: "POST",
        headers: {
          accept: "application/json",
          authorization: `Bearer ${data.token}`,
          "content-type": "application/json",
        },
        body: JSON.stringify({ id: data.id }),
      });
    },
    markAllAsRead(context: any, data: { token: string; ids: string[] }) {
      return fetch(`${environment.api}/notifications/mark-all-as-read`, {
        method: "POST",
        headers: {
          accept: "application/json",
          authorization: `Bearer ${data.token}`,
          "content-type": "application/json",
        },
        body: JSON.stringify({ ids: data.ids }),
      });
    },
    delete(context: any, data: { token: string; id: string }) {
      return fetch(`${environment.api}/notifications/delete`, {
        method: "POST",
        headers: {
          accept: "application/json",
          authorization: `Bearer ${data.token}`,
          "content-type": "application/json",
        },
        body: JSON.stringify({ id: data.id }),
      });
    },
  },
  getters: {},
};
