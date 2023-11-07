<template>
  <button class="btn" :class="mode" :type="type">
    <slot></slot>
  </button>
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
.btn {
  padding: 10px 20px;
  border: none;
  font-weight: 600;
  background-color: var(--color-primary);
  &.warning {
    background: var(--color-warning);
  }
  &.announcement {
    background: var(--color-announcement);
  }
  &.danger {
    background: var(--color-danger);
  }
  &.negative {
    background: var(--color-negative);
  }

  &.accent {
    background: var(--color-accent);
  }

  /* Material Design primary color */
  color: white;
  text-align: center;
  text-decoration: none;
  font-size: 16px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);

  /* Material Design shadow */

  &:hover {
    background-color: var(--color-secondary);
  }

  &:focus {
    outline: none;
  }

  &:active {
    position: relative;

    &::after {
      content: "";
      display: block;
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background: rgba(0, 0, 0, 0.1);

      /* Ripple color */
      border-radius: inherit;
      animation: ripple 0.4s ease;
    }
  }
}
</style>
