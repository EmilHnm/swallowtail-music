<template>
  <div class="wrapper">
    <div class="news" ref="news">
      <div class="latest-songs">
        <h2>Latest Songs Update</h2>
        <BaseCircleLoad v-if="isLatestSongLoading" />
        <div
          class="latest-song__container"
          :class="{ min: min, medium: medium }"
          v-else
        >
          <base-list-item v-for="(song, index) in latestSongs" :key="index">
            <div class="latest-song__song">
              <div class="latest-song__song--title">
                <h3>{{ song.title }}</h3>
                <h4>
                  <router-link
                    v-for="(artist, i) in song.artist"
                    :key="artist.artist_id"
                    :to="{
                      name: 'artistPage',
                      params: { id: artist.artist_id },
                    }"
                  >
                    {{ artist.name
                    }}<span v-if="i != song.artist.length - 1">, </span>
                  </router-link>
                </h4>
              </div>
              <div class="latest-song__song--duration">
                {{
                  Math.floor(song.duration / 60) +
                  ":" +
                  (Math.floor(song.duration % 60) < 10
                    ? "0" + Math.floor(song.duration % 60)
                    : Math.floor(song.duration % 60))
                }}
              </div>
              <div class="latest-song__song--add-queue">
                <IconPlay @click="playSong(song.song_id)" />
              </div>
            </div>
          </base-list-item>
        </div>
      </div>
      <div
        class="latest-album"
        v-if="!isLatestAlbumLoading && latestAlbums.length > 0"
      >
        <h2>Latest Album Update</h2>
        <BaseHorizontalScroll v-if="isLatestAlbumLoading">
          <BaseSkeletonsLoadingCard v-for="index in 8" :key="index"
        /></BaseHorizontalScroll>
        <BaseHorizontalScroll v-else>
          <BaseCardAlbum
            v-for="album in latestAlbums"
            :key="album.album_id"
            :title="album.name"
            :uploader="album.user_name"
            :id="album.album_id"
            :img="`${environment.album_cover}/${album.image_path}`"
            :type="'album'"
            :songCount="album.songCount"
            @playAlbum="playAlbum"
        /></BaseHorizontalScroll>
      </div>
    </div>
    <div class="top">
      <div
        class="top__album"
        v-if="!isTopAlbumLoading && topAlbums.length != 0"
      >
        <h2>Top Album All Time</h2>
        <BaseHorizontalScroll v-if="isTopAlbumLoading">
          <BaseSkeletonsLoadingCard v-for="index in 8" :key="index"
        /></BaseHorizontalScroll>
        <BaseHorizontalScroll v-else>
          <BaseCardAlbum
            v-for="album in topAlbums"
            :key="album.album_id"
            :title="album.name"
            :uploader="album.user_name"
            :id="album.album_id"
            :img="`${environment.album_cover}/${album.image_path}`"
            :type="'album'"
            :listens="+album.totalListen"
            @playAlbum="playAlbum"
          />
        </BaseHorizontalScroll>
      </div>
      <div class="top__artist">
        <h2>Top Artist All Time</h2>
        <BaseHorizontalScroll v-if="isTopArtistLoading">
          <BaseSkeletonsLoadingCard v-for="index in 8" :key="index"
        /></BaseHorizontalScroll>
        <BaseHorizontalScroll>
          <BaseCardArtist
            v-for="artist in topArtist"
            :key="artist.artist_id"
            :data="artist"
            @playArtistSong="playArtistSong"
          />
        </BaseHorizontalScroll>
      </div>
    </div>
    <div class="favorite">
      <div class="favorite__playlist" v-if="userPlaylist.length > 0">
        <h2>Your Favorite List</h2>
        <div
          class="favorite__playlist--container"
          :class="{ min: min, medium: medium }"
        >
          <base-list-item
            v-for="playlist in userPlaylist"
            :key="playlist.playlist_id"
          >
            <div class="favorite__playlist__item">
              <div
                class="favorite__playlist__item--title"
                @click="redirectToPlaylist(playlist.playlist_id)"
              >
                {{ playlist.title }}
              </div>
              <div class="favorite__playlist__item--songCount">
                {{ playlist.songCount }} songs
              </div>
              <div class="favorite__playlist__item--play">
                <IconPlay @click="playPlaylist(playlist.playlist_id)" />
              </div>
            </div>
          </base-list-item>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "../../components/icons/IconPlay.vue";
import BaseHorizontalScroll from "../../components/UI/BaseHorizontalScroll.vue";
import BaseSkeletonsLoadingCard from "../../components/UI/BaseSkeletonsLoadingCard.vue";
import BaseCardArtist from "../../components/UI/BaseCardArtist.vue";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { playlist } from "@/model/playlistModel";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import type { song } from "@/model/songModel";

type LatestSong = song & {
  artist: artist[];
};

type LatestAlbum = album & {
  user_name: string;
  songCount: number;
};
type TopAlbum = album & {
  user_name: string;
  totalListen: number;
};

type playlistData = playlist & {
  songCount: number;
};

export default defineComponent({
  components: {
    IconPlay,
    BaseHorizontalScroll,
    BaseCardArtist,
    BaseCardAlbum,
    BaseCircleLoad,
    BaseSkeletonsLoadingCard,
  },
  inject: {
    userPlaylist: {
      from: "userPlaylist",
      default: [] as playlistData[],
    },
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
  data() {
    return {
      news: 0,
      observer: null as ResizeObserver | null,
      environment: environment,
      min: false,
      medium: false,
      testArr: [1, 2, 3, 4, 5, 6, 7, 8],
      latestSongs: [] as LatestSong[],
      latestAlbums: [] as LatestAlbum[],
      topAlbums: [] as TopAlbum[],
      topArtist: [] as artist[],
      //loading
      isLatestSongLoading: true,
      isLatestAlbumLoading: true,
      isTopAlbumLoading: true,
      isTopArtistLoading: true,
    };
  },
  methods: {
    ...mapActions("song", ["getLatestSong"]),
    ...mapActions("artist", ["getTopArtists"]),
    ...mapActions("album", ["getLatestAlbums", "getTopAlbums"]),
    redirectToPlaylist(id: string) {
      this.$router.push({ name: "playlistViewPage", params: { id: id } });
    },
    playPlaylist(id: string) {
      this.$emit("playPlaylist", id);
    },
    playArtistSong(artist_id: string) {
      this.$emit("playArtistSong", artist_id);
    },
    playAlbum(album_id: string) {
      this.$emit("playAlbum", album_id);
    },
    playSong(song_id: string) {
      this.$emit("playSong", song_id);
    },
    onLoadLatestSong() {
      this.getLatestSong(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.isLatestSongLoading = false;
          this.latestSongs = res.songs;
        });
    },
    onLoadLatestAlbum() {
      this.getLatestAlbums(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.latestAlbums = [...res.albums];
          this.isLatestAlbumLoading = false;
        });
    },
    onLoadTopAlbum() {
      this.getTopAlbums(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.topAlbums = [...res.albums];
          this.isTopAlbumLoading = false;
        });
    },
    onLoadTopArtist() {
      this.getTopArtists(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.topArtist = [...res.artists];
          this.isTopArtistLoading = false;
        });
    },
  },
  computed: {
    ...mapGetters({ token: "auth/userToken" }),
  },
  created() {
    this.onLoadLatestSong();
    this.onLoadLatestAlbum();
    this.onLoadTopAlbum();
    this.onLoadTopArtist();
  },
  mounted() {
    const newsEle = this.$refs.news as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.news = entry.contentRect.width;
      }
    });
    this.observer.observe(newsEle);
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
  watch: {
    news(o: number) {
      if (550 < o && o <= 900) {
        this.medium = true;
        this.min = false;
      } else if (o <= 550) {
        this.min = true;
        this.medium = false;
      } else {
        this.min = false;
        this.medium = false;
      }
    },
  },
});
</script>
<style lang="scss" scoped>
.wrapper {
  width: 100%;
  height: 100%;
  overflow: auto;
  overflow-x: hidden;
  padding-bottom: 40px;
  h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--color-primary);
    user-select: none;
  }
}
.news {
  width: 100%;
  padding: 0 10px;
  & .latest-songs {
    width: 100%;
    & .latest-song__container {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      & > * {
        cursor: pointer;
        width: 25%;
        &:hover .latest-song__song--add-queue {
          opacity: 1;
        }
        &:hover .latest-song__song--duration {
          opacity: 0;
        }
      }
    }
    & .latest-song__container.min {
      & > * {
        width: 90%;
      }
    }
    & .latest-song__container.medium {
      & > * {
        width: 35%;
      }
    }
    & .latest-song__song {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      width: 100%;
      &--title {
        font-size: 1.2rem;
        width: 100%;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        & > h3 {
          width: 100%;
          font-size: 1.2rem;
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
          margin: 2px 0;
          user-select: none;
        }
        & > h4 {
          width: 100%;
          font-size: 0.8rem;
          white-space: nowrap;
          text-overflow: ellipsis;
          color: var(--text-subdued);
          user-select: none;
          margin: 0;
          a {
            color: var(--text-subdued);
            &:hover {
              color: var(--color-primary);
            }
          }
          &:hover {
            text-decoration: underline;
          }
        }
      }
      &--duration {
        font-size: 1rem;
        font-weight: 500;
        flex: 0 0 auto;
      }

      &--add-queue {
        position: absolute;
        top: 50%;
        right: 0;
        transform: translateY(-50%);
        width: 25px;
        height: 25px;
        background: var(--color-primary);
        border-radius: 50%;
        padding: 3px;
        color: var(--text-subdued);
        opacity: 0;
        transition: 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        & svg {
          width: 70%;
          height: 70%;
          fill: #fff;
        }
      }
    }
  }
  & .latest-album {
    width: 100%;
  }
}

.top {
  padding: 0 10px;
  width: 100%;
}
.favorite {
  padding: 0 10px;
  width: 100%;
  & .favorite__playlist {
    width: 100%;
    & .favorite__playlist--container {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      & > * {
        cursor: pointer;
        width: 30%;
        &:hover .favorite__playlist__item--play {
          opacity: 1;
        }
        &:hover .favorite__playlist__item--songCount {
          opacity: 0;
        }
      }
    }
    & .favorite__playlist--container.min {
      & > * {
        width: 90%;
      }
    }
    & .favorite__playlist--container.medium {
      & > * {
        width: 40%;
      }
    }
    & .favorite__playlist__item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      width: 100%;
      &--title {
        cursor: pointer;
        font-size: 1.2rem;
        font-weight: 600;
        text-overflow: ellipsis;
      }
      &--songCount {
        font-size: 1rem;
        font-weight: 500;
      }

      &--play {
        position: absolute;
        top: 50%;
        right: 0;
        transform: translateY(-50%);
        width: 25px;
        height: 25px;
        background: var(--color-primary);
        border-radius: 50%;
        padding: 3px;
        color: var(--text-subdued);
        opacity: 0;
        transition: 0.3s;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        & svg {
          width: 70%;
          height: 70%;
          fill: #fff;
        }
      }
    }
  }
}
</style>
