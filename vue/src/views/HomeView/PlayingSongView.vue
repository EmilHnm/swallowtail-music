<template>
  <div class="playingSong-container" ref="container">
    <div class="information" :class="{ mobile: mobile }" v-if="playingAudio">
      <BaseCircleProgress
        :completed-steps="progress"
        :total-steps="playingAudio.duration"
        :animation-duration="500"
        :diameter="300"
      >
        <div
          class="album-image rotate"
          :class="{ 'animation-pause': !isPlaying }"
        >
          <img
            v-lazyload
            :data-url="
              playingAudio.album
                ? `${environment.album_cover}/${playingAudio.album.image_path}`
                : `${environment.default}/no_image.jpg`
            "
            alt=""
          />
        </div>
      </BaseCircleProgress>
      <div class="information__detail">
        <div class="information__detail--sub">Now Playing</div>
        <div class="information__detail--title">
          {{ playingAudio.title }}
        </div>
        <div class="information__detail--artist">
          <span
            v-for="(artist, index) in playingAudio.artist"
            :key="artist.artist_id"
            >{{ artist.name
            }}<span v-if="index != playingAudio.artist.length - 1"
              >,
            </span></span
          >
        </div>
        <div class="information__detail--duration">
          {{
            Math.floor(progress / 60) +
            ":" +
            (Math.floor(progress % 60) < 10
              ? "0" + Math.floor(progress % 60)
              : Math.floor(progress % 60))
          }}
          -
          {{
            new Date(playingAudio.duration * 1000)
              .toISOString()
              .substring(14, 19)
          }}
        </div>
      </div>
    </div>
    <h3 v-else>No Audio Playing</h3>
    <div class="visualizer" :class="{ mobile: mobile }">
      <div
        v-for="index in 100"
        :key="index"
        class="visualizer__col"
        :style="{ height: isPlaying ? frequencyData[index] + 2 + 'px' : '2px' }"
      ></div>
    </div>
  </div>
</template>

<script lang="ts">
import { environment } from "@/environment/environment";
import { defineComponent } from "vue";
import { useMeta } from "vue-meta";
import BaseCircleProgress from "@/components/UI/BaseCircleProgress.vue";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import type { song } from "@/model/songModel";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    playingAudio: songData;
    progress: number;
    isPlaying: boolean;
    frequencyData: Array<number>;
    audio: HTMLAudioElement;
  }
}
export default defineComponent({
  emits: [
    "updatePlaylist",
    "deletePlaylist",
    "playPlaylist",
    "playSongInPlaylist",
    "addPlaylistToQueue",
    "playAlbum",
    "addAlbumToQueue",
    "playSongInAlbum",
    "playArtistSong",
    "playSongOfArtist",
    "addArtistSongToQueue",
    "playLikedSong",
    "addLikedSongToQueue",
    "addToQueue",
    "playSong",
  ],
  data() {
    return {
      environment: environment,
      observer: null as ResizeObserver | null,
      mobile: true,
    };
  },
  inject: ["playingAudio", "progress", "isPlaying", "audio", "frequencyData"],
  components: { BaseCircleProgress },
  mounted() {
    const container: HTMLElement = this.$refs.container as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        if (entry.contentRect.width < 600) {
          this.mobile = true;
        } else {
          this.mobile = false;
        }
      }
    });
    if (container) this.observer.observe(container);
    useMeta({
      title: `Now Playing - ${this.playingAudio.title}`,
      meta: [
        {
          name: "description",
          content: "Now Playing",
        },
        {
          name: "keywords",
          content: "Now Playing",
        },
      ],
    });
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
});
</script>

<style lang="scss" scoped>
.playingSong-container {
  width: 100%;
  height: 100%;

  position: relative;
  .information {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 20px;
    &__detail {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 0 20px;
      &--sub {
        font-size: 14px;
        color: var(--text-subdued);
      }
      &--title {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary);
      }
      &--artist {
        font-size: 18px;
        color: var(--text-primary);
      }
      &--uploader {
        font-size: 14px;
        color: var(--text-subdued);
      }
      &--duration {
        font-size: 14px;
        color: var(--text-subdued);
      }
    }
    & .album-image {
      aspect-ratio: 1/1;
      width: 90%;
      height: auto;
      border-radius: 50%;
      overflow: hidden;
      position: relative;
      user-select: none;
      & img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      &::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20%;
        height: 20%;
        border-radius: 50%;
        background: var(--background-color-primary);
        z-index: 10;
      }
      &::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 30%;
        height: 30%;
        border-radius: 50%;
        background: var(--background-color-primary);
        opacity: 0.5;
        z-index: 11;
      }
    }
    &.mobile {
      flex-direction: column;
      .information__detail {
        padding-top: 20px;
      }
    }
  }

  .visualizer {
    width: 100%;
    height: 40%;
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    position: absolute;
    bottom: 0;
    &.mobile {
      filter: blur(1px);
    }
    .visualizer__col {
      width: 0.714%;
      background: linear-gradient(
        180deg,
        var(--color-primary-blur) 0%,
        var(--color-primary) 100%
      );
      transition: 0.5s;
    }
  }
}
.rotate {
  animation: rotate 10s linear infinite;
}
.animation-pause {
  animation-play-state: paused;
}

@keyframes rotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
