<template>
  <div class="search-result__container">
    <div class="search-result__container--artist">
      <BaseCardArtist
        v-for="artist in artistResult"
        :key="artist.artist_id"
        :data="artist"
        :title="'虹ヶ咲学園スクールアイドル同好会'"
        @playArtistSong="playArtistSong"
      />
    </div>
  </div>
</template>

<script lang="ts">
import type { artist } from "@/model/artistModel";
import { defineComponent } from "vue";
import BaseCardArtist from "@/components/UI/BaseCardArtist.vue";

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    artistResult: artist[];
  }
}

export default defineComponent({
  inject: ["artistResult"],
  components: { BaseCardArtist },
  methods: {
    playArtistSong(artist_id: string) {
      this.$emit("playArtistSong", artist_id);
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
