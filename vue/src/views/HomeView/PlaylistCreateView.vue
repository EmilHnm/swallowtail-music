<template>
  <div class="container">
    <loading
      v-model:active="isLoading"
      :is-full-page="false"
      :z-indez="50"
      :opacity="0.1"
    >
      Creating playlist...
      <loading
        v-model:active="isLoading"
        :is-full-page="false"
        :loader="'spinner'"
        :z-indez="50"
        :opacity="0"
      >
      </loading>
    </loading>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import { mapActions, mapGetters } from "vuex";
import globalEmitListener from "@/shared/constants/globalEmitListener";
export default defineComponent({
  data() {
    return {
      isLoading: true,
    };
  },
  methods: {
    ...mapActions("playlist", ["createPlaylist"]),
  },
  mounted() {
    this.createPlaylist(this.token).then((playlist_id) => {
      this.$router.push({
        name: "playlistViewPage",
        params: { id: playlist_id },
      });
    });
  },
  components: {
    Loading,
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  emits: [...globalEmitListener],
});
</script>
<style lang="scss" scoped>
.container {
  width: 100%;
  height: 100%;
  margin: auto;
  position: relative;
  & > * {
    width: 100%;
  }
}
</style>
