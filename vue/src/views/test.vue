<template>
  <div class="root">
    <button @click="inc">Add 2</button>
    <h3>{{ finalCounter }} - {{ normalizedCounter }}</h3>
  </div>
</template>

<script lang="ts">
import { mapActions, mapGetters } from "vuex";
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
    ...mapGetters("test", {
      finalCounter: "finalCounter",
      normalizedCounter: "normalizedCounter",
    }),
  },
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
