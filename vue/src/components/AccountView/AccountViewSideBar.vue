<template>
  <nav :class="{ active: isSideBarActive }">
    <img
      :src="
        user.profile_photo_url
          ? `${environment.profile_image}/${user.profile_photo_url}`
          : 'http://127.0.0.1:5173/src/assets/default/default-avatar.jpg'
      "
      alt=""
    />
    <ul class="menu">
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountOverview' }">
          <IconHome />
          <span>Overview</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountProfile' }">
          <IconBallPen />
          <span>Profile</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountAvatar' }">
          <IconAvatarProfile />
          <span>Avatar</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountSecurity' }">
          <IconSecurity />
          <span>Security</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountUpload' }">
          <IconDisk />
          <span>Upload Management</span>
        </router-link>
      </li>
    </ul>
  </nav>
</template>

<script lang="ts">
import { environment } from "@/environment/environment";

import IconHome from "../icons/IconHome.vue";
import IconBallPen from "../icons/IconBallPen.vue";
import IconAvatarProfile from "../icons/IconAvatarProfile.vue";
import IconSecurity from "../icons/IconSecurity.vue";
import IconDisk from "../icons/IconDisk.vue";
import { mapGetters } from "vuex";
export default {
  props: {
    isSideBarActive: Boolean,
  },
  data() {
    return {
      environment: environment,
    };
  },
  methods: {
    closeSideBar() {
      this.$emit("closeSideBar", false);
    },
  },
  computed: {
    ...mapGetters({
      user: "auth/userData",
    }),
  },
  components: {
    IconHome,
    IconBallPen,
    IconAvatarProfile,
    IconSecurity,
    IconDisk,
  },
};
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
nav {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem 0;
  width: 25%;
  flex: 0 0 auto;
  overflow: hidden;
  height: calc(100vh - 150px);
  img {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    object-fit: cover;
    margin: 1.6rem auto;
  }
  ul {
    list-style: none;
    width: 100%;
    li {
      display: flex;
      align-items: center;
      padding: 1rem;
      border-left: 6px solid transparent;
      border-bottom: 1px solid rgba(0, 0, 0, 0.2);
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      width: 100%;
      cursor: pointer;
      a {
        display: flex;
        align-items: center;
        width: 100%;
        height: 100%;
        color: var(--text-color-primary);
      }
      svg {
        width: 1.2rem;
        height: 1.2rem;
        margin-right: 1rem;
        fill: var(--text-primary-color);
        stroke: var(--text-primary-color);
      }
      span {
        font-size: 1.3rem;
        color: var(--text-subdued);
      }
      &:hover {
        border-left: 6px solid var(--color-primary);
      }
      &:has(.router-link-active) {
        border-left: 6px solid var(--color-primary);
        span {
          color: var(--text-primary-color);
        }
      }
    }
  }
}

@media (max-width: $mobile-width) {
  nav {
    flex-basis: 100%;
    width: 100%;
    position: absolute;
    top: 71px;
    left: 0;
    backdrop-filter: blur(10px);
    z-index: 100;
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    &.active {
      transform: translateX(0);
    }
  }
}
</style>
