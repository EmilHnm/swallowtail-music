<template>
  <div class="search-result__container" ref="container">
    <div class="search-result__container--song" v-if="songResult.length > 0">
      <h3>Song</h3>
      <div v-for="index in 5" :key="index">
        <BaseSongItem
          v-if="index <= songResult.length"
          :data="songResult[index - 1]"
          @select-song="playSong"
        />
      </div>
      <span @click="changeSearchPage('SongSearchPage')">See more</span>
    </div>
    <div class="search-result__container--album" v-if="albumResult.length > 0">
      <h3>Album</h3>
      <swiper :slides-per-view="itemPerSlide" :space-between="10" navigation>
        <swiper-slide v-for="index in 5" :key="index">
          <BaseCardAlbum
            v-if="index <= albumResult.length"
            :img="`${environment.album_cover}/${
              albumResult[index - 1].image_path
            }`"
            :id="albumResult[index - 1].album_id"
            :title="albumResult[index - 1].name"
            :songCount="albumResult[index - 1].song_count"
            @playAlbum="playAlbum"
          />
        </swiper-slide>
      </swiper>
      <span @click="changeSearchPage('AlbumSearchPage')">See more</span>
    </div>
    <div
      class="search-result__container--artist"
      v-if="artistResult.length > 0"
    >
      <h3>Artist</h3>
      <swiper :slides-per-view="itemPerSlide" :space-between="10" navigation>
        <swiper-slide v-for="index in 5" :key="index">
          <BaseCardArtist
            v-if="index <= artistResult.length"
            :key="artistResult[index - 1].artist_id"
            :data="artistResult[index - 1]"
            @playArtistSong="playArtistSong"
          />
        </swiper-slide>
      </swiper>
      <span @click="changeSearchPage('ArtistSearchPage')">See more</span>
    </div>
    <div class="search-result__container--user" v-if="userResult.length > 0">
      <h3>User</h3>
      <swiper :slides-per-view="itemPerSlide" :space-between="10" navigation>
        <swiper-slide v-for="index in 5" :key="index">
          <BaseCardUser
            v-if="index <= userResult.length"
            :id="userResult[index - 1].user_id"
            :userName="userResult[index - 1].name"
            :imageSrc="userResult[index - 1].profile_photo_url"
            :songCount="userResult[index - 1].song_count"
          />
        </swiper-slide>
      </swiper>
      <span @click="changeSearchPage('UserSearchPage')">See more</span>
    </div>
  </div>
</template>

<script lang="ts">
import "swiper/css";
import "swiper/css/navigation";
import { Swiper, SwiperSlide } from "swiper/vue";

import { defineComponent } from "vue";
import BaseCardArtist from "@/components/UI/BaseCardArtist.vue";
import BaseCardAlbum from "@/components/UI/BaseCardAlbum.vue";
import BaseCardUser from "@/components/UI/BaseCardUser.vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { user } from "@/model/userModel";
import { environment } from "@/environment/environment";
import type { song } from "@/model/songModel";
import type { like } from "@/model/likeModel";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

type albumData = album & { song_count: number };

type userData = user & { song_count: number };

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    songResult: songData[];
    albumResult: albumData[];
    artistResult: artist[];
    userResult: userData[];
  }
}

export default defineComponent({
  data() {
    return {
      environment: environment,
      itemPerSlide: 6,
      containerWidthObserver: null as ResizeObserver | null,
    };
  },
  methods: {
    changeSearchPage(componentName: string) {
      this.$emit("changeSearchPage", componentName);
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
  inject: ["songResult", "albumResult", "artistResult", "userResult"],
  components: {
    BaseCardArtist,
    BaseCardAlbum,
    BaseCardUser,
    BaseSongItem,
    Swiper,
    SwiperSlide,
  },
  mounted() {
    const container: HTMLDivElement = this.$refs.container as HTMLDivElement;
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
h3 {
  color: var(--color-primary);
  font-size: 1.5rem;
}
span {
  color: var(--color-primary);
  font-size: 1.2rem;
  cursor: pointer;
  user-select: none;
  &:hover {
    text-decoration: underline;
  }
}
</style>
