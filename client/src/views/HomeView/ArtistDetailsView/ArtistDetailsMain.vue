<template>
  <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
    <template #default>
      <BaseLineLoad />
    </template>
    <template #action><div></div></template>
  </BaseDialog>
  <div class="main" ref="main">
    <div class="popular">
      <h2>Popular</h2>
      <div class="songs" v-if="songs.length > 0">
        <div v-for="index in isSongListOpen ? 10 : 5" :key="index">
          <BaseSongItem
            v-if="index <= songs.length"
            :data="Object.values(songs)[index - 1]"
            @select-song="playSongOfArtist(songs[index - 1].song_id)"
          />
        </div>
        <span v-if="songs.length > 5" @click="toggleTopSong">{{
          isSongListOpen ? "Show Less" : "Show More"
        }}</span>
      </div>
      <div class="songs" v-else>
        <BaseCircleLoadVue />
      </div>
    </div>
    <div class="album">
      <h2>Album</h2>
      <swiper
        v-if="albums.length > 0"
        :slides-per-view="itemPerSlide"
        :space-between="10"
        navigation
      >
        <swiper-slide v-for="index in 10">
          <BaseCardAlbum
            v-if="albums[index - 1]"
            :key="albums[index - 1].album_id"
            :id="albums[index - 1].album_id"
            :songCount="albums[index - 1].song_count"
            :title="albums[index - 1].name"
            :img="`${environment.album_cover}/${albums[index - 1].image_path}`"
            @playAlbum="onPlayAlbum"
            type="album"
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
      <span @click="redirectToAlbum">Show All</span>
    </div>
  </div>
</template>
<script lang="ts">
import "swiper/css";
import "swiper/css/navigation";
import { Swiper, SwiperSlide } from "swiper/vue";
import { defineComponent } from "vue";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import {mapActions, mapGetters, mapMutations} from "vuex";
import { environment } from "@/environment/environment";
import BaseCircleLoadVue from "@/components/UI/BaseCircleLoad.vue";
import BaseSkeletonsLoadingCard from "@/components/UI/BaseSkeletonsLoadingCard.vue";
import type { album } from "@/model/albumModel";
import type { song } from "@/model/songModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

type albumData = album & {
  song_count: number;
};

export default defineComponent({
  data() {
    return {
      environment: environment,
      isSongListOpen: false,
      songs: [] as songData[],
      albums: [] as albumData[],
      itemPerSlide: 6,
      containerWidthObserver: null as ResizeObserver | null,
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("artist", ["getArtistTopSongById", "getArtistAlbumById"]),
    ...mapMutations("queue", [
      "clearQueue",
      "setQueue",
      "setCurrentIndex",
      "setPlaying",
    ]),
    ...mapActions("queue", ["playAlbum"]),
    toggleTopSong() {
      this.isSongListOpen = !this.isSongListOpen;
    },
    redirectToAlbum() {
      this.$router.push({ name: "artistAlbumsPage" });
    },
    playSongOfArtist(song_id: string) {
      this.clearQueue();
      this.setQueue([...this.songs]);
      const index = this.songs.findIndex((song) => song.song_id === song_id);
      this.setCurrentIndex(index);
      this.setPlaying(true);
    },
    onPlayAlbum(album_id: string) {
      this.isLoading = true;
      this.playAlbum(album_id)
        .then(() => (this.isLoading = false))
        .catch(() => (this.isLoading = false));
    },
  },
  mounted() {
    const container: HTMLDivElement = this.$refs.main as HTMLDivElement;
    this.containerWidthObserver = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.itemPerSlide = Math.floor(entry.contentRect.width / 280);
      }
    });
    if (container) this.containerWidthObserver.observe(container);
  },
  components: {
    BaseDialog, BaseLineLoad,
    BaseCardAlbum,
    BaseSongItem,
    BaseCircleLoadVue,
    Swiper,
    SwiperSlide,
    BaseSkeletonsLoadingCard,
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      deep: true,
      handler() {
        if (this.$route.name === "artistOverviewPage") {
          this.getArtistTopSongById({
            token: this.token,
            artist_id: this.$route.params.id,
          })
            .then((res: any) => {
              return res.json();
            })
            .then((res) => {
              if (res.status === "success") this.songs = res.songs;
            });
          this.getArtistAlbumById({
            token: this.token,
            artist_id: this.$route.params.id,
          })
            .then((res) => {
              return res.json();
            })
            .then((res) => {
              if (res.status === "success") {
                this.albums = res.albums;
              }
            });
        }
      },
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
});
</script>
<style lang="scss">
.main {
  padding: 20px;
  span {
    cursor: pointer;
    font-weight: bolder;
  }
}
</style>
