<template>
  <div class="collapse-table">
    <div class="collapse-table__header" @click="toggle">
      <slot name="header"></slot>
    </div>
    <div class="collapse-table__body" :class="{ active: active }">
      <slot name="body"></slot>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  data() {
    return {
      active: false,
    };
  },
  methods: {
    toggle() {
      this.active = !this.active;
    },
  },
});
</script>

<style lang="scss" scoped>
.collapse-table {
  width: 100%;
  background-color: var(--background-color-secondary);
  &__header {
    width: 100%;
    background-color: var(--background-glass-color-primary);
    cursor: pointer;
  }
  &__body {
    width: 100%;
    height: 0px;
    overflow: hidden;
    transition: all 0.2s ease-in-out;
    &.active {
      height: max-content;
    }
  }
}
</style>
