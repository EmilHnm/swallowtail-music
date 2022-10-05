<template>
  <nav class="nav" :class="{ active: isActive }">
    <div class="nav__navigation">
      <router-link :to="{ name: 'home' }">
        <base-list-item>
          <IconHome />
          <div class="nav__navigation--title">Home</div>
        </base-list-item>
      </router-link>
      <router-link :to="{ name: 'searchPage' }">
        <base-list-item>
          <IconSearch />
          <div class="nav__navigation--title">Search</div>
        </base-list-item>
      </router-link>
      <router-link :to="{ name: 'libraryPage' }">
        <base-list-item>
          <IconLibrary />
          <div class="nav__navigation--title">Library</div>
        </base-list-item>
      </router-link>
    </div>
    <div class="nav__navigation">
      <router-link :to="{ name: 'uploadPage' }">
        <base-list-item>
          <IconUpload />
          <div class="nav__navigation--title">Upload</div>
        </base-list-item>
      </router-link>
      <router-link :to="{ name: 'home' }">
        <base-list-item>
          <IconPlus />
          <div class="nav__navigation--title">New Playlist</div>
        </base-list-item>
      </router-link>
      <router-link :to="{ name: 'home' }">
        <base-list-item>
          <IconHeartFilled />
          <div class="nav__navigation--title">Liked Songs</div>
        </base-list-item>
      </router-link>
    </div>
    <div class="nav__playlist">
      <router-link :to="{ name: 'home' }">
        <base-list-item>
          <div class="nav__navigation--title">BangDream</div>
        </base-list-item>
      </router-link>
    </div>
  </nav>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconHome from "../icons/IconHome.vue";
import IconSearch from "../icons/IconSearch.vue";
import IconLibrary from "../icons/IconLibrary.vue";
import IconPlus from "../icons/IconPlus.vue";
import IconHeartFilled from "../icons/IconHeartFilled.vue";
import IconUpload from "../icons/IconUpload.vue";
export default defineComponent({
  components: {
    IconHome,
    IconSearch,
    IconLibrary,
    IconPlus,
    IconHeartFilled,
    IconUpload,
  },
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
  },
});
</script>
<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.nav.active {
  width: 30%;
  max-width: 350px;
  display: flex;
  flex-direction: column;
  .nav__navigation {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
    & a {
      text-decoration: none;
      display: block;
      width: 100%;
    }
    & svg {
      width: 22px;
      height: 22px;
    }
    &--title {
      margin-left: 10px;
      font-weight: bolder;
      display: block;
    }
    & > :nth-child(n) {
      margin: 5px 0px;
      user-select: none;
      cursor: pointer;
    }
  }
  .nav__playlist {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
    border-top: 1px solid var(--text-subdued);
    padding-top: 20px;
    & a {
      text-decoration: none;
      display: block;
      width: 100%;
    }
    & svg {
      width: 22px;
      height: 22px;
    }
    &--title {
      margin-left: 10px;
      font-weight: bolder;
    }
    & > :nth-child(n) {
      margin: 5px 0px;
      user-select: none;
      cursor: pointer;
    }
  }
}
.nav {
  width: 100px;
  background: var(--background-color-secondary);
  padding: 10px 0px;
  height: 100%;
  overflow: auto;

  transition: 0.5s;
  .nav__navigation {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
    text-align: center & a {
      text-decoration: none;
      display: block;
      width: 100%;
    }
    & svg {
      width: 22px;
      height: 22px;
    }
    &--title {
      margin-left: 10px;
      font-weight: bolder;
      display: none;
    }
    & > :nth-child(n) {
      margin: 5px 0px;
      user-select: none;
      cursor: pointer;
    }
  }
  &__playlist {
    display: none;
  }
}
@media (max-width: $tablet-width) {
  .nav {
    width: 0px;
    position: absolute;
    z-index: 100;
  }
  .nav.active {
    width: 100%;
    max-width: 500px;
  }
}
</style>
