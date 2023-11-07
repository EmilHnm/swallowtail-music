<template>
  <div
    class="notification"
    :class="{
      unread: notification.read_at == null,
    }"
  >
    <div v-show="isLoading" class="notification__loading"></div>
    <div class="notification__body">
      <p
        class="notification__body--message"
        v-html="notification.data.message"
      ></p>
      <p class="notification__body--timestamp">
        {{ dateService.dateDiffForHuman(notification.created_at) }}
      </p>
    </div>
    <div
      class="notification__option"
      v-click-outside="() => (isMenuActive = false)"
    >
      <button @click="isMenuActive = !isMenuActive">
        <IconThreeDots />
      </button>
      <ul v-show="isMenuActive" class="notification__option--menu">
        <li @click="onMarkAsRead">Mark as Read</li>
        <li @click="onDelete">Delete</li>
      </ul>
    </div>
  </div>
</template>

<script lang="ts">
import type { notification } from "@/model/notificationModel";
import { defineComponent } from "vue";
import { DateService } from "@/mixins/DateService";
import IconThreeDots from "@/components/icons/IconThreeDots.vue";
import { mapActions, mapGetters } from "vuex";
export default defineComponent({
  props: {
    notification: {
      type: Object as () => notification,
      required: true,
    },
  },
  data() {
    return {
      dateService: new DateService(),
      isMenuActive: false,
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("notification", ["markAsRead", "delete"]),
    onMarkAsRead() {
      this.isMenuActive = false;
      this.isLoading = true;
      this.markAsRead({ token: this.token, id: this.notification.id })
        .then((res) => res.json())
        .then((data: { message: "success" | "failed" }) => {
          if (data.message === "success") {
            this.$emit("mark-as-read", this.notification.id);
          }
          this.isLoading = false;
        });
    },
    onDelete() {
      this.isMenuActive = false;
      this.isLoading = true;
      this.delete({ token: this.token, id: this.notification.id })
        .then((res) => res.json())
        .then((data: { message: "success" | "failed" }) => {
          if (data.message === "success") {
            this.$emit("delete");
          }
          this.isLoading = false;
        });
    },
  },
  computed: {
    ...mapGetters({ token: "auth/userToken" }),
  },
  components: { IconThreeDots },
});
</script>

<style scoped lang="scss">
.notification {
  margin-bottom: 10px;
  background-color: var(--background-blur-color-primary);
  padding: 10px;
  border-radius: 5px;
  cursor: default;
  display: flex;
  position: relative;
  width: 100%;
  &.unread {
    background-color: var(--color-primary);
  }
  &:hover {
    box-shadow: 0px 0px 5px var(--color-primary);
  }
  &__loading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 5px;
    backdrop-filter: blur(50px);
    background-color: #ffffff80;
    z-index: 10;
  }
  &__body {
    &--message {
      margin: auto;
      font-size: 1rem;
      color: var(--text-primary-color);
    }
    &--timestamp {
      margin: auto;
      font-size: 0.8rem;
      color: var(--text-subdued);
    }
  }
  &__option {
    width: 20%;
    max-width: 30px;
    height: max-content;
    position: relative;
    button {
      background: none;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      &:hover {
        background-color: var(--background-blur-color-primary);
      }
      svg {
        width: 20px;
        height: 20px;
        fill: var(--text-subdued);
      }
    }
    &--menu {
      position: absolute;
      top: 102%;
      right: 5px;
      border-radius: 10px;
      width: max-content;
      background-color: var(--background-blur-color-primary);
      backdrop-filter: blur(50px);
      overflow: hidden;
      padding: 5px;
      z-index: 1;
      li {
        padding: 5px;
        cursor: pointer;
        border-radius: 3px;
        user-select: none;
        &:hover {
          background-color: var(--background-blur-color-primary);
        }
      }
    }
  }
}
</style>
