<template>
  <teleport to="body"
    ><BaseDialog
      :open="isLoading"
      :title="'Loading ...'"
      :mode="'announcement'"
    >
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseDialog>
  </teleport>
  <div class="user-header">
    <div class="header__background"></div>
    <div class="header__image">
      <img
        :src="
          user.profile_photo_url
            ? `${environment.profile_image}/${user.profile_photo_url}`
            : `${environment.default}/default-avatar.jpg`
        "
        alt=""
        srcset=""
      />
    </div>
    <div class="info">
      <div class="info__type">Profile</div>
      <div class="info__title">{{ user.name }}</div>
      <div class="info__other">
        <div class="info__other--playlistCount">
          {{ user.publicPlaylist_count }} public playlists
        </div>
        <div class="info__other--songUploaded">
          - {{ user.songUploaded_count }} Songs -
        </div>
        <div class="info__other--albumUploaded">
          {{ user.albumUploaded_count }} Album
        </div>
      </div>
    </div>
  </div>
  <div class="control" v-if="userData.user_id == user.user_id">
    <BaseButton>Edit Profile</BaseButton>
  </div>
  <div class="detail" ref="detail">
    <div class="topArtist">
      <h2>Top artists</h2>
      <BaseHorizontalScroll>
        <BaseCardArtist
          v-for="artist in topArtist"
          :key="artist.artist_id"
          :data="artist"
        />
      </BaseHorizontalScroll>
    </div>
    <div class="topTracks">
      <h2>Top tracks</h2>
      <span>This only visible to you</span>
      <BaseSongItem
        v-for="song in topTracks"
        :key="song[0].song_id"
        :data="song"
      />
    </div>
    <div class="publicPlaylist">
      <h2>Public Playlist</h2>
      <BaseHorizontalScroll>
        <BaseCardAlbum
          v-for="playlist in userPlaylist"
          :key="playlist.playlist_id"
          :title="playlist.title"
          :id="playlist.playlist_id"
          :img="
            playlist.image_path
              ? `${environment.playlist_cover}/${playlist.image_path}`
              : `${environment.default}/no_image.jpg`
          "
          :type="'playlist'"
          :songCount="playlist.songCount"
        />
      </BaseHorizontalScroll>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseButton from "../../components/UI/BaseButton.vue";
import BaseHorizontalScroll from "../../components/UI/BaseHorizontalScroll.vue";
import BaseCardArtist from "../../components/UI/BaseCardArtist.vue";
import BaseSongItem from "../../components/UI/BaseSongItem.vue";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";
import { mapActions, mapGetters } from "vuex";
import type { user } from "@/model/userModel";
import type { artist } from "@/model/artistModel";
import { environment } from "@/environment/environment";
import type { playlist } from "@/model/playlistModel";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";

type userProfileData = user & {
  publicPlaylist_count: number;
  songUploaded_count: number;
  albumUploaded_count: number;
};

type playlistData = playlist & {
  songCount: number;
};

type songData = {
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
    liked: number;
  }[];
};

export default defineComponent({
  setup() {},
  components: {
    BaseButton,
    BaseHorizontalScroll,
    BaseCardArtist,
    BaseSongItem,
    BaseCardAlbum,
    BaseDialog,
    BaseLineLoad,
  },
  data() {
    return {
      environment: environment,
      isMenuOpen: false,
      isLoading: true,
      filterText: "",
      detailWidth: 0,
      observer: null as ResizeObserver | null,
      small: false,
      medium: false,
      user: {} as userProfileData,
      topArtist: [] as artist[],
      topTracks: {} as songData,
      playlists: [] as playlistData[],
    };
  },
  methods: {
    ...mapActions("user", ["getUser"]),
    ...mapActions("user", [
      "getTopArtists",
      "getPublicPlaylist",
      "getTopTracks",
    ]),
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    onLoadUser(user_id: string) {
      this.getUser({
        token: this.token,
        user_id: user_id,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.user = res.user;
          } else {
            this.$router.push({ name: "mainPage" });
          }
        });
    },
    onLoadTopArtist(user_id: string) {
      this.getTopArtists({
        token: this.token,
        user_id: user_id,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.topArtist = res.artists;
          } else {
            this.$router.push({ name: "mainPage" });
          }
        });
    },
    onGetPublicPlaylist(user_id: string) {
      this.getPublicPlaylist({
        token: this.token,
        user_id: user_id,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.playlists = res.playlists;
          } else {
            this.$router.push({ name: "mainPage" });
          }
        });
    },
    onGetTopTracks(user_id: string) {
      this.getTopTracks({
        token: this.token,
        user_id: user_id,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.topTracks = res.songs.reduce((r: any, a: any) => {
              r[a.song_id] = r[a.song_id] || [];
              r[a.song_id].push(a);
              return r;
            }, Object.create(null));
          } else {
            this.$router.push({ name: "mainPage" });
          }
        });
    },
  },
  mounted() {
    const detail = this.$refs.detail as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.detailWidth = entry.contentRect.width;
      }
    });
    if (this.observer && detail) this.observer.observe(detail);
  },
  watch: {
    detailWidth(o) {
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
    "$route.params.id": {
      immediate: true,
      deep: true,
      handler() {
        if (this.$route.name === "profilePage") {
          this.onLoadUser(this.$route.params.id as string);
          this.onLoadTopArtist(this.$route.params.id as string);
          this.onGetTopTracks(this.$route.params.id as string);
        }
      },
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      userData: "auth/userData",
    }),
  },
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
$mobile-width: 480px;
$tablet-width: 768px;
.user-header {
  display: flex;
  align-items: center;
  padding: 40px 20px;
  position: relative;
  & .header__background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(60deg, var(--color-primary), transparent);
    z-index: -1;
    filter: blur(6px);
  }
  & .header__image {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    border-radius: 50%;
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

    &__type {
      font-size: 14px;
      color: #fff;
      font-weight: 700;
    }
    &__title {
      font-size: 42px;
      color: #fff;
      font-weight: 900;
    }
    &__other {
      display: flex;
      color: #fff;
      font-size: 16px;
      &--playlistCount {
        font-weight: 700;
      }
      &--songUploaded {
        margin: 0 10px;
        font-weight: 400;
      }
      &--albumUploaded {
        margin: 0 10px;
        font-weight: 400;
      }
    }
  }
}
.control {
  height: 100px;
  width: 20%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}
.detail {
  width: 100%;
  padding: 20px;
}

@media (max-width: $mobile-width) {
  .user-header {
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
</style>
