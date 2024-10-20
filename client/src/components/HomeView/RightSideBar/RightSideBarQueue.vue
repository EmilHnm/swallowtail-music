<template>
  <div class="playing-list">
    <div class="playing-list__playing">
      <div class="playing-list--title"><h3>Playing</h3></div>
      <base-list-item :selected="true">
        <div class="playing-list__song">
          <IconPlay />
          <div class="playing-list__song--title">
            <BaseTooltip
              :position="'bottom'"
              :tooltipText="getCurrentSong.title"
            >
              {{ getCurrentSong.title }}
            </BaseTooltip>
          </div>
          <div class="playing-list__song--duration">
            {{
              Math.floor(getCurrentSong.file.duration / 60) +
              ":" +
              Math.floor(getCurrentSong.file.duration % 60)
            }}
          </div>
        </div>
      </base-list-item>
    </div>
    <div class="playing-list__queue" ref="queue">
      <div class="playing-list--title"><h3>In Queue</h3></div>
      <transition-group tag="ul" name="queue-list">
        <li
          class="playing-list__queue--item"
          v-for="(item, i) in getQueue"
          :key="item.song_id"
          draggable="true"
          @dragstart="dragStart($event, +i)"
          @dragend="onDrop($event)"
          @dragenter="dragEnter($event, +i)"
        >
          <BaseSongItem
            :selected="i === getCurrentIndex"
            :data="item"
            :inQueue="true"
            @deleteFromQueue="deleteFromQueue(+i)"
            @selectSong="setPlaySong(+i)"
          />
        </li>
      </transition-group>
    </div>
  </div>
</template>

<script lang="ts">
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import type { song } from "@/model/songModel";
import { defineComponent } from "vue";
import IconPlay from "@/components/icons/IconPlay.vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import BaseTooltip from "@/components/UI/BaseTooltip.vue";
import {mapGetters, mapMutations} from "vuex";

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
  },
  data() {
    return {
      movingIndex: -1,
      movingOverIndex: -1,
    };
  },
  methods: {
    ...mapMutations("queue", ["moveSong", "setCurrentIndex"]),
    dragStart(e: DragEvent, i: number) {
      if (e.target) (e.target as HTMLLIElement).classList.add("dragging");
      this.movingIndex = i;
    },
    onDrop(e: DragEvent) {
      if (e.target) (e.target as HTMLLIElement).classList.remove("dragging");
      this.moveSong({ from: this.movingIndex, to: this.movingOverIndex });
      this.movingIndex = -1;
      this.movingOverIndex = -1;
    },
    dragEnter(e: DragEvent, i: number) {
      this.movingOverIndex = i;
    },
    setPlaySong(index: number) {
      this.setCurrentIndex(index);
    },
    deleteFromQueue(index: number) {
      this.$emit("deleteFromQueue", index);
    },
  },
  computed: {
    ...mapGetters("queue", ["getQueue", "getCurrentSong", "getCurrentIndex"]),
  },
  components: { IconPlay, BaseSongItem, BaseTooltip },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.playing-list {
  width: 100%;
  .playing-list--title {
    width: 100%;
    h3 {
      width: 100%;
      text-align: center;
      font-size: 1.2rem;
      font-weight: bolder;
      color: var(--text-subdued);
    }
  }
  .playing-list__song {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    user-select: none;
    cursor: pointer;
    .playing-list__song--order {
      font-size: 16px;
      color: var(--text-subdued);
    }
    .playing-list__song--title {
      font-size: 16px;
      color: var(--text-primary-color);
      width: 100%;
      text-overflow: ellipsis;
      overflow: hidden;
      white-space: nowrap;
      margin: 0 5px;
    }
    .playing-list__song--duration {
      font-size: 16px;
      color: var(--text-subdued);
    }
    & svg {
      width: 20px;
      height: 20px;
      fill: var(--text-primary-color);
      padding: 5px;
    }
  }
  .playing-list__queue {
    width: 100%;
    &--item {
      margin-bottom: 10px;
      &.dragging {
        opacity: 0.5;
      }
    }
  }
}
.playing-list.active {
  width: 30%;
  max-width: 350px;
}

@media (max-width: $mobile-width) {
  .playing-list.active {
    width: 100%;
    max-width: 500px;
    position: absolute;
    top: 0;
    right: 0;
    z-index: 50;
    .playing-list__song {
      .playing-list__song--order {
        display: none;
      }
    }
  }
}
.queue-list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.queue-list-enter-active {
  transition: all 0.3s ease-out;
}

.queue-list-enter-to,
.queue-list-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.queue-list-leave-active {
  transition: all 0.3s ease-in;
  position: absolute;
}

.queue-list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.queue-list-move {
  transition: transform 0.3s ease;
}
</style>
