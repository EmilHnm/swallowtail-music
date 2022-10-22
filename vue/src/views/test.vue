<template>
  <div class="root">
    <button @click="inc">Add 2</button>
    <h3>{{ counter }}</h3>
  </div>
</template>

<script lang="ts">
import { computed } from "vue";
import { mapActions } from "vuex";
export default {
  data() {
    return {};
  },
  methods: {
    ...mapActions("test", {
      inc: "increment",
      increase: "increase",
    }),
  },
  computed: {
    counter() {
      return computed(() => this.$store.state.test.counter);
    },
  },
  // setup() {
  //   const store = $store;

  //   watch(
  //     () => store.getters["numbers/normalizedCounter"],
  //     function () {
  //       console.log("value changes detected");
  //     }
  //   );

  //   store.watch(
  //     (state, getters) => getters["numbers/normalizedCounter"],
  //     () => {
  //       console.log("value changes detected via vuex watch");
  //     }
  //   );

  //   return {
  //     myvalue: computed(() => store.getters["numbers/normalizedCounter"]),
  //     change: () => {
  //       store.dispatch("increment");
  //     },
  //   };
  // },
};
</script>

<style lang="scss" scoped>
.root {
  width: 100vw;
  height: 100vh;
  background-color: #000;
  color: #fff;
}
</style>
