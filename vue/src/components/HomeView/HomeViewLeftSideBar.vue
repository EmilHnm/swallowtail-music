<template>
  <nav class="nav" :class="{ active: isActive }">
    <div class="nav__navigation">
      <router-link :to="{ name: 'mainPage' }">
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
      <router-link
        :to="{ name: 'uploadPage' }"
        v-if="user.email_verified_at !== null"
      >
        <base-list-item>
          <IconUpload />
          <div class="nav__navigation--title">Upload</div>
        </base-list-item>
      </router-link>
      <router-link :to="{ name: 'playlistCreatePage' }">
        <base-list-item>
          <IconPlus />
          <div class="nav__navigation--title">New Playlist</div>
        </base-list-item>
      </router-link>
      <router-link :to="{ name: 'collectionPage' }">
        <base-list-item>
          <IconHeartFilled />
          <div class="nav__navigation--title">Liked Songs</div>
        </base-list-item>
      </router-link>
    </div>
    <div class="nav__playlist">
      <router-link
        v-for="playlist in userPlaylist"
        :key="playlist.playlist_id"
        :to="{
          name: 'playlistViewPage',
          params: { id: playlist.playlist_id ? playlist.playlist_id : '0' },
        }"
      >
        <base-list-item>
          <div class="nav__navigation--title">
            {{ playlist.title ? playlist.title : "default" }}
          </div>
        </base-list-item>
      </router-link>
    </div>
  </nav>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconHome from "@/components/icons/IconHome.vue";
import IconSearch from "@/components/icons/IconSearch.vue";
import IconLibrary from "@/components/icons/IconLibrary.vue";
import IconPlus from "@/components/icons/IconPlus.vue";
import IconHeartFilled from "@/components/icons/IconHeartFilled.vue";
import IconUpload from "@/components/icons/IconUpload.vue";
import type { playlist } from "@/model/playlistModel";
import { mapGetters } from "vuex";

type playlistData = playlist & {
  songCount: number;
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    userPlaylist: playlistData[];
  }
}

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
  inject: {
    userPlaylist: {
      from: "userPlaylist",
      default: [] as playlistData[],
    },
  },
  data() {
    return {};
  },
  methods: {},
  computed: {
    ...mapGetters({
      user: "auth/userData",
    }),
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
