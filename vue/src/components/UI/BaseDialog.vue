<template>
  <teleport to="body">
    <div class="dialog-bg" @click="$emit('close')"></div>
    <div class="dialog" :class="mode">
      <span></span>
      <div class="content">
        <header>
          <h2>{{ title }}</h2>
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
  </teleport>
</template>
<script lang="ts">
import { defineComponent } from "vue";
export default defineComponent({
  name: "BaseDialog",
  props: {
    mode: {
      type: String,
      default: "",
    },
    title: {
      type: String,
      default: "Warning!!!",
    },
  },
  emit: ["close"],
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
  &::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80%;
    height: 80%;
    background: #fff;
    border-radius: 8px;
    transform: translate(-50%, -50%);
    background: linear-gradient(
      315deg,
      var(--color-primary),
      var(--color-secondary)
    );
  }

  &::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80%;
    height: 80%;
    background: #fff;
    border-radius: 8px;
    filter: blur(30px);
    transition: 0.5s;
    transform: translate(-50%, -50%);
    background: linear-gradient(
      315deg,
      var(--color-primary),
      var(--color-secondary)
    );
  }

  &.negative {
    &::before {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-negative)
      );
    }
    &::after {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-negative)
      );
    }
  }
  &.danger {
    &::before {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-danger)
      );
    }
    &::after {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-danger)
      );
    }
  }
  &.warning {
    &::before {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-warning)
      );
    }
    &::after {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-warning)
      );
    }
  }
  &.announcement {
    &::before {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-announcement)
      );
    }
    &::after {
      background: linear-gradient(
        315deg,
        var(--color-primary),
        var(--color-announcement)
      );
    }
  }

  & .content {
    position: relative;
    left: 0;
    padding: 20px 40px;
    background: rgba(255, 255, 255, 0.05);
    box-shadow: rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    backdrop-filter: blur(10px);
    z-index: 1;
    transition: 0.5s;
    color: var(--text-primary-color);
    width: 100%;
    & h2 {
      font-size: 2em;
      color: var(--text-primary-color);
      margin-bottom: 10px;
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
</style>
