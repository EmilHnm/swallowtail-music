<template>
  <Teleport to="body">
    <BaseFlatDialog
      :open="isOpen"
      :title="title"
      :mode="mode"
      @close="isOpen = false"
    >
      <template #default>
        <slot name="message" />
      </template>
      <template #action>
        <button @click="isOpen = false">Cancel</button>
        <button
          class="confirm"
          @click="
            isOpen = false;
            $emit('confirm', passingData);
          "
        >
          Confirm
        </button>
      </template>
    </BaseFlatDialog>
  </Teleport>
  <div class="trigger" @click="isOpen = true">
    <slot />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";

export default defineComponent({
  emits: ["confirm"],
  props: {
    passingData: {
      type: null,
      required: true,
    },
    title: {
      type: String,
      default: "Confirm",
    },
    mode: {
      type: String,
      default: "warning",
    },
  },
  components: { BaseFlatDialog },
  data() {
    return {
      isOpen: false,
    };
  },
});
</script>

<style lang="scss" scoped>
.trigger {
  width: fit-content;
  cursor: pointer;
}
button {
  padding: 5px 10px;
  background: transparent;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
  margin: 5px 2px;
  border: 1px solid var(--color-primary);
  color: var(--color-primary);
  outline: none;
  &:hover {
    background: var(--color-primary);
    color: var(--background-color-secondary);
  }
  &.confirm {
    border: 1px solid var(--color-danger);
    color: var(--color-danger);
    &:focus {
      outline: none;
    }
    &:hover {
      background: var(--color-danger);
      color: var(--background-color-secondary);
    }
  }
}
</style>
