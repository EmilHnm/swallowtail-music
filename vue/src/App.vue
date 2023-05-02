<script lang="ts">
import { defineComponent } from "vue";
import { RouterView } from "vue-router";
import { environment } from "./environment/environment";
export default {
  name: "App",
  data() {
    return {
      environment: environment,
    };
  },
  components: {
    RouterView,
  },
};
</script>

<template>
  <metainfo>
    <template v-slot:title="{ content }">{{ content }}</template>
  </metainfo>
  <RouterView />
</template>

<style scoped></style>
