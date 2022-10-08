<template>
  <div class="container">
    <loading
      v-model:active="isLoading"
      :is-full-page="false"
      :z-indez="50"
      :opacity="0.1"
    >
      Creating playlist...
      <loading
        v-model:active="isLoading"
        :is-full-page="false"
        :loader="'spinner'"
        :z-indez="50"
        :opacity="0"
      >
      </loading>
    </loading>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
export default defineComponent({
  data() {
    return {
      isLoading: true,
    };
  },
  mounted() {
    setTimeout(() => {
      this.isLoading = false;
      this.$router.push({ name: "playlistPage", params: { id: 1 } });
    }, 3000);
  },
  components: {
    Loading,
  },
});
</script>
<style lang="scss" scoped>
.container {
  width: 100%;
  height: 100%;
  margin: auto;
  position: relative;
  & > * {
    width: 100%;
  }
}
</style>
