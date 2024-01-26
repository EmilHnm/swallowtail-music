<template>
  <BaseCard>
    <div class="album">
      <div class="album__image">
        <img v-lazyload class="img" :data-url="img" :alt="title" />
        <div class="album__image--play">
          <IconPlay @click="play" />
        </div>
      </div>
      <div class="album__title" @click="redirect">
        <BaseTooltipVue :position="'bottom'" :tooltip-text="title">
          {{ title }}
        </BaseTooltipVue>
      </div>
      <div class="album__uploader" v-if="uploader">{{ uploader }}</div>
      <div class="album__songCount" v-if="songCount !== undefined">
        {{ songCount }} songs
      </div>
      <div class="album__songCount" v-if="listens !== undefined">
        {{ listens }} listens
      </div>
    </div>
  </BaseCard>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseCard from "./BaseCard.vue";
import IconPlay from "@/components/icons/IconPlay.vue";
import BaseTooltipVue from "./BaseTooltip.vue";

export default defineComponent({
  components: { BaseCard, IconPlay, BaseTooltipVue },
  props: {
    title: {
      type: String,
      required: true,
    },
    uploader: {
      type: String,
      default: "",
    },
    songCount: {
      type: Number,
    },
    listens: {
      type: Number,
    },
    id: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      default: "album",
    },
    img: {
      type: String,
      default: "",
    },
  },
  methods: {
    redirect() {
      if (this.type === "playlist")
        this.$router.push({
          name: "playlistViewPage",
          params: {
            id: this.id,
          },
        });
      if (this.type === "album")
        this.$router.push({
          name: "albumViewPage",
          params: {
            id: this.id,
          },
        });
    },
    play() {
      if (this.type === "playlist") {
        this.$emit("playPlaylist", this.id);
      }
      if (this.type === "album") {
        this.$emit("playAlbum", this.id);
      }
    },
  },
  emits: ["playPlaylist", "playAlbum"],
});
</script>

<style lang="scss" scoped>
.album {
  width: 180px;
  height: 100%;
  margin: 10px auto;
  padding: 10px;
  cursor: pointer;
  &__image {
    aspect-ratio: 1/1;
    width: 100%;
    height: auto;
    position: relative;
    border-radius: 10px;
    overflow: hidden;

    & img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    &--play {
      position: absolute;
      bottom: -100px;
      right: 10px;
      transform: translateY(-50%);
      width: 25px;
      height: 25px;
      background: var(--color-primary);
      border-radius: 50%;
      padding: 10px;
      color: var(--text-subdued);
      transition: 0.3s;
      opacity: 0;
      & svg {
        width: 100%;
        height: 100%;
        fill: #fff;
      }
    }
    &:hover .album__image--play {
      opacity: 1;
      bottom: -10px;
    }
  }
  &__title {
    font-size: 1.2rem;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    margin-top: 0.5rem;
    user-select: none;
  }
  &__uploader {
    font-size: 1rem;
    font-weight: 500;
    text-overflow: ellipsis;
    user-select: none;
  }
  &__songCount {
    font-size: 1rem;
    font-weight: 500;
    text-overflow: ellipsis;
    user-select: none;
  }
}
</style>
