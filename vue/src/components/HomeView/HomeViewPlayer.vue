<template>
  <footer class="now_playing">
    <div class="now_playing__info">
      <div class="now_playing__info--cover" @click="navigateToPlaying">
        <img
          :src="
            playingAudio.album
              ? `${environment.album_cover}/${playingAudio.album.image_path}`
              : `${environment.default}/no_image.jpg`
          "
          alt="cover"
        />
      </div>
      <div class="now_playing__info--title" ref="title">
        <div class="now_playing__info--title--name" @click="navigateToPlaying">
          {{ playingAudio.title }}
        </div>
        <div
          class="now_playing__info--title--artist"
          :style="{
            transform:
              isHovered && artistOverflow
                ? `translateX(-${artistListWidth - titleWidth}px)`
                : 'translateX(0%)',
          }"
          @mouseover="isHovered = true"
          @mouseleave="isHovered = false"
          ref="artistList"
        >
          <span
            v-for="(item, index) in playingAudio.artist"
            :key="item.artist_id"
            @click="redirectToArtist(item.artist_id)"
            >{{ item.name
            }}<span v-if="index != playingAudio.artist.length - 1"
              >,
            </span></span
          >
        </div>
      </div>
      <div class="now_playing__info--heart" @click="onLikeSong">
        <IconHeartFilled v-if="isLike" />
        <IconHeart v-else />
      </div>
    </div>
    <div class="now_playing__controls">
      <div class="now_playing__controls--button">
        <button
          class="now_playing__controls--button--shuffle"
          :class="{ active: shuffle }"
          @click="shuffleToggle"
        >
          <Iconshuffle />
        </button>
        <button class="now_playing__controls--button--prev" @click="prevSong">
          <IconPrevious />
        </button>
        <button class="now_playing__controls--button--play" @click="playToggle">
          <IconCircleLoading v-if="isWating" />
          <IconPause v-if="!isWating && isPlaying" />
          <IconPlay v-if="!isWating && !isPlaying" />
        </button>
        <button class="now_playing__controls--button--next" @click="nextSong">
          <IconNext />
        </button>
        <button
          class="now_playing__controls--button--repeat"
          :class="{ active: repeat !== 'off' }"
          @click="repeatToggle"
        >
          <IconRepeatOne v-if="repeat === 'one'" />
          <IconRepeat v-else />
        </button>
      </div>
      <div class="now_playing__controls--progress">
        <div class="now_playing__controls--progress--time--current">
          {{
            Math.floor(progress / 60) +
            ":" +
            (Math.floor(progress % 60) < 10
              ? "0" + Math.floor(progress % 60)
              : Math.floor(progress % 60))
          }}
        </div>
        <div class="now_playing__controls--progress--bar">
          <div
            class="now_playing__controls--progress--bar--progress"
            :style="{
              width: (progress / playingAudio.file.duration) * 100 + '%',
            }"
          ></div>
          <input
            type="range"
            class="now_playing__controls--progress--bar--range"
            :value="(progress / playingAudio.file.duration) * 100"
            @input="setProgress"
          />
        </div>
        <div class="now_playing__controls--progress--time--duration">
          {{
            new Date(playingAudio.file.duration * 1000)
              .toISOString()
              .substring(14, 19)
          }}
        </div>
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
          <button class="now_playing__menu--volume--mute" @click="setMuted">
            <IconVolume v-if="volume !== 0" />
            <IconMuted v-else />
          </button>
          <div class="now_playing__menu--volume--bar">
            <div
              class="now_playing__menu--volume--bar--progress"
              :style="{ width: volume + '%' }"
            ></div>
            <input
              type="range"
              :value="volume"
              class="now_playing__menu--volume--bar--range"
              @input="setVolume"
            />
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import IconHeartFilled from "@/components/icons/IconHeartFilled.vue";
import IconHeart from "@/components/icons/IconHeart.vue";
import IconPrevious from "@/components/icons/IconPrevious.vue";
import Iconshuffle from "@/components/icons/IconShuffle.vue";
import IconPlay from "@/components/icons/IconPlay.vue";
import IconRepeat from "@/components/icons/IconRepeat.vue";
import IconNext from "@/components/icons/IconNext.vue";
import IconQueue from "@/components/icons/IconQueue.vue";
import IconVolume from "@/components/icons/IconVolume.vue";
import IconPause from "@/components/icons/IconPause.vue";
import IconRepeatOne from "@/components/icons/IconRepeatOne.vue";
import IconMuted from "@/components/icons/IconMuted.vue";
import IconCircleLoading from "@/components/icons/IconCircleLoading.vue";
import { mapActions, mapGetters } from "vuex";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    $refs: {
      artistList: HTMLDivElement;
      title: HTMLDivElement;
    };
  }
}

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

export default defineComponent({
  name: "HomeViewPlayer",
  props: {
    playingAudio: {
      required: true,
      type: Object as () => songData,
    },
    isPlaying: {
      required: true,
      type: Boolean,
    },
    progress: {
      required: true,
      type: Number,
    },
    volume: {
      required: true,
      type: Number,
    },
    repeat: {
      required: true,
      type: String,
    },
    shuffle: {
      required: true,
      type: Boolean,
    },
    isWating: {
      required: true,
      type: Boolean,
    },
  },
  components: {
    IconHeartFilled,
    IconPrevious,
    Iconshuffle,
    IconPlay,
    IconRepeat,
    IconNext,
    IconQueue,
    IconVolume,
    IconPause,
    IconRepeatOne,
    IconMuted,
    IconHeart,
    IconCircleLoading,
  },
  data() {
    return {
      environment: environment,
      isRightSideBarActive: false,
      currentTime: "0:00",
      durationTime: "",
      isLike: null as boolean | null,
      isLikeLoading: false,
      isHovered: false,
      artistOverflow: false,
      titleObserver: null as ResizeObserver | null,
      artistListObserver: null as ResizeObserver | null,
      titleWidth: 0,
      artistListWidth: 0,
    };
  },
  methods: {
    ...mapActions("song", ["likeSong", "likedSong"]),
    toggleRightSideBar() {
      this.isRightSideBarActive = !this.isRightSideBarActive;
      this.$emit("toggleRightSideBar", this.isRightSideBarActive);
    },
    shuffleToggle() {
      this.$emit("shuffleToggle");
    },
    repeatToggle() {
      this.$emit("repeatToggle");
    },
    playToggle() {
      if (this.isWating) return;
      if (!this.isPlaying) {
        this.$emit("playSong");
      } else {
        this.$emit("pauseSong");
      }
    },
    setProgress(e: any) {
      this.$emit("setProgress", e.target.value);
    },
    nextSong() {
      this.$emit("nextSong");
    },
    prevSong() {
      this.$emit("prevSong");
    },
    setVolume(e: any) {
      this.$emit("setVolume", e.target.value);
    },
    setMuted() {
      this.$emit("setMuted");
    },
    navigateToPlaying() {
      this.$router.push({
        name: "playingPage",
      });
    },
    redirectToArtist(id: string) {
      this.$router.push({
        name: "artistPage",
        params: {
          id: id,
        },
      });
    },
    onLoadLikeSong() {
      this.isLikeLoading = true;
      this.isLike = null;
      this.likedSong({
        songId: this.playingAudio.song_id,
        userToken: this.token,
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res: any) => {
          this.isLike = res.liked;
        });
    },
    onLikeSong() {
      if (this.isLike !== null) {
        this.isLike = null;
        this.likeSong({
          userToken: this.token,
          songId: this.playingAudio.song_id,
        }).then(() => {
          this.onLoadLikeSong();
        });
      }
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  mounted() {
    this.titleObserver = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.titleWidth = entry.contentRect.width;
      }
    });
    this.titleObserver.observe(this.$refs.title);

    this.artistListObserver = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.artistListWidth = entry.contentRect.width;
      }
    });
    this.artistListObserver.observe(this.$refs.artistList);
  },
  watch: {
    playingAudio: {
      handler() {
        this.onLoadLikeSong();
      },
      immediate: true,
      deep: true,
    },
    artistListWidth() {
      if (this.artistListWidth > this.titleWidth) {
        this.artistOverflow = true;
      } else {
        this.artistOverflow = false;
      }
    },
    titleWidth() {
      if (this.artistListWidth > this.titleWidth) {
        this.artistOverflow = true;
      } else {
        this.artistOverflow = false;
      }
    },
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;

.now_playing {
  width: 100%;
  height: 79px;
  background-color: var(--background-color-primary);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 10px;
  margin-bottom: 10px;
  width: calc(100% - 20px);
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
      cursor: pointer;

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
      cursor: pointer;

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
        display: flex;
        width: max-content;
        transition: all 2s ease-in-out;

        span {
          flex: 0 0 auto;
          padding-right: 2px;
          font-size: 12px;
        }
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
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        background-color: var(--color-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;

        svg {
          fill: #fff;
          width: 15px;
          height: 15px;
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

      &--shuffle.active,
      &--repeat.active {
        svg {
          fill: var(--color-primary);
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
      margin-left: 10px;
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
  // -webkit-appearance: none;
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
