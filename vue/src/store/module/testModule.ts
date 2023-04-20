export const testModule = {
  namespaced: true,
  state() {
    return {
      counter: 0,
    };
  },
  mutations: {
    increment(state: any) {
      state.counter = state.counter + 2;
      console.log("increment", state.counter);
    },
  },
  actions: {
    increment(context: any) {
      context.commit("increment");
    },
  },
  getters: {
    finalCounter(state: any) {
      console.log("finalCounter", state.counter);
      return state.counter * 3;
    },
    normalizedCounter(_: any, getters: any) {
      const finalCounter = getters.finalCounter;
      if (finalCounter < 0) {
        return 0;
      }
      if (finalCounter > 100) {
        return 100;
      }
      return finalCounter;
      console.log("normalizedCounter", finalCounter);
    },
  },
};
