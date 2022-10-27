<template>
  <teleport to="body">
    <div v-if="open" class="dialog-bg" @click="$emit('close')"></div>
    <transition name="dialog">
      <div class="dialog" v-if="open">
        <span></span>
        <div class="content">
          <header>
            <h2 :class="mode">{{ title }}</h2>
          </header>
          <section>
            <slot></slot>
          </section>
          <menu>
            <slot name="action">
              <base-button @click="$emit('close')">Close</base-button>
            </slot>
          </menu>
        </div>
      </div>
    </transition>
  </teleport>
</template>
<script lang="ts">
import { defineComponent } from "vue";
export default defineComponent({
  name: "BaseFlatDialog",
  props: {
    mode: {
      type: String,
      default: "",
    },
    title: {
      type: String,
      default: "Warning!!!",
    },
    open: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["close"],
});
</script>
<style lang="scss" scoped>
.dialog-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 50;
}
.dialog {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80%;
  max-width: 600px;
  height: max-content;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 100;

  & .content {
    position: relative;
    left: 0;
    padding: 20px 40px;
    background: var(--background-color-primary);
    box-shadow: rgba(0, 0, 0, 0.05);
    z-index: 1;
    transition: 0.5s;
    color: var(--text-primary-color);
    width: 100%;
    & h2 {
      font-size: 2em;
      color: var(--text-primary-color);
      margin-bottom: 10px;
      &.negative {
        color: var(--color-negative);
      }
      &.danger {
        color: var(--color-danger);
      }
      &.warning {
        color: var(--color-warning);
      }
      &.announcement {
        color: var(--color-announcement);
      }
    }

    & section {
      font-size: 1.1em;
      margin-bottom: 20px;
      line-height: 1.4em;
    }

    & menu {
      margin-block-start: 0em;
      margin-block-end: 0em;
      margin-inline-start: 0px;
      margin-inline-end: 0px;
      padding-inline-start: 0px;
      width: 100%;
      display: flex;
      justify-content: space-evenly;
      & > * {
        width: 25%;
      }
    }
  }
}

.dialog-enter-from {
  opacity: 0;
  top: 25%;
}
.dialog-enter-to {
  opacity: 1;
  top: 50%;
}
.dialog-enter-active {
  transition: all 0.5s;
}
.dialog-leave-from {
  opacity: 1;
  top: 50%;
}
.dialog-leave-to {
  opacity: 0;
  top: 75%;
}
.dialog-leave-active {
  transition: all 0.5s;
}
</style>
