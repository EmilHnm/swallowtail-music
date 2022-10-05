<template>
  <div class="search-result__container">
    <div class="search-result__container--artist">
      <BaseCardArtist
        v-for="item in testArr"
        :key="item"
        :title="'虹ヶ咲学園スクールアイドル同好会'"
      />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseCardArtist from "../UI/BaseCardArtist.vue";

export default defineComponent({
  inject: ["testArr"],
  components: { BaseCardArtist },
});
</script>

<style lang="scss" scoped>
.search-result__container {
  width: 100%;
  height: 100%;
  .search-result__container--artist {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }
}
</style>
