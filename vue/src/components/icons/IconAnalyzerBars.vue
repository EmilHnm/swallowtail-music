<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "IconAnalyzerBars",
});
</script>

<template>
  <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
    <g transform="rotate(180 100 100)">
      <rect x="0" y="0" width="20" height="20" class="bars">
        <animate
          attributeName="height"
          dur="1s"
          repeatCount="indefinite"
          values="200;40;80;160;180"
        />
      </rect>
      <rect x="30" y="0" width="20" height="30" class="bars">
        <animate
          attributeName="height"
          dur="1s"
          repeatCount="indefinite"
          values="90;124;85;119;90"
        />
      </rect>
      <rect x="60" y="0" width="20" height="40" class="bars">
        <animate
          attributeName="height"
          dur="1s"
          repeatCount="indefinite"
          values="12;32;36;111;12"
        />
      </rect>
      <rect x="90" y="0" width="20" height="50" class="bars">
        <animate
          attributeName="height"
          dur="1s"
          repeatCount="indefinite"
          values="140;100;10;100;140"
        />
      </rect>
      <rect x="120" y="0" width="20" height="60" class="bars">
        <animate
          attributeName="height"
          dur="1s"
          repeatCount="indefinite"
          values="12;80;112;40"
        />
      </rect>
      <rect x="150" y="0" width="20" height="70" class="bars">
        <animate
          attributeName="height"
          dur="1s"
          repeatCount="indefinite"
          values="160;100;63;140;160"
        />
      </rect>
      <rect x="180" y="0" width="20" height="70" class="bars">
        <animate
          attributeName="height"
          dur="1s"
          repeatCount="indefinite"
          values="200;120;90;150;200"
        />
      </rect>
    </g>
  </svg>
</template>

<style scoped lang="scss">
.bars {
  fill: var(--color-secondary);
}
</style>
