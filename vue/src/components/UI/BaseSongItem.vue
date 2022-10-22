<template>
  <div class="song-item" ref="songItem">
    <BaseListItem :selected="selected">
      <div class="song-item__left">
        <div class="song-item__left--image" @click="selectSong">
          <img src="../../assets/music/cover.jpg" alt="" srcset="" />
        </div>
        <div class="song-item__left--title">
          <span @click="selectSong">{{ title }}</span>
          <span>{{ artist }}</span>
        </div>
      </div>
      <div class="song-item__right">
        <div class="song-item__right--album" :class="{ small: small }">
          <span>{{ album }}</span>
        </div>
        <div
          class="song-item__right--hears"
          v-if="hears"
          :class="{ medium: medium }"
        >
          <span>{{ hears }}</span>
        </div>
        <div
          class="song-item__right--addDate"
          v-else
          :class="{ medium: medium }"
        >
          <span>{{ addedDate }}</span>
        </div>
        <div
          class="song-item__right--duration"
          :class="{ medium: medium, small: small }"
        >
          <span>{{ duration }}</span>
        </div>
        <div
          class="song-item__right--control"
          :class="{ medium: medium, small: small }"
        >
          <div class="song-item__right--control--button" @click="toggleMenu">
            <IconThreeDots />
          </div>
        </div>
      </div>
    </BaseListItem>
  </div>
  <teleport to="body">
    <div class="bg" @click="toggleMenu" v-if="isMenuOpen"></div>
    <div class="menu" v-if="isMenuOpen">
      <div class="menu__song">
        <div class="menu__song--img">
          <img src="../../assets/music/cover.jpg" alt="" srcset="" />
        </div>
        <div class="menu__song--title">
          <span>{{ title }}</span>
          <span>{{ artist }}</span>
        </div>
        <div class="menu__song--album">
          <span>{{ album }}</span>
        </div>
      </div>
      <div class="menu__btn">
        <div class="" v-if="menuMode === 'default'">
          <BaseListItem>Like</BaseListItem>
          <BaseListItem @click="changeMenuMode('playlist')"
            >Add to Playlist</BaseListItem
          >
          <BaseListItem v-if="inQueue" @click="deleteFromQueue"
            >Delete from Queue</BaseListItem
          >
          <BaseListItem v-else>Add to Queue</BaseListItem>
          <BaseListItem>View Album</BaseListItem>
          <BaseListItem @click="changeMenuMode('artist')"
            >View Artist</BaseListItem
          >
        </div>
        <div class="" v-if="menuMode === 'artist'">
          <BaseListItem>虹ヶ咲学園スクールアイドル同好会</BaseListItem>
        </div>
        <div class="" v-if="menuMode === 'playlist'">
          <BaseListItem>BangDream</BaseListItem>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseListItem from "./BaseListItem.vue";
import IconThreeDots from "../icons/IconThreeDots.vue";

export default defineComponent({
  emits: ["deleteFromQueue", "selectSong"],
  data() {
    return {
      observer: null as ResizeObserver | null,
      songItemWidth: 0,
      small: false,
      medium: false,
      isMenuOpen: false,
      menuMode: "default",
    };
  },
  props: {
    title: {
      type: String,
      required: true,
    },
    artist: {
      type: String,
      required: true,
    },
    album: {
      type: String,
      required: true,
    },
    hears: {
      type: Number,
      default: -1,
    },
    inQueue: {
      type: Boolean,
      default: false,
    },
    duration: {
      type: String,
      required: true,
    },
    addedDate: {
      type: String,
      default: "",
    },
    selected: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
      this.changeMenuMode("default");
    },
    changeMenuMode(mode: string) {
      this.menuMode = mode;
    },
    deleteFromQueue() {
      this.isMenuOpen = false;
      this.$emit("deleteFromQueue");
    },
    selectSong() {
      this.$emit("selectSong");
    },
  },
  mounted() {
    const songItem = this.$refs.songItem as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.songItemWidth = entry.contentRect.width;
      }
    });
    if (this.observer && songItem) this.observer.observe(songItem);
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
  watch: {
    songItemWidth(o) {
      if (o < 600) {
        this.small = true;
        this.medium = true;
      } else if (o < 700 && o > 600) {
        this.small = false;
        this.medium = true;
      } else {
        this.small = false;
        this.medium = false;
      }
    },
  },
  components: { BaseListItem, IconThreeDots },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: var(--background-glass-color-primary);
  z-index: 10;
}
.menu {
  position: absolute;
  display: block;
  left: 50%;
  top: 50%;
  width: 250px;
  border-radius: 10px;
  z-index: 20;
  padding: 10px 20px;
  transform: translate(-50%, -50%);
  background: var(--background-color-primary);
  user-select: none;
  &__song {
    width: 100%;
    &--img {
      aspect-ratio: 1/1;
      width: 90%;
      height: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: auto;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
      }
    }
    &--title {
      margin: 10px auto;
      span {
        display: block;
        font-size: 1rem;
        &:first-child {
          color: var(--text-color-primary);
          font-size: 1.2rem;
          font-weight: 900;
        }
        &:last-child {
          color: var(--text-color-secondary);
          font-size: 0.8rem;
          white-space: nowrap;
          text-overflow: ellipsis;
        }
      }
    }
    &--album {
      margin: 10px auto;
      span {
        display: block;
        font-size: 0.8rem;
        color: var(--text-color-secondary);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
  }
  &__btn {
    margin-top: 10px;
    width: 100%;
    & > div {
      max-height: 300px;
      overflow: auto;
      & > * {
        margin-bottom: 10px;
        cursor: pointer;
      }
    }
  }
}
@media (max-width: $mobile-width) {
  .menu {
    width: 100%;
    padding: 10px 0;
    height: 70%;
    overflow: auto;
    z-index: 300;
    bottom: 0;
    top: auto;
    left: 0;
    transform: none;
    animation: 0.3s;
    border-radius: 0;
    &__song {
      width: 100%;
      &--img {
        width: 50%;
      }
      &--title {
        & > span {
          text-align: center;
        }
      }
      &--album {
        span {
          text-align: center;
        }
      }
    }
  }
}
.song-item {
  width: 100%;
  height: max-content;
  display: flex;
  margin: 10px auto;
  cursor: pointer;
  &__left {
    width: 70%;
    display: flex;
    &--image {
      width: 50px;
      height: 50px;
      flex: 1 0 50px;
      margin-right: 10px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    &--title {
      display: flex;
      flex-direction: column;
      justify-content: center;
      overflow: hidden;
      width: 100%;
      user-select: none;
      span {
        &:first-child {
          font-size: 1rem;
          font-weight: 600;
          white-space: nowrap;
        }
        &:last-child {
          font-size: 0.8rem;
          color: var(--text-subdued);
          white-space: nowrap;
          text-overflow: ellipsis;
          &:hover {
            text-decoration: underline;
          }
        }
      }
    }
  }
  &__right {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
    &--album {
      width: 60%;
      overflow: hidden;
      text-align: center;
      &:hover {
        text-decoration: underline;
      }
      &.small {
        display: none;
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--hears {
      width: 20%;
      overflow: hidden;
      text-align: center;
      &.medium {
        display: none;
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--addDate {
      width: 20%;
      overflow: hidden;
      text-align: center;
      &.medium {
        display: none;
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--duration {
      width: 20%;
      overflow: hidden;
      text-align: center;
      &.medium {
        width: 20%;
        &.small {
          width: 80%;
        }
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--control {
      height: 30px;
      width: 30px;
      border-radius: 20%;
      position: relative;

      &:hover {
        background: var(--background-glass-color-primary);
        box-shadow: 0 0px 15px rgb(0 0 0 / 50%);
      }
      &--button {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        & svg {
          width: 20px;
          height: 20px;
          fill: var(--text-primary-color);
        }
      }
    }
  }
}
</style>
