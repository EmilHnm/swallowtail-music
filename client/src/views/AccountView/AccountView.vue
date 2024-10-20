<template>
  <div class="account">
    <account-view-header
      :isSideBarActive="isSideBarActive"
      @toggleLeftSideBar="toggleSideBar"
    />
    <div class="wrapper">
      <account-view-side-bar
        :isSideBarActive="isSideBarActive"
        @closeSideBar="toggleSideBar"
      />
      <main>
        <router-view />
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import AccountViewHeader from "@/components/AccountView/AccountViewHeader.vue";
import AccountViewSideBar from "@/components/AccountView/AccountViewSideBar.vue";
export default defineComponent({
  name: "AccountView",
  components: { AccountViewHeader, AccountViewSideBar },
  data() {
    return {
      isSideBarActive: false,
    };
  },
  methods: {
    toggleSideBar(status: boolean) {
      this.isSideBarActive = status;
    },
  },
  computed: {},
  mounted() {},
});
</script>

<style lang="scss" scoped>
$screen-width: 1280px;
.account {
  width: 100vw;
}
.wrapper {
  display: flex;
  width: 80%;
  margin: auto;
  background: var(--background-glass-color-primary);
  height: calc(100vh - 80px);
  main {
    flex: 1;
    box-sizing: border-box;
  }
  @media screen and (max-width: $screen-width) {
    width: 100%;
  }
}
</style>
