<template>
  <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
    <template #default>
      <BaseLineLoad />
    </template>
    <template #action>
      <div></div>
    </template>
  </BaseDialog>
  <div class="search-result__container">
    <div class="search-result__container--album">
      <BaseCardAlbum
        v-for="album in albumResult"
        :key="album.album_id"
        :id="album.album_id"
        :title="album.name"
        :songCount="album.song_count"
        :img="`${environment.album_cover}/${album.image_path}`"
        @playAlbum="onPlayAlbum"
      />
    </div>
  </div>
</template>

<script lang="ts">
import type { album } from "@/model/albumModel";
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import {mapActions} from "vuex";

type albumData = album & { song_count: number };
declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    albumResult: albumData[];
  }
}

export default defineComponent({
  inject: ["albumResult"],
  emits: ["changeSearchPage"],
  components: { BaseDialog, BaseLineLoad, BaseCardAlbum },
  data() {
    return {
      environment: environment,
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("queue", ["playAlbum"]),
    onPlayAlbum(album_id: string) {
      this.isLoading = true;
      this.playAlbum(album_id)
        .then(() => {
          this.isLoading = false;
        })
        .catch(() => {
          this.isLoading = false;
        });
    },
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
