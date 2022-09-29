<template>
  <div class="base-input">
    <label class="base-input__label" :for="id">
      {{ label }}<span class="required" v-if="required">*</span>
    </label>
    <div class="base-input__inputBox">
      <input
        class="base-input__input"
        :id="id"
        :type="type"
        v-model="value"
        :required="required"
        @input="onInput"
      />
      <span></span>
    </div>
  </div>
</template>
<script lang="ts">
export default {
  emits: ["input"],
  props: {
    id: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      required: true,
    },
    mode: {
      type: String,
      default: "",
    },
    required: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      value: "",
    };
  },
  methods: {
    onInput() {
      this.$emit("input", this.value);
    },
  },
};
</script>
<style lang="scss" scoped>
.base-input {
  position: relative;
  width: 100%;
  &__label {
    display: block;
    padding: 0px 2px;
    font-size: 0.75rem;
    margin-bottom: 0.1rem;
    color: var(--color-primary);

    font-size: 1.2rem;
    transition: 0.3s;
    font-weight: bold;
    & > .required {
      color: var(--color-danger);
    }
  }
  &__inputBox {
    position: relative;
    width: 100%;
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 5px 2px 5px 0px var(--color-primary-blur);
    & span {
      position: absolute;
      top: -300%;
      left: 100%;
      width: 50%;
      height: 300%;
      background: linear-gradient(
        90deg,
        transparent,
        var(--color-primary),
        transparent
      );
      z-index: -1;
      transform: skewX(45deg);
      transition: 0.5s;
    }
  }
  &__input {
    display: block;
    position: relative;
    padding: 12px 18px;
    outline: none;
    border: none;
    border-left: solid 1px var(--text-primary-color);
    border-top: solid 2px var(--text-primary-color);
    border-radius: 30px;
    background: transparent;
    color: var(--text-primary-color);
    font-family: Consolas;
    font-size: 1rem;
    transition: 0.3s;

    width: calc(100% - 36px);
    &:focus {
      border-left: solid 1px var(--color-primary);
      border-top: solid 2px var(--color-primary);
    }
  }
  &__input:focus + span {
    top: 90%;
    left: -50%;
  }
}
</style>
