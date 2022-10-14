<template>
  <div class="artist-header">
    <div class="header__background"></div>
    <div class="header__image">
      <img src="../../../assets/music/cover.jpg" alt="" srcset="" />
    </div>
    <div class="info">
      <div class="info__type">Artist</div>
      <div class="info__title">虹ヶ咲学園スクールアイドル同好会</div>
      <div class="info__other">
        <div class="info__other--monthlyListeners">4,000 monthly listeners</div>
        <div class="info__other--albumCount">- 1 Album</div>
      </div>
    </div>
  </div>
  <div class="control">
    <div class="control__left">
      <div class="control__left--play"><IconPlay /></div>
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
    <RouterView />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseListItem from "../../../components/UI/BaseListItem.vue";
import IconHorizontalThreeDot from "../../../components/icons/IconHorizontalThreeDot.vue";
import IconPlay from "../../../components/icons/IconPlay.vue";

export default defineComponent({
  setup() {
    return {};
  },
  data() {
    return {
      isMenuOpen: false,
      isSongListOpen: false,
    };
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    toggleTopSong() {
      this.isSongListOpen = !this.isSongListOpen;
    },
  },
  components: {
    BaseListItem,
    IconHorizontalThreeDot,
    IconPlay,
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
