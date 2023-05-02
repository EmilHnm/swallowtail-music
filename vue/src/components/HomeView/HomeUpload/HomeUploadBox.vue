<template>
  <div class="upload-box" :class="{ active: isActive, isPlaying }">
    <button
      class="upload-box__open"
      :class="{ active: !isActive }"
      @click="open()"
    >
      <IconLeftArrow></IconLeftArrow>
    </button>
    <button class="upload-box__close" @click="close()">
      <IconRightArrowVue></IconRightArrowVue>
    </button>
    <div class="upload-box__title">
      <h4>Uploading Queue ({{ pendingUpload.length }})</h4>
    </div>
    <div class="upload-box__body" :class="{ active: isActive }">
      <HomeUploadBoxItem
        v-for="item in uploadQueue"
        :key="item.song_id"
        :title="item.file.name"
        :progress="item.progress"
        :status="item.status"
      ></HomeUploadBoxItem>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import HomeUploadBoxItem from "@/components/HomeView/HomeUpload/HomeUploadBoxItem.vue";
import IconRightArrowVue from "@/components/icons/IconRightArrow.vue";
import IconLeftArrow from "@/components/icons/IconLeftArrow.vue";

type songFileUpload = {
  blob: Blob[];
  file: File;
  chunk_count: number;
  progress: number;
  status: "waiting" | "uploading" | "finish" | "error";
  song_id: string;
};

export default defineComponent({
  data() {
    return {
      isActive: false,
    };
  },
  props: {
    isPlaying: {
      type: Boolean,
      default: false,
    },
    uploadQueue: {
      type: Array as () => songFileUpload[],
      default: () => [],
    },
  },
  methods: {
    close() {
      this.isActive = false;
    },
    open() {
      this.isActive = true;
    },
  },
  computed: {
    pendingUpload() {
      return this.uploadQueue.filter((item) => item.status !== "finish");
    },
  },
  components: { IconRightArrowVue, IconLeftArrow, HomeUploadBoxItem },
});
</script>

<style lang="scss" scoped>
.upload-box {
  position: absolute;
  bottom: 12px;
  right: -300px;
  min-width: 300px;
  max-width: 300px;
  transition: all 0.5s ease-in-out;
  background-color: var(--background-color-primary);
  z-index: 100;
  button {
    position: absolute;
    display: block;
    aspect-ratio: 1 / 1;
    width: auto;
    border: none;
    z-index: 10;
    cursor: pointer;
    &.upload-box__close {
      height: 30px;
      top: 10px;
      right: 10px;
      background: transparent;
      svg {
        height: 100%;
        width: auto;
      }
    }
    &.upload-box__open {
      height: 50%;
      top: 0px;
      left: -48px;
      width: 48px;
      height: 48px;
      background-color: var(--background-color-secondary);
      border-radius: 10px 0 0 10px;
      display: none;
      &.active {
        display: block;
      }
      svg {
        height: 50%;
        width: auto;
      }
    }
    &:hover {
      svg {
        fill: var(--color-primary);
      }
    }
  }
  &.active {
    right: 10px;
  }
  &__title {
    background-color: var(--background-color-secondary);
    padding: 12px;
    position: relative;
    h4 {
      margin: 0;
      text-align: center;
    }
  }
  &__body {
    max-height: 0;
    overflow: auto;
    -ms-overflow-style: none;
    scrollbar-width: none;
    &::-webkit-scrollbar {
      display: none;
    }
    &.active {
      max-height: 300px;
    }
  }
}
</style>
