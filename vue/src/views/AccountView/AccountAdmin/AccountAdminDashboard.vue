<template>
  <div class="container">
    <button @click="$router.push({ name: 'accountAdminUser' })">
      <div class="icon"><IconAvatarProfile /></div>
      <div class="title">Users</div>
    </button>
    <button @click="$router.push({ name: 'accountAdminDisc' })">
      <div class="icon"><IconDisk /></div>
      <div class="title">Song</div>
    </button>
    <button @click="$router.push({ name: 'accountAdminArtists' })">
      <div class="icon"><IconArtist /></div>
      <div class="title">Artist</div>
    </button>
    <button @click="$router.push({ name: 'accountAdminGenres' })">
      <div class="icon"><IconTag /></div>
      <div class="title">Song Genre</div>
    </button>
  </div>
</template>

<script lang="ts">
import IconAvatarProfile from "@/components/icons/IconAvatarProfile.vue";
import IconDisk from "@/components/icons/IconDisk.vue";
import IconArtist from "@/components/icons/IconArtist.vue";
import IconTag from "@/components/icons/IconTag.vue";
import { defineComponent } from "vue";

export default defineComponent({
  setup() {
    return {};
  },
  components: { IconAvatarProfile, IconDisk, IconArtist, IconTag },
});
</script>

<style lang="scss" scoped>
.container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-around;
  height: 100%;
  width: 100%;
  button {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 40%;
    height: auto;
    min-height: 200px;
    border: 2px solid var(--color-primary);
    background: none;
    cursor: pointer;
    &:hover {
      transition: all 0.2s;
      transform: scale(1.05);
    }
    .icon {
      width: 40px;
      height: 40px;
      svg {
        fill: var(--color-primary);
        stroke: var(--color-primary);
        width: 100%;
        height: 100%;
      }
    }
    .title {
      font-size: 1.3rem;
      font-weight: 500;
      margin-top: 1rem;
      color: var(--text-primary-color);
    }
  }
}
</style>
