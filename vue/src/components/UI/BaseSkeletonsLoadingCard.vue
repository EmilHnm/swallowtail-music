<template>
  <BaseCard>
    <div class="skeleton">
      <div class="skeleton__image"></div>
      <div class="skeleton__title"></div>
      <div class="skeleton__subtitle"></div>
      <div class="skeleton__subtitle"></div>
    </div>
  </BaseCard>
</template>

<script lang="ts">
import BaseCard from "./BaseCard.vue";

export default {
  components: { BaseCard },
};
</script>

<style lang="scss" scoped>
.skeleton {
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
    background-color: var(--background-glass-color-primary);
    margin-bottom: 12px;
    &::before {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      transform: translateX(-100%);
      background-image: linear-gradient(
        90deg,
        rgba(#fff, 0) 0,
        rgba(#fff, 0.2) 20%,
        rgba(#fff, 0.5) 60%,
        rgba(#fff, 0)
      );
      animation: shimmer 2s infinite;
      content: "";
    }
  }
  &__title {
    height: 28px;
    background-color: var(--background-glass-color-primary);
    width: 100%;
    margin-bottom: 10px;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    &::before {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      transform: translateX(-100%);
      background-image: linear-gradient(
        90deg,
        rgba(#fff, 0) 0,
        rgba(#fff, 0.2) 20%,
        rgba(#fff, 0.5) 60%,
        rgba(#fff, 0)
      );
      animation: shimmer 2s infinite;
      content: "";
    }
  }
  &__subtitle {
    height: 24px;
    margin-bottom: 5px;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    background-color: var(--background-glass-color-primary);
    &::before {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      transform: translateX(-100%);
      background-image: linear-gradient(
        90deg,
        rgba(#fff, 0) 0,
        rgba(#fff, 0.2) 20%,
        rgba(#fff, 0.5) 60%,
        rgba(#fff, 0)
      );
      animation: shimmer 2s infinite;
      content: "";
    }
  }
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}
</style>
