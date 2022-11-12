<template>
  <div class="upload-container">
    <div class="upload-container__header">
      <h1 class="upload-container__title">Upload</h1>
      <div class="upload-container__type">
        <BaseButton @click="toggleComponent('SongUpload')">Songs</BaseButton>
        <BaseButton @click="toggleComponent('AlbumUpload')">Album</BaseButton>
      </div>
      <keep-alive>
        <component :is="componentName"></component>
      </keep-alive>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseButton from "../../components/UI/BaseButton.vue";
import SongUpload from "../../components/UploadPage/SongUpload.vue";
import AlbumUpload from "../../components/UploadPage/AlbumUpload.vue";
export default defineComponent({
  data() {
    return { componentName: "SongUpload" };
  },
  methods: {
    toggleComponent(component: string) {
      this.componentName = component;
    },
  },
  components: { BaseButton, SongUpload, AlbumUpload },
  emits: [
    "updatePlaylist",
    "deletePlaylist",
    "playPlaylist",
    "playSongInPlaylist",
    "addPlaylistToQueue",
    "playAlbum",
    "addAlbumToQueue",
    "playSongInAlbum",
    "playArtistSong",
    "playSongOfArtist",
    "addArtistSongToQueue",
    "playLikedSong",
    "addLikedSongToQueue",
    "addToQueue",
    "playSong",
  ],
});
</script>

<style lang="scss" scoped>
.upload-container {
  width: 80%;
  margin: auto;
  &__title {
    font-size: 2rem;
    font-weight: 700;
    text-align: center;
  }
  &__type {
    display: flex;
    justify-content: center;
    margin: 1rem 0;
    & > * {
      margin: 0 0.5rem;
    }
  }
}
</style>
