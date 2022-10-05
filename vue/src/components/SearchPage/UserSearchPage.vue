<template>
  <div class="search-result__container">
    <div class="search-result__container--user">
      <BaseCardUser
        v-for="item in testArr"
        :key="item"
        :userName="'Emil'"
        :songCount="6"
      />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseCardUser from "../UI/BaseCardUser.vue";

export default defineComponent({
  inject: ["testArr"],
  components: { BaseCardUser },
});
</script>

<style lang="scss" scoped>
.search-result__container {
  width: 100%;
  height: 100%;
  .search-result__container--user {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }
}
</style>
