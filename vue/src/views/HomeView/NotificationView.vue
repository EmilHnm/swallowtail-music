<template>
  <div class="notifications">
    <h1 class="notifications__title">Notification</h1>
    <div class="notifications__filter">
      <div class="notifications__filter--controll">
        <BaseButton @click="onMarkAllAsRead"> Mark all as read </BaseButton>
      </div>
      <div class="notifications__filter--status">
        <BaseButton
          @click="filters.status = 'all'"
          :mode="filters.status != 'all' ? 'accent' : ''"
        >
          All
        </BaseButton>
        <BaseButton
          @click="filters.status = 'unread'"
          :mode="filters.status != 'unread' ? 'accent' : ''"
        >
          Unread
        </BaseButton>
      </div>
    </div>
    <div class="notifications__content" v-if="isGettingNotification">
      <BaseCircleLoad />
    </div>
    <div class="notifications__content" v-else>
      <div
        class="notifications__content--result"
        v-if="notifications.length > 0"
      >
        <BaseNotificationItem
          v-for="notification in notifications"
          :notification="notification"
          :key="notification.id"
          @mark-as-read="markAsRead"
          @delete="onDelete"
        />
      </div>
      <div class="notifications__content--noResult" v-else>
        <p>No notification</p>
      </div>
    </div>
    <div class="notifications__paginator">
      <BaseTableBodyPagination
        :totalPages="totalPages"
        :currentPage="currentPage"
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
import { defineComponent } from "vue";
import type {
  notification,
  notificationPagination,
} from "@/model/notificationModel";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import { mapGetters, mapActions } from "vuex";
import BaseNotificationItem from "@/components/UI/BaseNotificationItem.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseButton from "@/components/UI/BaseButton.vue";

export default defineComponent({
  data() {
    return {
      currentPage: 0,
      totalPages: 0,
      itemPerPage: 10,
      isGettingNotification: true,
      notifications: [] as notification[],
      filters: {
        status: "all" as "all" | "unread",
        type: "all",
      },
    };
  },
  methods: {
    ...mapActions("notification", ["index", "markAllAsRead"]),
    onClickPage(index: number) {
      this.currentPage = index;
    },
    onGoToFirstPage() {
      this.currentPage = 0;
    },
    onGoToPreviousPage() {
      this.currentPage = this.currentPage - 1;
    },
    onGoToNextPage() {
      this.currentPage = this.currentPage + 1;
    },
    onGoToLastPage() {
      this.currentPage = this.totalPages - 1;
    },
    getNotification() {
      this.isGettingNotification = true;
      this.index({
        token: this.token,
        page: this.currentPage + 1,
        options: this.filters,
      })
        .then((res: any) => res.json())
        .then((res: notificationPagination) => {
          this.notifications = res.data;
          this.totalPages = res.last_page;
          this.currentPage = res.current_page - 1;
          this.isGettingNotification = false;
        });
    },
    onDelete() {
      this.getNotification();
    },
    onMarkAllAsRead() {
      this.isGettingNotification = true;
      let notifications_id = this.notifications.map((n) => n.id);
      this.markAllAsRead({
        token: this.token,
        ids: notifications_id,
      })
        .then((res: any) => res.json())
        .then((res: { message: "success" | "failed" }) => {
          if (res.message === "success") {
            this.getNotification();
          }
        });
    },
    markAsRead(id: string) {
      let notification = this.notifications.find((n) => n.id === id);
      if (notification) {
        notification.read_at = new Date().toDateString();
      }
    },
  },
  mounted() {
    this.getNotification();
  },
  computed: {
    ...mapGetters({
      user: "auth/userData",
      token: "auth/userToken",
    }),
  },
  watch: {
    currentPage() {
      this.getNotification();
    },
    filters: {
      handler() {
        this.getNotification();
      },
      deep: true,
    },
  },
  components: {
    BaseTableBodyPagination,
    BaseNotificationItem,
    BaseCircleLoad,
    BaseButton,
  },
});
</script>

<style scoped lang="scss">
$mobile-width: 480px;
$tablet-width: 768px;
.notifications {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;

  &__title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    text-align: center;
  }
  &__filter {
    display: flex;
    padding: 0rem;
    max-width: $tablet-width;
    justify-content: space-between;
    margin: auto;
    width: 100%;
    gap: 10px;
    @media screen and (max-width: $tablet-width) {
      flex-direction: column;
      gap: 5px;
      padding: 0 1.5rem;
    }
    &--status {
      display: flex;
      gap: 5px;
    }
  }
  &__content {
    flex: 1;
    display: flex;
    padding: 2rem;
    max-width: $tablet-width;
    margin: auto;
    &--result {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    &--noResult {
      margin: auto;
      text-align: center;
      font-size: 1.2rem;
      font-weight: 600;
    }
  }
}
</style>
