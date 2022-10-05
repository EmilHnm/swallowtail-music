<template>
  <div class="playing-list" :class="{ active: isActive }">
    <div class="playing-list__playing">
      <div class="playing-list--title"><h3>Playing</h3></div>
      <base-list-item :selected="true">
        <div class="playing-list__song">
          <IconPlay />
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div>
      </base-list-item>
    </div>
    <div class="playing-list__queue" ref="queue">
      <div class="playing-list--title"><h3>In Queue</h3></div>
      <div
        class="playing-list__queue--item"
        v-for="item in test"
        :key="item"
        draggable="true"
        @dragstart="dragStart($event, item)"
        @dragend="onDrop($event, item)"
        @dragenter="dragEnter($event, item)"
      >
        <base-list-item>
          <div class="playing-list__song">
            <div class="playing-list__song--order">{{ item }}</div>
            <div class="playing-list__song--title">Future Parade</div>
            <div class="playing-list__song--duration">3:00</div>
          </div>
        </base-list-item>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "../icons/IconPlay.vue";

export default defineComponent({
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      test: ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j"],
      tempArray: [],
      movingIndex: -1,
      movingOverIndex: -1,
    };
  },
  methods: {
    dragStart(e: DragEvent, item: string) {
      if (e.target) e.target.classList.add("dragging");
      this.movingIndex = this.test.findIndex((i) => i === item);
      console.log("movingIndex", this.movingIndex);
    },
    onDrop(e: DragEvent, item: string) {
      e.target.classList.remove("dragging");
      this.test.splice(this.movingIndex, 1);
      this.test.splice(this.movingOverIndex, 0, item);
    },
    dragEnter(e: DragEvent, item: string) {
      this.movingOverIndex = this.test.findIndex((i) => i == item);
    },
  },
  components: { IconPlay },
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
</style>
