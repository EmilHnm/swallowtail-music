<script lang="ts">
import HomeViewLeftSideBar from "../../components/HomeView/HomeViewLeftSideBar.vue";
import HomeViewRightSideBar from "../../components/HomeView/HomeViewRightSideBar.vue";
import HomeViewPlayer from "../../components/HomeView/HomeViewPlayer.vue";
import HomeViewHeader from "../../components/HomeView/HomeViewHeader.vue";
export default {
  name: "HomeView",
  components: {
    HomeViewLeftSideBar,
    HomeViewRightSideBar,
    HomeViewPlayer,
    HomeViewHeader,
  },
  data() {
    return {
      isLeftSideBarActive: false,
      isRightSideBarActive: false,
    };
  },
  methods: {
    toggleLeftSideBar() {
      this.isLeftSideBarActive = !this.isLeftSideBarActive;
    },
    toggleRightSideBar(value: boolean) {
      this.isRightSideBarActive = value;
    },
  },
};
</script>

<template>
  <HomeViewHeader @toggleLeftSideBar="toggleLeftSideBar" />
  <div class="main-body">
    <HomeViewLeftSideBar :isActive="isLeftSideBarActive" />
    <main><RouterView /></main>
    <HomeViewRightSideBar :isActive="isRightSideBarActive" />
  </div>
  <HomeViewPlayer @toggleRightSideBar="toggleRightSideBar" />
</template>
<style lang="scss" scoped>
.main-body {
  display: flex;
  position: relative;
  height: calc(100vh - 60px - 80px);
  main {
    flex: 1;
    overflow-y: auto;
  }
}
</style>
