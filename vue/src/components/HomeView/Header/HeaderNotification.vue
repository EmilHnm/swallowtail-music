<template>
  <div class="header_notification" v-click-outside="() => (isActive = false)">
    <span class="header_notification--alert" v-show="hasUnreadMessage"></span>
    <button
      @click="toggle"
      :class="{
        active: isActive,
      }"
    >
      <IconBell></IconBell>
    </button>
    <div v-show="isActive" class="header_notification__board">
      <div class="board__header">
        <h2>Notification</h2>
        <div
          class="board__header--menu"
          v-click-outside="() => (isMenuActive = false)"
        >
          <button @click="toggleMenu">
            <IconThreeDots />
          </button>
          <div class="board__header--menu--option" v-show="isMenuActive">
            <ul>
              <li @click="onMarkAllAsRead">Mark all as read</li>
              <li @click="onSeeAll">See All</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="board__content" v-if="isGettingNotification">
        <BaseCircleLoadVue />
      </div>
      <div class="board__content" v-else>
        <div v-if="notifications.length > 0">
          <BaseNotificationItem
            v-for="notification in notifications"
            :notification="notification"
            :key="notification.id"
            @mark-as-read="markAsRead"
            @delete="onDelete"
          />
        </div>
        <div v-else>
          <p>No notification</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapGetters, mapActions } from "vuex";
import type {
  notification,
  notificationPagination,
} from "@/model/notificationModel";
import IconBell from "@/components/icons/IconBell.vue";
import IconThreeDots from "@/components/icons/IconThreeDots.vue";
import { DateService } from "@/mixins/DateService";
import BaseCircleLoadVue from "@/components/UI/BaseCircleLoad.vue";
import { environment } from "@/environment/environment";
import BaseNotificationItem from "@/components/UI/BaseNotificationItem.vue";
export default defineComponent({
  data() {
    return {
      isActive: false,
      isGettingNotification: true,
      isMenuActive: false,
      dateService: new DateService(),
      hasUnreadMessage: false,
      notifications: [] as notification[],
    };
  },
  methods: {
    ...mapActions("notification", ["index", "hasUnread", "markAllAsRead"]),
    toggle() {
      if (window.innerWidth < 480) {
        this.isMenuActive = false;
        this.$router.push({ name: "notificationPage" });
      }
      this.isActive = !this.isActive;
    },
    toggleMenu() {
      this.isMenuActive = !this.isMenuActive;
    },
    getNotification() {
      this.isGettingNotification = true;
      this.index({
        token: this.token,
        page: 1,
      })
        .then((res: any) => res.json())
        .then((res: notificationPagination) => {
          this.notifications = res.data;
          this.isGettingNotification = false;
        });
    },
    getHasUnread() {
      this.hasUnread({
        token: this.token,
      })
        .then((res: any) => res.json())
        .then((res: { hasUnread: number }) => {
          if (res.hasUnread > 0) {
            this.hasUnreadMessage = true;
          } else {
            this.hasUnreadMessage = false;
          }
        });
    },
    markAsRead(id: string) {
      let notification = this.notifications.find((n) => n.id === id);
      if (notification) {
        notification.read_at = new Date().toDateString();
      }
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
            this.notifications.forEach((n) => {
              n.read_at = new Date().toDateString();
            });
            this.isGettingNotification = false;
          }
        });
    },
    onSeeAll() {
      this.$router.push({ name: "notificationPage" });
    },
  },
  computed: {
    ...mapGetters({
      user: "auth/userData",
      token: "auth/userToken",
    }),
  },
  watch: {
    isActive(val) {
      if (val) {
        if (this.notifications.length === 0) this.getNotification();
      }
    },
  },
  created() {
    const echo = window.Echo;
    echo
      .channel(
        `swallowtail_music_database_private-${environment.notification_channel}.${this.user.user_id}`
      )
      .listen(`.${environment.notification_channel}`, () => {
        this.getNotification();
        this.hasUnreadMessage = true;
      });
  },
  mounted() {
    this.getHasUnread();
  },
  unmounted() {
    const echo = window.Echo;
    echo.leave(
      `swallowtail_music_database_private-${environment.notification_channel}.${this.user.user_id}`
    );
  },
  components: {
    IconBell,
    IconThreeDots,
    BaseCircleLoadVue,
    BaseNotificationItem,
  },
});
</script>

<style scoped lang="scss">
$mobile-width: 480px;
$tablet-width: 768px;
.header_notification {
  position: relative;
  height: max-content;
  &--alert {
    position: absolute;
    top: 0;
    right: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #ff0000;
  }
  button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px;
    display: flex;
    align-items: center;
    &:hover {
      svg {
        fill: var(--color-primary);
      }
    }
    svg {
      fill: var(--text-subdued);
    }
    &.active {
      svg {
        fill: var(--color-primary);
      }
    }
  }

  &__board {
    position: absolute;
    top: 130%;
    right: 0;
    z-index: 100;
    background-color: var(--background-blur-color-primary);
    backdrop-filter: blur(50px);
    width: 100vw;
    max-width: 450px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px var(--color-primary);
    padding: 10px;
    @media screen and (max-width: $mobile-width) {
      & {
        display: none;
      }
    }
    .board__header {
      background-color: var(--background-blur-color-primary);
      border-radius: 5px;
      display: flex;
      align-items: center;
      padding: 10px 20px;
      margin-bottom: 10px;
      h2 {
        margin: 0;
        flex: 1;
      }
      &--menu {
        position: relative;
        button {
          border-radius: 50%;
          padding: 5px;
          &:hover {
            background-color: var(--background-glass-color-primary);
          }
          svg {
            width: 20px;
            height: 20px;
          }
        }
        &--option {
          position: absolute;
          top: 110%;
          right: 0;
          width: max-content;
          background-color: var(--background-blur-color-primary);
          backdrop-filter: blur(50px);
          border-radius: 5px;
          z-index: 10;
          ul {
            padding: 10px;
            li {
              padding: 5px 15px;
              border-radius: 3px;
              cursor: pointer;
              margin-bottom: 5px;
              &:hover {
                text-shadow: 0px 0px 10px var(--color-primary);
                background-color: var(--background-blur-color-primary);
              }
            }
          }
        }
      }
    }
    .board__content {
      background-color: var(--background-glass-color-primary);
      border-radius: 5px;
      padding: 10px;
      max-height: 40vh;
      overflow-y: scroll;
    }
  }
}
</style>
