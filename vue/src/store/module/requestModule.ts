import type { ActionTree, GetterTree, MutationTree } from "vuex";
import { environment } from "@/environment/environment";

type RootState = ReturnType<typeof state>;

const state = () => ({});

const getters: GetterTree<RootState, RootState> = {};
const mutations: MutationTree<RootState> = {};
const actions: ActionTree<RootState, RootState> = {
  create(
    { commit },
    payload: {
      token: string;
      type: "artist" | "genre" | "album" | "song";
      name: string;
      description: string;
    }
  ): Promise<Response> {
    return fetch(`${environment.api}/requests/create`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${payload.token}`,
        Accept: "application/json",
      },
      body: JSON.stringify({
        type: payload.type,
        name: payload.name,
        description: payload.description,
      }),
    });
  },
  index(
    { commit },
    payload: {
      token: string;
      type?: "artist" | "genre" | "album" | "song";
      status?: "pending" | "approved" | "rejected";
      my_requests?: boolean;
      query?: string;
      page: number;
      signal: AbortSignal;
    }
  ): Promise<Response> {
    let endpoint = `${environment.api}/requests?page=${payload.page}`;
    if (payload.type) endpoint += `&type=${payload.type}`;
    if (payload.status) endpoint += `&status=${payload.status}`;
    if (payload.my_requests) endpoint += `&my_requests=${payload.my_requests}`;
    if (payload.query) endpoint += `&q=${payload.query}`;
    return fetch(endpoint, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${payload.token}`,
        Accept: "application/json",
      },
      signal: payload.signal,
    });
  },
  cancel(
    { commit },
    payload: {
      token: string;
      id: number;
    }
  ): Promise<Response> {
    return fetch(`${environment.api}/requests/${payload.id}/cancel`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${payload.token}`,
        Accept: "application/json",
      },
    });
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
