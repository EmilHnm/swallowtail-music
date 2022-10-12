<template>
  <div
    class="scroll_wrapper"
    :class="{ small: small, medium: medium }"
    ref="wrapper"
  >
    <button
      class="left-arrow"
      :class="{ small: small, medium: medium }"
      @click="moveRight"
      v-if="rollableWidth > 0"
    >
      <IconLeftArrow />
    </button>
    <button
      class="right-arrow"
      :class="{ small: small, medium: medium }"
      @click="moveLeft"
      v-if="rollableWidth > 0"
    >
      <IconRightArrow />
    </button>
    <div
      class="container"
      ref="container"
      :style="{ transform: `translateX(${currentPosition}px)` }"
      @wheel="onWheel"
    >
      <slot></slot>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconLeftArrow from "../icons/IconLeftArrow.vue";
import IconRightArrow from "../icons/IconRightArrow.vue";

export default defineComponent({
  data() {
    return {
      observer: null as ResizeObserver | null,
      wrapperWidth: 0,
      containerWidth: 0,
      rollableWidth: 0,
      currentPosition: 0,
      medium: false,
      small: false,
    };
  },
  methods: {
    moveRight() {
      this.currentPosition += this.rollableWidth / 8;
      if (this.currentPosition > 0) {
        this.currentPosition = -this.rollableWidth;
        return;
      }
    },
    moveLeft() {
      this.currentPosition -= this.rollableWidth / 8;
      if (this.currentPosition < -this.rollableWidth) {
        this.currentPosition = 0;
      }
    },
    onWheel(e: WheelEvent) {
      if (this.rollableWidth < 0) return;
      e.preventDefault();
      if (e.deltaY > 0) {
        this.currentPosition -= this.rollableWidth / 16;
        if (this.currentPosition < -this.rollableWidth) {
          this.currentPosition = -this.rollableWidth;
        }
      } else {
        this.currentPosition += this.rollableWidth / 8;
        if (this.currentPosition > 0) {
          this.currentPosition = 0;
          return;
        }
      }
    },
  },
  mounted() {
    this.containerWidth = this.$refs.container.clientWidth;
    const wrapper = this.$refs.wrapper;
    if (wrapper) this.wrapperWidth = wrapper.clientWidth;

    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.wrapperWidth = entry.contentRect.width;
      }
    });
    if (wrapper) this.observer.observe(wrapper);
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
  watch: {
    wrapperWidth(o) {
      if (o > 900 && o < 920) {
        return;
      }
      if (550 < o && o <= 900) {
        this.medium = true;
        this.small = false;
      } else if (o <= 550) {
        this.small = true;
        this.medium = false;
      } else {
        this.small = false;
        this.medium = false;
      }
      this.currentPosition = 0;
      this.rollableWidth = this.containerWidth - this.wrapperWidth;
    },
  },
  components: { IconLeftArrow, IconRightArrow },
});
</script>
<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.scroll_wrapper {
  position: relative;
  overflow: hidden;
  padding: 0 40px;

  .left-arrow,
  .right-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: var(--color-primary);
    border-radius: 50%;
    padding: 3px;
    opacity: 0;
    transition: 0.3s;
    z-index: 10;
    border: none;
    cursor: pointer;
    & svg {
      width: 100%;
      height: 100%;
      fill: #fff;
    }
  }

  &:hover {
    .left-arrow,
    .right-arrow {
      opacity: 1;
    }
  }
  .left-arrow {
    left: 0;
  }
  .right-arrow {
    right: 15px;
  }
  .left-arrow.medium {
    width: 40px;
    height: 40px;
  }
  .right-arrow.medium {
    width: 40px;
    height: 40px;
  }
  .left-arrow.small {
    width: 30px;
    height: 30px;
  }
  .right-arrow.small {
    width: 30px;
    height: 30px;
  }
  .container {
    display: flex;
    transition: transform 0.3s ease;
    width: max-content;
  }
}
.scroll_wrapper.medium {
  padding: 0 30px;
}
.scroll_wrapper.small {
  padding: 0 25px;
}
@media screen and (max-width: $tablet-width) {
  .scroll_wrapper {
    .left-arrow,
    .right-arrow {
      opacity: 1;
    }
  }
}
</style>
