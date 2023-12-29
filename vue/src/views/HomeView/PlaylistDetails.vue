<template>
  <teleport to="body">
    <BaseDialog
      v-if="playlistDetail.user_id == user.user_id"
      :open="playlistEditDialog.show"
      :title="playlistEditDialog.title"
      :mode="playlistEditDialog.mode"
      @close="closeEditDetailsDialog"
    >
      <template #default>
        <div class="playlist-edit-container">
          <div class="edit__image">
            <input type="file" id="file" @change="onImageFileChange" />
            <img v-if="edit.img" v-lazyload :data-url="edit.img" />
            <img
              v-else
              v-lazyload
              :data-url="
                playlistDetail.image_path
                  ? `${environment.playlist_cover}/${playlistDetail.image_path}`
                  : `${environment.default}/no_image.jpg`
              "
            />
            <label>
              <span><IconBallPen /></span>
              <span>Choose Photo</span>
            </label>
          </div>
          <div class="edit__detail">
            <div class="edit__detail--title">
              <label for="title">Title</label>
              <input type="text" id="title" v-model="edit.title" />
            </div>
            <div class="edit__detail--description">
              <label for="title">Description</label>
              <textarea
                name="description"
                id="description"
                cols="30"
                rows="10"
                v-model="edit.description"
              ></textarea>
            </div>
          </div>
        </div>
      </template>
      <template #action>
        <BaseButton @click="onUpdateDetails">Update</BaseButton>
        <BaseButton :mode="'warning'" @click="closeEditDetailsDialog"
          >Cancel</BaseButton
        >
      </template>
    </BaseDialog>
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
    <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseDialog>
    <BaseDialog
      :open="addToPlaylistDialog.show"
      :title="addToPlaylistDialog.title"
      :mode="addToPlaylistDialog.mode"
      @close="closeAddToPlaylist"
    >
      <template #default>
        <div v-for="playlist in playlists" :key="playlist.playlist_id">
          <BaseListItem
            v-if="playlist.playlist_id != playlistDetail.playlist_id"
            @click="onAddToPlaylist(playlist.playlist_id)"
          >
            {{ playlist.title }}
          </BaseListItem>
        </div>
      </template>
    </BaseDialog>
  </teleport>
  <div class="playlist-header" :class="{ medium: medium, small: small }">
    <div
      class="header__background"
      :style="[
        playlistDetail.image_path
          ? {
              background: `url(${environment.playlist_cover}/${playlistDetail.image_path})
              no-repeat center center / cover`,
            }
          : {
              background: `url(${environment.default}/no_image.jpg) no-repeat center center / cover`,
            },
      ]"
    ></div>
    <div class="header__image" @click="openEditDetailsDialog">
      <img
        v-lazyload
        :data-url="
          playlistDetail.image_path
            ? `${environment.playlist_cover}/${playlistDetail.image_path}`
            : `${environment.default}/no_image.jpg`
        "
      />
    </div>
    <div class="info">
      <div class="info__type">{{ playlistDetail.type }}</div>
      <div class="info__title" @click="openEditDetailsDialog">
        {{ playlistDetail.title }}
      </div>
      <div class="info__description">{{ playlistDetail.description }}</div>
      <div class="info__other">
        <router-link
          :to="{ name: 'profilePage', params: { id: playlistOwner.user_id } }"
        >
          <div class="info__other--owner">
            {{ playlistOwner.name }}
          </div></router-link
        >
        <div class="info__other--songCount">- {{ songCount }} Songs -</div>
        <div class="info__other--totalDuration">
          {{ totalDuration }}
        </div>
      </div>
    </div>
  </div>
  <div class="control">
    <div class="control__left">
      <div class="control__left--play" v-if="songCount > 0">
        <IconPlay @click="playPlaylist" />
      </div>
      <div
        class="control__left--menu"
        v-click-outside="() => (isMenuOpen = false)"
      >
        <IconHorizontalThreeDot @click="toggleMenu" />
        <transition name="playlist-menu">
          <div class="playlist-menu" v-if="isMenuOpen">
            <BaseListItem @click="scrollToSearch">Add Song</BaseListItem>
            <BaseListItem @click="addPlaylistToQueue"
              >Add to Queue</BaseListItem
            >
            <BaseListItem @click="openAddToPlaylist"
              >Add to Other Playlist</BaseListItem
            >
            <div v-if="playlistDetail.user_id == user.user_id">
              <BaseListItem
                v-if="playlistDetail.type === 'Private'"
                @click="onUpdateType('Public')"
                >Make Public</BaseListItem
              >
              <BaseListItem v-else @click="onUpdateType('Private')"
                >Make Private</BaseListItem
              >
            </div>
            <BaseListItem
              v-if="playlistDetail.user_id == user.user_id"
              @click="onDeletePlaylist"
              >Delete Playlist</BaseListItem
            >
          </div>
        </transition>
      </div>
    </div>
    <div class="control__right" v-if="songCount > 0">
      <div class="control__right--filter">
        <button :class="{ active: isSearchBarOpen }" @click="toggleSearchBar">
          <IconSearch />
        </button>
        <transition name="search-input">
          <input
            type="text"
            placeholder="Search"
            v-model="filterText"
            v-if="isSearchBarOpen"
          />
        </transition>
      </div>
      <div class="control__right--sort">
        <select name="sort" id="" @change="onSortSong">
          <option value="date">Sort by added date</option>
          <option value="title">Sort by title</option>
          <option value="artist">Sort by artist</option>
          <option value="album">Sort by album</option>
        </select>
      </div>
    </div>
  </div>
  <div class="songList" ref="songList">
    <div class="songList__header" v-if="songCount > 0">
      <div class="songList__header--left">
        <div class="songList__header--left--img"></div>
        <div class="songList__header--left--title">Title</div>
      </div>
      <div class="songList__header--right">
        <div class="songList__header--right--album" :class="{ small: small }">
          Album
        </div>
        <div class="songList__header--right--hears" :class="{ medium: medium }">
          Added
        </div>
        <div
          class="songList__header--right--duration"
          :class="{ medium: medium, small: small }"
        >
          Duration
        </div>
        <div
          class="songList__header--right--control"
          :class="{ medium: medium, small: small }"
        ></div>
      </div>
    </div>
    <div class="songList__content" v-if="isSongLoading">
      <BaseCircleLoad />
    </div>
    <div class="songList__content" v-else-if="filterText === ''">
      <BaseSongItem
        v-for="song in songList"
        :data="song"
        :key="song.song_id"
        @addToQueue="addToQueue"
        @selectSong="playSongInPlaylist"
      />
    </div>
    <div class="songList__content" v-else>
      <BaseSongItem
        v-for="song in songFilterResuilt"
        :data="song"
        :key="song.song_id"
        @addToQueue="addToQueue"
        @selectSong="playSongInPlaylist"
      />
    </div>
  </div>
  <div class="searchMore" ref="searchMore">
    <h3>Let find more song for your playlist</h3>
    <div class="searchMore__form" :class="{ medium: medium, small: small }">
      <button>
        <IconSearch />
      </button>
      <input type="text" placeholder="Search" v-model="searchText" />
    </div>
    <div class="searchMore__result" v-if="isGettingSongSearch">
      <BaseCircleLoad />
    </div>
    <div
      class="searchMore__result"
      v-else-if="Object.keys(songSearchResuilt).length > 0"
    >
      <BaseSongItem
        v-for="song in songSearchResuilt"
        :data="song"
        :key="song.song_id"
        :control="false"
        @selectSong="onAddSongToList"
      />
    </div>
    <div class="searchMore__result">
      <h3 v-if="songSearchResuilt.length === 0 && !isGettingSongSearch">
        No result
      </h3>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import { _function } from "@/mixins";
import { useMeta } from "vue-meta";
import type { playlist } from "@/model/playlistModel";
import type { album } from "@/model/albumModel";
import type { user } from "@/model/userModel";
import type { song } from "@/model/songModel";
import type { like } from "@/model/likeModel";
import type { artist } from "@/model/artistModel";
import IconPlay from "@/components/icons/IconPlay.vue";
import IconSearch from "@/components/icons/IconSearch.vue";
import IconHorizontalThreeDot from "@/components/icons/IconHorizontalThreeDot.vue";
import BaseListItem from "@/components/UI/BaseListItem.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import IconBallPen from "@/components/icons/IconBallPen.vue";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
  playlist: playlist &
    {
      pivot: {
        song_id: string;
        playlist_id: string;
        created_at: string;
        updated_at: string;
      };
    }[];
};

export default defineComponent({
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
    "uploadSong",
  ],
  components: {
    IconPlay,
    IconSearch,
    IconHorizontalThreeDot,
    BaseListItem,
    BaseSongItem,
    BaseDialog,
    BaseButton,
    IconBallPen,
    BaseLineLoad,
    BaseCircleLoad,
  },
  inject: ["userPlaylist"],
  data() {
    return {
      environment: environment,
      songListWidth: 0,
      observer: null as ResizeObserver | null,
      small: false,
      medium: false,
      songCount: 0,
      totalDuration: "",
      //song list part
      playlistDetail: {} as playlist,
      songList: [] as songData[],
      playlistOwner: {
        user_id: "1",
      } as user,
      isMenuOpen: false,
      //filter
      isSearchBarOpen: false,
      filterText: "",
      filteredSongList: [] as songData[],
      //search part
      searchText: "",
      songSearchResuilt: [] as songData[],
      addbleSongController: null as AbortController | null,
      addableSongSignal: null as AbortSignal | null,
      //Edit part
      edit: {
        file: null as File | null,
        img: "",
        title: "",
        description: "",
      },
      //dialog
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      playlistEditDialog: {
        title: "Edit details",
        mode: "announcement",
        show: false,
      },
      addToPlaylistDialog: {
        title: "Add to playlist",
        mode: "announcement",
        show: false,
      },
      isLoading: false,
      isSongLoading: false,
      isGettingSongSearch: false,
    };
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    toggleSearchBar() {
      this.isSearchBarOpen = !this.isSearchBarOpen;
    },
    scrollToSearch() {
      (this.$refs.searchMore as HTMLDivElement).scrollIntoView({
        behavior: "smooth",
        block: "end",
        inline: "nearest",
      });
      return;
    },
    loadPlaylist() {
      this.isLoading = true;
      this.getPlaylist({
        playlist_id: this.$route.params.id,
        token: this.token,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "error") {
            this.$router.push({ name: "mainPage" });
          } else {
            this.playlistDetail = res.playlistDetail;
            this.playlistOwner = res.owner;
            this.edit.title = this.playlistDetail.title ?? "";
            this.edit.description = this.playlistDetail.description ?? "";
          }
        });
    },
    loadPlaylistSong() {
      this.isSongLoading = true;
      this.getPlaylistSongs({
        playlist_id: this.$route.params.id,
        token: this.token,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isSongLoading = false;
          if (res.status !== "error") {
            this.songList = res.songList;
            this.songCount = this.songList.length;
            let duration = this.songList.reduce((a: number, key: songData) => {
              return a + key.duration;
            }, 0);
            this.totalDuration = new Date(duration * 1000)
              .toISOString()
              .substring(11, 19);
          }
        });
    },
    setMeta(title: string) {
      useMeta({
        title: title,
      });
    },
    loadSearchResult(query: string) {
      if (!this.addbleSongController) {
        this.addbleSongController = new AbortController();
      } else {
        this.addbleSongController.abort();
        this.addbleSongController = new AbortController();
      }
      this.addableSongSignal = this.addbleSongController.signal;
      this.getAddableSongs({
        token: this.token,
        playlist_id: this.playlistDetail.playlist_id,
        query: query,
        signal: this.addableSongSignal,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isGettingSongSearch = false;
          if (res.status !== "error") {
            this.songSearchResuilt = res.songs;
          }
        });
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    closeEditDetailsDialog() {
      this.playlistEditDialog.show = false;
      this.edit.img = "";
      this.edit.file = null;
    },
    openEditDetailsDialog() {
      this.playlistEditDialog.show = true;
    },
    closeAddToPlaylist() {
      this.addToPlaylistDialog.show = false;
    },
    openAddToPlaylist() {
      this.addToPlaylistDialog.show = true;
    },
    onImageFileChange(e: Event) {
      const target = e.target as HTMLInputElement;
      if (target.files)
        if (target.files.length > 0) {
          if (!_function.validateImageFileType(target.files[0])) {
            this.dialogWaring.content = "Please upload valid image file";
            this.dialogWaring.show = true;
            return;
          }
          let reader = new FileReader();
          reader.readAsDataURL(target.files[0]);
          reader.onload = () => {
            this.edit.img = reader.result as string;
          };
          this.edit.file = target.files[0];
        }
    },
    onUpdateDetails() {
      if (this.edit.title === "") {
        this.dialogWaring.content = "Please fill title field";
        this.dialogWaring.show = true;
        return;
      }
      //update playlist
      this.isLoading = true;
      const formData = new FormData();
      if (this.playlistDetail.playlist_id)
        formData.append("playlist_id", this.playlistDetail.playlist_id);
      formData.append("title", this.edit.title);
      formData.append("description", this.edit.description);
      if (this.edit.file) formData.append("image", this.edit.file);
      this.updateDetailsPlaylist({
        token: this.token,
        formData: formData,
      })
        .then((res) => {
          this.closeEditDetailsDialog();
          this.isLoading = false;
          return res.json();
        })
        .then((res) => {
          if (res.status === "success") {
            this.loadPlaylist();
            this.$emit("updatePlaylist");
          }
        });
    },
    onUpdateType(type: string) {
      this.isLoading = true;
      this.setPlaylistType({
        token: this.token,
        type: {
          playlist_id: this.playlistDetail.playlist_id,
          type: type,
        },
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.playlistDetail.type = type;
          }
        });
    },
    onDeletePlaylist() {
      this.deletePlaylist({
        playlist_id: this.playlistDetail.playlist_id,
        token: this.token,
      })
        .then(() => {
          this.dialogWaring.mode = "announcement";
          this.dialogWaring.content = "Playlist has been deleted";
          this.dialogWaring.show = true;
          this.$router.push({ name: "libraryPage", replace: true });
        })
        .catch((message) => {
          this.dialogWaring.content = message;
          this.dialogWaring.show = true;
        });
    },
    onAddSongToList(id: string) {
      this.isLoading = true;
      this.addSongToPlaylist({
        token: this.token,
        song_id: id,
        playlist_id: this.playlistDetail.playlist_id,
      }).then((res) => {
        this.isLoading = false;
        this.loadPlaylistSong();
        this.loadSearchResult(this.searchText);
      });
    },
    onSortSong(e: Event): void {
      const target = e.target as HTMLSelectElement;
      if (this.filterText === "") {
        if (target.value === "title") {
          this.songList = this.songList.sort((a: songData, b: songData) => {
            return a.title.localeCompare(b.title);
          });
        } else if (target.value === "artist") {
          this.songList = this.songList.sort((a: songData, b: songData) => {
            return a.artist[0].name.localeCompare(b.artist[0].name);
          });
        } else if (target.value === "album") {
          this.songList = this.songList.sort((a: songData, b: songData) => {
            return a.album.name.localeCompare(b.album.name);
          });
        } else if (target.value === "date") {
          this.songList = this.songList.sort((a: songData, b: songData) => {
            return a.playlist[0].pivot.created_at.localeCompare(
              b.playlist[0].pivot.created_at
            );
          });
        }
      }
    },
    onAddToPlaylist(id: string) {
      this.addToPlaylistDialog.show = false;
      this.addToPlaylist({
        from: this.playlistDetail.playlist_id,
        to: id,
        token: this.token,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.$emit("updatePlaylist");
          }
        });
    },
    addToQueue(song: any) {
      this.$emit("addToQueue", song);
    },
    playPlaylist() {
      this.$emit("playPlaylist", this.playlistDetail.playlist_id);
    },
    playSongInPlaylist(song_id: string) {
      this.$emit("playSongInPlaylist", [
        this.playlistDetail.playlist_id,
        song_id,
      ]);
    },
    addPlaylistToQueue() {
      this.$emit("addPlaylistToQueue", this.playlistDetail.playlist_id);
    },
    ...mapActions("playlist", [
      "getPlaylist",
      "getPlaylistSongs",
      "updateDetailsPlaylist",
      "setPlaylistType",
      "getAddableSongs",
      "addSongToPlaylist",
      "addToPlaylist",
      "deletePlaylist",
    ]),
  },
  mounted() {
    const songList = this.$refs.songList as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.songListWidth = entry.contentRect.width;
      }
    });
    if (this.observer && songList) this.observer.observe(songList);
    this.setMeta("Playlist");
  },
  created() {
    this.loadPlaylist();
    this.loadPlaylistSong();
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      user: "auth/userData",
      playlists: "playlist/getPlaylists",
    }),
    songFilterResuilt() {
      if (this.filterText === "") return this.songList;
      const resuilt = this.songList.filter((song: any) =>
        song.title.toLowerCase().includes(this.filterText.toLowerCase())
      );
      return resuilt;
    },
  },
  watch: {
    songListWidth(o: number) {
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
    searchText(o: string) {
      this.isGettingSongSearch = true;
      if (o !== "") {
        this.loadSearchResult(o);
      } else {
        this.isGettingSongSearch = false;
        this.songSearchResuilt = [];
      }
    },
    "$route.params.id": {
      handler() {
        if (this.$route.name === "playlistViewPage") {
          this.loadPlaylist();
          this.loadPlaylistSong();
        }
      },
      deep: true,
      immediate: true,
    },
  },
});
</script>
<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;

.playlist-header {
  display: flex;
  align-items: center;
  padding: 40px 20px;
  position: relative;
  &.small {
    display: block;
    & .header__image {
      width: 150px;
      height: 150px;
      margin: auto;
    }
  }
  & .header__background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    z-index: -1;
    filter: blur(6px) brightness(0.7);
  }

  & .header__image {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    cursor: pointer;
    & img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  .info {
    width: 100%;
    height: 100%;
    padding: 0 20px;
    display: flex;
    flex-direction: column;
    a {
      color: #fff;
      &:hover {
        text-decoration: underline;
      }
    }
    &__type {
      font-size: 14px;
      color: #fff;
      font-weight: 700;
    }

    &__title {
      font-size: 32px;
      color: #fff;
      font-weight: 900;
      cursor: pointer;
      word-break: break-all;
    }

    &__description {
      font-size: 16px;
      color: #fff;
      font-weight: 400;
      word-break: break-all;
    }

    &__other {
      display: flex;
      color: #fff;
      font-size: 16px;

      &--owner {
        font-weight: 700;
      }

      &--songCount {
        margin: 0 10px;
        font-weight: 400;
      }
    }
  }
}

.control {
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;

  &__left {
    display: flex;
    height: 100%;
    align-items: center;
    justify-content: center;

    &--play {
      aspect-ratio: 1/1;
      height: 50px;
      width: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      cursor: pointer;
      background: var(--color-primary);
      transition: all 0.2s ease-in-out;

      & svg {
        width: 25px;
        height: 25px;
        fill: #fff;
      }

      &:hover {
        transform: scale(1.1);
      }
    }

    &--menu {
      aspect-ratio: 1/1;
      height: 50px;
      width: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      margin: 0 10px;

      & svg {
        width: 25px;
        height: 25px;
        fill: var(--text-subdued);
      }

      &:hover svg {
        fill: var(--text-primary-color);
      }

      .playlist-menu {
        position: absolute;
        display: block;
        top: 105%;
        left: 0px;
        width: 250px;
        border-radius: 10px;
        z-index: 20;
        padding: 10px 0px;
        background: var(--background-glass-color-primary);

        & > * {
          margin: 5px auto;
        }
      }
    }
  }

  &__right {
    display: flex;
    height: 100%;
    align-items: center;
    justify-content: center;
    width: max-content;

    &--filter {
      display: flex;
      height: 100%;
      align-items: center;
      justify-content: center;
      width: max-content;
      margin-right: 10px;

      & button {
        display: flex;
        width: 40px;
        height: 40px;
        background-color: var(--background-glass-color-primary);
        outline: none;
        border: none;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        fill: var(--text-subdued);
        cursor: pointer;

        &.active {
          border-radius: 50% 0 0 50%;
        }
      }

      & input {
        height: 40px;
        display: block;
        border-radius: 0px 10px 10px 0px;
        border: none;
        outline: none;
        line-height: 50px;
        padding: 0 10px;
        background: var(--background-glass-color-primary);
        color: var(--text-primary-color);
        font-size: 16px;
        font-weight: 400;
        transition: all 0.5s forwards;
        -webkit-transition: width 0.5s;
        -moz-transition: width 0.5s;

        &::placeholder {
          color: var(--text-subdued);
        }
      }
    }

    &--sort {
      display: flex;
      height: 100%;
      align-items: center;
      justify-content: center;
      width: max-content;

      & select {
        height: 40px;
        width: 200px;
        border-radius: 10px;
        border: none;
        outline: none;
        line-height: 50px;
        padding: 0 10px;
        margin-right: 10px;
        background: var(--background-glass-color-primary);
        color: var(--text-primary-color);
        font-size: 16px;
        font-weight: 400;

        &::placeholder {
          color: var(--text-primary-color);
        }

        & > * {
          color: var(--text-primary-color);
          background: var(--background-color-primary);
        }
      }
    }
  }
}

.songList {
  width: 100%;

  &__header {
    width: 80%;
    height: 50px;
    color: var(--text-subdued);
    margin: 0 auto;
    display: flex;
    align-items: center;
    border-bottom: 1px solid var(--text-subdued);
    padding: 0px 17px;

    &--left {
      width: 70%;
      display: flex;

      &--img {
        width: 50px;
        flex: 1 0 50px;
      }

      &--title {
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        width: 100%;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
        text-align: center;
        cursor: pointer;
      }
    }

    &--right {
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      user-select: none;

      &--album {
        width: 60%;
        overflow: hidden;
        text-align: center;
        cursor: pointer;

        &.small {
          display: none;
        }
      }

      &--hears {
        width: 20%;
        overflow: hidden;
        text-align: center;
        cursor: pointer;

        &.medium {
          display: none;
        }
      }

      &--duration {
        width: 20%;
        overflow: hidden;
        text-align: center;
        cursor: pointer;

        &.medium {
          width: 20%;

          &.small {
            width: 80%;
          }
        }
      }

      &--control {
        width: 30px;
      }
    }
  }
}

.searchMore {
  width: 100%;
  h3 {
    width: 100%;
    text-align: center;
    color: var(--text-subdued);
    font-size: 1.5rem;
    font-weight: 600;
    margin: 20px 0px;
  }
  &__form {
    display: flex;
    button {
      margin: 0 10px;
      width: 50px;
      height: 50px;
      flex: 0 0 auto;
      border-radius: 10px;
      border: none;
      outline: none;
      background: var(--background-glass-color-primary);
      color: var(--text-primary-color);
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
      &:hover {
        transform: scale(1.1);
      }
    }
    input {
      width: 50%;
      height: 50px;
      background: var(--background-color-secondary);
      color: var(--text-primary-color);
      outline: none;
      border: none;
      padding: 0 10px;
    }
    &.medium {
      input {
        width: calc(70% - 70px);
      }
    }
    &.small {
      input {
        width: calc(90% - 70px);
      }
    }
  }
  &__result {
    padding: 20px;
  }
}

.playlist-edit-container {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  .edit__image {
    aspect-ratio: 1/1;
    width: 40%;
    height: 40%;
    position: relative;
    flex: 0 0 auto;
    border-radius: 10px;
    overflow: hidden;
    input {
      opacity: 0;
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      cursor: pointer;
      z-index: 2;
    }
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    label {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      cursor: pointer;
      z-index: 1;
      background: var(--background-color-secondary);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      opacity: 0;
      span {
        display: block;
      }
      svg {
        width: 40px;
        height: 40px;
      }
    }
    &:hover label {
      opacity: 1;
    }
  }
  .edit__detail {
    width: 60%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 0 20px;
    label {
      font-weight: 600;
      padding: 2px 0;
    }
    .edit__detail--title {
      width: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: center;
      input {
        width: 100%;
        height: 50px;
        background: var(--background-glass-color-primary);
        color: var(--text-primary-color);
        outline: none;
        border: none;
        padding: 0 10px;
        font-size: 1.1rem;
        border-radius: 10px;
        backdrop-filter: blur(10px);
      }
    }
    .edit__detail--description {
      width: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: center;
      textarea {
        width: 100%;
        height: 100px;
        background: var(--background-glass-color-primary);
        color: var(--text-primary-color);
        outline: none;
        border: none;
        padding: 10px;
        font-size: 1rem;
        font-weight: 400;
        resize: none;
        border-radius: 10px;
        backdrop-filter: blur(10px);
      }
    }
  }
}

@media (max-width: $mobile-width) {
  .playlist-header {
    & .header__image {
      display: none;
    }
  }
}

.bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: transparent;
  z-index: 10;
  display: none;

  &.active {
    display: block;
  }
}

.playlist-menu-enter-from {
  opacity: 0;
  top: 55%;
}

.playlist-menu-enter-to {
  opacity: 1;
  top: 105%;
}

.playlist-menu-enter-active {
  transition: all 0.5s;
}

.playlist-menu-leave-from {
  opacity: 1;
  top: 105%;
}

.playlist-menu-leave-to {
  opacity: 0;
  top: 55%;
}

.playlist-menu-leave-active {
  transition: all 0.5s;
}

.search-input-enter-from,
.search-input-leave-to {
  width: 0;
}

.search-input-enter-to,
.search-input-leave-from {
  width: 200px;
}
</style>
