<template>
  <div class="wrapper">
    <div class="news" ref="news">
      <div
        class="latest-songs"
        v-if="Array.from(latestSongs).length > 0 || !latestSongs"
      >
        <h1>Latest Songs Update</h1>
        <BaseCircleLoad v-if="!latestSongs" />
        <div class="latest-song__container" v-else>
          <base-list-item v-for="(song, index) in latestSongs" :key="index">
            <div class="latest-song__song">
              <div class="latest-song__song--title">
                <h2 @click="playSong(song.song_id)">
                  <BaseTooltipVue
                    :position="'bottom'"
                    :tooltip-text="song.title"
                  >
                    {{ song.title }}
                  </BaseTooltipVue>
                </h2>
                <p>
                  <BaseTooltipVue
                    :position="'bottom'"
                    :tooltip-text="song.artist.map((a) => a.name).join(', ')"
                  >
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
                  </BaseTooltipVue>
                </p>
              </div>
              <div class="latest-song__song--duration">
                {{
                  Math.floor(song.file.duration / 60) +
                  ":" +
                  (Math.floor(song.file.duration % 60) < 10
                    ? "0" + Math.floor(song.file.duration % 60)
                    : Math.floor(song.file.duration % 60))
                }}
              </div>
              <div class="latest-song__song--add-queue">
                <IconPlay @click="playSong(song.song_id)" />
              </div>
            </div>
          </base-list-item>
        </div>
      </div>
      <div class="latest-album">
        <h2>Latest Album Update</h2>
        <swiper
          v-if="latestAlbums.length > 0"
          :slides-per-view="itemPerSlide"
          :space-between="10"
          navigation
        >
          <swiper-slide v-for="album in latestAlbums">
            <BaseCardAlbum
              :key="album.album_id"
              :title="album.name"
              :uploader="album.user?.name ?? 'Unknown'"
              :id="album.album_id"
              :img="`${environment.album_cover}/${album.image_path}`"
              :type="'album'"
              :songCount="album.song_count"
              @playAlbum="playAlbum"
            />
          </swiper-slide>
        </swiper>
        <swiper
          v-else
          :slides-per-view="itemPerSlide"
          :space-between="10"
          navigation
        >
          <swiper-slide v-for="i in 8">
            <BaseSkeletonsLoadingCard />
          </swiper-slide>
        </swiper>
      </div>
    </div>
    <div class="top">
      <div class="top__album">
        <h2>Top Albums</h2>
        <swiper
          v-if="topAlbums.length > 0"
          :slides-per-view="itemPerSlide"
          :space-between="10"
          navigation
        >
          <swiper-slide v-for="album in topAlbums">
            <BaseCardAlbum
              :key="album.album_id"
              :title="album.name"
              :id="album.album_id"
              :uploader="album.user?.name || 'Unknown'"
              :img="`${environment.album_cover}/${album.image_path}`"
              :type="'album'"
              :listens="+album.song_sum_listens"
              @playAlbum="playAlbum"
            />
          </swiper-slide>
        </swiper>
        <swiper
          v-else
          :slides-per-view="itemPerSlide"
          :space-between="10"
          navigation
        >
          <swiper-slide v-for="i in 8">
            <BaseSkeletonsLoadingCard />
          </swiper-slide>
        </swiper>
      </div>
      <div class="top__artist">
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
        <swiper
          v-else
          :slides-per-view="itemPerSlide"
          :space-between="10"
          navigation
        >
          <swiper-slide v-for="i in 8">
            <BaseSkeletonsLoadingCard />
          </swiper-slide>
        </swiper>
      </div>
    </div>
    <div class="favorite">
      <div class="favorite__playlist" v-if="playlists.length > 0">
        <h2>Your Favorite List</h2>
        <div class="favorite__playlist--container">
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
import "swiper/css";
import "swiper/css/navigation";
import { Swiper, SwiperSlide } from "swiper/vue";

import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import { useMeta } from "vue-meta";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { song } from "@/model/songModel";
import type { user } from "@/model/userModel";
import IconPlay from "@/components/icons/IconPlay.vue";
import BaseSkeletonsLoadingCard from "@/components/UI/BaseSkeletonsLoadingCard.vue";
import BaseCardArtist from "@/components/UI/BaseCardArtist.vue";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import globalEmitListener from "@/shared/constants/globalEmitListener";
import BaseTooltipVue from "@/components/UI/BaseTooltip.vue";

type LatestSong = song & {
  artist: artist[];
  file: {
    song_id: string;
    duration: number;
  };
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
    BaseCardArtist,
    BaseCardAlbum,
    BaseCircleLoad,
    BaseSkeletonsLoadingCard,
    Swiper,
    SwiperSlide,
    BaseTooltipVue,
  },
  emits: [...globalEmitListener],
  data() {
    return {
      environment: environment,
      latestSongs: [] as LatestSong[],
      latestAlbums: [] as LatestAlbum[],
      topAlbums: [] as TopAlbum[],
      topArtist: [] as artist[],
      //loading

      itemPerSlide: 6,
      containerWidthObserver: null as ResizeObserver | null,
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
      this.getLatestSong(this.token).then((res) => {
        this.latestSongs = res.songs;
      });
    },
    onLoadLatestAlbum() {
      this.getLatestAlbums(this.token).then((res) => {
        this.latestAlbums = [...res.albums];
      });
    },
    onLoadTopAlbum() {
      this.getTopAlbums(this.token).then((res) => {
        this.topAlbums = [...res.albums];
      });
    },
    onLoadTopArtist() {
      this.getTopArtists(this.token).then((res) => {
        this.topArtist = [...res.artists];
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
    useMeta({
      title: environment.site_name,
    });
    const container: HTMLDivElement = this.$refs.news as HTMLDivElement;
    this.containerWidthObserver = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.itemPerSlide = Math.floor(entry.contentRect.width / 280);
      }
    });
    if (container) this.containerWidthObserver.observe(container);
  },
});
</script>
<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
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
        @container main (max-width: #{$tablet-width}) {
          & {
            width: 35%;
          }
        }
        @container main (max-width: #{$mobile-width}) {
          & {
            width: 90%;
          }
        }
        &:hover .latest-song__song--add-queue {
          opacity: 1;
        }
        &:hover .latest-song__song--duration {
          opacity: 0;
        }
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
        @container main (max-width: #{$tablet-width}) {
          & {
            width: 35%;
          }
        }
        @container main (max-width: #{$mobile-width}) {
          & {
            width: 90%;
          }
        }
        &:hover .favorite__playlist__item--play {
          opacity: 1;
        }
        &:hover .favorite__playlist__item--songCount {
          opacity: 0;
        }
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
