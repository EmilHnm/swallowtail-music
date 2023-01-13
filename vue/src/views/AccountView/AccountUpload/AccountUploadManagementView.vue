<template>
  <div class="container">
    <h2>Upload Management</h2>
    <div class="navigation">
      <router-link :to="{ name: 'accountUploadSong' }">Songs</router-link>
      <router-link :to="{ name: 'accountUploadAlbum' }">Albums</router-link>
    </div>
    <RouterView />
  </div>
</template>

<script lang="ts">
export default {
  data() {
    return {};
  },
};
</script>

<style lang="scss" scoped>
.container {
  width: 90%;
  max-height: 100%;
  display: flex;
  flex-direction: column;
  overflow: scroll;
  padding: 20px;
  h2 {
    margin-bottom: 20px;
    font-weight: 900;
    font-size: 2rem;
  }
  .navigation {
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    a {
      height: 40px;
      width: 40%;
      border: 1px solid var(--color-primary);
      color: var(--color-primary);
      background: transparent;
      padding: 0 20px;
      font-size: 1rem;
      cursor: pointer;
      text-align: center;
      line-height: 40px;
      transition: all 0.3s ease-in-out;
      &:focus {
        outline: none;
      }
      &:hover,
      &.active {
        color: var(--background-color-secondary);
        background: var(--color-primary);
      }
      &.router-link-active {
        font-weight: 900;
        color: var(--background-color-secondary);
        background: var(--color-primary);
      }
    }
  }
}
</style>
