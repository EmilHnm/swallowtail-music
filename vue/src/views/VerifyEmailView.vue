<template>
  <div class="wrapper">
    <div class="verify-email">
      <div class="verify-email__logo">
        <IconLogo />
      </div>
      <div class="verify-email__darkmode">
        <DarkModeButton />
      </div>
      <div class="verify-email__title">Verify your email</div>
      <div class="verify-email__content" v-if="isLoading">
        <div class="verify-email__content__text">
          We are confirm your email address. Please do not close the tab.
        </div>
        <BaseCircleLoad />
      </div>
      <div class="verify-email__content" v-else>
        <div class="verify-email__content__text">
          {{ message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import IconLogo from "@/components/icons/IconLogo.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import DarkModeButton from "@/components/UI/DarkModeButton.vue";
import { defineComponent } from "vue";
import { mapActions } from "vuex";

export default defineComponent({
  data() {
    return {
      isLoading: true,
      message:
        "Your email address has been verified. You can close this tab now.",
    };
  },
  methods: {
    ...mapActions("auth", ["emailVerify"]),
  },
  mounted() {
    this.emailVerify(this.$route.query.token)
      .then((res) => res.json())
      .then((res) => {
        this.isLoading = false;
        if (res.status === "success") {
          this.message =
            "Your email address has been verified. You can close this tab now.";
        } else {
          this.message = res.message;
        }
      });
  },
  components: { BaseCircleLoad, DarkModeButton, IconLogo },
});
</script>

<style lang="scss" scoped>
.wrapper {
  width: 100vw;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  user-select: none;
  .verify-email {
    width: max-content;
    padding: 20px;
    &__logo {
      margin: auto;
      width: 150px;
      height: 150px;
      svg {
        width: 100%;
        height: 100%;
        fill: var(--color-primary);
      }
    }
    &__darkmode {
      margin: 20px auto;
      width: 20px;
      height: 20px;
      svg {
        width: 100%;
        height: 100%;
      }
    }
    &__title {
      font-size: 1.5rem;
      font-weight: 600;
      text-align: center;
      margin: 20px auto;
    }
    &__content {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
  }
}
</style>
