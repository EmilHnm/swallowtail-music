<template>
  <div class="disc-container">
    <div class="disc-container__header">
      <div class="disc-container__header__title">
        <h3>Discs Uploaded Managenment</h3>
      </div>
      <div class="disc-container__header__button">
        <router-link :to="{ name: 'accountAdminDiscSong' }">Songs</router-link>
        <router-link :to="{ name: 'accountAdminDiscAlbum' }"
          >Albums</router-link
        >
      </div>
    </div>
    <div class="disc-container__body">
      <RouterView />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  data() {
    return {};
  },
});
</script>

<style lang="scss" scoped>
.disc-container {
  width: 100%;
  height: calc(100% - 110px);
  display: flex;
  flex-direction: column;
  overflow: scroll;
  padding-bottom: 20px;
  &__header {
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    &__title {
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      h3 {
        font-size: 1.5rem;
        font-weight: 600;
      }
    }
    &__button {
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: space-around;
      align-items: center;
      a {
        width: 40%;
        height: 50px;
        text-align: center;
        line-height: 50px;
        border: none;
        outline: none;
        background: var(--color-primary-blur);
        color: var(--text-primary-color);
        font-size: 1.2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        &:hover {
          background: var(--color-primary);
        }
        &.router-link-active {
          background: var(--color-primary);
          color: var(--background-color-secondary);
        }
      }
    }
  }
  &__body {
    width: 100%;
  }
}
</style>
