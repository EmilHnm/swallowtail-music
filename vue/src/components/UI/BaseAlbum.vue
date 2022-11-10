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
          v-for="playlist in userPlaylist"
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
  <div class="album-container">
    <div class="album__details">
      <div class="album__details--cover">
        <img :src="`${environment.album_cover}/${data.image_path}`" />
      </div>
      <div class="album__details--prof">
        <div class="album__details--prof--title">
          <h3>{{ data.name }}</h3>
        </div>
        <div class="album__details--prof--sub">
          <span> {{ data.release_year }} - {{ data.song_count }} Songs</span>
        </div>
        <div class="album__details--prof--menu">
          <div class="album__details--prof--menu--play">
            <IconPlay @click="playAlbum" />
          </div>
          <div class="album__details--prof--menu--heart">
            <IconHeartFilled />
          </div>
          <div class="album__details--prof--menu--more">
            <IconHorizontalThreeDot @click="toggleMenu" />
            <teleport to="body">
              <div
                class="bg"
                :class="{ active: isMenuOpen }"
                @click="toggleMenu"
              ></div>
            </teleport>
            <transition name="playlist-menu">
              <div class="playlist-menu" v-if="isMenuOpen">
                <BaseListItem @click="addAlbumToQueue"
                  >Add to Queue</BaseListItem
                >
                <BaseListItem>Add to Library</BaseListItem>
                <BaseListItem @click="isPlaylistOpening = true"
                  >Add to Playlist</BaseListItem
                >
                <BaseListItem>Report</BaseListItem>
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
          :key="song[0].song_id"
          :control="true"
          @select-song="playSongInAlbum(song[0].song_id)"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type { album } from "@/model/albumModel";
import { environment } from "@/environment/environment";
import { mapActions, mapGetters } from "vuex";
import IconHeartFilled from "../icons/IconHeartFilled.vue";
import IconHorizontalThreeDot from "../icons/IconHorizontalThreeDot.vue";
import BaseSongItem from "./BaseSongItem.vue";
import IconPlay from "../icons/IconPlay.vue";
import BaseDialog from "./BaseDialog.vue";
import BaseLineLoad from "./BaseLineLoad.vue";
import BaseCircleLoad from "./BaseCircleLoad.vue";
import BaseListItem from "./BaseListItem.vue";
import type { playlist } from "@/model/playlistModel";

type albumData = album & {
  song_count: number;
};

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

type playlistData = playlist & {
  songCount: number;
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    userPlaylist: playlistData[];
  }
}

export default defineComponent({
  props: {
    data: {
      type: Object as () => albumData,
      required: true,
    },
  },
  inject: ["userPlaylist"],
  emits: ["playAlbum", "addAlbumToQueue", "playSongInAlbum"],
  data() {
    return {
      environment: environment,
      isMenuOpen: false,
      songs: {} as songPlaylist,
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
    playAlbum() {
      this.$emit("playAlbum", this.data.album_id);
    },
    addAlbumToQueue() {
      this.$emit("addAlbumToQueue", this.data.album_id);
    },
    playSongInAlbum(id: string) {
      this.$emit("playSongInAlbum", id);
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.getAlbumSongs({
      userToken: this.token,
      album_id: this.data.album_id,
    })
      .then((res) => {
        return res.json();
      })
      .then((res) => {
        if (res.status === "success") {
          this.songs = res.songs.reduce((r: any, a: any) => {
            r[a.song_id] = r[a.song_id] || [];
            r[a.song_id].push(a);
            return r;
          }, Object.create(null));
          this.isSongsLoading = false;
        }
      });
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
.album-container {
  width: 90%;
  margin: 20px auto;
  box-shadow: 0 2px 10px var(--background-glass-color-primary);
  padding-bottom: 20px;
  & .album__details {
    display: flex;
    height: 150px;
    margin-bottom: 30px;
    background-color: var(--background-glass-color-primary);
    &--cover {
      aspect-ratio: 1/1;
      height: 100%;
      width: auto;
      flex: 0 0 auto;
      & img {
        height: 100%;
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
      &--title {
        cursor: pointer;
        user-select: none;
        flex: 0 0 auto;
        & h3 {
          font-size: 2rem;
          font-weight: 700;
          margin: 2px;
          width: 80%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
      }
      &--sub {
        flex: 0 0 auto;
      }
      &--menu {
        display: flex;
        align-items: center;
        margin-top: auto;
        height: 100%;
        &--play {
          margin-right: 10px;
          border-radius: 50%;
          background-color: var(--color-primary);
          display: flex;
          align-items: center;
          padding: 4px;
          cursor: pointer;
          transition: transform 0.2s ease-in-out;
          & svg {
            width: 25px;
            height: 25px;
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
          & .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
            &.active {
              opacity: 1;
              visibility: visible;
            }
          }
          & .playlist-menu {
            position: absolute;
            top: 105%;
            right: 0;
            width: 250px;
            z-index: 2;
            padding: 10px 0px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            & > * {
              margin-bottom: 10px;
            }
          }
          & .playlist-menu-enter-from,
          .playlist-menu-leave-to {
            opacity: 0;
            transform: translateY(-10px);
          }
          & .playlist-menu-enter-to,
          .playlist-menu-leave-from {
            opacity: 1;
            transform: translateY(0);
          }
        }
      }
    }
  }
}
</style>
