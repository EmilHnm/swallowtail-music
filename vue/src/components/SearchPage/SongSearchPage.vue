<template>
  <BaseSongItem
    v-for="song in songResult"
    :key="song[0].song_id"
    :data="song"
  />
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseSongItem from "../UI/BaseSongItem.vue";

type songPlaylist = {
  [song_id: string]: {
    song_id: string;
    title: string;
    artist_name: string;
    artist_id: string;
    added_date?: string;
    album_name: string;
    album_id: string;
    image_path: string;
    duration: number;
    listens?: number;
  }[];
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    songResult: songPlaylist;
  }
}

export default defineComponent({
  setup() {},
  inject: ["songResult"],
  components: { BaseSongItem },
});
</script>
