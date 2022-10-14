<template>
  <div class="album-container">
    <div class="album__details">
      <div class="album__details--cover">
        <img src="../../assets/music/cover.jpg" />
      </div>
      <div class="album__details--prof">
        <div class="album__details--prof--title">
          <h3>Future Parade</h3>
        </div>
        <div class="album__details--prof--sub">
          <span> 2022 - 4 Songs</span>
        </div>
        <div class="album__details--prof--menu">
          <div class="album__details--prof--menu--play"><IconPlay /></div>
          <div class="album__details--prof--menu--heart">
            <IconHeartFilled />
          </div>
          <div class="album__details--prof--menu--more">
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
                <BaseListItem>Add to Library</BaseListItem>
                <BaseListItem>Add to Playlist</BaseListItem>
                <BaseListItem>Report</BaseListItem>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
    <div class="album__songs">
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
import IconHeartFilled from "../icons/IconHeartFilled.vue";
import BaseListItem from "./BaseListItem.vue";
import IconHorizontalThreeDot from "../icons/IconHorizontalThreeDot.vue";
import BaseSongItem from "./BaseSongItem.vue";
import IconPlay from "../icons/IconPlay.vue";

export default defineComponent({
  data() {
    return {
      isMenuOpen: false,
      testArr: [1, 2, 3, 4],
    };
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
  },
  components: {
    IconHeartFilled,
    BaseListItem,
    IconHorizontalThreeDot,
    BaseSongItem,
    IconPlay,
  },
});
</script>

<style lang="scss" scoped>
.album-container {
  width: 90%;
  margin: 20px auto;
  & .album__details {
    display: flex;
    height: 150px;
    margin-bottom: 30px;
    background-color: var(--background-glass-color-primary);
    &--cover {
      aspect-ratio: 1/1;
      height: 100%;
      width: auto;
      flex: 0 0 auto;
      & img {
        height: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    &--prof {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      padding-left: 20px;
      &--title {
        cursor: pointer;
        user-select: none;
        flex: 0 0 auto;
        & h3 {
          font-size: 2.5rem;
          font-weight: 700;
          margin: 2px;
          width: 80%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
      }
      &--sub {
        flex: 0 0 auto;
      }
      &--menu {
        display: flex;
        align-items: center;
        margin-top: auto;
        height: 100%;
        &--play {
          margin-right: 10px;
          border-radius: 50%;
          background-color: var(--color-primary);
          display: flex;
          align-items: center;
          padding: 3px;
          cursor: pointer;
          transition: transform 0.2s ease-in-out;
          & svg {
            width: 25px;
            height: 25px;
            fill: #fff;
          }
          &:hover {
            transform: scale(1.05);
          }
        }
        &--heart {
          margin-right: 10px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          padding: 3px;
          cursor: pointer;
          & svg {
            width: 25px;
            height: 25px;
          }
        }
        &--more {
          position: relative;
          margin-right: 10px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          padding: 3px;
          cursor: pointer;
          & svg {
            width: 25px;
            height: 25px;
          }
          & .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
            &.active {
              opacity: 1;
              visibility: visible;
            }
          }
          & .playlist-menu {
            position: absolute;
            top: 105%;
            right: 0;
            width: 250px;
            z-index: 2;
            padding: 10px 0px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            & > * {
              margin-bottom: 10px;
            }
          }
          & .playlist-menu-enter-from,
          .playlist-menu-leave-to {
            opacity: 0;
            transform: translateY(-10px);
          }
          & .playlist-menu-enter-to,
          .playlist-menu-leave-from {
            opacity: 1;
            transform: translateY(0);
          }
        }
      }
    }
  }
}
</style>
