<template>
  <footer class="now_playing">
    <div class="now_playing__info">
      <div class="now_playing__info--cover">
        <img src="../../assets/music/cover.jpg" alt="cover" />
      </div>
      <div class="now_playing__info--title">
        <div class="now_playing__info--title--name">Future Parade</div>
        <div class="now_playing__info--title--artist">
          虹ヶ咲学園スクールアイドル同好会
        </div>
      </div>
      <div class="now_playing__info--heart">
        <IconHeartFilled />
      </div>
    </div>
    <div class="now_playing__controls">
      <div class="now_playing__controls--button">
        <button class="now_playing__controls--button--shuffle">
          <IconSuffle />
        </button>
        <button class="now_playing__controls--button--prev">
          <IconPrevious />
        </button>
        <button class="now_playing__controls--button--play">
          <IconPlay />
        </button>
        <button class="now_playing__controls--button--next">
          <IconNext />
        </button>
        <button class="now_playing__controls--button--repeat">
          <IconRepeat />
        </button>
      </div>
      <div class="now_playing__controls--progress">
        <div class="now_playing__controls--progress--time--current">0:00</div>
        <div class="now_playing__controls--progress--bar">
          <div
            class="now_playing__controls--progress--bar--progress"
            :style="{ width: songProgress + '%' }"
          ></div>
          <input
            type="range"
            class="now_playing__controls--progress--bar--range"
            v-model="songProgress"
          />
        </div>
        <div class="now_playing__controls--progress--time--duration">3:00</div>
      </div>
    </div>
    <div class="now_playing__menu">
      <div class="now_playing__menu--container">
        <button
          class="now_playing__menu--playlistToggle"
          @click="toggleRightSideBar"
          :class="{ active: isRightSideBarActive }"
        >
          <IconQueue />
        </button>
        <div class="now_playing__menu--volume">
          <button class="now_playing__menu--volume--mute">
            <IconVolume />
          </button>
          <div class="now_playing__menu--volume--bar">
            <div
              class="now_playing__menu--volume--bar--progress"
              :style="{ width: volume + '%' }"
            ></div>
            <input
              type="range"
              v-model="volume"
              class="now_playing__menu--volume--bar--range"
            />
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconHeartFilled from "../icons/IconHeartFilled.vue";
import IconPrevious from "../icons/IconPrevious.vue";
import IconSuffle from "../icons/IconSuffle.vue";
import IconPlay from "../icons/IconPlay.vue";
import IconRepeat from "../icons/IconRepeat.vue";
import IconNext from "../icons/IconNext.vue";
import IconQueue from "../icons/IconQueue.vue";
import IconVolume from "../icons/IconVolume.vue";

export default defineComponent({
  name: "HomeViewPlayer",
  setup() {},
  components: {
    IconHeartFilled,
    IconPrevious,
    IconSuffle,
    IconPlay,
    IconRepeat,
    IconNext,
    IconQueue,
    IconVolume,
  },
  data() {
    return {
      songProgress: 0,
      volume: 100,
      isRightSideBarActive: false,
    };
  },
  methods: {
    toggleRightSideBar() {
      this.isRightSideBarActive = !this.isRightSideBarActive;
      this.$emit("toggleRightSideBar", this.isRightSideBarActive);
    },
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;

.now_playing {
  position: fixed;
  bottom: 0;
  left: 0;
  z-index: 200;
  width: 100%;
  height: 79px;
  background-color: var(--background-color-primary);
  display: flex;
  align-items: center;
  justify-content: space-between;

  .now_playing__info {
    display: flex;
    align-items: center;
    width: 30%;
    margin-left: 20px;
    &--cover {
      width: 60px;
      height: 60px;
      border-radius: 5px;
      overflow: hidden;
      flex: 0 0 auto;
      margin-right: 5px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    &--title {
      width: 100%;
      margin: 0 5px;
      overflow: hidden;
      user-select: none;
      text-overflow: ellipsis;
      max-width: 260px;
      &--name {
        font-weight: 600;
        color: var(--text-color-primary);
        white-space: nowrap;
        cursor: pointer;
        &:hover {
          text-decoration: underline;
        }
      }
      &--artist {
        color: var(--text-subdued);
        white-space: nowrap;
        cursor: pointer;
      }
    }
    &--heart {
      width: 25px;
      height: 25px;
      overflow: hidden;
      flex: 0 0 auto;
      cursor: pointer;
      svg {
        fill: var(--color-primary);
        width: 100%;
        height: 100%;
      }
    }
  }
  .now_playing__controls {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    padding: 10px;
    max-width: 722px;
    width: 40%;
    &--button {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      width: 100%;
      height: 32px;
      margin-bottom: 10px;
      &--play {
        height: 100%;
        border-radius: 50%;
        background-color: var(--color-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        svg {
          fill: #fff;
          width: 20px;
          height: 20px;
        }
      }
      &--shuffle,
      &--prev,
      &--next,
      &--repeat {
        background: var(--background-color-primary);
        height: 80%;
        padding: 0;
        border: none;
        overflow: hidden;
        cursor: pointer;
        svg {
          width: 100%;
          height: 100%;
        }
      }
    }
    &--progress {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      &--bar {
        width: 100%;
        height: 4px;
        background-color: var(--background-color-secondary);
        border-radius: 2px;
        overflow: hidden;
        margin: 0 10px;
        position: relative;
        &--progress {
          width: 0%;
          height: 100%;
          background-color: var(--color-primary);
        }
        &--range {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          opacity: 0;
          cursor: pointer;
        }
      }
      &--time {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        &--current,
        &--duration {
          color: var(--text-subdued);
          font-size: 12px;
        }
      }
    }
  }
  .now_playing__menu {
    display: flex;
    width: 30%;
    margin-right: 20px;
    &--container {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }
    &--playlistToggle {
      background: var(--background-color-primary);
      height: 20px;
      width: 20px;
      padding: 0;
      border: none;
      margin: 0 10px;
      overflow: hidden;
      cursor: pointer;
      svg {
        width: 100%;
        height: 100%;
      }
    }
    &--playlistToggle.active {
      svg {
        fill: var(--color-primary);
      }
    }
    &--volume {
      display: flex;
      justify-content: space-around;
      align-items: center;
      width: 100%;
      max-width: 220px;
      &--mute {
        background: var(--background-color-primary);
        height: 30px;
        width: 30px;
        padding: 0;
        border: none;
        margin: 0 10px;
        overflow: hidden;
        flex-shrink: 1;
        cursor: pointer;
        svg {
          width: 100%;
          height: 100%;
        }
      }
      &--bar {
        width: 100%;
        height: 4px;
        background-color: var(--background-color-secondary);
        border-radius: 2px;
        overflow: hidden;
        margin: 0 10px;
        position: relative;
        &--progress {
          width: 50%;
          height: 100%;
          background-color: var(--color-primary);
        }
        &--range {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          opacity: 0;
          cursor: pointer;
        }
      }
    }
  }
}
@media (max-width: $tablet-width) {
  .now_playing {
    .now_playing__info {
      margin-left: 0px;
      width: 20%;
      &--title {
        display: none;
      }
      &--heart {
        display: none;
      }
    }
    .now_playing__controls {
      width: 60%;
    }
    .now_playing__menu {
      margin-right: 10px;
      width: 20%;
      &--volume {
        display: none;
      }
    }
  }
}
// @media (max-width: $mobile-width) {
//   .now_playing {
//     .now_playing__controls {
//       &--progress {
//         display: none;
//       }
//     }
//   }
// }

input[type="range"] {
  -webkit-appearance: none;
  margin: 0;
  width: 100%;
}
input[type="range"]:focus {
  outline: none;
}
input[type="range"]::-webkit-slider-thumb {
  width: 1px;
  -webkit-appearance: none;
}
</style>
