<template>
  <BaseCard>
    <div class="artist">
      <div class="artist__image">
        <img class="img" src="../../assets/music/cover.jpg" />
        <div class="artist__image--play">
          <IconPlay />
        </div>
      </div>
      <div class="artist__name">{{ title }}</div>
      <div class="artist__sub">Artist</div>
    </div></BaseCard
  >
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseCard from "./BaseCard.vue";
import IconPlay from "../icons/IconPlay.vue";

export default defineComponent({
  props: {
    // imageSrc: {
    //   type: String,
    //   required: true,
    // },
    title: {
      type: String,
      required: true,
    },
  },
  setup() {},
  components: { BaseCard, IconPlay },
});
</script>
<style lang="scss" scoped>
.artist {
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
    clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0 50%);
    transition: 0.3s;
    overflow: hidden;
    & img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    &--play {
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translate(-50%, -50%);
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
  }
  &__name {
    font-size: 1.2rem;
    font-weight: 600;
    text-overflow: ellipsis;
    margin-top: 0.5rem;
    user-select: none;
  }
  &__sub {
    font-size: 1rem;
    font-weight: 500;
    text-overflow: ellipsis;
    user-select: none;
  }
  &:hover {
    .artist__image {
      clip-path: polygon(50% 0, 95% 25%, 95% 75%, 50% 100%, 5% 75%, 5% 25%);
    }
    .artist__image--play {
      opacity: 1;
    }
  }
}
</style>
