<template>
  <div class="sb-right" :class="{ active: isActive }">
    <div class="sb-right__navigation">
      <BaseButton
        v-for="(tab, i) in tabs"
        :key="tab.name"
        @click="setActiveTab(i)"
        >{{ tab.name }}</BaseButton
      >
    </div>
    <keep-alive>
      <component
        :is="tabs[activeTab].value"
        :playlist="playlist"
        :shuffledPlaylist="shuffledPlaylist"
        :playingAudio="playingAudio"
        :audioIndex="audioIndex"
        :shuffle="shuffle"
        @onDrop="onDrop"
        @setPlaySong="setPlaySong"
        @deleteFromQueue="deleteFromQueue"
      ></component>
    </keep-alive>
  </div>
</template>
<script lang="ts">
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import type { song } from "@/model/songModel";
import { defineComponent } from "vue";
import RightSideBarDetail from "./RightSideBarDetail.vue";
import RightSideBarQueue from "./RightSideBarQueue.vue";
import BaseButton from "@/components/UI/BaseButton.vue";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

export default defineComponent({
  emits: ["onDrop", "setPlaySong", "deleteFromQueue"],
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
    playlist: {
      type: Array as () => songData[],
      required: true,
    },
    shuffledPlaylist: {
      type: Array as () => songData[],
      required: true,
    },
    playingAudio: {
      type: Object as () => songData,
      required: true,
    },
    audioIndex: {
      type: Number,
      required: true,
    },
    shuffle: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      tabs: [
        { name: "Queue", value: "RightSideBarQueue" },
        { name: "Details", value: "RightSideBarDetail" },
      ],
      activeTab: 0,
    };
  },
  methods: {
    setActiveTab(index: number) {
      this.activeTab = index;
    },
    onDrop(index: number, movingOverIndex: number) {
      this.$emit("onDrop", index, movingOverIndex);
    },
    setPlaySong(index: number) {
      this.$emit("setPlaySong", index);
    },
    deleteFromQueue(index: number) {
      this.$emit("deleteFromQueue", index);
    },
  },
  components: { RightSideBarDetail, RightSideBarQueue, BaseButton },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
$desktop-width: 1024px;

.sb-right {
  width: 0px;
  max-width: 400px;
  transition: 0.5s;
  background: var(--background-color-primary);
  border-radius: 10px;
  height: 100%;
  overflow: auto;
  &__navigation {
    width: 80%;
    margin: auto;
    margin-top: 20px;
    display: flex;
    gap: 4px;
    button {
      flex: 1;
    }
  }
}

.sb-right.active {
  width: 100%;
}

@media (max-width: $desktop-width) {
  .sb-right {
    width: 0px;
    position: absolute;
    z-index: 90;
  }
  .sb-right.active {
    width: 100%;
    max-width: none;
  }
}
</style>
