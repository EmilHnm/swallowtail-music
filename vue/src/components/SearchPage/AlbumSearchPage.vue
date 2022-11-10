<template>
  <div class="search-result__container">
    <div class="search-result__container--album">
      <BaseCardAlbum
        v-for="album in albumResult"
        :key="album.album_id"
        :id="album.album_id"
        :title="album.name"
        :songCount="album.song_count"
        :img="`${environment.album_cover}/${album.image_path}`"
      />
    </div>
  </div>
</template>

<script lang="ts">
import type { album } from "@/model/albumModel";
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import BaseCardAlbum from "../UI/BaseCardAlbum.vue";

type albumData = album & { song_count: number };
declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    albumResult: albumData[];
  }
}

export default defineComponent({
  inject: ["albumResult"],
  components: { BaseCardAlbum },
  data() {
    return {
      environment: environment,
    };
  },
});
</script>

<style lang="scss" scoped>
.search-result__container {
  width: 100%;
  height: 100%;
  .search-result__container--album {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }
}
</style>
