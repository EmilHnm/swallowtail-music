<template>
  <div class="wrapper">
    <div class="news" ref="news">
      <div
        class="latest-songs"
        v-if="Array.from(latestSongs).length > 0 || !latestSongs"
      >
        <h1>Latest Songs Update</h1>
        <BaseCircleLoad v-if="isLatestSongLoading" />
        <div
          class="latest-song__container"
          :class="{ min: min, medium: medium }"
          v-else
        >
          <base-list-item v-for="(song, index) in latestSongs" :key="index">
            <div class="latest-song__song">
              <div class="latest-song__song--title">
                <h2>{{ song.title }}</h2>
                <p>
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
                </p>
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
        <h1 v-if="!isLatestAlbumLoading || latestAlbums.length > 0">
          Latest Album Update
        </h1>
        <BaseHorizontalScroll v-if="isLatestAlbumLoading">
          <BaseSkeletonsLoadingCard v-for="index in 8" :key="index"
        /></BaseHorizontalScroll>
        <BaseHorizontalScroll v-else>
          <BaseCardAlbum
            v-for="album in latestAlbums"
            :key="album.album_id"
            :title="album.name"
            :uploader="album.user?.name ?? 'Unknown'"
            :id="album.album_id"
            :img="`${environment.album_cover}/${album.image_path}`"
            :type="'album'"
            :songCount="album.song_count"
            @playAlbum="playAlbum"
        /></BaseHorizontalScroll>
      </div>
    </div>
    <div class="top">
      <div class="top__album" v-if="!topArtist || topArtist.length != 0">
        <h1>Top Albums</h1>
        <BaseHorizontalScroll v-if="isTopAlbumLoading">
          <BaseSkeletonsLoadingCard v-for="index in 8" :key="index"
        /></BaseHorizontalScroll>
        <BaseHorizontalScroll v-else>
          <BaseCardAlbum
            v-for="album in topAlbums"
            :key="album.album_id"
            :title="album.name"
            :uploader="album.user?.name || 'Unknown'"
            :id="album.album_id"
            :img="`${environment.album_cover}/${album.image_path}`"
            :type="'album'"
            :listens="+album.song_sum_listens"
            @playAlbum="playAlbum"
          />
        </BaseHorizontalScroll>
      </div>
      <div class="top__artist">
        <h1 v-if="!topArtist || topArtist.length != 0">Top Artists</h1>
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
      <div class="favorite__playlist" v-if="playlists.length > 0">
        <h1>Your Favorite List</h1>
        <div
          class="favorite__playlist--container"
          :class="{ min: min, medium: medium }"
        >
          <base-list-item
            v-for="playlist in playlists"
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
                {{ playlist.song_count ?? 0 }} songs
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
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import { useMeta } from "vue-meta";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { playlist } from "@/model/playlistModel";
import type { song } from "@/model/songModel";
import type { user } from "@/model/userModel";
import IconPlay from "@/components/icons/IconPlay.vue";
import BaseHorizontalScroll from "@/components/UI/BaseHorizontalScroll.vue";
import BaseSkeletonsLoadingCard from "@/components/UI/BaseSkeletonsLoadingCard.vue";
import BaseCardArtist from "@/components/UI/BaseCardArtist.vue";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";

type LatestSong = song & {
  artist: artist[];
};

type LatestAlbum = album & {
  song_count: number;
  user: user;
};
type TopAlbum = album & {
  user: user;
  song_sum_listens: number;
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
  emits: [
    "updatePlaylist",
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
      this.isLatestSongLoading = true;
      this.getLatestSong(this.token).then((res) => {
        this.isLatestSongLoading = false;
        this.latestSongs = res.songs;
      });
    },
    onLoadLatestAlbum() {
      this.isLatestAlbumLoading = true;
      this.getLatestAlbums(this.token).then((res) => {
        this.latestAlbums = [...res.albums];
        this.isLatestAlbumLoading = false;
      });
    },
    onLoadTopAlbum() {
      this.isTopAlbumLoading = true;
      this.getTopAlbums(this.token).then((res) => {
        this.topAlbums = [...res.albums];
        this.isTopAlbumLoading = false;
      });
    },
    onLoadTopArtist() {
      this.getTopArtists(this.token).then((res) => {
        this.topArtist = [...res.artists];
        this.isTopArtistLoading = false;
      });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      cached: "cache/data",
      playlists: "playlist/getPlaylists",
    }),
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
    useMeta({
      title: environment.site_name,
    });
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
  h1 {
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
        & > h2 {
          width: 100%;
          font-size: 1.2rem;
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
          margin: 2px 0;
          user-select: none;
        }
        & > p {
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
  padding-bottom: 20px;
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
