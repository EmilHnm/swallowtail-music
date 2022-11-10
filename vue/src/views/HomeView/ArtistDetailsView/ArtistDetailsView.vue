<template>
  <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
    <template #default>
      <BaseLineLoad />
    </template>
    <template #action><div></div></template>
  </BaseDialog>
  <div class="artist-header">
    <div class="header__background"></div>
    <div class="header__image">
      <img
        :src="
          artist.image_path
            ? `${environment.artist_image}/${artist.image_path}`
            : `${environment.default}/no_image.jpg`
        "
        alt=""
        srcset=""
      />
    </div>
    <div class="info">
      <div class="info__type">Artist</div>
      <div class="info__title">{{ artist.name }}</div>
      <div class="info__other">
        <div class="info__other--monthlyListeners">
          {{ artist.total_listens }} listeners
        </div>
        <div class="info__other--albumCount">
          - {{ artist.total_album }} Album(s)
        </div>
      </div>
    </div>
  </div>
  <div class="control">
    <div class="control__left">
      <div class="control__left--play">
        <IconPlay @click="playArtistSong" />
      </div>
      <div class="control__left--menu">
        <IconHorizontalThreeDot @click="toggleMenu" />
        <teleport to="body">
          <div
            class="bg"
            :class="{ active: isMenuOpen }"
            @click="toggleMenu"
          ></div>
        </teleport>
        <transition name="playlist-menu">
          <div class="playlist-menu" v-if="isMenuOpen">
            <BaseListItem>Add to Queue</BaseListItem>
            <BaseListItem>Report</BaseListItem>
          </div>
        </transition>
      </div>
    </div>
  </div>
  <div class="artist-details">
    <RouterView
      @playAlbum="playAlbum"
      @addAlbumToQueue="addAlbumToQueue"
      @playSongInAlbum="playSongInAlbum"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import type { artist } from "@/model/artistModel";
import { environment } from "@/environment/environment";
import BaseListItem from "../../../components/UI/BaseListItem.vue";
import IconHorizontalThreeDot from "../../../components/icons/IconHorizontalThreeDot.vue";
import IconPlay from "../../../components/icons/IconPlay.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";

type artistData = artist & {
  total_listens: number;
  total_album: number;
};

export default defineComponent({
  emits: [
    "updatePlaylist",
    "deletePlaylist",
    "playPlaylist",
    "playAlbum",
    "addAlbumToQueue",
    "playSongInAlbum",
    "playArtistSong",
  ],
  data() {
    return {
      environment: environment,
      isMenuOpen: false,
      isSongListOpen: false,
      artist: {} as artistData,
      isLoading: true,
    };
  },
  methods: {
    ...mapActions("artist", ["getArtist"]),
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    toggleTopSong() {
      this.isSongListOpen = !this.isSongListOpen;
    },
    playAlbum(id: string) {
      this.$emit("playAlbum", id);
    },
    addAlbumToQueue(id: string) {
      this.$emit("addAlbumToQueue", id);
    },
    playSongInAlbum(id: string) {
      this.$emit("playSongInAlbum", id);
    },
    playArtistSong() {
      this.$emit("playArtistSong", this.artist.artist_id);
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      deep: true,
      handler() {
        this.getArtist({
          token: this.token,
          artist_id: this.$route.params.id,
        })
          .then((res: any) => {
            return res.json();
          })
          .then((res) => {
            this.isLoading = false;
            if (res.status === "success") {
              this.artist = res.artist;
            }
          });
      },
    },
  },

  components: {
    BaseListItem,
    IconHorizontalThreeDot,
    IconPlay,
    BaseDialog,
    BaseLineLoad,
  },
});
</script>

<style lang="scss" scoped>
.artist-header {
  display: flex;
  align-items: center;
  padding: 40px 20px;
  position: relative;
  & .header__background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(60deg, var(--color-primary), transparent);
    z-index: -1;
    filter: blur(6px);
  }
  & .header__image {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    border-radius: 50%;
    & img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0 50%);
    }
  }
  .info {
    width: 100%;
    height: 100%;
    padding: 0 20px;
    display: flex;
    flex-direction: column;

    &__type {
      font-size: 14px;
      color: #fff;
      font-weight: 700;
    }
    &__title {
      font-size: 42px;
      color: #fff;
      font-weight: 900;
    }
    &__other {
      display: flex;
      color: #fff;
      font-size: 16px;
      &--monthlyListeners {
        font-weight: 700;
      }
      &--songUploaded {
        margin: 0 10px;
        font-weight: 400;
      }
      &--albumCount {
        margin: 0 10px;
        font-weight: 400;
      }
    }
  }
}
.control {
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  &__left {
    display: flex;
    height: 100%;
    align-items: center;
    justify-content: center;
    &--play {
      aspect-ratio: 1/1;
      height: 50px;
      width: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      cursor: pointer;
      background: var(--color-primary);
      transition: all 0.2s ease-in-out;
      & svg {
        width: 25px;
        height: 25px;
        fill: #fff;
      }
      &:hover {
        transform: scale(1.1);
      }
    }
    &--menu {
      aspect-ratio: 1/1;
      height: 50px;
      width: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      margin: 0 10px;
      & svg {
        width: 25px;
        height: 25px;
        fill: var(--text-subdued);
      }
      &:hover svg {
        fill: var(--text-primary-color);
      }
      .playlist-menu {
        position: absolute;
        display: block;
        top: 105%;
        left: 0px;
        width: 250px;
        border-radius: 10px;
        z-index: 20;
        padding: 10px 0px;
        background: var(--background-glass-color-primary);
        & > * {
          margin: 5px auto;
        }
      }
    }
  }
}
.playlist-menu-enter-from {
  opacity: 0;
  top: 55%;
}
.playlist-menu-enter-to {
  opacity: 1;
  top: 105%;
}
.playlist-menu-enter-active {
  transition: all 0.5s;
}
.playlist-menu-leave-from {
  opacity: 1;
  top: 105%;
}
.playlist-menu-leave-to {
  opacity: 0;
  top: 55%;
}
.playlist-menu-leave-active {
  transition: all 0.5s;
}
</style>
