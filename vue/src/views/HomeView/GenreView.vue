<template>
  <BaseDialog
    :open="!genre?.name"
    :title="'Loading ...'"
    :mode="'announcement'"
  >
    <template #default>
      <BaseLineLoad />
    </template>
    <template #action><div></div></template>
  </BaseDialog>
  <div ref="genreBody">
    <div class="genre-header" v-if="genre">
      <h1 class="genre-header__title">{{ genre.name }}</h1>
      <p
        class="genre-header__description"
        :class="showMore ? 'show-more' : ''"
        @click="showMore = !showMore"
      >
        {{ genre.description }}
      </p>
    </div>
    <div class="genre-body" v-if="genre">
      <div class="genre-body__top-song">
        <h2>Top Song</h2>
        <div class="" v-if="topSongs.length > 0">
          <BaseSongItem
            v-for="song in topSongs"
            :key="song.id"
            :data="song"
            @selectSong="playSong(song.song_id)"
            @addToQueue="addToQueue(song)"
          >
          </BaseSongItem>
        </div>
        <BaseCircleLoad v-else />
      </div>
      <div class="genre-body__top-artist" v-if="topArtists.length > 0">
        <h2>Top Artist</h2>
        <swiper
          v-if="topArtists.length > 0"
          :slides-per-view="itemPerSlide"
          :space-between="10"
          navigation
        >
          <swiper-slide v-for="artist in topArtists">
            <BaseCardArtistVue
              :data="artist"
              @playArtistSong="playArtistSong"
            />
          </swiper-slide>
        </swiper>
        <swiper
          v-else
          :slides-per-view="itemPerSlide"
          :space-between="10"
          navigation
        >
          <swiper-slide v-for="i in 10">
            <BaseSkeletonsLoadingCard />
          </swiper-slide>
        </swiper>
      </div>
      <div class="genre-body__top-album">
        <h2>Top Album</h2>
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
          <swiper-slide v-for="i in 10">
            <BaseSkeletonsLoadingCard />
          </swiper-slide>
        </swiper>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import "swiper/css";
import "swiper/css/navigation";
import { Swiper, SwiperSlide } from "swiper/vue";

import type { genre } from "@/model/genreModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import globalEmitListener from "@/shared/constants/globalEmitListener";
import type { songData } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import BaseCardArtistVue from "@/components/UI/BaseCardArtist.vue";
import BaseSkeletonsLoadingCard from "@/components/UI/BaseSkeletonsLoadingCard.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import { environment } from "@/environment/environment";

type TopAlbum = album & {
  song_sum_listens: number;
};

export default defineComponent({
  data() {
    return {
      genre: null as genre | null,
      topSongs: [] as songData[],
      topAlbums: [] as TopAlbum[],
      topArtists: [] as artist[],
      itemPerSlide: 6,
      containerWidthObserver: null as ResizeObserver | null,
      showMore: false,
      environment,
    };
  },
  methods: {
    ...mapActions({
      getGenre: "genre/fetchGenre",
      getTopSongs: "genre/getTopSongs",
      getTopArtists: "genre/getTopArtists",
      getTopAlbums: "genre/getTopAlbums",
    }),
    playArtistSong(artist_id: string) {
      this.$emit("playArtistSong", artist_id);
    },
    playSong(song_id: string) {
      this.$emit("playSong", song_id);
    },
    addToQueue(song: any) {
      this.$emit("addToQueue", song);
    },
    playAlbum(album_id: string) {
      this.$emit("playAlbum", album_id);
    },
  },
  mounted() {
    const container: HTMLDivElement = this.$refs.genreBody as HTMLDivElement;
    this.containerWidthObserver = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.itemPerSlide = Math.floor(entry.contentRect.width / 280);
      }
    });
    if (container) this.containerWidthObserver.observe(container);
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
    id() {
      return this.$route.params.id;
    },
  },
  watch: {
    id: {
      immediate: true,
      async handler() {
        this.genre = await this.getGenre({ id: this.id, token: this.token });
        if (!this.genre?.id) {
          this.$router.push({ name: "notFound" });
        }
        this.getTopSongs({ id: this.id, token: this.token }).then(
          (res: songData[]) => (this.topSongs = res)
        );
        this.getTopArtists({ id: this.id, token: this.token }).then(
          (res: artist[]) => (this.topArtists = res)
        );
        this.getTopAlbums({ id: this.id, token: this.token }).then(
          (res: TopAlbum[]) => (this.topAlbums = res)
        );
      },
    },
  },
  components: {
    BaseDialog,
    BaseLineLoad,
    Swiper,
    SwiperSlide,
    BaseSongItem,
    BaseCardArtistVue,
    BaseSkeletonsLoadingCard,
    BaseCircleLoad,
    BaseCardAlbum,
  },
  emits: [...globalEmitListener],
});
</script>

<style lang="scss" scoped>
.genre-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  margin-bottom: 20px;
  background: linear-gradient(
    180deg,
    var(--background-color-primary) 0%,
    rgba(255, 255, 255, 0) 100%
  );
  &__title {
    font-size: 36px;
    font-weight: 700;
  }
  &__description {
    font-size: 18px;
    font-weight: 400;
    max-width: 1024px;
    cursor: pointer;
    line-clamp: 3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    &.show-more {
      display: block;
    }
  }
}
.genre-body {
  padding: 20px;
  .swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
  }
}
</style>
