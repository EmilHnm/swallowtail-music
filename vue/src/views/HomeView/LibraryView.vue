<template>
  <div class="container" v-if="playlists.length > 0">
    <BaseCardAlbum
      v-for="playlist in playlists"
      :key="playlist.playlist_id"
      :title="playlist.title"
      :id="playlist.playlist_id"
      :img="
        playlist.image_path
          ? `${environment.playlist_cover}/${playlist.image_path}`
          : `${environment.default}/no_image.jpg`
      "
      :type="'playlist'"
      :songCount="playlist.song_count ?? 0"
      @playPlaylist="playPlaylist"
    />
  </div>

  <div class="container" v-else>
    <h1>You have no playlists</h1>
  </div>
</template>
<script lang="ts">
import type { playlist } from "@/model/playlistModel";
import { defineComponent } from "vue";
import { mapGetters, mapActions } from "vuex";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import { environment } from "@/environment/environment";
import { useMeta } from "vue-meta";
import globalEmitListener from "@/shared/constants/globalEmitListener";

export default defineComponent({
  components: { BaseCardAlbum },
  computed: {
    ...mapGetters({
      playlists: "playlist/getPlaylists",
    }),
  },
  methods: {
    playPlaylist(id: string) {
      this.$emit("playPlaylist", id);
    },
  },
  emits: [...globalEmitListener],
  data() {
    return {
      environment: environment,
    };
  },
  mounted() {
    useMeta({
      title: "Library",
    });
  },
});
</script>
<style lang="scss" scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  flex-wrap: wrap;
}
</style>
