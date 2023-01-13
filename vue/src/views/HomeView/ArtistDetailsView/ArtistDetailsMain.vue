<template>
  <div class="main">
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
        <span @click="toggleTopSong">{{
          isSongListOpen ? "Show Less" : "Show More"
        }}</span>
      </div>
      <div class="songs" v-else>
        <BaseCircleLoadVue />
      </div>
    </div>
    <div class="album">
      <h2>Album</h2>
      <BaseHorizontalScroll>
        <BaseCardAlbum
          v-for="album in albums"
          :key="album.album_id"
          :id="album.album_id"
          :songCount="album.song_count"
          :title="album.name"
          :img="`${environment.album_cover}/${album.image_path}`"
          type="album"
        />
      </BaseHorizontalScroll>
      <span @click="redirectToAlbum">Show All</span>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseHorizontalScroll from "@/components/UI/BaseHorizontalScroll.vue";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import BaseCircleLoadVue from "@/components/UI/BaseCircleLoad.vue";
import type { album } from "@/model/albumModel";
import type { song } from "@/model/songModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";

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
    };
  },
  methods: {
    ...mapActions("artist", ["getArtistTopSongById", "getArtistAlbumById"]),
    toggleTopSong() {
      this.isSongListOpen = !this.isSongListOpen;
    },
    redirectToAlbum() {
      this.$router.push({ name: "artistAlbumsPage" });
    },
    playSongOfArtist(song_id: string) {
      this.$emit("playSongOfArtist", song_id);
    },
  },
  components: {
    BaseHorizontalScroll,
    BaseCardAlbum,
    BaseSongItem,
    BaseCircleLoadVue,
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
