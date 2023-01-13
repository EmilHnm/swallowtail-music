<template>
  <span class="label">
    <slot></slot>
  </span>
</template>
<style lang="scss" scoped>
.label {
  margin: 2px 0;
  text-align: center;
  display: inline-block;
  padding: 2px 10px;
  font-weight: 700;
  overflow: inherit;
  cursor: pointer;
  user-select: none;
  background-color: var(--color-primary);
  clip-path: polygon(5% 0, 95% 0, 100% 50%, 95% 100%, 5% 100%, 0 50%);
}
</style>
