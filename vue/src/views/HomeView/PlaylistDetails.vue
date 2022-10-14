<template>
  <div class="playlist-header">
    <div
      class="header__background"
      style="
        background: url('http://127.0.0.1:5173/src/assets/music/cover.jpg');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
      "
    ></div>
    <div class="header__image">
      <img src="../../assets/music/cover.jpg" alt="" srcset="" />
    </div>
    <div class="info">
      <div class="info__type">Public</div>
      <div class="info__title">LoveLive Song</div>
      <div class="info__description">This is Love Live Song</div>
      <div class="info__other">
        <div class="info__other--owner">Emil</div>
        <div class="info__other--songCount">- 4 Songs -</div>
        <div class="info__other--totalDuration">20:00</div>
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
            <BaseListItem>Add Song</BaseListItem>
            <BaseListItem>Add to Queue</BaseListItem>
            <BaseListItem>Add to Other Playlist</BaseListItem>
            <BaseListItem>Make Private</BaseListItem>
            <BaseListItem>Make Public</BaseListItem>
            <BaseListItem>Delete Playlist</BaseListItem>
          </div>
        </transition>
      </div>
    </div>
    <div class="control__right">
      <div class="control__right--filter">
        <button :class="{ active: isSearchBarOpen }" @click="toggleSearchBar">
          <IconSearch />
        </button>
        <transition name="search-input">
          <input
            type="text"
            placeholder="Search"
            v-model="filterText"
            v-if="isSearchBarOpen"
          />
        </transition>
      </div>
      <div class="control__right--sort">
        <select name="sort" id="">
          <option value="date">Sort by added date</option>
          <option value="title">Sort by title</option>
          <option value="artist">Sort by artist</option>
          <option value="album">Sort by album</option>
        </select>
      </div>
    </div>
  </div>
  <div class="songList" ref="songList">
    <div class="songList__header">
      <div class="songList__header--left">
        <div class="songList__header--left--img"></div>
        <div class="songList__header--left--title">Title</div>
      </div>
      <div class="songList__header--right">
        <div class="songList__header--right--album" :class="{ small: small }">
          Album
        </div>
        <div class="songList__header--right--hears" :class="{ medium: medium }">
          Hears
        </div>
        <div
          class="songList__header--right--duration"
          :class="{ medium: medium, small: small }"
        >
          Duration
        </div>
        <div
          class="songList__header--right--control"
          :class="{ medium: medium, small: small }"
        ></div>
      </div>
    </div>
    <div class="songList__content">
      <BaseSongItem
        v-for="item in testArr"
        :key="item"
        :title="'Future Parade'"
        :artist="'虹ヶ咲学園スクールアイドル同好会'"
        :album="'Future Parade'"
        :duration="'4:36'"
        :hears="1000000"
      />
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "../../components/icons/IconPlay.vue";
import IconSearch from "../../components/icons/IconSearch.vue";
import IconHorizontalThreeDot from "../../components/icons/IconHorizontalThreeDot.vue";
import BaseListItem from "../../components/UI/BaseListItem.vue";
import BaseSongItem from "../../components/UI/BaseSongItem.vue";

export default defineComponent({
  setup() {},
  components: {
    IconPlay,
    IconSearch,
    IconHorizontalThreeDot,
    BaseListItem,
    BaseSongItem,
  },
  data() {
    return {
      isMenuOpen: false,
      isSearchBarOpen: false,
      filterText: "",
      songListWidth: 0,
      observer: null as ResizeObserver | null,
      small: false,
      medium: false,
      testArr: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    };
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    toggleSearchBar() {
      this.isSearchBarOpen = !this.isSearchBarOpen;
    },
  },
  mounted() {
    const songList = this.$refs.songList as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.songListWidth = entry.contentRect.width;
      }
    });
    if (this.observer && songList) this.observer.observe(songList);
  },
  watch: {
    songListWidth(o) {
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
});
</script>
<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.playlist-header {
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
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    z-index: -1;
    filter: blur(6px) brightness(0.7);
  }
  & .header__image {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    & img {
      width: 100%;
      height: 100%;
      object-fit: cover;
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
      font-size: 32px;
      color: #fff;
      font-weight: 900;
    }
    &__description {
      font-size: 16px;
      color: #fff;
      font-weight: 400;
    }
    &__other {
      display: flex;
      color: #fff;
      font-size: 16px;
      &--owner {
        font-weight: 700;
      }
      &--songCount {
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
  &__right {
    display: flex;
    height: 100%;
    align-items: center;
    justify-content: center;
    width: max-content;
    &--filter {
      display: flex;
      height: 100%;
      align-items: center;
      justify-content: center;
      width: max-content;
      margin-right: 10px;
      & button {
        display: flex;
        width: 40px;
        height: 40px;
        background-color: var(--background-glass-color-primary);
        outline: none;
        border: none;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        fill: var(--text-subdued);
        cursor: pointer;
        &.active {
          border-radius: 50% 0 0 50%;
        }
      }

      & input {
        height: 40px;
        display: block;
        border-radius: 0px 10px 10px 0px;
        border: none;
        outline: none;
        line-height: 50px;
        padding: 0 10px;
        background: var(--background-glass-color-primary);
        color: var(--text-primary-color);
        font-size: 16px;
        font-weight: 400;
        transition: all 0.5s forwards;
        -webkit-transition: width 0.5s;
        -moz-transition: width 0.5s;
        &::placeholder {
          color: var(--text-subdued);
        }
      }
    }
    &--sort {
      display: flex;
      height: 100%;
      align-items: center;
      justify-content: center;
      width: max-content;
      & select {
        height: 40px;
        width: 200px;
        border-radius: 10px;
        border: none;
        outline: none;
        line-height: 50px;
        padding: 0 10px;
        margin-right: 10px;
        background: var(--background-glass-color-primary);
        color: var(--text-primary-color);
        font-size: 16px;
        font-weight: 400;
        &::placeholder {
          color: var(--text-primary-color);
        }
        & > * {
          color: var(--text-primary-color);
          background: var(--background-color-primary);
        }
      }
    }
  }
}
.songList {
  width: 100%;
  &__header {
    width: 80%;
    height: 50px;
    color: var(--text-subdued);
    margin: 0 auto;
    display: flex;
    align-items: center;
    border-bottom: 1px solid var(--text-subdued);
    padding: 0px 17px;
    &--left {
      width: 70%;
      display: flex;
      &--img {
        width: 50px;
        flex: 1 0 50px;
      }
      &--title {
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        width: 100%;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
        text-align: center;
        cursor: pointer;
      }
    }
    &--right {
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      user-select: none;
      &--album {
        width: 60%;
        overflow: hidden;
        text-align: center;
        cursor: pointer;
        &.small {
          display: none;
        }
      }
      &--hears {
        width: 20%;
        overflow: hidden;
        text-align: center;
        cursor: pointer;
        &.medium {
          display: none;
        }
      }
      &--duration {
        width: 20%;
        overflow: hidden;
        text-align: center;
        cursor: pointer;
        &.medium {
          width: 20%;
          &.small {
            width: 80%;
          }
        }
      }
      &--control {
        width: 30px;
      }
    }
  }
}

@media (max-width: $mobile-width) {
  .playlist-header {
    & .header__image {
      display: none;
    }
  }
}

.bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: transparent;
  z-index: 10;
  display: none;
  &.active {
    display: block;
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

.search-input-enter-from,
.search-input-leave-to {
  width: 0;
}
.search-input-enter-to,
.search-input-leave-from {
  width: 200px;
}
</style>
