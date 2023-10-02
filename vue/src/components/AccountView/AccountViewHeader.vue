<template>
  <teleport to="body">
    <BaseFlatDialog
      :open="isLoading"
      :title="'Loading ...'"
      :mode="'announcement'"
    >
      <template #default>
        <BaseCircleLoad />
      </template>
      <template #action><div></div></template>
    </BaseFlatDialog>
  </teleport>
  <header class="header">
    <div
      class="header__menuToggle"
      :class="{ active: isSideBarActive }"
      @click="toggleSidebar"
    >
      <IconMenu />
    </div>
    <div class="header__logo">
      <router-link :to="{ name: 'mainPage' }">
        <div class="header__logo--icon">
          <IconLogo />
        </div>
        <div class="header__logo--text">
          <span>Swallowtail Music</span>
        </div>
      </router-link>
    </div>
    <div class="header__right">
      <div class="header__darkmode">
        <DarkModeButton />
      </div>
      <div class="header__account" @click="toggleMenu">
        <div class="header__account--avatar">
          <img
            v-lazyload
            :data-url="
              user.profile_photo_url
                ? `${environment.profile_image}/${user.profile_photo_url}`
                : 'http://127.0.0.1:5173/src/assets/default/default-avatar.jpg'
            "
            alt=""
            srcset=""
          />
        </div>
        <div class="header__account--text">Profile</div>
        <div class="header__account--toggleMenu">
          <IconDownArrow v-if="!menuActive" />
          <IconUpArrow v-else />
        </div>
        <ul class="header__account--menu" v-if="menuActive">
          <li @click="navigateToOverview">Account</li>
          <li @click="logout">Log Out</li>
        </ul>
      </div>
    </div>
  </header>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import { mapActions, mapGetters, mapMutations } from "vuex";
import IconLogo from "@/components/icons/IconLogo.vue";
import IconDownArrow from "@/components/icons/IconDownArrow.vue";
import IconUpArrow from "@/components/icons/IconUpArrow.vue";
import DarkModeButton from "../UI/DarkModeButton.vue";
import IconMenu from "@/components/icons/IconMenu.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";

export default defineComponent({
  props: ["isSideBarActive"],
  emits: ["toggleLeftSideBar"],
  data() {
    return {
      menuActive: false,
      environment: environment,
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("auth", ["logOut"]),
    ...mapMutations("auth", ["clearUser"]),
    toggleMenu() {
      this.menuActive = !this.menuActive;
    },
    toggleSidebar() {
      if (this.isSideBarActive) {
        this.$emit("toggleLeftSideBar", false);
      } else {
        this.$emit("toggleLeftSideBar", true);
      }
    },
    navigateToOverview() {
      this.$router.push({ name: "accountOverview" });
    },
    logout() {
      this.isLoading = true;
      this.logOut().then((res) => {
        if (res.status < 300) {
          this.isLoading = false;
          this.clearUser();
          this.$router.push({ name: "login" });
        }
      });
    },
  },
  computed: {
    ...mapGetters({
      user: "auth/userData",
    }),
  },
  components: {
    IconLogo,
    IconDownArrow,
    IconUpArrow,
    DarkModeButton,
    IconMenu,
    BaseFlatDialog,
    BaseCircleLoad,
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.header {
  height: 50px;
  background: var(--background-color-secondary);
  display: flex;
  justify-content: space-between;
  align-items: center;
  &__menuToggle {
    display: none;
    width: 30px;
    height: 30px;
    cursor: pointer;
    justify-content: center;
    align-items: center;
    padding: 5px;
    border-radius: 50%;
    background-color: var(--background-color-primary);
  }
  &__logo {
    display: flex;
    align-items: center;
    margin-left: 20px;
    & > a {
      display: flex;
      align-items: center;
    }
    &--icon {
      width: 40px;
      height: 40px;
      margin-right: 10px;
      border-radius: 50%;
      background: var(--color-primary);
      & svg {
        width: 100%;
        height: 100%;
        fill: #fff;
      }
    }
    &--text {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-primary-color);
    }
  }
  &__account {
    height: 40px;
    display: flex;
    border-radius: 20px;
    align-items: center;
    background: var(--background-glass-color-primary);
    margin: 0px 20px;
    position: relative;
    cursor: pointer;
    user-select: none;
    &--avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 10px;
      & img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    &--text {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-subbed);
    }
    &--toggleMenu {
      width: 40px;
      height: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-left: 10px;
      & svg {
        width: 20px;
        height: 20px;
        fill: var(--text-primary-color);
      }
    }
    &:hover {
      .header__account--text {
        color: var(--color-primary);
      }
    }
    &--menu {
      position: absolute;
      top: 50px;
      right: 0;
      width: 150px;
      background: var(--background-glass-color-primary);
      z-index: 100;
      & li {
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary-color);
        cursor: pointer;
        &:hover {
          background: var(--background-color-secondary);
          color: var(--color-primary);
        }
      }
      &::before {
        content: "";
        position: absolute;
        top: -10px;
        right: 10px;
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid var(--background-glass-color-primary);
      }
    }
  }
}
@media (max-width: $tablet-width) {
  .header {
    &__menuToggle {
      display: flex;
    }
    &__logo {
      &--text {
        display: none;
      }
    }
    &__account {
      &--text,
      &--toggleMenu {
        display: none;
      }
      &--avatar {
        margin-right: 0;
      }
    }
  }
}
</style>
