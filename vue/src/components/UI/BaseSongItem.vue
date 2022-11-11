<template>
  <teleport to="body">
    <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseDialog>
  </teleport>
  <div class="song-item" ref="songItem">
    <BaseListItem :selected="selected">
      <div class="song-item__left" :class="{ small: small }">
        <div class="song-item__left--image" @click="selectSong">
          <img
            :src="`${environment.album_cover}/${data[0].image_path}`"
            alt=""
            srcset=""
          />
        </div>
        <div class="song-item__left--title">
          <span @click="selectSong">{{ data[0].title }}</span>
          <span v-if="data.length > 0">
            <span>
              <router-link
                :to="{
                  name: 'artistPage',
                  params: { id: artistitem.artist_id },
                }"
                v-for="(artistitem, index) in data"
                :key="artistitem.artist_id"
                >{{ artistitem.artist_name }}
                <span v-if="index !== data.length - 1">,</span>
              </router-link></span
            >
          </span>
          <span v-else><BaseLineLoad /></span>
        </div>
      </div>
      <div class="song-item__right" :class="{ small: small }">
        <div class="song-item__right--album" :class="{ small: small }">
          <span
            v-if="data[0].album_id"
            @click="redirectToAlbum(data[0].album_id)"
            >{{ data[0].album_name }}</span
          >
          <span v-else><BaseLineLoad /></span>
        </div>
        <div
          class="song-item__right--hears"
          v-if="data[0].listens !== undefined"
          :class="{ medium: medium }"
        >
          <span>{{ data[0].listens }}</span>
        </div>
        <div
          class="song-item__right--addDate"
          v-else
          :class="{ medium: medium }"
        >
          <span v-if="data[0].added_date">{{
            new Date(data[0].added_date).toLocaleDateString()
          }}</span>
        </div>
        <div
          class="song-item__right--duration"
          :class="{ medium: medium, small: small }"
        >
          <span>{{
            new Date(data[0].duration * 1000).toISOString().substring(14, 19)
          }}</span>
        </div>
        <div
          class="song-item__right--control"
          :class="{ medium: medium, small: small }"
          v-if="control"
        >
          <div class="song-item__right--control--button" @click="toggleMenu">
            <IconThreeDots />
          </div>
        </div>
      </div>
    </BaseListItem>
  </div>
  <teleport to="body">
    <div class="bg" @click="toggleMenu" v-if="isMenuOpen"></div>
    <div class="menu" v-if="isMenuOpen && control">
      <div class="menu__song">
        <div class="menu__song--img">
          <img
            :src="`${environment.album_cover}/${data[0].image_path}`"
            alt=""
            srcset=""
          />
        </div>
        <div class="menu__song--title">
          <span class="title">{{ data[0].title }}</span>
          <span class="artist">
            <router-link
              :to="{ name: 'artistPage', params: { id: artistitem.artist_id } }"
              v-for="(artistitem, index) in data"
              :key="artistitem.artist_id"
              >{{ artistitem.artist_name }}
              <span v-if="index !== data.length - 1">,</span>
            </router-link>
          </span>
        </div>
        <div class="menu__song--album">
          <span v-if="data[0].album_id">{{ data[0].album_name }}</span>
          <span v-else><BaseLineLoad /></span>
        </div>
      </div>
      <div class="menu__btn">
        <div class="" v-if="menuMode === 'default'">
          <BaseListItem @click="onLikeSong">
            <span v-if="liked !== null">
              {{ liked ? "Unlike" : "Like" }}
            </span>
            <span v-else>
              <BaseLineLoad />
            </span>
          </BaseListItem>
          <BaseListItem @click="changeMenuMode('playlist')"
            >Add to Playlist</BaseListItem
          >
          <BaseListItem v-if="inQueue" @click="deleteFromQueue"
            >Delete from Queue</BaseListItem
          >
          <BaseListItem v-else @click="addToQueue">Add to Queue</BaseListItem>
          <BaseListItem v-if="inPlaylist" @click="deleteFromPlaylist"
            >Delete from Playlist</BaseListItem
          >
          <BaseListItem @click="changeMenuMode('artist')"
            >View Artist</BaseListItem
          >
        </div>
        <div class="" v-if="menuMode === 'artist'">
          <router-link
            :to="{ name: 'artistPage', params: { id: artistitem.artist_id } }"
            v-for="artistitem in data"
            :key="artistitem.artist_id"
          >
            <BaseListItem>{{ artistitem.artist_name }}</BaseListItem>
          </router-link>
        </div>
        <div class="" v-if="menuMode === 'playlist'">
          <BaseListItem
            v-for="playlist in userPlaylist"
            :key="playlist.playlist_id"
            @click="onAddSongToPlaylistlist(playlist.playlist_id)"
            >{{ playlist.title }}</BaseListItem
          >
        </div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import BaseListItem from "./BaseListItem.vue";
import IconThreeDots from "../icons/IconThreeDots.vue";
import BaseLineLoad from "./BaseLineLoad.vue";
import { mapActions, mapGetters } from "vuex";
import BaseDialog from "./BaseDialog.vue";
import type { playlist } from "@/model/playlistModel";

type songData = {
  song_id: string;
  title: string;
  artist_name: string;
  artist_id: string;
  added_date?: string;
  album_name: string;
  album_id: string;
  duration: number;
  image_path: string;
  listens?: number;
}[];

type playlistData = playlist & {
  songCount: number;
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    userPlaylist: playlistData[];
  }
}

export default defineComponent({
  emits: [
    "deleteFromQueue",
    "deleteFromPlaylist",
    "selectSong",
    "likeSong",
    "addToQueue",
  ],
  inject: ["userPlaylist"],
  data() {
    return {
      environment: environment,
      observer: null as ResizeObserver | null,
      songItemWidth: 0,
      small: false,
      medium: false,
      isMenuOpen: false,
      menuMode: "default",
      isLoading: false,
      liked: null,
    };
  },
  props: {
    data: {
      type: Object as () => songData,
      required: true,
    },
    selected: {
      type: Boolean,
      default: false,
    },
    inQueue: {
      type: Boolean,
      default: false,
    },
    inPlaylist: {
      type: Boolean,
      default: false,
    },
    control: {
      type: Boolean,
      default: true,
    },
  },
  methods: {
    ...mapActions("playlist", ["addSongToPlaylist"]),
    ...mapActions("song", ["likeSong", "likedSong"]),
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
      this.changeMenuMode("default");
    },
    changeMenuMode(mode: string) {
      this.menuMode = mode;
    },
    deleteFromQueue() {
      this.isMenuOpen = false;
      this.$emit("deleteFromQueue");
    },
    deleteFromPlaylist() {
      this.isMenuOpen = false;
      this.$emit("deleteFromPlaylist");
    },
    addToQueue() {
      this.isMenuOpen = false;
      this.$emit("addToQueue", { [this.data[0].song_id]: this.data });
    },
    selectSong() {
      this.$emit("selectSong", this.data[0].song_id);
    },
    onAddSongToPlaylistlist(id: string) {
      this.isLoading = true;
      this.addSongToPlaylist({
        token: this.token,
        song_id: this.data[0].song_id,
        playlist_id: id,
      }).then(() => {
        this.isLoading = false;
        this.toggleMenu();
      });
    },
    redirectToAlbum(id: string) {
      this.$router.push({ name: "albumViewPage", params: { id: id } });
    },
    loadLiked() {
      this.likedSong({
        userToken: this.token,
        songId: this.data[0].song_id,
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res: any) => {
          this.liked = res.liked;
          this.isLoading = false;
        });
    },
    onLikeSong() {
      if (this.liked !== null) {
        this.isLoading = true;
        this.isMenuOpen = false;
        this.likeSong({
          userToken: this.token,
          songId: this.data[0].song_id,
        }).then(() => {
          this.$emit("likeSong", this.data[0].song_id);
          this.loadLiked();
        });
      }
    },
  },
  mounted() {
    const songItem = this.$refs.songItem as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.songItemWidth = entry.contentRect.width;
      }
    });
    if (this.observer && songItem) this.observer.observe(songItem);
  },
  created() {
    this.loadLiked();
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
  watch: {
    songItemWidth(o) {
      if (o < 600) {
        this.small = true;
        this.medium = true;
      } else if (o < 700 && o > 600) {
        this.small = false;
        this.medium = true;
      } else {
        this.small = false;
        this.medium = false;
      }
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  components: { BaseListItem, IconThreeDots, BaseLineLoad, BaseDialog },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: var(--background-glass-color-primary);
  z-index: 10;
}
.menu {
  position: absolute;
  display: block;
  left: 50%;
  top: 50%;
  width: 250px;
  border-radius: 10px;
  z-index: 20;
  padding: 30px 20px;
  transform: translate(-50%, -50%);
  background: var(--background-color-primary);
  user-select: none;
  max-height: 70%;
  overflow: scroll;
  &::-webkit-scrollbar {
    width: 0;
  }
  &__song {
    width: 100%;
    &--img {
      aspect-ratio: 1/1;
      width: calc(100% - 40px);
      height: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: auto;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
      }
    }
    &--title {
      margin: 10px auto;
      width: calc(100% - 40px);
      span {
        font-size: 1rem;

        &.artist {
          display: flex;
          a {
            color: var(--text-color-primary);
            text-decoration: none;
            margin-right: 2px;
            &:hover {
              text-decoration: underline;
            }
          }
        }
        &.title {
          font-weight: 600;
          width: 100%;
          overflow-x: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
          display: block;
        }
      }
    }
    &--album {
      margin: 10px auto;
      span {
        display: block;
        font-size: 0.8rem;
        color: var(--text-color-secondary);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
  }
  &__btn {
    margin-top: 10px;
    width: 100%;
    & > div {
      max-height: 300px;
      overflow: auto;
      & > * {
        margin-bottom: 10px;
        cursor: pointer;
      }
    }
  }
}
@media (max-width: $mobile-width) {
  .menu {
    width: 100%;
    padding: 10px 0;
    height: 70%;
    overflow: auto;
    z-index: 300;
    bottom: 0;
    top: auto;
    left: 0;
    transform: none;
    animation: 0.3s;
    border-radius: 0;
    &__song {
      width: 100%;
      &--img {
        width: 50%;
      }
      &--title {
        & > span {
          text-align: center;
          justify-content: center;
        }
      }
      &--album {
        span {
          text-align: center;
        }
      }
    }
  }
}
.song-item {
  width: 100%;
  height: max-content;
  display: flex;
  margin: 10px auto;
  cursor: pointer;
  &__left {
    width: 40%;
    display: flex;
    overflow: hidden;
    flex: 0 0 auto;
    &.small {
      width: 70%;
    }
    &--image {
      width: 50px;
      height: 50px;
      flex: 0 0 auto;
      margin-right: 10px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    &--title {
      display: flex;
      flex-direction: column;
      justify-content: center;
      overflow: hidden;
      width: 100%;
      user-select: none;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      span {
        &:first-child {
          font-size: 1rem;
          font-weight: 600;
          white-space: nowrap;
          text-overflow: ellipsis;
          overflow: hidden;
        }
        a {
          font-size: 0.8rem;
          color: var(--text-subdued);
          white-space: nowrap;
          text-overflow: ellipsis;
          overflow: hidden;
          &:hover {
            text-decoration: underline;
          }
        }
      }
    }
  }
  &__right {
    width: 60%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
    &.small {
      width: auto;
    }
    &--album {
      width: 50%;
      overflow: hidden;
      text-align: center;
      text-overflow: ellipsis;
      &:hover {
        text-decoration: underline;
      }
      &.small {
        display: none;
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--hears {
      width: 20%;
      overflow: hidden;
      text-overflow: ellipsis;
      text-align: center;
      &.medium {
        display: none;
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--addDate {
      width: 20%;
      overflow: hidden;
      text-overflow: ellipsis;
      text-align: center;
      &.medium {
        display: none;
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--duration {
      width: 20%;
      overflow: hidden;
      text-align: center;
      &.medium {
        width: 20%;
        &.small {
          width: 80%;
        }
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--control {
      height: 30px;
      width: 30px;
      border-radius: 20%;
      position: relative;
      flex: 0 0 auto;
      &:hover {
        background: var(--background-glass-color-primary);
        box-shadow: 0 0px 15px rgb(0 0 0 / 50%);
      }
      &--button {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        & svg {
          width: 20px;
          height: 20px;
          fill: var(--text-primary-color);
        }
      }
    }
  }
}
</style>
