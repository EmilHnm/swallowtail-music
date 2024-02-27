import type {ActionContext, ActionTree, GetterTree, Mutation, MutationTree} from "vuex";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import { _function } from "@/mixins";
import { playlistModule } from "@/store/module/playlistModule";
import { albumModule } from "@/store/module/albumModule";
import { artistModule } from "@/store/module/artistModule";
import { songModule } from "@/store/module/songModule";

type RootState = ReturnType<typeof state>;

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

type res = {
  status: "success" | "error";
  message: string;
};
const state = () => ({
  queues: [] as songData[],
  shuffledQueue: [] as songData[],
  isPlaying: false,
  shuffled: false,
  repeat: "off",
  currentIndex: 0,
  progress: 0,
  volume: 100,
});

const getters: GetterTree<RootState, RootState> = {
  getQueue(state) {
    if (state.shuffled) return state.shuffledQueue;
    return state.queues;
  },
  getShuffle(state) {
    return state.shuffled;
  },
  getRepeat(state) {
    return state.repeat;
  },
  getCurrentIndex(state) {
    return state.currentIndex;
  },
  getCurrentSong(state) {
    if (state.shuffled) return state.shuffledQueue[state.currentIndex];
    return state.queues[state.currentIndex] ?? null;
  },
  getNextSong(state) {
    if (state.shuffled) {
      if (
        state.repeat != "off" &&
        state.currentIndex == state.shuffledQueue.length - 1
      ) {
        return state.shuffledQueue[0];
      } else {
        return null;
      }
    } else {
      if (
        state.repeat != "off" &&
        state.currentIndex == state.queues.length - 1
      ) {
        return state.queues[0];
      } else {
        return null;
      }
    }
  },
  getPrevSong(state) {
    if (state.shuffled) {
      if (state.repeat != "off" && state.currentIndex == 0) {
        return state.shuffledQueue[state.shuffledQueue.length - 1];
      } else {
        return null;
      }
    } else {
      if (state.repeat != "off" && state.currentIndex == 0) {
        return state.queues[state.queues.length - 1];
      } else {
        return null;
      }
    }
  },
  getCurrentTime(state) {
    const minutes = Math.floor(state.progress / 60);
    const seconds = Math.floor(state.progress % 60);
    const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;

    return `${minutes}:${formattedSeconds}`;
  },
  getCurrentProgress(state) {
    return state.progress;
  },
  getVolume(state) {
    return state.volume;
  },
};
const mutations: MutationTree<RootState> = {
  setQueue(state, payload: songData[]) {
    state.queues = payload;
    state.shuffled = false;
  },
  setShuffle(state, payload: boolean) {
    if (payload) {
      const playingSongData = state.queues[state.currentIndex];
      const listNotContainPlaying = state.queues.filter(
        (item: songData) => item.song_id !== playingSongData.song_id
      );
      const temp: songData[] = _function.shuffleArr(listNotContainPlaying);
      state.shuffledQueue = [playingSongData, ...temp];
      state.currentIndex = 0;
    } else {
      const playingKey = state.shuffledQueue[state.currentIndex].song_id;
      state.currentIndex = state.queues.findIndex(
        (key: songData) => key.song_id === playingKey
      );
    }
    state.shuffled = payload;
  },
  setRepeat(state) {
    if (state.repeat === "off") {
      state.repeat = "one";
    } else if (state.repeat === "one") {
      state.repeat = "all";
    } else {
      state.repeat = "off";
    }
  },
  setCurrentIndex(state, payload: number) {
    if (state.shuffled) {
      if (state.shuffledQueue.length <= payload) {
        if (state.repeat != "off") {
          state.currentIndex = 0;
          state.isPlaying = false;
          return;
        } else {
          state.currentIndex = 0;
          return;
        }
      } else if (payload < 0) {
        if (state.repeat != "off") {
          state.currentIndex = state.shuffledQueue.length - 1;
          state.isPlaying = false;
          return;
        } else {
          state.currentIndex = state.shuffledQueue.length - 1;
          return;
        }
      }
      state.currentIndex = payload;
      return;
    }
    if (state.queues.length <= payload) {
      if (state.repeat != "off") {
        state.currentIndex = 0;
        state.isPlaying = false;
        return;
      } else {
        state.currentIndex = 0;
        return;
      }
    } else if (payload < 0) {
      if (state.repeat != "off") {
        state.currentIndex = state.queues.length - 1;
        state.isPlaying = false;
        return;
      } else {
        state.currentIndex = state.queues.length - 1;
        return;
      }
    }
    state.currentIndex = payload;
  },
  setProgress(state, payload: number | null) {
    state.progress = payload ?? 0;
  },
  setVolume(state, payload: number) {
    state.volume = payload;
  },
  moveSong(state, payload: { from: number; to: number }) {
    if (state.shuffled) {
      const temp = state.shuffledQueue[payload.from];
      state.shuffledQueue.splice(payload.from, 1);
      state.shuffledQueue.splice(payload.to, 0, temp);
    } else {
      const temp = state.queues[payload.from];
      state.queues.splice(payload.from, 1);
      state.queues.splice(payload.to, 0, temp);
    }
    if (payload.from === state.currentIndex) {
      state.currentIndex = payload.to;
    } else if (
      payload.from < state.currentIndex &&
      payload.to >= state.currentIndex
    ) {
      state.currentIndex--;
    } else if (
      payload.from > state.currentIndex &&
      payload.to <= state.currentIndex
    ) {
      state.currentIndex++;
    }
  },
  deleteSong(state, payload: number) {
    if (payload === state.currentIndex) {
      return;
    }
    if (state.shuffled) {
      const deleted = state.shuffledQueue.splice(payload, 1);
      state.queues = state.queues.filter(
        (item) => item.song_id !== deleted[0].song_id
      );
    } else {
      state.queues.splice(payload, 1);
    }
    if (payload < state.currentIndex) {
      state.currentIndex--;
    }
  },
  addSong(state, payload: songData) {
    state.queues = [...state.queues, payload].filter(
      (song: songData, index: number, self: songData[]) =>
        index === self.findIndex((t) => t.song_id === song.song_id)
    );
    if (state.shuffled)
      state.shuffledQueue = [...state.shuffledQueue, payload].filter(
        (song: songData, index: number, self: songData[]) =>
          index === self.findIndex((t) => t.song_id === song.song_id)
      );
  },
  addSongs(state, payload: songData[]) {
    if (state.shuffled) {
      state.shuffledQueue = [...state.shuffledQueue, ...payload].filter(
        (song: songData, index: number, self: songData[]) =>
          index === self.findIndex((t) => t.song_id === song.song_id)
      );
    }
    state.queues = [...state.queues, ...payload].filter(
      (song: songData, index: number, self: songData[]) =>
        index === self.findIndex((t) => t.song_id === song.song_id)
    );
  },
  clearQueue(state) {
    state.queues = [];
    state.shuffledQueue = [];
    state.currentIndex = 0;
  },
};
const actions: ActionTree<RootState, RootState> = {
  playQueuePlaylist(
    context: ActionContext<RootState, RootState>,
    payload: string
  ): Promise<res> {
    return new Promise((res, rej) => {
      context
        .dispatch(
          "playlist/getPlaylistSongs",
          {
            token: context.rootGetters["auth/userToken"],
            playlist_id: payload,
          },
          { root: true }
        )
        .then((res) => res.json())
        .then((data: any) => {
          context.state.shuffled = false;
          context.state.shuffledQueue = [];
          if (data.songList) {
            if (data.songList.length) {
              context.commit("setQueue", data.songList);
              context.commit("setCurrentIndex", 0);
              dispatchEvent(new CustomEvent("play"));
              res({ status: "success", message: "Playing" });
            } else {
              rej({
                status: "error",
                message: "This playlist is empty, cannot be played",
              });
            }
          } else {
            rej({
              status: "error",
              message: "This playlist is empty, cannot be played",
            });
          }
        });
    });
  },
};

const modules: any = {
  playlist: playlistModule,
  album: albumModule,
  artist: artistModule,
  song: songModule,
};

export const queueModule = {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
  modules,
};
