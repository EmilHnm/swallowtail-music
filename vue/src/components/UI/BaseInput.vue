<template>
  <div class="base-input">
    <label class="base-input__label" :class="labelColor" :for="id">
      {{ label }}<span class="required" v-if="required">*</span>
    </label>
    <div class="base-input__inputBox">
      <input
        v-if="mode === 'input'"
        class="base-input__input"
        :id="id"
        :type="type"
        :required="required"
        :placeholder="placeholder"
        :autocomplete="autocomplete ? 'on' : 'off'"
        :value="modelValue"
        @input="update($event.target.value)"
      />
      <textarea
        v-else-if="mode === 'textarea'"
        class="base-input__input"
        :id="id"
        :type="type"
        :required="required"
        :placeholder="placeholder"
        :autocomplete="autocomplete ? 'on' : 'off'"
        :cols="size.cols"
        :rows="size.rows"
        @input="update($event.target.value)"
      >{{ modelValue }}</textarea>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
export default defineComponent({
  emits: ["update:modelValue"],
  props: {
    id: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
    labelColor: {
      type: String,
      default: "primary",
    },
    type: {
      type: String,
      default: "text",
    },
    mode: {
      type: String as () => "input" | "textarea",
      default: "input",
    },
    required: {
      type: Boolean,
      default: false,
    },
    placeholder: {
      type: String,
      default: "",
    },
    modelValue: {
      default: "",
    },
    autocomplete: {
      type: Boolean,
      default: false,
    },
    size: {
      type: Object as () => { cols: number; rows: number },
      default: { cols: 20, rows: 2 },
      required: false,
    },
  },
  methods: {
    update(newValue: string) {
      this.$emit("update:modelValue", newValue);
    },
  },
});
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

    &.primary {
      color: var(--color-primary);
    }
    &.secondary {
      color: var(--color-secondary);
    }
    &.text {
      color: var(--text-primary-color);
    }
    & > .required {
      color: var(--color-danger);
    }
  }
  &__inputBox {
    position: relative;
    width: 100%;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 5px 2px 5px 0px var(--color-primary-blur);
    backdrop-filter: blur(10px);
    background: var();
  }
  &__input {
    display: block;
    position: relative;
    padding: 12px 18px;
    outline: none;
    border: none;
    border-left: solid 1px var(--text-primary-color);
    border-top: solid 2px var(--text-primary-color);
    border-radius: 10px;
    background: var(--background-glass-color-primary);
    font-family: "Inconsolata", monospace;
    color: var(--text-primary-color);
    font-size: 1rem;
    transition: 0.3s;
    width: calc(100% - 36px);
    &:focus {
      border-left: solid 1px var(--color-primary);
      border-top: solid 2px var(--color-primary);
    }
    &::placeholder {
      color: var(--text-subdued);
    }
  }
}
</style>
