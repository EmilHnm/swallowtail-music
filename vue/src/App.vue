<script lang="ts">
import { RouterView } from "vue-router";
import { environment } from "./environment/environment";
import { defineComponent } from "vue";
import {mapActions, mapGetters} from "vuex";

export default defineComponent({
  name: "App",
  data() {
    return {
      environment: environment,
      currentTime: 0,
      date: new Date() as Date | null,
    };
  },
  methods: {
    ...mapActions("statistic", ["saveTotalSessionsDuration"]),
  },
  mounted() {
    document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "visible") {
        if (!this.date) this.date = new Date();
      } else if (document.visibilityState === "hidden" && !this.getPlaying) {
        if (this.date)
          this.currentTime += Math.abs(
            (new Date().getTime() - this.date.getTime()) / 1000
          );
        this.date = null;
      }
    });
    window.addEventListener("beforeunload", () => {
      if (this.date)
        this.currentTime += Math.abs(
          (new Date().getTime() - this.date.getTime()) / 1000
        );
      this.currentTime = Math.round(this.currentTime);
      this.saveTotalSessionsDuration({
        token: this.userToken,
        duration: this.currentTime,
      });
      return "Are you sure you want to leave? Your current session will be lost.";
    });
  },
  computed: {
    ...mapGetters("queue", ["getPlaying"]),
    ...mapGetters("auth", ["userToken"]),
  },
  components: {
    RouterView,
  },
});
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
