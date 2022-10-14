<template>
  <div class="popular">
    <h2>Popular</h2>
    <BaseSongItem
      v-for="index in isSongListOpen ? 10 : 5"
      :key="index"
      :title="'Future Parade'"
      :artist="'虹ヶ咲学園スクールアイドル同好会'"
      :album="'Future Parade'"
      :duration="'4:36'"
      :hears="1000000"
    />
    <span @click="toggleTopSong">{{
      isSongListOpen ? "Show Less" : "Show More"
    }}</span>
  </div>
  <div class="album">
    <h2>Album</h2>
    <BaseHorizontalScroll>
      <BaseCardAlbum
        v-for="index in 6"
        :key="index"
        :songCount="4"
        :title="'Future Parade'"
      />
    </BaseHorizontalScroll>
    <span @click="redirectToAlbum">Show All</span>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseHorizontalScroll from "../../../components/UI/BaseHorizontalScroll.vue";
import BaseCardAlbum from "../../../components/UI/BaseCardAlbum.vue";
import BaseSongItem from "../../../components/UI/BaseSongItem.vue";
export default defineComponent({
  data() {
    return {
      isSongListOpen: false,
    };
  },
  methods: {
    toggleTopSong() {
      this.isSongListOpen = !this.isSongListOpen;
    },
    redirectToAlbum() {
      this.$router.push({ name: "artistAlbumsPage" });
    },
  },
  components: {
    BaseHorizontalScroll,
    BaseCardAlbum,
    BaseSongItem,
  },
  created() {
    console.log(this.$route.params);
  },
});
</script>
