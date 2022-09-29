<script lang="ts">
import { defineComponent } from "vue";
import DarkModeButton from "../../components/UI/DarkModeButton.vue";
import IconLogo from "../../components/icons/IconLogo.vue";
import { RouterView } from "vue-router";
export default defineComponent({
  name: "SignUpView",
  setup() {
    return {};
  },

  components: { DarkModeButton, IconLogo },
});
</script>
<template>
  <div class="container">
    <span class="container--darkmodeBtn">
      <DarkModeButton />
    </span>
    <span class="container--logo">
      <icon-logo></icon-logo>
    </span>
    <span class="container--glass-1"></span>
    <span class="container--glass-2"></span>
    <span class="container--glass-3"></span>
    <span class="container--glass-4"></span>
    <RouterView />
  </div>
</template>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.container {
  position: absolute;
  width: 50%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  max-width: 500px;
  &--logo {
    position: absolute;
    top: 20%;
    left: -20%;
    width: 80px;
    height: 80px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 30%;
    padding: 5px;
    z-index: 10;
    box-shadow: 1px 1px 3px 0px var(--text-primary-color);
    padding: 10px;
    z-index: 10;
    border-top: 1px solid var(--color-primary);
    border-left: 1px solid var(--color-primary);
    box-shadow: rgba(0, 0, 0, 0.05);
    animation: animate 2s infinite;
    animation-delay: 0.5s;
    background: linear-gradient(
      -145deg,
      var(--color-primary),
      transparent 40%,
      var(--color-secondary)
    );
    svg {
      width: 100%;
      height: 100%;
      fill: var(--color-primary);
    }
  }
  &--darkmodeBtn {
    position: absolute;
    top: 40px;
    right: -15px;
    display: flex;
    width: 20px;
    height: 20px;
    justify-content: center;
    align-items: center;
    border-radius: 30%;
    box-shadow: 1px 1px 3px 0px var(--text-primary-color);
    padding: 10px;
    z-index: 10;
    border-top: 1px solid var(--color-primary);
    border-left: 1px solid var(--color-primary);
    background: linear-gradient(
      145deg,
      var(--color-primary),
      transparent 40%,
      var(--color-secondary)
    );
    animation: animate 2s infinite;
  }
  &--glass-1 {
    position: absolute;
    top: 80%;
    right: 20%;
    width: 40px;
    height: 40px;
    border-radius: 30%;
    z-index: -10;
    box-shadow: 1px 1px 3px 0px var(--text-primary-color);
    border-top: 1px solid var(--color-primary);
    border-left: 1px solid var(--color-primary);
    box-shadow: rgba(0, 0, 0, 0.05);
    animation: animate 2s infinite;
    animation-delay: 1.5s;
    background: linear-gradient(
      -145deg,
      var(--color-primary),
      transparent 40%,
      var(--color-secondary)
    );
    opacity: 0.5;
  }
  &--glass-2 {
    position: absolute;
    top: 40%;
    right: -20%;
    width: 50px;
    height: 50px;
    border-radius: 30%;
    z-index: -10;
    box-shadow: 1px 1px 3px 0px var(--text-primary-color);
    border-top: 1px solid var(--color-primary);
    border-left: 1px solid var(--color-primary);
    box-shadow: rgba(0, 0, 0, 0.05);
    animation: animate 2s infinite;
    animation-delay: 2.1s;
    background: linear-gradient(
      -145deg,
      var(--color-primary),
      transparent 40%,
      var(--color-secondary)
    );
    opacity: 0.7;
  }
  &--glass-3 {
    position: absolute;
    top: -10px;
    left: 20%;
    width: 30px;
    height: 30px;
    border-radius: 30%;
    z-index: -10;
    box-shadow: 1px 1px 3px 0px var(--text-primary-color);
    border-top: 1px solid var(--color-primary);
    border-left: 1px solid var(--color-primary);
    box-shadow: rgba(0, 0, 0, 0.05);
    animation: animate 2s infinite;
    animation-delay: 1.8s;
    background: linear-gradient(
      -145deg,
      var(--color-primary),
      transparent 40%,
      var(--color-secondary)
    );
    opacity: 0.3;
  }
  &--glass-4 {
    position: absolute;
    top: 90%;
    left: -5%;
    width: 50px;
    height: 50px;
    border-radius: 30%;
    z-index: -10;
    box-shadow: 1px 1px 3px 0px var(--text-primary-color);
    border-top: 1px solid var(--color-primary);
    border-left: 1px solid var(--color-primary);
    box-shadow: rgba(0, 0, 0, 0.05);
    animation: animate 2s infinite;
    animation-delay: 0.3s;
    background: linear-gradient(
      -145deg,
      var(--color-primary),
      transparent 40%,
      var(--color-secondary)
    );
    opacity: 0.3;
  }
}

@media (max-width: $tablet-width) {
  .container {
    width: 80vw;
    height: 80vh;
    &--logo {
      width: 70px;
      height: 70px;
      top: 15%;
      left: -15%;
    }
  }
}
@media (max-width: $mobile-width) {
  .container {
    width: 100vw;
    height: 100vh;
    &--logo {
      top: 10px;
      left: 10px;
      animation: none;
      width: 50px;
      height: 50px;
    }
    &--darkmodeBtn {
      top: 10px;
      right: 10px;
      animation: none;
    }
    &--glass-1,
    &--glass-2,
    &--glass-3,
    &--glass-4 {
      display: none;
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
