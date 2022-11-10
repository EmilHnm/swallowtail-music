<template>
  <div class="search-result__container">
    <div
      class="search-result__container--song"
      v-if="Object.keys(songResult).length > 0"
    >
      <h3>Song</h3>
      <div v-for="index in 5" :key="index">
        <BaseSongItem
          v-if="index <= Object.keys(songResult).length"
          :data="Object.values(songResult)[index - 1]"
        />
      </div>
      <span @click="changeSearchPage('SongSearchPage')">See more</span>
    </div>
    <div class="search-result__container--album" v-if="albumResult.length > 0">
      <h3>Album</h3>
      <BaseHorizontalScroll>
        <div v-for="index in 5" :key="index">
          <BaseCardAlbum
            v-if="index <= albumResult.length"
            :img="`${environment.album_cover}/${
              albumResult[index - 1].image_path
            }`"
            :id="albumResult[index - 1].album_id"
            :title="albumResult[index - 1].name"
            :songCount="albumResult[index - 1].song_count"
          />
        </div>
      </BaseHorizontalScroll>
      <span @click="changeSearchPage('AlbumSearchPage')">See more</span>
    </div>
    <div
      class="search-result__container--artist"
      v-if="artistResult.length > 0"
    >
      <h3>Artist</h3>
      <BaseHorizontalScroll>
        <div v-for="index in 5" :key="index">
          <BaseCardArtist
            v-if="index <= artistResult.length"
            :key="artistResult[index - 1].artist_id"
            :data="artistResult[index - 1]"
          />
        </div>
      </BaseHorizontalScroll>
      <span @click="changeSearchPage('ArtistSearchPage')">See more</span>
    </div>
    <div class="search-result__container--user" v-if="userResult.length > 0">
      <h3>User</h3>
      <BaseHorizontalScroll>
        <div v-for="index in 5" :key="index">
          <BaseCardUser
            v-if="index <= userResult.length"
            :id="userResult[index - 1].user_id"
            :userName="userResult[index - 1].name"
            :imageSrc="userResult[index - 1].profile_photo_url"
            :songCount="userResult[index - 1].song_count"
          />
        </div>
      </BaseHorizontalScroll>
      <span @click="changeSearchPage('UserSearchPage')">See more</span>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseHorizontalScroll from "../../components/UI/BaseHorizontalScroll.vue";
import BaseCardArtist from "../../components/UI/BaseCardArtist.vue";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";
import BaseCardUser from "../../components/UI/BaseCardUser.vue";
import BaseSongItem from "../UI/BaseSongItem.vue";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { user } from "@/model/userModel";
import { environment } from "@/environment/environment";

type songPlaylist = {
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
  }[];
};

type albumData = album & { song_count: number };

type userData = user & { song_count: number };

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    songResult: songPlaylist;
    albumResult: albumData[];
    artistResult: artist[];
    userResult: userData[];
  }
}

export default defineComponent({
  data() {
    return {
      environment: environment,
    };
  },
  methods: {
    changeSearchPage(componentName: string) {
      this.$emit("changeSearchPage", componentName);
    },
  },
  inject: ["songResult", "albumResult", "artistResult", "userResult"],
  components: {
    BaseHorizontalScroll,
    BaseCardArtist,
    BaseCardAlbum,
    BaseCardUser,
    BaseSongItem,
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
