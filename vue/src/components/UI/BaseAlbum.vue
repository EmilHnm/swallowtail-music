<template>
  <teleport to="body">
    <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseDialog>
    <BaseDialog
      :open="isPlaylistOpening"
      :title="'Select Playlist'"
      :mode="'announcement'"
      @close="isPlaylistOpening = false"
    >
      <template #default>
        <BaseListItem
          v-for="playlist in playlists"
          :key="playlist.playlist_id"
          @click="onAddAlbumToPlaylist(playlist.playlist_id)"
          >{{ playlist.title }}</BaseListItem
        >
      </template>
      <template #action><div></div></template
    ></BaseDialog>
    <BaseDialog
      :open="dialogWaring.show"
      :title="dialogWaring.title"
      :mode="dialogWaring.mode"
      @close="closeDialog"
    >
      <template #default>
        <p>{{ dialogWaring.content }}</p>
      </template>
    </BaseDialog>
  </teleport>
  <div class="album-container" ref="albumContainer">
    <div class="album__details">
      <div class="album__details--cover">
        <img
          v-lazyload
          :data-url="`${environment.album_cover}/${data.image_path}`"
        />
      </div>
      <div class="album__details--prof">
        <h3 class="album__details--prof--title" :title="data.name">
          {{ data.name }}
        </h3>
        <div class="album__details--prof--sub">
          <span>
            {{ data.release_year }} - {{ data.song_count }} Songs -
            {{ data.type }}</span
          >
        </div>
        <div class="album__details--prof--menu">
          <div class="album__details--prof--menu--play">
            <IconPlay @click="playAlbum" />
          </div>
          <div class="album__details--prof--menu--heart">
            <IconHeartFilled />
          </div>
          <div
            class="album__details--prof--menu--more"
            v-click-outside="() => (isMenuOpen = false)"
          >
            <IconHorizontalThreeDot @click="toggleMenu" />
            <transition name="playlist-menu">
              <div class="playlist-menu" v-if="isMenuOpen">
                <BaseListItem @click="addAlbumToQueue"
                  ><span class="playlist-menu__item"
                    >Add to Queue</span
                  ></BaseListItem
                >
                <BaseListItem
                  ><span class="playlist-menu__item"
                    >Add to Library</span
                  ></BaseListItem
                >
                <BaseListItem @click="isPlaylistOpening = true"
                  ><span class="playlist-menu__item"
                    >Add to Playlist</span
                  ></BaseListItem
                >
                <BaseListItem
                  ><span class="playlist-menu__item">Report</span></BaseListItem
                >
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
    <div class="album__songs">
      <BaseCircleLoad v-if="isSongsLoading" />
      <div v-else>
        <BaseSongItem
          v-for="song in songs"
          :data="song"
          :key="song.song_id"
          :control="true"
          @select-song="playSongInAlbum(song.song_id)"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type { album } from "@/model/albumModel";
import { environment } from "@/environment/environment";
import {mapActions, mapGetters, mapMutations} from "vuex";
import IconHeartFilled from "@/components/icons/IconHeartFilled.vue";
import IconHorizontalThreeDot from "@/components/icons/IconHorizontalThreeDot.vue";
import BaseSongItem from "./BaseSongItem.vue";
import IconPlay from "@/components/icons/IconPlay.vue";
import BaseDialog from "./BaseDialog.vue";
import BaseLineLoad from "./BaseLineLoad.vue";
import BaseCircleLoad from "./BaseCircleLoad.vue";
import BaseListItem from "./BaseListItem.vue";
import type { song } from "@/model/songModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";

type albumData = album & {
  song_count: number;
};

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

export default defineComponent({
  props: {
    data: {
      type: Object as () => albumData,
      required: true,
    },
  },
  emits: ["playAlbum", "addAlbumToQueue", "playSongInAlbum", "addToQueue"],
  data() {
    return {
      environment: environment,
      isMenuOpen: false,
      songs: [] as songData[],
      isPlaylistOpening: false,
      isLoading: false,
      isSongsLoading: true,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  methods: {
    ...mapActions("album", ["getAlbumSongs"]),
    ...mapActions("playlist", ["addAlbumToPlaylist"]),
    ...mapMutations("queue", [
      "addSongs",
      "setCurrentIndex",
      "setShuffle",
      "clearQueue",
    ]),
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    onAddAlbumToPlaylist(id: string) {
      this.isLoading = true;
      this.isPlaylistOpening = false;
      this.addAlbumToPlaylist({
        token: this.token,
        album_id: this.data.album_id,
        playlist_id: id,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.dialogWaring.title = "Success";
            this.dialogWaring.content = res.message;
            this.dialogWaring.mode = "announcement";
            this.dialogWaring.show = true;
          } else {
            this.dialogWaring.title = "Error";
            this.dialogWaring.content = res.message;
            this.dialogWaring.mode = "warning";
            this.dialogWaring.show = true;
          }
        });
    },
    loadAlbumSong() {
      this.getAlbumSongs({
        userToken: this.token,
        album_id: this.data.album_id,
      })
        .then((res) => {
          return res.json();
        })
        .then((res) => {
          if (res.status === "success") {
            this.songs = res.songs;
            this.isSongsLoading = false;
          }
        });
    },
    playAlbum() {
      if (this.songs.length > 0) {
        this.clearQueue();
        this.addSongs(this.songs);
        this.setCurrentIndex(0);
        this.setShuffle(false);
        document.dispatchEvent(new CustomEvent("play"));
      } else {
        this.dialogWaring.show = true;
        this.dialogWaring.title = "Warning";
        this.dialogWaring.content = "No song in this album";
        this.dialogWaring.mode = "warning";
      }
    },
    addAlbumToQueue() {
      if (this.songs.length > 0) {
        this.addSongs(this.songs);
      } else {
        this.dialogWaring.show = true;
        this.dialogWaring.title = "Warning";
        this.dialogWaring.content = "No song in this album";
        this.dialogWaring.mode = "warning";
      }
    },
    playSongInAlbum(id: string) {
      if (this.songs.length > 0) {
        this.clearQueue();
        this.addSongs(this.songs);
        const index = this.songs.findIndex((song) => song.song_id === id);
        this.setCurrentIndex(index);
        this.setShuffle(false);
        document.dispatchEvent(new CustomEvent("play"));
      } else {
        this.dialogWaring.show = true;
        this.dialogWaring.title = "Warning";
        this.dialogWaring.content = "No song in this album";
        this.dialogWaring.mode = "warning";
      }
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      playlists: "playlist/getPlaylists",
    }),
  },
  mounted() {
    if (window.IntersectionObserver) {
      const options: IntersectionObserverInit = {
        root: null,
        threshold: 0,
      };
      const observer = new IntersectionObserver(
        (
          entries: IntersectionObserverEntry[],
          observer: IntersectionObserver
        ) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              this.loadAlbumSong();
              observer.unobserve(this.$refs.albumContainer as HTMLDivElement);
            }
          });
        },
        options
      );
      observer.observe(this.$refs.albumContainer as HTMLDivElement);
    } else {
      this.loadAlbumSong();
    }
  },
  components: {
    IconHeartFilled,
    BaseListItem,
    IconHorizontalThreeDot,
    BaseSongItem,
    IconPlay,
    BaseDialog,
    BaseLineLoad,
    BaseCircleLoad,
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.album-container {
  width: 90%;
  margin: 20px auto;
  box-shadow: 0 2px 10px var(--background-glass-color-primary);
  padding-bottom: 20px;
  container-name: album-container;
  container-type: inline-size;
  & .album__details {
    display: flex;
    height: 150px;
    margin-bottom: 30px;
    background-color: var(--background-glass-color-primary);
    @container album-container (max-width: #{$mobile-width}) {
      & {
        flex-direction: column;
        height: max-content;
        justify-content: center;
        align-items: center;
      }
    }
    &--cover {
      aspect-ratio: 1/1;
      height: 100%;
      width: auto;
      flex: 0 0 auto;
      overflow: hidden;
      max-width: 150px;
      @container album-container (max-width: #{$mobile-width}) {
        & {
          height: 90%;
          width: auto;
        }
      }
      & img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    &--prof {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      padding-left: 20px;
      flex: 1;
      @container album-container (max-width: #{$mobile-width}) {
        & {
          padding-left: 0px;
          padding: 0 10px;
        }
      }
      &--title {
        display: block;
        width: 100%;
        height: max-content;
        user-select: none;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        @container album-container (max-width: #{$tablet-width}) {
          & {
            font-size: 1.7rem;
            font-weight: 600;
            text-align: center;
          }
        }
      }
      &--sub {
        @container album-container (max-width: #{$mobile-width}) {
          & {
            text-align: center;
          }
        }
      }
      &--menu {
        display: flex;
        align-items: center;
        margin-top: auto;
        flex: 1;
        @container album-container (max-width: #{$mobile-width}) {
          & {
            justify-content: center;
            padding-bottom: 10px;
          }
        }
        &--play {
          margin-right: 10px;
          border-radius: 50%;
          background-color: var(--color-primary);
          display: flex;
          align-items: center;
          padding: 10px;
          cursor: pointer;
          transition: transform 0.2s ease-in-out;
          & svg {
            width: 15px;
            height: 15px;
            fill: #fff;
          }
          &:hover {
            transform: scale(1.05);
          }
        }
        &--heart {
          margin-right: 10px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          padding: 3px;
          cursor: pointer;
          & svg {
            width: 25px;
            height: 25px;
          }
          &:hover {
            transform: scale(1.05);
          }
        }
        &--more {
          position: relative;
          margin-right: 10px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          padding: 3px;
          cursor: pointer;
          & svg {
            width: 25px;
            height: 25px;
          }
          & .playlist-menu {
            position: absolute;
            display: flex;
            flex-direction: column;
            gap: 10px;
            top: 105%;
            right: 0;
            width: 250px;
            z-index: 2;
            padding: 25px 0px;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            backdrop-filter: blur(15px);
            background-color: var(--background-blur-color-primary);
            &__item {
              font-weight: 900;
              text-shadow: 0 0 1px var(--color-primary);
            }
            @container album-container (max-width: #{$mobile-width}) {
              & {
                right: auto;
                left: 50%;
                transform: translateX(-50%);
                padding: 25px 0px;
                &__item {
                  font-weight: 400;
                }
              }
            }
          }
        }
      }
    }
  }
}
</style>
