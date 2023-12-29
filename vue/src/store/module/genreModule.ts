import type { GetterTree, MutationTree, ActionTree } from "vuex";
import { environment } from "@/environment/environment";

const state = () => ({
  genres: [],
});

type RootState = ReturnType<typeof state>;

const getters: GetterTree<RootState, RootState> = {
  getGenres(state) {
    return state.genres;
  },
};

const mutations: MutationTree<RootState> = {
  setGenres(state, genres) {
    state.genres = genres;
  },
};

const actions: ActionTree<RootState, RootState> = {
  async fetchGenres({ commit }, token: string) {
    await fetch(`${environment.api}/genre/`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
      method: "GET",
    }).then(async (response: Response) => {
      const genres = await response.json();
      commit("setGenres", genres);
    });
  },
};

export const genreModule = {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
