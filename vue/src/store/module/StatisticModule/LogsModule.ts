import { environment } from "@/environment/environment";
export const logsModule = {
  namespaced: true,
  state: {},
  mutations: {},
  actions: {
    fecthLogs(
      context: any,
      payload: {
        userToken: string;
        page: number;
        itemPerPage: number;
        query_type: string;
        query: string;
        class: string;
        level: string;
      }
    ) {
      return fetch(
        `${environment.api}/admin/statistic/logs?page=${payload.page}&itemPerPage=${payload.itemPerPage}&query_type=${payload.query_type}&query=${payload.query}&class=${payload.class}&level=${payload.level}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
        }
      );
    },
  },
  getters: {},
};
