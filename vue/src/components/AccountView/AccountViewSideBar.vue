<template>
  <nav :class="{ active: isSideBarActive }">
    <img
      v-lazyload
      :data-url="
        user.profile_photo_url
          ? `${environment.profile_image}/${user.profile_photo_url}`
          : `${environment.default}/default-avatar.jpg`
      "
      alt=""
    />
    <ul class="menu">
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountOverview', replace: true }">
          <IconHome />
          <span>Overview</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountProfile', replace: true }">
          <IconBallPen />
          <span>Profile</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountAvatar', replace: true }">
          <IconAvatarProfile />
          <span>Avatar</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountSecurity', replace: true }">
          <IconSecurity />
          <span>Security</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountUpload', replace: true }">
          <IconDisk />
          <span>Upload Management</span>
        </router-link>
      </li>
      <li @click="closeSideBar">
        <router-link :to="{ name: 'accountRequest', replace: true }">
          <IconRequest />
          <span>Request Management</span>
        </router-link>
      </li>
    </ul>
  </nav>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import IconHome from "@/components/icons/IconHome.vue";
import IconBallPen from "@/components/icons/IconBallPen.vue";
import IconAvatarProfile from "@/components/icons/IconAvatarProfile.vue";
import IconSecurity from "@/components/icons/IconSecurity.vue";
import IconDisk from "@/components/icons/IconDisk.vue";
import { mapGetters } from "vuex";
import IconRequest from "@/components/icons/IconRequest.vue";
export default defineComponent({
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
    IconRequest,
  },
});
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
  overflow: scroll;
  height: calc(100vh - 150px);
  @media (max-width: $tablet-width) {
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
      background: var(--background-color-primary);
    }
  }
  &::-webkit-scrollbar {
    display: none;
  }
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
</style>
