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
        request_type: payload.type,
        name: payload.name,
        description: payload.description,
      }),
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
