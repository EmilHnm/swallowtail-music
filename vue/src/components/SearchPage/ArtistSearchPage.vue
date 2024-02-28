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
    <div class="search-result__container--artist">
      <BaseCardArtist
        v-for="artist in artistResult"
        :key="artist.artist_id"
        :data="artist"
        :title="artist.name"
        @playArtistSong="playArtistSong"
      />
    </div>
  </div>
</template>

<script lang="ts">
import type { artist } from "@/model/artistModel";
import { defineComponent } from "vue";
import BaseCardArtist from "@/components/UI/BaseCardArtist.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import { mapActions } from "vuex";

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    artistResult: artist[];
  }
}

export default defineComponent({
  inject: ["artistResult"],
  components: { BaseDialog, BaseLineLoad, BaseCardArtist },
  data() {
    return {
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("queue", ["playArtist"]),
    playArtistSong(artist_id: string) {
      this.isLoading = true;
      this.playArtist(artist_id)
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
  .search-result__container--artist {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }
}
</style>
