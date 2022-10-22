<template>
  <base-dialog
    :title="'Waring!!!'"
    :mode="'warning'"
    :open="isLogOutConfirm"
    @close="onCloseLogOutConfirm"
  >
    <template #default>
      <p>Are you sure you want to log out?</p>
    </template>
    <template #action>
      <div style="width: 25%">
        <base-button :mode="'warning'" @click="onConfirmLogOut"
          >Yes</base-button
        >
      </div>
      <div style="width: 25%">
        <base-button @click="onCloseLogOutConfirm">Cancel</base-button>
      </div>
    </template>
  </base-dialog>
  <header class="header">
    <div class="header__left">
      <button
        class="header__left--sidebar-toggle"
        @click="$emit('toggleLeftSideBar')"
      >
        <icon-menu />
      </button>
    </div>
    <div class="header__center">
      <div class="header__center--logo">
        <router-link class="header__center--logo" :to="{ name: 'home' }">
          <icon-logo></icon-logo>
        </router-link>
        <span class="header__center--logo-text">Swallowtail Music</span>
      </div>
    </div>
    <div class="header__right">
      <div class="header__right--darkmodeBtn">
        <darkmode-button />
      </div>
      <div class="header__right--account" @click="toggleAccountMenu">
        <div class="header__right--account--username">
          {{ userData.username }}
        </div>
        <div class="header__right--account--img">
          <img
            :src="
              userData.profile_photo_url
                ? userData.profile_photo_url
                : environment.default + '/default-avatar.jpg'
            "
            alt=""
            srcset=""
          />
        </div>
        <transition name="menu">
          <div class="header__right--account--menu" v-if="isAccountMenuActive">
            <div class="header__right--account--menu-item">
              <base-list-item>
                <a href="">Account</a>
              </base-list-item>
            </div>
            <div class="header__right--account--menu-item">
              <base-list-item>
                <router-link
                  :to="{
                    name: 'profilePage',
                    params: { id: userData.user_id },
                  }"
                  >Profile</router-link
                >
              </base-list-item>
            </div>
            <div class="header__right--account--menu-item">
              <base-list-item>
                <a href="">Settings</a>
              </base-list-item>
            </div>
            <hr />
            <div class="header__right--account--menu-item">
              <base-list-item>
                <a @click="logOutConfirm">Logout</a>
              </base-list-item>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import { mapGetters, mapMutations, mapActions } from "vuex";
import { environment } from "@/environment/environment";
import IconMenu from "../icons/IconMenu.vue";

export default defineComponent({
  components: {
    IconMenu,
  },
  name: "HomeViewHeader",
  data() {
    return {
      isAccountMenuActive: false,
      isLogOutConfirm: false,
      environment: environment,
    };
  },
  methods: {
    toggleAccountMenu() {
      this.isAccountMenuActive = !this.isAccountMenuActive;
    },
    logOutConfirm() {
      this.isLogOutConfirm = true;
    },
    onCloseLogOutConfirm() {
      this.isLogOutConfirm = false;
    },
    onConfirmLogOut() {
      this.isLogOutConfirm = false;
      this.logOut().then((res) => {
        if (res.status < 300) {
          this.clearUser();
          this.$router.push({ name: "login" });
        }
      });
    },
    ...mapActions({
      logOut: "auth/logOut",
    }),
    ...mapMutations({
      clearUser: "auth/clearUser",
    }),
  },
  computed: {
    ...mapGetters({
      userData: "auth/userData",
    }),
  },
  emits: ["toggleLeftSideBar"],
});
</script>
<style lang="scss">
$mobile-width: 480px;
$tablet-width: 768px;
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 40px;
  padding: 10px;
  background-color: transparent;
  border-bottom: 0.5px solid var(--background-color-secondary);
  &__left {
    display: flex;
    align-items: center;
    flex-grow: 1;
    flex-basis: 0;
    &--sidebar-toggle {
      width: 40px;
      height: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: transparent;
      border: none;
      padding: 0;
      border-radius: 999px;
      cursor: pointer;
      background-color: var(--background-color-secondary);
      opacity: 0.5;
      &:hover {
        opacity: 1;
      }
      & svg {
        width: 20px;
        height: 20px;
        fill: var(--text-primary-color);
      }
    }
  }
  &__center {
    display: flex;
    align-items: center;
    &--logo {
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bolder;
      font-size: 1.5rem;
      user-select: none;
      cursor: pointer;
      svg {
        border-radius: 999px;
        padding: 5px;
        width: 30px;
        height: 30px;
        margin-right: 5px;
        background: var(--color-primary);
        fill: var(--background-color-primary);
      }
    }
  }
  &__right {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-grow: 1;
    flex-basis: 0;
    &--darkmodeBtn {
      margin-left: 10px;
      width: 25px;
      height: 25px;
      overflow: hidden;
    }
    &--account {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      border-radius: 20px;
      margin-left: 10px;
      width: auto;
      height: 40px;
      position: relative;
      cursor: pointer;
      &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: var(--background-color-secondary);
        opacity: 1;
        z-index: -1;
        border-radius: 20px;
        transition: all 0.5s ease;
      }
      &:hover {
        &::before {
          background-color: var(--color-primary);
          opacity: 0.4;
        }
      }
      &.active {
        &::before {
          background-color: var(--color-primary);
          opacity: 0.4;
        }
      }
      &--username {
        margin-right: 10px;
        font-size: 1rem;
        font-weight: bolder;
        color: var(--text-primary-color);
        padding-left: 20px;
        white-space: nowrap;
        overflow: hidden;
        user-select: none;
      }
      &--img {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        overflow: hidden;
        border: 1.2px solid var(--color-primary);
        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }
      &--menu {
        position: absolute;
        display: block;
        top: 105%;
        right: 0px;
        width: 250px;
        border-radius: 10px;
        z-index: 20;
        padding: 10px 0px;
        background: var(--background-glass-color-primary);
        a {
          color: var(--text-primary-color);
        }
        hr {
          background-color: var(--text-primary-color);
          opacity: 0.3;
        }
        &-item {
          margin: 10px 0px;
          & a {
            width: 100%;
            text-align: center;
          }
        }
      }
    }
  }
}

@media (max-width: $mobile-width) {
  .header {
    &__center {
      &--logo {
        &-text {
          display: none;
        }
      }
    }
    &__right {
      &--account {
        &--username {
          display: none;
        }
      }
    }
  }
}

.menu-enter-from {
  opacity: 0;
  top: 55%;
}
.menu-enter-to {
  opacity: 1;
  top: 105%;
}
.menu-enter-active {
  transition: all 0.5s;
}
.menu-leave-from {
  opacity: 1;
  top: 105%;
}
.menu-leave-to {
  opacity: 0;
  top: 55%;
}
.menu-leave-active {
  transition: all 0.5s;
}
</style>
