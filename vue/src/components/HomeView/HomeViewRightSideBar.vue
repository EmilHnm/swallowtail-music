<template>
  <div class="playing-list" :class="{ active: isActive }">
    <div class="playing-list__playing">
      <div class="playing-list--title"><h3>Playing</h3></div>
      <base-list-item :selected="true">
        <div class="playing-list__song">
          <IconPlay />
          <div class="playing-list__song--title">{{ playingAudio.title }}</div>
          <div class="playing-list__song--duration">
            {{
              Math.floor(playingAudio.duration / 60) +
              ":" +
              Math.floor(playingAudio.duration % 60)
            }}
          </div>
        </div>
      </base-list-item>
    </div>
    <div class="playing-list__queue" ref="queue" v-if="!shuffle">
      <div class="playing-list--title"><h3>In Queue</h3></div>
      <!-- BUG: transition not work  -->
      <transition-group tag="ul" name="queue-list">
        <li
          class="playing-list__queue--item"
          v-for="(item, index) in playlist"
          :key="index"
          draggable="true"
          @dragstart="dragStart($event, item)"
          @dragend="onDrop($event)"
          @dragenter="dragEnter($event, item)"
        >
          <BaseSongItem
            :selected="index === audioIndex ? true : false"
            :title="item.title"
            :artist="item.artist"
            :album="'Future Paradise'"
            :duration="'3:30'"
            :inQueue="true"
            @deleteFromQueue="deleteFromQueue(index)"
            @selectSong="setPlaySong(index)"
          />
        </li>
      </transition-group>
    </div>
    <div class="playing-list__queue" ref="queue" v-else>
      <div class="playing-list--title"><h3>In Queue</h3></div>
      <transition-group tag="ul" name="queue-list" mode="out-in">
        <li
          class="playing-list__queue--item"
          v-for="(item, index) in shuffledPlaylist"
          :key="index"
          draggable="true"
          @dragstart="dragStart($event, item)"
          @dragend="onDrop($event)"
          @dragenter="dragEnter($event, item)"
        >
          <BaseSongItem
            :selected="index === audioIndex ? true : false"
            :title="item.title"
            :artist="item.artist"
            :album="'Future Paradise'"
            :duration="'3:30'"
            :inQueue="true"
            @deleteFromQueue="deleteFromQueue(index)"
            @selectSong="setPlaySong(index)"
          />
        </li>
      </transition-group>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "../icons/IconPlay.vue";
import BaseSongItem from "../UI/BaseSongItem.vue";

export default defineComponent({
  emits: ["onDrop", "setPlaySong", "deleteFromQueue"],
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
    playlist: {
      type: Array as () => Array<Object>,
      required: true,
    },
    shuffledPlaylist: {
      type: Array as () => Array<Object>,
      required: true,
    },
    playingAudio: {
      type: Object,
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
      movingIndex: -1,
      movingOverIndex: -1,
    };
  },
  methods: {
    dragStart(e: DragEvent, item: Object) {
      if (e.target) (e.target as HTMLLIElement).classList.add("dragging");
      if (this.shuffle) {
        this.movingIndex = this.shuffledPlaylist.findIndex((i) => i === item);
      } else {
        this.movingIndex = this.playlist.findIndex((i) => i === item);
      }
    },
    onDrop(e: DragEvent) {
      if (e.target) (e.target as HTMLLIElement).classList.remove("dragging");
      this.$emit("onDrop", this.movingIndex, this.movingOverIndex);
    },
    dragEnter(e: DragEvent, item: Object) {
      if (this.shuffle) {
        this.movingOverIndex = this.shuffledPlaylist.findIndex(
          (i) => i === item
        );
      } else {
        this.movingOverIndex = this.playlist.findIndex((i) => i === item);
      }
    },
    setPlaySong(index: number) {
      console.log("setPlaySong: " + index);
      this.$emit("setPlaySong", index);
    },
    deleteFromQueue(index: number) {
      this.$emit("deleteFromQueue", index);
    },
  },
  components: { IconPlay, BaseSongItem },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.playing-list {
  width: 0px;
  max-width: 350px;
  transition: 0.5s;
  background: var(--background-color-secondary);
  height: 100%;
  overflow: auto;
  border-right: 0.5px solid var(--text-subdued);
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
      width: 30px;
      height: 30px;
      fill: var(--text-primary-color);
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
  transition: all 0.5s ease-out;
}

.queue-list-enter-to,
.queue-list-leave-from {
  opacity: 1;
  transform: translateX(0);
}

.queue-list-leave-active {
  transition: all 0.5s ease-in;
  position: absolute;
}

.queue-list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.queue-list-move {
  transition: transform 0.8s ease;
}
</style>
