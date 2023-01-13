<template>
  <div class="base-button" :class="mode">
    <button :type="type">
      <slot></slot>
    </button>
    <span class="base-button_light"></span>
    <span class="base-button__background"></span>
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
  border-radius: 30px;
  overflow: hidden;
  text-align: center;
  &__background {
    position: absolute;
    top: 0;
    left: 50%;
    width: 100%;
    height: 100%;
    border-radius: 30px;
    z-index: -2;
    translate: -50%;
    background: var(--color-primary);
    transition: 0.5s;
  }
  &_light {
    position: absolute;
    top: -120%;
    left: 130%;
    width: 50%;
    height: 200%;
    z-index: 0;
    translate: -50%;
    background: var(--color-primary);
    transform: skewX(45deg);
    transition: 0.5s;
    background: #fff;
  }
  & button {
    width: 100%;
    padding: 0.75rem 1.5rem;
    background: transparent;
    backdrop-filter: blur(10px);
    color: var(--text-primary-color);
    cursor: pointer;
    border-radius: 30px;
    display: block;
    font-weight: bolder;
    position: relative;
    overflow: hidden;
    border: none;
    z-index: 1;
  }

  &.warning > .base-button__background {
    background: var(--color-warning);
  }
  &.announcement .base-button__background {
    background: var(--color-announcement);
  }
  &.danger > .base-button__background {
    background: var(--color-negative);
  }
  &.warning .base-button__background {
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
