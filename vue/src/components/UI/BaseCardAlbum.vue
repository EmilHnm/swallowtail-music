<template>
  <BaseCard>
    <div class="album">
      <div class="album__image">
        <img class="img" src="../../assets/music/cover.jpg" />
        <div class="album__image--play">
          <IconPlay />
        </div>
      </div>
      <div class="album__title" @click="redirect">{{ title }}</div>
      <div class="album__uploader">{{ uploader }}</div>
      <div class="album__songCount">{{ songCount }} songs</div>
    </div>
  </BaseCard>
</template>

<script lang="ts">
import BaseCard from "./BaseCard.vue";
import IconPlay from "../icons/IconPlay.vue";
export default {
  components: { BaseCard, IconPlay },
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
      required: true,
    },
    id: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      default: "album",
    },
  },
  methods: {
    redirect() {
      this.$emit("redirect", { id: this.id, type: this.type });
    },
  },
};
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
      bottom: -10px;
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
    }
  }
  &__title {
    font-size: 1.2rem;
    font-weight: 600;
    text-overflow: ellipsis;
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
