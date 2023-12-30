import type { GetterTree, MutationTree, ActionTree } from "vuex";
import { environment } from "@/environment/environment";
import type { songFileUpload } from "@/model/songModel";
import { _function } from "@/mixins/index";
type RootState = ReturnType<typeof state>;

const state = () => ({
  queues: [] as songFileUpload[],
  uploadingIndex: -1,
  chunk_size: 2 * 1024 * 1024,
});

const getters: GetterTree<RootState, RootState> = {
  getQueues(state) {
    return state.queues;
  },
  getUploadingFile(state) {
    return state.queues[state.uploadingIndex]
      ? state.queues[state.uploadingIndex]
      : null;
  },
};

const mutations: MutationTree<RootState> = {
  setQueues(state, payload: songFileUpload[]) {
    state.queues = payload;
  },
  setUploadIndex(state, payload: number) {
    state.uploadingIndex = payload;
  },
};

const actions: ActionTree<RootState, RootState> = {
  addFileToQueue(
    { state, dispatch },
    payload: { file: File | null; song_id: string; token: string }
  ) {
    if (payload.file === null) return;
    let songFile: songFileUpload = {
      file: payload.file,
      blob: [],
      chunk_count: 0,
      progress: 0,
      song_id: payload.song_id,
      status: "waiting",
    };
    songFile = _function.createChunks(songFile, state.chunk_size);
    state.queues.push(songFile);
    if (state.uploadingIndex === -1) {
      if (state.queues.length > 1) {
        if (state.queues[state.queues.length - 2].blob.length == 0) {
          state.uploadingIndex = state.queues.length - 1;
          dispatch("uploadChunk", {
            file: state.queues[state.uploadingIndex],
            token: payload.token,
          });
        }
      } else {
        state.uploadingIndex = 0;
        dispatch("uploadChunk", {
          file: state.queues[state.uploadingIndex],
          token: payload.token,
        });
      }
    }
  },
  async uploadChunk(
    { commit, dispatch, state },
    payload: { file: songFileUpload; token: string }
  ) {
    const xhr = new XMLHttpRequest();
    xhr.open(
      "POST",
      `${environment.api}/song/${payload.file.song_id}/file`,
      true
    );
    xhr.setRequestHeader("Authorization", `Bearer ${payload.token}`);
    // xhr.setRequestHeader("Content-Type", "multipart/form-data");
    xhr.setRequestHeader("Accept", "application/json");
    payload.file.status = "uploading";
    let formData = new FormData();
    formData.append("is_last", JSON.stringify(payload.file.blob.length === 1));
    formData.append("song_id", payload.file.song_id);
    formData.set(
      "file",
      payload.file.blob[0],
      `${payload.file.file.name}.part`
    );
    xhr.setRequestHeader("Accept", "multipart/form-data");
    xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        const percentComplete =
          (e.loaded / e.total / payload.file.chunk_count) * 100;
        payload.file.progress += percentComplete;
      }
    };
    xhr.onload = async () => {
      if (xhr.status === 200) {
        payload.file.blob.shift();
        if (payload.file.blob.length > 0) {
          await new Promise((r) => setTimeout(r, 1000));
          dispatch("uploadChunk", { file: payload.file, token: payload.token });
        } else {
          payload.file.progress = 100;
          payload.file.status = "finish";
          commit("setUploadIndex", state.uploadingIndex + 1);
          if (state.uploadingIndex <= state.queues.length - 1) {
            dispatch("uploadChunk", {
              file: state.queues[state.uploadingIndex],
              token: payload.token,
            });
          } else {
            commit("setUploadIndex", -1);
          }
        }
      } else {
        payload.file.status = "error";
      }
    };
    xhr.send(formData);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
