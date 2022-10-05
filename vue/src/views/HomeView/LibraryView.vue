<template>
  <div class="container">
    <BaseCardAlbum
      v-for="item in testArr"
      :key="item"
      :title="'Future Parade'"
      :uploader="'Emil'"
      :songCount="6"
    />
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";

export default defineComponent({
  data() {
    return { testArr: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10] };
  },
  components: { BaseCardAlbum },
});
</script>
<style lang="scss" scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  flex-wrap: wrap;
}
</style>
