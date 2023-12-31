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
        v-lazyload
        :data-url="
          user.profile_photo_url
            ? `${environment.profile_image}/${user.profile_photo_url}`
            : `${environment.default}/default-avatar.jpg`
        "
        alt="avatar"
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
      <h2>Top Artists</h2>
      <swiper
        v-if="topArtist.length > 0"
        :slides-per-view="itemPerSlide"
        :space-between="10"
        navigation
      >
        <swiper-slide v-for="artist in topArtist">
          <BaseCardArtist :data="artist" @playArtistSong="playArtistSong" />
        </swiper-slide>
      </swiper>
    </div>
    <div class="topTracks" v-if="topTracks.length > 0">
      <h2>Top Uploaded Tracks</h2>
      <BaseSongItem
        v-for="song in topTracks"
        :key="song.song_id"
        :data="song"
        @select-song="playSong(song)"
        @addToQueue="addToQueue(song)"
      />
    </div>
    <div class="publicPlaylist" v-if="userPlaylist.length > 0">
      <h2>
        {{
          $route.params.id === userData.user_id
            ? "Your Playlist"
            : "Public Playlist"
        }}
      </h2>
      <swiper
        v-if="userPlaylist.length > 0"
        :slides-per-view="itemPerSlide"
        :space-between="10"
        navigation
      >
        <swiper-slide v-for="playlist in userPlaylist">
          <BaseCardAlbum
            :key="playlist.playlist_id"
            :title="playlist.title"
            :id="playlist.playlist_id"
            :img="
              playlist.image_path
                ? `${environment.playlist_cover}/${playlist.image_path}`
                : `${environment.default}/no_image.jpg`
            "
            :type="'playlist'"
            :songCount="playlist.song_count"
            @play-playlist="playPlaylist(playlist)"
          />
        </swiper-slide>
      </swiper>
    </div>
  </div>
</template>
<script lang="ts">
import "swiper/css";
import "swiper/css/navigation";
import { Swiper, SwiperSlide } from "swiper/vue";
import { defineComponent } from "vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import BaseCardArtist from "@/components/UI/BaseCardArtist.vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import { mapActions, mapGetters } from "vuex";
import type { user } from "@/model/userModel";
import type { artist } from "@/model/artistModel";
import { environment } from "@/environment/environment";
import type { playlist } from "@/model/playlistModel";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { like } from "@/model/likeModel";
import globalEmitListener from "@/shared/globalEmitListener";

type userProfileData = user & {
  publicPlaylist_count: number;
  songUploaded_count: number;
  albumUploaded_count: number;
};

type playlistData = playlist & {
  song_count: number;
};

type songData = song & {
  artist: artist[];
  album: album;
  like: like[];
};

export default defineComponent({
  components: {
    BaseButton,
    BaseCardArtist,
    BaseSongItem,
    BaseCardAlbum,
    BaseDialog,
    BaseLineLoad,
    Swiper,
    SwiperSlide,
  },
  data() {
    return {
      environment: environment,
      isMenuOpen: false,
      isLoading: true,
      filterText: "",
      observer: null as ResizeObserver | null,
      itemPerSlide: 6,
      user: {} as userProfileData,
      topArtist: [] as artist[],
      topTracks: {} as songData[],
      userPlaylist: [] as playlistData[],
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
            this.userPlaylist = res.playlists;
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
            this.topTracks = res.songs;
          } else {
            this.$router.push({ name: "mainPage" });
          }
        });
    },
    playArtistSong(artist: artist) {
      this.$emit("playArtistSong", artist);
    },
    playSong(song: song) {
      this.$emit("playSong", song.song_id);
    },
    addToQueue(song: any) {
      this.$emit("addToQueue", song);
    },
    playPlaylist(playlist: playlist) {
      this.$emit("playPlaylist", playlist.playlist_id);
    },
  },
  mounted() {
    const detail = this.$refs.detail as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.itemPerSlide = Math.floor(entry.contentRect.width / 280);
      }
    });
    if (this.observer && detail) this.observer.observe(detail);
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      deep: true,
      handler() {
        if (this.$route.name === "profilePage") {
          this.onLoadUser(this.$route.params.id as string);
          this.onLoadTopArtist(this.$route.params.id as string);
          this.onGetTopTracks(this.$route.params.id as string);
          if (this.userData.user_id === this.$route.params.id) {
            this.userPlaylist = this.playlists;
          } else {
            this.onGetPublicPlaylist(this.$route.params.id as string);
          }
        }
      },
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      userData: "auth/userData",
      playlists: "playlist/getPlaylists",
    }),
  },
  emits: [...globalEmitListener],
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
  @container main (max-width: #{$tablet-width}) {
    flex-direction: column;
    jusify-content: center;
  }
  & .header__background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(60deg, var(--color-primary), transparent);
    @container main (max-width: #{$tablet-width}) {
      background: linear-gradient(180deg, var(--color-primary), transparent);
    }
    z-index: -1;
    filter: blur(6px);
  }
  & .header__image {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    border-radius: 50%;
    @container main (max-width: #{$tablet-width}) {
      width: 150px;
      height: 150px;
    }
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
      @container main (max-width: #{$tablet-width}) {
        padding-top: 10px;
        text-align: center;
      }
    }
    &__title {
      font-size: 42px;
      color: #fff;
      font-weight: 900;
      @container main (max-width: #{$tablet-width}) {
        text-align: center;
        font-size: 32px;
      }
    }
    &__other {
      display: flex;
      color: #fff;
      font-size: 16px;
      @container main (max-width: #{$tablet-width}) {
        justify-content: center;
      }
      @container main (max-width: #{$mobile-width}) {
        flex-direction: column;
        align-items: center;
      }
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
  @container main (max-width: #{$tablet-width}) {
    width: 100%;
  }
}
.detail {
  width: 100%;
  padding: 20px 0px;
  h2 {
    padding: 0 20px;
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
