<template>
  <div class="main" ref="main">
    <h3>Edit Artist Details</h3>
    <div class="details">
      <div class="details__image">
        <img src="" alt="" srcset="" />
        <input type="file" name="" id="" />
      </div>
      <div class="details__statistic">
        <div class="row">
          <span class="lab">Name</span>
          <span class="content">{{ user ? user.name : "" }}</span>
        </div>
      </div>
    </div>
    <div class="song"></div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  setup() {
    return {};
  },
});
</script>

<style lang="scss" scoped></style>
