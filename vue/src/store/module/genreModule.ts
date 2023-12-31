import type { GetterTree, MutationTree, ActionTree } from "vuex";
import { environment } from "@/environment/environment";
import type { genre } from "@/model/genreModel";
import type { songData } from "@/model/songModel";
import type { artist } from "@/model/artistModel";
import type { album } from "@/model/albumModel";

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
  async fetchGenre({ commit }, { token, id }: { token: string; id: number }) {
    return new Promise<genre>((resolve, reject) => {
      fetch(`${environment.api}/genre/${id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
        method: "GET",
      })
        .then(async (response: Response) => {
          const genre = await response.json();
          resolve(genre);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  async getTopSongs({ commit }, { token, id }: { token: string; id: number }) {
    return new Promise<songData[]>((resolve, reject) => {
      fetch(`${environment.api}/genre/${id}/top-song`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
        method: "GET",
      })
        .then(async (response: Response) => {
          const songs = await response.json();
          resolve(songs);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  async getTopArtists(
    { commit },
    { token, id }: { token: string; id: number }
  ) {
    return new Promise<artist[]>((resolve, reject) => {
      fetch(`${environment.api}/genre/${id}/top-artist`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
        method: "GET",
      })
        .then(async (response: Response) => {
          const songs = await response.json();
          resolve(songs);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  async getTopAlbums({ commit }, { token, id }: { token: string; id: number }) {
    return new Promise<
      album &
        {
          song_count: number;
          song_sum_listens: number;
        }[]
    >((resolve, reject) => {
      fetch(`${environment.api}/genre/${id}/top-album`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
        method: "GET",
      })
        .then(async (response: Response) => {
          const albums = await response.json();
          resolve(albums);
        })
        .catch((error) => {
          reject(error);
        });
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
