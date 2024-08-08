<template>
  <div class="pagination">
    <li class="pagination--item">
      <button
        type="button"
        :disabled="currentPage === 0"
        aria-label="Go to first page"
        @click="onGoToFirstPage"
      >
        First
      </button>
    </li>

    <li class="pagination--item" v-if="currentPage !== 0">
      <button
        type="button"
        aria-label="Go to previous page"
        @click="onGoToPreviousPage"
      >
        <IconPreviousArrow />
      </button>
    </li>

    <li v-for="page in pages" :key="page.count" class="pagination--item">
      <button
        type="button"
        @click="onClickPage(page.count)"
        :class="{ active: page.count === currentPage }"
        :aria-label="`Go to page number ${page.count}`"
      >
        {{ page.count + 1 }}
      </button>
    </li>

    <li class="pagination--item" v-if="currentPage !== totalPages - 1">
      <button
        type="button"
        aria-label="Go to next page"
        @click="onGoToNextPage"
      >
        <IconNextArrow />
      </button>
    </li>

    <li class="pagination--item">
      <button
        type="button"
        aria-label="Go to last page"
        :disabled="currentPage === totalPages - 1"
        @click="onGoToFirstLast"
      >
        Last
      </button>
    </li>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import IconNextArrow from "@/components/icons/IconNextArrow.vue";
import IconPreviousArrow from "@/components/icons/IconPreviousArrow.vue";

export default defineComponent({
  props: {
    maxVisibleButtons: {
      type: Number,
      required: false,
      default: 3,
    },
    totalPages: {
      type: Number,
      required: true,
    },
    currentPage: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {};
  },
  emits: [
    "onClickPage",
    "onGoToFirstPage",
    "onGoToPreviousPage",
    "onGoToNextPage",
    "onGoToLastPage",
  ],
  methods: {
    onClickPage(page: number) {
      this.$emit("onClickPage", page);
    },
    onGoToFirstPage() {
      this.$emit("onGoToFirstPage");
    },
    onGoToPreviousPage() {
      this.$emit("onGoToPreviousPage");
    },
    onGoToNextPage() {
      this.$emit("onGoToNextPage");
    },
    onGoToFirstLast() {
      this.$emit("onGoToLastPage");
    },
  },
  computed: {
    startPage() {
      if (this.currentPage === 0) {
        return 0;
      }
      if (this.currentPage === this.totalPages - 1) {
        return this.totalPages - this.maxVisibleButtons > 0
          ? this.totalPages - this.maxVisibleButtons
          : 0;
      }
      return this.currentPage - 1;
    },
    endPage() {
      return Math.min(
        this.startPage + this.maxVisibleButtons,
        this.totalPages - 1
      );
    },
    pages() {
      const range = [];
      for (let i = this.startPage; i <= this.endPage; i += 1) {
        range.push({
          count: i,
          isDisabled: i === this.currentPage,
        });
      }
      return range;
    },
  },
  components: { IconNextArrow, IconPreviousArrow },
});
</script>

<style lang="scss" scoped>
.pagination {
  display: flex;
  justify-content: center;
  gap: 5px;
  list-style: none;
  padding: 10px 0;
  &--item {
    button {
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      outline: none;
      padding: 0 10px;
      background: var(--color-primary-blur);
      color: var(--text-primary-color);
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
      border-radius: 5px;
      svg {
        width: 20px;
        height: 20px;
      }
      &:hover {
        background: var(--color-primary);
      }
      &.active {
        background: var(--color-primary);
      }
      &:disabled {
        cursor: not-allowed;
        opacity: 0.6;
      }
    }
  }
}
</style>
