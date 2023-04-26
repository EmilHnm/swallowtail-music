<template>
  <div class="container" ref="main">
    <h2>Server Log Viewer</h2>
    <div class="control">
      <div class="control__page"></div>
      <div class="control__query"></div>
      <div class="control__filter"></div>
      <div class="control__date"></div>
    </div>
    <div class="logs">
      <div class="logs-item" v-for="(log, key) in logs" :key="key">
        <BaseCollapseTable>
          <template #header>
            <div class="logs-item__header">
              <div class="logs-item__header--index">
                {{ parseInt(key as string) + 1 + page * itemPerPage }}
              </div>
              <div class="logs-item__header--context" :class="log.context">
                {{ log.context }}
              </div>
              <div class="logs-item__header--level" :class="log.level">
                {{ log.level }}
              </div>
              <div class="logs-item__header--title">
                {{ log.text }}
              </div>
              <div class="logs-item__header--date">
                {{ new Date(log.date).toLocaleDateString() }}
              </div>
            </div>
          </template>
          <template #body>
            <div class="logs-item__content">
              <div class="logs-item__content--context">
                <span>Context:</span> {{ log.context }}
              </div>
              <div class="logs-item__content--level">
                <span>Level:</span> {{ log.level }}
              </div>
              <div class="logs-item__content--date">
                <span>Date:</span> {{ log.date }}
              </div>
              <div class="logs-item__content--date">
                <span>File:</span> {{ log.in_file }}
              </div>
              <div class="logs-item__content--title">
                <span>Title:</span><br />
                {{ log.text }}
              </div>
              <div class="logs-item__content--title">
                <span>Stack:</span><br />
                {{ log.stack }}
              </div>
            </div>
          </template>
        </BaseCollapseTable>
      </div>
    </div>
    <div class="navigation" v-if="Object.keys(logs).length > 0">
      <BaseTableBodyPagination
        :totalPages="lastPage"
        :currentPage="page"
        @onClickPage="onClickPage"
        @onGoToFirstPage="onGoToFirstPage"
        @onGoToPreviousPage="onGoToPreviousPage"
        @onGoToNextPage="onGoToNextPage"
        @onGoToLastPage="onGoToLastPage"
      />
    </div>
  </div>
</template>

<script lang="ts">
import BaseCollapseTable from "@/components/UI/BaseCollapseTable.vue";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import type { log } from "@/model/logModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

export default defineComponent({
  data() {
    return {
      logs: {} as log,
      page: 0,
      itemPerPage: 10,
      lastPage: 0,
      query: "",
      query_type: "",
      class: "all",
      level: "all",
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("admin/logs", ["fecthLogs"]),
    onClickPage(index: number) {
      this.page = index;
    },
    onGoToFirstPage() {
      this.page = 0;
    },
    onGoToPreviousPage() {
      this.page = this.page - 1;
    },
    onGoToNextPage() {
      this.page = this.page + 1;
    },
    onGoToLastPage() {
      this.page = this.lastPage - 1;
    },
    loadLogs() {
      this.isLoading = true;
      this.fecthLogs({
        userToken: this.token,
        page: this.page + 1,
        itemPerPage: this.itemPerPage,
        query: this.query,
        query_type: this.query_type,
        class: this.class,
        level: this.level,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.logs = res.logs.data;
            this.page = res.logs.current_page - 1;
            this.lastPage = res.logs.last_page;
          }
        });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  watch: {
    page() {
      this.loadLogs();
    },
  },
  created() {
    this.loadLogs();
  },
  components: { BaseCollapseTable, BaseTableBodyPagination },
});
</script>

<style lang="scss" scoped>
.container {
  width: calc(100% - 20px);
  overflow-x: hidden;
  overflow-y: scroll;
  height: 100%;
  padding: 10px;
  .control {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-direction: column;
    margin-bottom: 20px;
    width: 100%;
  }
  .logs {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    overflow: scroll;
    &-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-direction: column;
      &__header {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        user-select: none;
        width: calc(100% - 40px);
        &--index {
          padding: 0 5px;
          text-align: center;
          width: 25px;
        }
        &--title {
          max-width: 60%;
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
        }
        &--context {
          padding: 0 5px;
          margin: 0 5px;
          text-align: center;
          width: 100px;
          border-radius: 5px;
          color: white;
          &.local {
            background-color: var(--color-primary);
          }
          &.laravel {
            background-color: var(--color-secondary);
          }
          &.production {
            background-color: var(--color-negative);
          }
        }
        &--date {
          padding: 0 5px;
          margin: 0 5px;
          text-align: center;
          margin-left: auto;
        }
        &--level {
          margin: 0 5px;
          text-align: center;
          width: 100px;
          color: white;
          border-radius: 5px;
          &.error {
            background-color: var(--color-danger);
          }
          &.emergency {
            background-color: var(--color-warning);
          }
          &.info {
            background-color: var(--color-announcement);
          }
        }
      }
      &__content {
        font-size: 14px;
        line-height: 20px;
        overflow: scroll;
        padding: 10px 20px;
        max-height: 200px;
        & span {
          font-weight: bold;
          font-size: 16px;
          line-height: 24px;
        }
      }
    }
  }
}
</style>
