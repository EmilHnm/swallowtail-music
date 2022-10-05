<template>
  <div class="login">
    <div class="login__header">
      <h1 class="login__title">Log In</h1>
      <p class="login__subtitle">
        Welcome back, please log in to your account.
      </p>
    </div>
    <form class="login__form" @submit.prevent="onSubmitForm">
      <div class="login__form__input">
        <BaseInput
          :label="'Email'"
          :type="'email'"
          :required="true"
          :id="'email'"
          v-model="email"
        ></BaseInput>
      </div>
      <div class="login__form__input">
        <BaseInput
          :label="'Password'"
          :type="'password'"
          :required="true"
          :id="'password'"
          v-model="password"
        ></BaseInput>
      </div>
      <div class="login__form__input">
        <input type="checkbox" name="remember_me" id="rememer_me" />
        <label>Remember Me</label>
      </div>
      <div class="login__form__input">
        <BaseButton :type="'submit'">Log In</BaseButton>
      </div>
      <div class="login__form__input">
        Forgot your password? <a>Reset it</a>
      </div>
      <div class="login__form__input">
        Have no account?
        <router-link :to="{ name: 'signup' }">Sign Up here!</router-link>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import BaseInput from "../../components/UI/BaseInput.vue";
import BaseButton from "../../components/UI/BaseButton.vue";

export default defineComponent({
  name: "LogInView",
  components: { BaseInput, BaseButton },
  data() {
    return {
      email: "",
      password: "",
    };
  },
  methods: {
    onSubmitForm() {
      fetch(environment.api + "/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email: this.email,
          password: this.password,
        }),
      })
        .then((response) => response.trim().json())
        .then((data) => {
          console.log(data);
        });
    },
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;

.login {
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
