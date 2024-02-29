<template>
  <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
    <template #default>
      <BaseLineLoad />
    </template>
    <template #action><div></div></template>
  </BaseDialog>
  <div class="artist-album" ref="artist-album">
    <div class="">
      <div class="artist-album__year">
        <div
          v-for="year in Object.keys(albums)"
          class="artist-album__year--item"
          :class="{ active: activeYear === +year }"
          @click="setActiveYear(year)"
        >
          {{ year }}
        </div>
      </div>
    </div>
    <div class="artist-album__list">
      <template v-if="activeYear !== 0">
        <h3>{{ activeYear }}</h3>
        <div
          v-for="album in albums[` ${activeYear}`]"
          :key="album.album_id"
          class="artist-album__list--item"
        >
          <BaseAlbum :data="album" />
        </div>
      </template>
    </div>
  </div>
</template>

<script lang="ts">
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import type { album } from "@/model/albumModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseAlbum from "@/components/UI/BaseAlbum.vue";
import { _function } from "@/mixins";

type albumData = album & {
  song_count: number;
};

type groupedAlbum = {
  [key: number]: albumData[];
};

export default defineComponent({
  emits: [
    "playAlbum",
    "addAlbumToQueue",
    "playSongInAlbum",
    "playSongOfArtist",
  ],
  data() {
    return {
      isMenuOpen: false,
      albums: {} as groupedAlbum,
      isLoading: true,
      activeYear: 0,
    };
  },
  methods: {
    ...mapActions("artist", ["getArtistAlbumById"]),
    setActiveYear(year: string) {
      this.activeYear = +year;
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.getArtistAlbumById({
      token: this.token,
      artist_id: this.$route.params.id,
    })
      .then((res) => {
        return res.json();
      })
      .then((res) => {
        this.isLoading = false;
        if (res.status === "success") {
          this.albums = _function.sortObject(
            Object.groupBy(
              res.albums,
              (item: { release_year: string }) => item.release_year
            ),
            (a, b) => b - a
          );
          this.activeYear = +Object.keys(this.albums)[0];
        }
      });
  },
  components: { BaseAlbum, BaseDialog, BaseLineLoad },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
.artist-album {
  display: flex;
  flex-direction: row;
  width: 100%;
  @container main (max-width: #{$mobile-width}) {
    & {
      flex-direction: column;
    }
  }
  &__year {
    padding: 20px;
    position: sticky;
    top: 0;
    right: 0;
    box-sizing: border-box;
    @container main (max-width: #{$mobile-width}) {
      display: flex;
      width: 100%;
      overflow: scroll;
    }
    &--item {
      border-right: 1px solid var(--text-primary-color);
      color: var(--text-primary-color);
      padding: 0 10px;
      font-size: 1.2rem;
      font-weight: 600;
      cursor: pointer;
      &:hover {
        color: var(--color-primary);
        border-right: 1px solid var(--color-primary);
      }
      &.active {
        color: var(--color-primary);
        border-right: 1px solid var(--color-primary);
      }
    }
  }
  &__list {
    flex: 1;
    width: 80%;
    padding: 0 20px;
    box-sizing: border-box;
    @container main (max-width: #{$mobile-width}) {
      width: 100%;
    }
    h3 {
      margin-top: 0;
      margin-bottom: 0;
      color: var(--text-primary-color);
      font-size: 1.6rem;
      font-weight: 600;
      background: var(--background-blur-color-primary);
      display: block;
      padding: 10px 20px;
      border-radius: 8px;
      text-align: center;
      position: sticky;
      top: 5px;
    }
    & .album-container {
      margin: auto !important;
    }
  }
}
</style>
