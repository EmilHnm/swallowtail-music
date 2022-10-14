<template>
  <BaseAlbum v-for="test in testArr" :key="test"></BaseAlbum>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseAlbum from "../../../components/UI/BaseAlbum.vue";

export default defineComponent({
  data() {
    return {
      isMenuOpen: false,
      testArr: [1, 2, 3, 4],
    };
  },
  methods: {},
  components: { BaseAlbum },
});
</script>

<style lang="scss" scoped></style>
