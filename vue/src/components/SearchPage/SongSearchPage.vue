<template>
  <BaseSongItem
    v-for="song in songResult"
    :key="song.song_id"
    :data="song"
    @select-song="playSong"
  />
</template>
<script lang="ts">
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import type { song } from "@/model/songModel";
import { defineComponent } from "vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    songResult: songData[];
  }
}

export default defineComponent({
  setup() {},
  methods: {
    playSong(song_id: string) {
      this.$emit("playSong", song_id);
    },
  },
  inject: ["songResult"],
  components: { BaseSongItem },
});
</script>
