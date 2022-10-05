<template>
  <div class="search-result__container">
    <div class="search-result__container--album">
      <BaseCardAlbum
        v-for="item in testArr"
        :key="item"
        :title="'Future Parade'"
        :uploader="'Emil'"
        :songCount="6"
      />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseCardAlbum from "../UI/BaseCardAlbum.vue";

export default defineComponent({
  inject: ["testArr"],
  components: { BaseCardAlbum },
});
</script>

<style lang="scss" scoped>
.search-result__container {
  width: 100%;
  height: 100%;
  .search-result__container--album {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }
}
</style>
