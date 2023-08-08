<template>
  <div class="base-button" :class="mode">
    <button :type="type">
      <slot></slot>
    </button>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    mode: {
      type: String,
      default: "",
    },
    type: {
      type: String as () => "button" | "submit" | "reset" | undefined,
      default: "button",
    },
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.base-button {
  position: relative;
  width: 100%;
  border-radius: 10px;
  overflow: hidden;
  text-align: center;
  & button {
    width: 100%;
    background: var(--color-primary);
    padding: 0.75rem 1.5rem;
    backdrop-filter: blur(10px);
    color: var(--text-primary-color);
    cursor: pointer;
    border-radius: 10px;
    font-weight: bolder;
    font-size: 16px;
    position: relative;
    overflow: hidden;
    border: none;
    z-index: 1;
  }

  &.warning > button {
    background: var(--color-warning);
  }
  &.announcement button {
    background: var(--color-announcement);
  }
  &.danger > button {
    background: var(--color-negative);
  }
  &.warning button {
    background: var(--color-danger);
  }
  & button:hover {
    color: #fff;
    & + .base-button_light {
      top: 100%;
      left: -100%;
    }
  }
}
@media (max-width: $mobile-width) {
  .base-button {
    & button {
      padding: 10px 5px;
    }
  }
}
</style>
