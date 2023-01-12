<template>
  <div class="container">
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
              <div class="logs-item__header--index">{{ key }}</div>
              <div class="logs-item__header--context">{{ log.context }}</div>
              <div class="logs-item__header--level">{{ log.level }}</div>
            </div>
          </template>
          <template #body>
            <div class="logs-item__content">{{ log.stack }}</div>
          </template>
        </BaseCollapseTable>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import BaseCollapseTable from "@/components/UI/BaseCollapseTable.vue";
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
    loadLogs() {
      this.isLoading = true;
      this.fecthLogs({
        userToken: this.token,
        page: this.page,
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
            this.page = res.current_page;
            this.lastPage = res.total_pages;
          }
        });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.loadLogs();
  },
  components: { BaseCollapseTable },
});
</script>

<style lang="scss" scoped>
.container {
  width: 100%;
  overflow: hidden;
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
    width: 100%;
    overflow: scroll;
    &-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-direction: column;
      width: 100%;
      &__header {
        display: flex;
        align-items: center;
        width: 100%;
        margin-bottom: 10px;
      }
      &__content {
        margin-top: 10px;
        margin-bottom: 10px;
        font-size: 14px;
        line-height: 20px;
      }
    }
  }
}
</style>
