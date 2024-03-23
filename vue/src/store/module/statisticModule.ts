import type { ActionTree } from "vuex";
import { environment } from "@/environment/environment";

type RootState = ReturnType<typeof state>;

const state = () => ({});

const actions: ActionTree<RootState, RootState> = {
  saveTotalPlayedDuration(
    { commit },
    payload: { token: string; duration: number }
  ) {
    fetch(`${environment.api}/statistics/played-duration`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${payload.token}`,
      },
      keepalive: true,
      body: JSON.stringify({ duration: payload.duration }),
    }).then();
  },
  saveTotalSessionsDuration(
    { commit },
    payload: { token: string; duration: number }
  ) {
    fetch(`${environment.api}/statistics/sessions-duration`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${payload.token}`,
      },
      keepalive: true,
      body: JSON.stringify({ duration: payload.duration }),
    }).then();
  },
};

export default {
  namespaced: true,
  state,
  actions,
};
