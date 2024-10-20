<template>
  <label>
    <input
      type="radio"
      :name="name"
      :checked="checked"
      :value="value"
      :disabled="disable"
      v-model="localValue"
    />
    <span><slot></slot></span>
  </label>
</template>
<script lang="ts">
import { defineComponent } from "vue";
export default defineComponent({
  emits: ["update:modelValue"],
  props: {
    name: {
      type: String,
      required: true,
    },
    value: {
      type: String,
      required: true,
    },

    checked: {
      type: Boolean,
      default: false,
    },
    disable: {
      type: Boolean,
      default: false,
    },
    modelValue: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      localValue: this.$props.modelValue,
    };
  },
  watch: {
    localValue(newValue) {
      this.$emit("update:modelValue", newValue);
    },
  },
});
</script>
<style lang="scss" scoped>
label {
  display: flex;
  cursor: pointer;
  font-weight: 500;
  position: relative;
  overflow: hidden;
  margin-bottom: 0.375em;

  &:has(input:disabled) {
    cursor: not-allowed;
    opacity: 0.5;
  }

  input {
    position: absolute;
    left: -9999px;
    &:checked + span {
      &:before {
        box-shadow: inset 0 0 0 0.4375em var(--color-primary);
      }
    }
  }
  span {
    display: flex;
    align-items: center;
    padding: 0.375em 0.75em 0.375em 0.375em;
    border-radius: 99em;
    transition: 0.25s ease;
    &:before {
      display: flex;
      flex-shrink: 0;
      content: "";
      background-color: var(--background-color-primary);
      width: 1.5em;
      height: 1.5em;
      border-radius: 50%;
      margin-right: 0.375em;
      transition: 0.25s ease;
      box-shadow: inset 0 0 0 0.125em var(--color-primary);
    }
  }
}
</style>
