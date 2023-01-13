<template>
  <div class="loading-line"></div>
</template>
<style scoped lang="scss">
.loading-line {
  width: 100%;
  height: 20px;
  background: linear-gradient(90deg, #0001 33%, #0005 50%, #0001 66%)
    var(--color-primary);
  background-size: 300% 100%;
  border-radius: 10px;
  animation: loading 1s infinite linear;
}

@keyframes loading {
  0% {
    background-position: right;
  }
}
</style>
