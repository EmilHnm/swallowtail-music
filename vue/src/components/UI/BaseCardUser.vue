<template>
  <BaseCard>
    <div class="user">
      <div class="user__image">
        <img
          v-lazyload
          class="img"
          :data-url="
            imageSrc
              ? `${environment.profile_image}/${imageSrc}`
              : `${environment.default}/default-avatar.jpg`
          "
          :alt="userName"
        />
      </div>
      <div class="user__name" @click="redirectToUserProfile">
        {{ userName }}
      </div>
      <div class="user__songCount">{{ songCount }} song(s) uploaded</div>
    </div></BaseCard
  >
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseCard from "./BaseCard.vue";
import { environment } from "@/environment/environment";
export default defineComponent({
  props: {
    imageSrc: {
      type: String,
    },
    userName: {
      type: String,
      required: true,
    },
    songCount: {
      type: Number,
      required: true,
    },
    id: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      environment: environment,
    };
  },
  methods: {
    redirectToUserProfile() {
      this.$router.push({ name: "profilePage", params: { id: this.id } });
    },
  },
  components: { BaseCard },
});
</script>
<style lang="scss" scoped>
.user {
  width: 180px;
  height: 100%;
  margin: 10px auto;
  padding: 10px;
  cursor: pointer;
  &__image {
    aspect-ratio: 1/1;
    width: 100%;
    height: auto;
    position: relative;
    border-radius: 999px;
    transition: 0.3s;
    overflow: hidden;
    & img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  &__name {
    font-size: 1.2rem;
    font-weight: 600;
    text-overflow: ellipsis;
    margin-top: 0.5rem;
    user-select: none;
  }
  &__songCount {
    font-size: 1rem;
    font-weight: 500;
    text-overflow: ellipsis;
    user-select: none;
  }
}
</style>
