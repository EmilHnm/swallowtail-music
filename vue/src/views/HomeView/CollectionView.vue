<template>
  <div class="collection-header">
    <div class="header__background"></div>
    <div class="header__icon">
      <IconHeartFilled />
    </div>
    <div class="info">
      <div class="info__type">Public</div>
      <div class="info__title">LoveLive Song</div>
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
        <div class="collection-menu" :class="{ active: isMenuOpen }">
          <BaseListItem>Add to Queue</BaseListItem>
          <BaseListItem>Make Private</BaseListItem>
          <BaseListItem>Make Public</BaseListItem>
        </div>
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
        v-for="(item, index) in testArr"
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
import IconHeartFilled from "../../components/icons/IconHeartFilled.vue";

export default defineComponent({
  setup() {},
  components: {
    IconPlay,
    IconSearch,
    IconHorizontalThreeDot,
    BaseListItem,
    BaseSongItem,
    IconHeartFilled,
  },
  data() {
    return {
      isMenuOpen: false,
      isSearchBarOpen: false,
      filterText: "",
      songListWidth: 0,
      observer: null,
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
    const songList = this.$refs.songList;
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
.collection-header {
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
  & .header__icon {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    position: relative;
    & svg {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 50%;
      height: 50%;
      fill: #fff;
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
      & svg {
        width: 25px;
        height: 25px;
        fill: #fff;
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
      .collection-menu {
        position: absolute;
        display: block;
        top: 105%;
        left: 0px;
        width: 250px;
        border-radius: 10px;
        z-index: 20;
        padding: 10px 0px;
        display: none;
        background: var(--background-glass-color-primary);
        & > * {
          margin: 5px auto;
        }
        &.active {
          display: block;
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

        &.small {
          display: none;
        }
      }
      &--hears {
        width: 20%;
        overflow: hidden;
        text-align: center;

        &.medium {
          display: none;
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
      }
      &--control {
        width: 30px;
      }
    }
  }
}

@media (max-width: $mobile-width) {
  .collection-header {
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
</style>
