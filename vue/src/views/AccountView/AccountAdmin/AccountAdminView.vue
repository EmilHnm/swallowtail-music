<template>
  <div class="admin-wrapper">
    <div class="admin-nav" v-if="$route.name !== 'accountAdminDashboard'">
      <button><IconAvatarProfile /></button>
      <button><IconDisk /></button>
      <button><IconArtist /></button>
      <button><IconTag /></button>
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
    if (this.user.role !== "Admin") {
      this.$router.push({ name: "accountOverview" });
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
