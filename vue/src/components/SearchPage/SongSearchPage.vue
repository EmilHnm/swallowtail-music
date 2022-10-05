<template>
  <BaseSongItem
    v-for="(item, index) in testArr"
    :key="item"
    :title="'Future Parade'"
    :artist="'虹ヶ咲学園スクールアイドル同好会'"
    :album="'Future Parade'"
    :duration="'4:36'"
    :hears="1000000"
  />
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseSongItem from "../UI/BaseSongItem.vue";

export default defineComponent({
  setup() {},
  inject: ["testArr"],
  components: { BaseSongItem },
});
</script>
