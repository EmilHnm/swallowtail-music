<template>
  <div
    class="box"
    :class="selected ? 'active' : ''"
    ref="listItem"
    :style="{ padding: '0px ' + listPadding + 'px' }"
  >
    <span></span>
    <div class="content"><slot></slot></div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
export default defineComponent({
  name: "BaseListItem",
  data() {
    return {
      listPadding: 0,
    };
  },
  props: {
    selected: {
      type: Boolean,
      default: false,
    },
  },
  mounted() {
    const height: number = (this.$refs.listItem as any).clientHeight;
    this.listPadding = height / 4;
  },
});
</script>
<style lang="scss" scoped>
.box {
  position: relative;
  width: 80%;
  height: max-content;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: 0.5s;
  margin: auto;

  &::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0%;
    height: 0%;
    background: var(--text-primary-color);
    border-radius: 8px;
    translate: -50% -50%;
    filter: blur(10px);
    transition: 0.5s;
  }

  &::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0%;
    height: 0%;
    background: #fff;
    border-radius: 8px;
    translate: -50% -50%;
    transition: 0.5s;
    filter: blur(30px);
    transition: 0.5s;
  }

  &:before,
  &:after {
    background: linear-gradient(315deg, #5f5ddb, #49b9f9);
  }
  // & span {
  //   display: block;
  //   position: absolute;
  //   top: 0;
  //   left: 0;
  //   right: 0;
  //   bottom: 0;
  //   width: 100%;
  //   z-index: 2;
  //   pointer-events: none;
  //   &::before {
  //     content: "";
  //     position: absolute;
  //     top: 0;
  //     left: 0;
  //     width: 100%;
  //     height: 100%;
  //     border-radius: 3px;
  //     background: var(--background-glass-color-primary);
  //     backdrop-filter: blur(10px);
  //     opacity: 0;
  //     transition: 0.5s;
  //     box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
  //   }
  //   &::after {
  //     content: "";
  //     position: absolute;
  //     bottom: 0;
  //     right: 0;
  //     width: 100%;
  //     height: 100%;
  //     border-radius: 3px;
  //     background: var(--background-glass-color-primary);
  //     backdrop-filter: blur(10px);
  //     opacity: 0;
  //     transition: 0.5s;
  //     box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
  //   }
  // }
  &:hover {
    &:before,
    &:after {
      width: 80%;
      height: 50%;
    }
    & span::before {
      display: block;
      top: 50%;
      left: 0;
      aspect-ratio: 1 / 1;
      height: 50%;
      width: auto;
      opacity: 1;
      translate: 0% -50%;
    }

    & span::after {
      top: 50%;
      right: 0;
      aspect-ratio: 1 / 1;
      height: 50%;
      width: auto;
      opacity: 1;
      translate: 0% -50%;
    }
  }
  &.active {
    &:before,
    &:after {
      width: 80%;
      height: 50%;
    }
  }
  & .content {
    display: flex;
    position: relative;
    gap: 10px;
    left: 0;
    width: 100%;
    overflow: hidden;
    padding: 10px 20px;
    background: var(--background-blur-color-primary);
    box-shadow: 0 0px 15px rgba(0, 0, 0, 0.5);
    border-radius: 8px;
    backdrop-filter: blur(10px);
    z-index: 1;
    transition: 0.5s;
    color: var(--text-primary-color);
    & p {
      font-size: 1.1em;
      margin-bottom: 20px;
      line-height: 1.4em;
    }
  }
}
.router-link-active.router-link-exact-active {
  .box {
    &:before,
    &:after {
      width: 70%;
      height: 50%;
    }
  }
}
@keyframes animate {
  0%,
  100% {
    transform: translateY(5px);
  }
  50% {
    transform: translateY(-5px);
  }
}
</style>
