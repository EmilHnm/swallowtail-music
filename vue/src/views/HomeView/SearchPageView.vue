<template>
  <form class="search-form" autocomplete="off" @submit.prevent="">
    <BaseInput
      :id="'search'"
      :label="'Search'"
      :type="'text'"
      :placeholder="'Enter your key word!'"
      v-model="searchText"
    />
  </form>
  <div v-if="searchText.length > 0" class="search-result">
    <div class="search-result__tag" ref="container" @wheel="searchFilterScroll">
      <div class="search-result__tag--all">
        <BaseButton @click="changeComponent('AllSearchPage')">
          <BaseDotLoading v-if="isSearching" />
          <span v-else>All</span>
        </BaseButton>
      </div>
      <div class="search-result__tag--song" v-if="songResult.length > 0">
        <BaseButton @click="changeComponent('SongSearchPage')">
          <BaseDotLoading v-if="searching.song" />
          <span v-else>Song</span>
        </BaseButton>
      </div>
      <div
        class="search-result__tag--album"
        v-if="albumResult.length > 0 || searching.album"
      >
        <BaseButton @click="changeComponent('AlbumSearchPage')">
          <BaseDotLoading v-if="searching.album" />
          <span v-else>Album</span>
        </BaseButton>
      </div>
      <div
        class="search-result__tag--artist"
        v-if="artistResult.length > 0 || searching.artist"
      >
        <BaseButton @click="changeComponent('ArtistSearchPage')">
          <BaseDotLoading v-if="searching.artist" />
          <span v-else>Artist</span></BaseButton
        >
      </div>
      <div
        class="search-result__tag--user"
        v-if="userResult.length > 0 || searching.user"
      >
        <BaseButton @click="changeComponent('UserSearchPage')">
          <BaseDotLoading v-if="searching.user" />
          <span v-else>User</span>
        </BaseButton>
      </div>
    </div>
    <keep-alive v-if="hasResult">
      <component
        :is="selectedSearchFilter"
        @changeSearchPage="changeComponent"
        @playSong="playSong"
        @playAlbum="playAlbum"
        @playArtistSong="playArtistSong"
      ></component>
    </keep-alive>
    <div v-else>
      <h3>No result</h3>
    </div>
  </div>
  <div class="genres-container" v-else>
    <router-link
      :to="{ name: 'genrePage', params: { id: genre.genre_id } }"
      v-for="genre in genres"
      :key="genre.id"
      class="genre-card"
    >
      <span class="genre-card__name">
        {{ genre.name }}
      </span>
    </router-link>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent } from "vue";
import BaseInput from "@/components/UI/BaseInput.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import AllSearchPage from "@/components/SearchPage/AllSearchPage.vue";
import ArtistSearchPage from "@/components/SearchPage/ArtistSearchPage.vue";
import AlbumSearchPage from "@/components/SearchPage/AlbumSearchPage.vue";
import UserSearchPage from "@/components/SearchPage/UserSearchPage.vue";
import SongSearchPage from "@/components/SearchPage/SongSearchPage.vue";
import { mapActions, mapGetters } from "vuex";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import BaseDotLoading from "@/components/UI/BaseDotLoading.vue";
import type { song } from "@/model/songModel";
import type { like } from "@/model/likeModel";
import { useMeta } from "vue-meta";
import BaseCardVue from "@/components/UI/BaseCard.vue";
import globalEmitListener from "@/shared/constants/globalEmitListener";
type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

type albumData = album & { song_count: number };

export default defineComponent({
  emits: [...globalEmitListener],
  data() {
    return {
      songResult: [] as songData[],
      albumResult: [] as albumData[],
      artistResult: [] as artist[],
      userResult: [],
      container: null as HTMLElement | null,
      songController: null as AbortController | null,
      albumController: null as AbortController | null,
      artistController: null as AbortController | null,
      userController: null as AbortController | null,
      songSignal: null as null | AbortSignal,
      albumSignal: null as null | AbortSignal,
      artistSignal: null as null | AbortSignal,
      userSignal: null as null | AbortSignal,
      selectedSearchFilter: "AllSearchPage",
      pos: { top: 0, left: 0, x: 0, y: 0 },
      searchText: "",
      searching: {
        song: false,
        album: false,
        artist: false,
        user: false,
      },
    };
  },
  provide() {
    return {
      songResult: computed(() => this.songResult),
      albumResult: computed(() => this.albumResult),
      artistResult: computed(() => this.artistResult),
      userResult: computed(() => this.userResult),
    };
  },
  components: {
    BaseInput,
    BaseButton,
    AllSearchPage,
    ArtistSearchPage,
    AlbumSearchPage,
    UserSearchPage,
    SongSearchPage,
    BaseDotLoading,
    BaseCardVue,
  },
  methods: {
    ...mapActions("song", ["searchSong"]),
    ...mapActions("album", ["searchAlbums"]),
    ...mapActions("artist", ["searchArtist"]),
    ...mapActions("user", ["searchUser"]),
    ...mapActions("genre", ["fetchGenres"]),
    changeComponent(componentName: string) {
      if (this.isSearching) return;
      this.selectedSearchFilter = componentName;
    },
    searchFilterScroll(e: WheelEvent) {
      e.preventDefault();
      if (this.container)
        if (e.deltaY > 0) {
          this.container.scrollLeft += 10;
        } else {
          this.container.scrollLeft -= 10;
        }
    },
    mouseDown(e: MouseEvent) {
      if (this.container) {
        this.container.style.cursor = "grabbing";
        this.container.style.userSelect = "none";
        this.container.addEventListener("mousemove", this.mouseMove);
        this.pos = {
          left: this.container.offsetLeft,
          top: this.container.offsetTop,
          x: e.clientX,
          y: e.clientY,
        };
      }
    },
    mouseUp() {
      if (this.container) {
        this.container.style.cursor = "grab";
        this.container.style.removeProperty("user-select");
        this.container.removeEventListener("mousemove", this.mouseMove);
      }
    },
    mouseMove(e: MouseEvent) {
      // How far the mouse has been moved
      const dx = e.clientX - this.pos.x;
      const dy = e.clientY - this.pos.y;
      // Scroll the element
      if (this.container) {
        this.container.scrollTop = this.pos.top - dy;
        this.container.scrollLeft = this.pos.left - dx;
      }
    },
    onSearchSong(query: string) {
      this.searching.song = true;
      if (!this.songController) {
        this.songController = new AbortController();
      } else {
        this.songController.abort();
        this.songController = new AbortController();
      }
      this.songSignal = this.songController.signal;
      this.searchSong({
        token: this.token,
        query: query,
        signal: this.songSignal,
      })
        .then((res: any) => res.json())
        .then((res) => {
          this.searching.song = false;
          if (res.status === "success") {
            this.songResult = res.songs;
          }
        });
    },
    onSearchAlbums(query: string) {
      this.searching.album = true;
      if (!this.albumController) {
        this.albumController = new AbortController();
      } else {
        this.albumController.abort();
        this.albumController = new AbortController();
      }
      this.albumSignal = this.albumController.signal;
      this.searchAlbums({
        token: this.token,
        query: query,
        signal: this.albumSignal,
      })
        .then((res: any) => res.json())
        .then((res) => {
          this.searching.album = false;
          if (res.status === "success") {
            this.albumResult = res.albums;
          }
        });
    },
    onSearchArtist(query: string) {
      if (!this.artistController) {
        this.artistController = new AbortController();
      } else {
        this.artistController.abort();
        this.artistController = new AbortController();
      }
      this.artistSignal = this.artistController.signal;
      this.searching.artist = true;
      this.searchArtist({
        token: this.token,
        query: query,
        signal: this.artistSignal,
      })
        .then((res: any) => res.json())
        .then((res) => {
          this.searching.artist = false;
          if (res.status === "success") {
            this.artistResult = res.artists;
          }
        });
    },
    onSearchUser(query: string) {
      this.searching.user = true;
      if (!this.userController) {
        this.userController = new AbortController();
      } else {
        this.userController.abort();
        this.userController = new AbortController();
      }
      this.userSignal = this.userController.signal;
      this.searchUser({
        token: this.token,
        query: query,
        signal: this.userSignal,
      })
        .then((res: any) => res.json())
        .then((res) => {
          this.searching.user = false;
          if (res.status === "success") {
            this.userResult = res.users;
          }
        });
    },
    playSong(song_id: string) {
      this.$emit("playSong", song_id);
    },
    playAlbum(album_id: string) {
      this.$emit("playAlbum", album_id);
    },
    playArtistSong(artist_id: string) {
      this.$emit("playArtistSong", artist_id);
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      genres: "genre/getGenres",
    }),
    hasResult() {
      return (
        Object.keys(this.songResult).length > 0 ||
        this.albumResult.length > 0 ||
        this.artistResult.length > 0 ||
        this.userResult.length > 0
      );
    },
    isSearching() {
      return (
        this.searching.song ||
        this.searching.album ||
        this.searching.artist ||
        this.searching.user
      );
    },
  },
  watch: {
    searchText() {
      this.$router.replace({
        query: {
          q: this.searchText,
        },
      });
      if (this.searchText !== "") {
        this.onSearchSong(this.searchText);
        this.onSearchAlbums(this.searchText);
        this.onSearchArtist(this.searchText);
        this.onSearchUser(this.searchText);
      } else {
        this.songResult = [];
        this.albumResult = [];
        this.artistResult = [];
        this.userResult = [];
      }
    },
  },
  mounted() {
    this.container = this.$refs.container as HTMLElement;
    this.searchText = (this.$route.query.q as string) ?? "";
    this.fetchGenres(this.token);
    useMeta({
      title: "Search",
    });
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
$desktop-width: 1024px;
$large-desktop-width: 1280px;
$widescreen-width: 1536px;

.search-form {
  margin: 0 auto;
  width: 80%;
  padding: 0 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  form > * {
    margin: 10px 0;
  }
}
.search-result {
  margin: 0 auto;
  width: 90%;
  &__tag {
    display: flex;
    justify-content: space-between;
    margin: 20px auto;
    width: 80%;
    overflow-y: auto;
    cursor: grab;
    & > * {
      width: 150px;
    }
  }
  &__container {
    margin: 20px 0;
  }
}

.genres-container {
  width: 90%;
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  grid-gap: 40px;
  padding: 40px 0;
  margin: auto;

  @container main (max-width: #{$large-desktop-width}) {
    & {
      grid-template-columns: repeat(4, 1fr);
      grid-gap: 10px;
    }
  }
  @container main (max-width: #{$tablet-width}) {
    & {
      grid-template-columns: repeat(2, 1fr);
      grid-gap: 20px;
    }
  }
  & .genre-card {
    width: 100%;
    height: auto;
    aspect-ratio: 1/1;
    cursor: pointer;
    background-color: var(--background-glass-color-primary);
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    display: flex;
    justify-content: center;
    align-items: center;
    &:hover {
      transform: scale(1.01);
      transition: 0.2s;
    }
    &__name {
      padding: 10px;
      font-size: 24px;
      font-weight: 600;
      text-align: center;
      color: var(--text-primary-color);
      @container main (max-width: #{$mobile-width}) {
        & {
          padding: 5px;
          font-size: 18px;
        }
      }
    }
  }
}
</style>
