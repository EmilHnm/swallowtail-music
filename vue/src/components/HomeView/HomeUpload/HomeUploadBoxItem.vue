<template>
  <div class="upload-item">
    <div class="upload-item__title">
      <h4>{{ title }}</h4>
    </div>
    <div
      class="upload-item__progress"
      :class="{ isError: status == 'error' }"
      :style="{ width: progress + '%' }"
    ></div>
    <button class="upload-item__cancel" v-if="status != 'finish'">
      <IconCrossVue></IconCrossVue>
    </button>
  </div>
</template>

<script lang="ts">
import IconCrossVue from "@/components/icons/IconCross.vue";
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    title: {
      type: String,
      required: true,
    },
    progress: {
      type: Number,
      required: true,
    },
    status: {
      type: String,
      required: true,
    },
  },
  components: { IconCrossVue },
});
</script>

<style lang="scss" scoped>
.upload-item {
  position: relative;
  min-height: 50px;
  max-height: 50px;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 0 10px;
  border-bottom: 1px solid var(--background-glass-color-primary);
  &__title {
    width: 100%;
    h4 {
      width: 100%;
      font-size: 1rem;
      font-weight: 500;
      margin: 0;
      text-overflow: ellipsis;
      white-space: nowrap;
      overflow: hidden;
    }
  }
  &__cancel {
    position: absolute;
    top: 0;
    right: 0;
    display: none;
    aspect-ratio: 1 / 1;
    height: 50px;
    width: auto;
    background-color: var(--background-glass-color-primary);
    border: none;
    cursor: pointer;
    z-index: 10;
    transition: all 0.5s ease-in-out;
    svg {
      width: 100%;
      height: 100%;
      path {
        stroke: #fff;
      }
    }
  }
  &:hover &__cancel {
    display: block;
  }
  &__progress {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 0;
    z-index: -1;
    opacity: 0.2;
    background-color: var(--color-primary);
    transition: all 0.5s ease-in-out;
    &.isError {
      background-color: var(--color-danger);
    }
  }
}
</style>
