<template>
  <span class="tooltip-container" ref="container">
    <Teleport to="body">
      <Transition>
        <div
          class="tooltips"
          :class="position"
          v-show="show"
          :style="{
            top: top + 'px',
            left: left + 'px',
          }"
          ref="tooltip"
        >
          {{ tooltipText }}
        </div>
      </Transition>
    </Teleport>
    <slot />
  </span>
</template>

<script lang="ts">
import { defineComponent } from "vue";
export default defineComponent({
  name: "Tooltip",
  props: {
    tooltipText: {
      type: String,
      default: "Tooltip text",
    },
    position: {
      default: "top",
      type: String,
      validator: (value: string) =>
        ["top", "bottom", "left", "right"].includes(value),
    },
  },
  data() {
    return {
      show: false,
      top: 0,
      left: 0,
    };
  },
  methods: {
    showTooltip() {
      this.show = true;
      this.renderPosition();
    },
    hideTooltip() {
      this.show = false;
    },
    renderPosition() {
      const container = this.$refs.container as HTMLElement;
      const { top, left, width, height } = container.getBoundingClientRect();
      switch (this.position) {
        case "top":
          this.top = top - 70;
          this.left = width / 2 + left;
          break;
        case "bottom":
          this.top = top + height;
          this.left = left + width / 2;
          break;
        case "left":
          this.top = top;
          this.left = left - 100;
          break;
        case "right":
          this.top = top;
          this.left = left + 100;
          break;
        default:
          this.top = top - 50;
          this.left = left;
          break;
      }
    },
  },
  mounted() {
    const container = this.$refs.container as HTMLElement;
    container.addEventListener("mouseenter", this.showTooltip);
    container.addEventListener("mouseleave", this.hideTooltip);
  },
  beforeUnmount() {
    const container = this.$refs.container as HTMLElement;
    container.removeEventListener("mouseenter", this.showTooltip);
    container.removeEventListener("mouseleave", this.hideTooltip);
  },
});
</script>
<style lang="scss">
.tooltip-container {
  display: block;
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
}
.tooltips {
  position: absolute;
  background: var(--background-blur-color-primary);
  width: max-content;
  max-width: 400px;
  @media screen and (max-width: 768px) {
    max-width: 200px;
  }
  padding: 10px;
  border-radius: 5px;
  color: var(--text-color-primary);
  font-size: 14px;
  backdrop-filter: blur(10px);
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  z-index: 9999;
  &.top,
  &.bottom {
    transform: translateX(-50%);
  }
  &.left,
  &.right {
    transform: translateY(-50%);
  }
}

.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
