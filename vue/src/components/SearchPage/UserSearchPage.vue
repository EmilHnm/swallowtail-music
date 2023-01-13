<template>
  <div class="search-result__container">
    <div class="search-result__container--user">
      <BaseCardUser
        v-for="user in userResult"
        :key="user.user_id"
        :id="user.user_id"
        :userName="user.name"
        :imageSrc="user.profile_photo_url"
        :songCount="user.song_count"
      />
    </div>
  </div>
</template>

<script lang="ts">
import type { user } from "@/model/userModel";
import { defineComponent } from "vue";
import BaseCardUser from "../UI/BaseCardUser.vue";

type userData = user & { song_count: number };

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    userResult: userData[];
  }
}

export default defineComponent({
  inject: ["userResult"],
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
