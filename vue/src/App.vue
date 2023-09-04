<script lang="ts">
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
    <meta
      name="description"
      content="Swallowtail Music - Your Gateway to Musical Bliss"
    />
  </metainfo>
  <RouterView />
</template>

<style scoped></style>
