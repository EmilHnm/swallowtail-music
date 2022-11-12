<template>
  <RouterView />
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapGetters } from "vuex";

export default defineComponent({
  setup() {
    return {};
  },
  created() {
    console.log(this.user);
    if (this.user.role !== "Admin") {
      this.$router.push({ name: "accountOverview" });
    }
  },
  computed: {
    ...mapGetters({
      user: "auth/userData",
    }),
  },
});
</script>

<style scoped></style>
