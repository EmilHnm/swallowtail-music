<template>
  <div class="admin-wrapper">
    <div
      class="admin-nav"
      v-if="
        $route.name !== 'accountAdminDashboard' &&
        $route.name !== 'accountAdminStatisticsLog'
      "
    >
      <button @click="$router.replace({ name: 'accountAdminUser' })">
        <IconAvatarProfile />
      </button>
      <button @click="$router.replace({ name: 'accountAdminDisc' })">
        <IconDisk />
      </button>
      <button @click="$router.replace({ name: 'accountAdminArtists' })">
        <IconArtist />
      </button>
      <button @click="$router.replace({ name: 'accountAdminGenres' })">
        <IconTag />
      </button>
    </div>
    <RouterView />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapGetters } from "vuex";
import IconAvatarProfile from "@/components/icons/IconAvatarProfile.vue";
import IconDisk from "@/components/icons/IconDisk.vue";
import IconArtist from "@/components/icons/IconArtist.vue";
import IconTag from "@/components/icons/IconTag.vue";
export default defineComponent({
  created() {
    if (this.user.role === "") {
      this.$router.push({ name: "accountOverview", replace: true });
    }
  },
  computed: {
    ...mapGetters({
      user: "auth/userData",
    }),
  },
  components: { IconAvatarProfile, IconDisk, IconArtist, IconTag },
});
</script>

<style lang="scss" scoped>
.admin-wrapper {
  width: 100%;
  overflow-x: scroll;
  overflow-y: hidden;
}
.admin-nav {
  width: 100%;
  height: 50px;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-around;
  padding: 20px 0;
  & button {
    width: 20%;
    height: 100%;
    border: 2px solid var(--color-primary);
    background: none;
    cursor: pointer;
    &:hover {
      transition: all 0.2s;
      transform: scale(1.05);
    }
    svg {
      fill: var(--color-primary);
      stroke: var(--color-primary);
      height: 30px;
      width: 30px;
    }
  }
}
</style>
