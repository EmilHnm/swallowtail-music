<template>
  <teleport to="body">
    <BaseDialog
      :open="dialogWaring.show"
      :title="dialogWaring.title"
      :mode="dialogWaring.mode"
      @close="closeDialog"
    >
      <template #default>
        <p>{{ dialogWaring.content }}</p>
      </template>
    </BaseDialog>
    <BaseDialog
      :open="dialogAlert.show"
      :title="dialogAlert.title"
      :mode="dialogAlert.mode"
      @close="confirmForgotPassword"
    >
      <template #default>
        <p>{{ dialogAlert.content }}</p>
      </template>
    </BaseDialog>
    <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseDialog>
  </teleport>
  <div class="forgot">
    <div class="forgot__header">
      <h1 class="forgot__title">Forgot Password</h1>
      <p class="forgot__subtitle">
        Enter your email here to recover your account.
      </p>
    </div>
    <form class="forgot__form" @submit.prevent="onSubmitForm">
      <div class="forgot__form__input">
        <BaseInput
          :label="'Email'"
          :type="'email'"
          :required="true"
          :id="'email'"
          v-model="email"
        ></BaseInput>
      </div>
      <div class="forgot__form__input">
        Back to
        <router-link :to="{ name: 'login' }">Login</router-link>
      </div>
      <div class="forgot__form__input">
        <BaseButton :type="'submit'">Submit</BaseButton>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseInput from "../../components/UI/BaseInput.vue";
import BaseButton from "../../components/UI/BaseButton.vue";
import { mapActions, mapMutations } from "vuex";
import BaseDialog from "../../components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";

export default defineComponent({
  name: "ForgotPasswordView",
  components: { BaseInput, BaseButton, BaseDialog, BaseLineLoad },
  data() {
    return {
      email: "",
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      dialogAlert: {
        title: "Alert",
        mode: "announcement",
        content: "Please check your email to reset your password",
        show: false,
      },
      isLoading: false,
    };
  },
  methods: {
    onSubmitForm() {
      this.isLoading = true;
      if (this.email === "") {
        this.dialogWaring = {
          title: "Warning",
          mode: "warning",
          content: "Please fill in all the fields",
          show: true,
        };
      } else {
        this.forgotPassword({ email: this.email })
          .then((res) => res.json())
          .then((res) => {
            if (res.status === "success") {
              this.isLoading = false;
              this.email = "";
              this.dialogAlert = {
                title: "Alert",
                mode: "announcement",
                content: "Please check your email to reset your password",
                show: true,
              };
            }
          });
      }
    },
    ...mapMutations("auth", ["setUser"]),
    ...mapActions("auth", ["forgotPassword"]),
    closeDialog() {
      this.dialogWaring.show = false;
    },
    confirmForgotPassword() {
      this.dialogAlert.show = false;
    },
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;

.forgot {
  position: relative;
  border: none;
  border-top: 1px solid var(--color-primary);
  border-left: 1px solid var(--color-primary);
  border-radius: 30px;
  box-shadow: 1px 1px 3px 0px var(--text-primary-color);
  padding: 30px;
  background: transparent;

  overflow: hidden;
  &::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120%;
    height: 120%;
    z-index: -1;
    background: linear-gradient(
      45deg,
      transparent 0 10%,
      var(--background-glass-color-primary) 20% 30%,
      transparent 40% 50%,
      var(--background-glass-color-primary) 60% 70%,
      transparent 80% 100%
    );
    transition: 0.5s;
  }
  &:hover::after {
    top: 60%;
    left: 40%;
  }

  &__title {
    display: block;
    font-weight: 700;
    color: var(--text-primary-color);
    border-bottom: 5px solid var(--text-primary-color);
    margin-bottom: 10px;
  }
  &__subtitle {
    color: var(--text-primary-color);
  }
  &__form {
    margin-top: 20px;
    &__input {
      margin-bottom: 20px;
      color: var(--text-primary-color);
      a {
        color: var(--text-primary-color);
        font-weight: 700;
        &:hover {
          color: var(--color-primary);
        }
      }
    }
  }
}

@media (max-width: $mobile-width) {
  .login {
    position: relative;
    border: none;
    box-shadow: none;
    border-radius: 0;
    &__title {
      display: block;
      width: 100%;
      text-align: center;
      color: var(--text-primary-color);
      border-bottom: 5px solid var(--text-primary-color);
      margin-bottom: 10px;
    }
  }
}
</style>
