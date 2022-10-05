<template>
  <div class="search-result__container">
    <div class="search-result__container--song">
      <h3>Song</h3>
      <BaseSongItem
        v-for="item in testArr"
        :key="item"
        :title="'Future Parade'"
        :artist="'虹ヶ咲学園スクールアイドル同好会'"
        :album="'Future Parade'"
        :duration="'4:36'"
        :hears="1000000"
      />
    </div>
    <div class="search-result__container--album">
      <h3>Album</h3>
      <BaseHorizontalScroll>
        <BaseCardAlbum
          v-for="item in testArr"
          :key="item"
          :title="'Future Parade'"
          :uploader="'Emil'"
          :songCount="6"
        />
      </BaseHorizontalScroll>
      <span>See more</span>
    </div>
    <div class="search-result__container--artist">
      <h3>Artist</h3>
      <BaseHorizontalScroll>
        <BaseCardArtist
          v-for="item in testArr"
          :key="item"
          :title="'虹ヶ咲学園スクールアイドル同好会'"
        />
      </BaseHorizontalScroll>
      <span>See more</span>
    </div>
    <div class="search-result__container--user">
      <h3>User</h3>
      <BaseHorizontalScroll>
        <BaseCardUser
          v-for="item in testArr"
          :key="item"
          :userName="'Emil'"
          :songCount="6"
        />
      </BaseHorizontalScroll>
      <span>See more</span>
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
export default defineComponent({
  setup() {
    return {};
  },
  inject: ["testArr"],
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
