<template>
  <div class="box">
    <div class="content">
      <slot></slot>
    </div>
  </div>
</template>

<scrip lang="ts">
import { defineComponent } from "vue";
export default defineComponent({});
</scrip>

<style lang="scss" scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.box {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 40px 30px;
  transition: 0.5s;
  &::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 75%;
    height: 60%;
    background: var(--text-primary-color);
    border-radius: 8px;
    translate: -50% -50%;
    transition: 0.5s;
    filter: blur(10px);
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
    height: 60%;
    background: #fff;
    border-radius: 8px;
    translate: -50% -50%;
    transition: 0.5s;
    filter: blur(30px);
    transition: 0.5s;
    background: linear-gradient(
      315deg,
      var(--color-primary),
      var(--color-secondary)
    );
  }
  &:hover:before,
  &:hover:after {
    transform: skewX(0deg);
    width: calc(100% - 40px);
    height: 70%;
  }
  & .content {
    position: relative;
    left: 0;
    background: var(--background-glass-color-primary);
    box-shadow: 0px 0px 20px 0px #000;
    border-radius: 8px;
    backdrop-filter: blur(10px);
    z-index: 1;
    transition: 0.5s;
    width: 100%;
    height: 100%;
    color: var(--text-primary-color);
  }

  .box .content p {
    font-size: 1.1em;
    margin-bottom: 20px;
    line-height: 1.4em;
  }

  .box .content a {
    background: #fff;
    color: #000;
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
  }
}
</style>
