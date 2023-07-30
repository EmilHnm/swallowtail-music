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
  </teleport>
  <div class="signup">
    <div class="signup__header">
      <h1 class="signup__title">Sign Up</h1>
      <p class="signup__subtitle">
        Welcome to Swallowtail Sound. Feel free to sign up.
      </p>
    </div>
    <form class="signup__form" @submit.prevent="onSubmitForm">
      <div class="signup__form__input">
        <BaseInput
          :label="'Username'"
          :id="'name'"
          :type="'text'"
          :required="false"
          v-model="name"
        ></BaseInput>
      </div>
      <div class="signup__form__input">
        <BaseInput
          :label="'Email'"
          :id="'email'"
          :type="'email'"
          :required="true"
          v-model="email"
        ></BaseInput>
      </div>
      <div class="signup__form__input">
        <BaseInput
          :label="'Password'"
          :id="'password'"
          :type="'password'"
          :required="true"
          v-model="password"
        ></BaseInput>
      </div>
      <div class="signup__form__input">
        <BaseInput
          :label="'Confirm password'"
          :id="'password_confirmation'"
          :type="'password'"
          :required="true"
          v-model="password_confirmation"
        ></BaseInput>
      </div>
      <div class="signup__form__input">
        <BaseButton :type="'submit'">Sign Up</BaseButton>
      </div>
      <div class="signup__form__input">
        Have an account?
        <router-link :to="{ name: 'login' }">Log In here!</router-link>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type Store from "@/store/index";
import { useMeta } from "vue-meta";
import BaseInput from "@/components/UI/BaseInput.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    $store: typeof Store;
  }
}

export default defineComponent({
  name: "SignUpView",
  components: { BaseInput, BaseButton, BaseDialog },
  data() {
    return {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  methods: {
    closeDialog() {
      this.dialogWaring.show = false;
    },
    onSubmitForm() {
      this.$store
        .dispatch("auth/register", {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        })
        .then((res: any) => res.json())
        .then((data: any) => {
          if (!data.message) {
            this.$store.commit("auth/setUser", data);
            this.$router.push({ name: "home" });
          } else {
            this.dialogWaring.content = data.message;
            this.dialogWaring.show = true;
          }
        });
    },
  },
  mounted() {
    useMeta({
      title: "Sign Up",
    });
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;

.signup {
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
  .signup {
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
