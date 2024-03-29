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
    <BaseFlatDialog
      :open="alertDialog.show"
      :title="alertDialog.title"
      :mode="alertDialog.mode"
      @close="onCloseDialog"
      >{{ alertDialog.message }}</BaseFlatDialog
    >
  </teleport>
  <div class="container">
    <h2>Account Overview</h2>
    <div class="accounts">
      <div class="account__row">
        <span class="lab">User Id</span>
        <span class="content">{{ user ? user.user_id : "" }}</span>
      </div>
      <div class="account__row">
        <span class="lab">User Name</span>
        <span class="content">{{ user ? user.name : "" }}</span>
      </div>
      <div class="account__row">
        <span class="lab">Email</span>
        <span class="content">{{ user ? user.email : "" }}</span>
      </div>
      <div class="account__row">
        <span class="lab">Gender</span>
        <span class="content">{{
          user ? (user.gender ? user.gender : "Unset") : ""
        }}</span>
      </div>
      <div class="account__row">
        <span class="lab">Date of Birth</span>
        <span class="content">{{
          user ? (user.dob ? user.dob : "Unset") : ""
        }}</span>
      </div>
      <div class="account__row">
        <span class="lab">Region</span>
        <span class="content">{{
          user ? (user.dob ? user.region : "Unset") : ""
        }}</span>
      </div>
      <div class="account__row">
        <span class="lab">Join Date</span>
        <span class="content">{{ user ? user.created_at : "" }}</span>
      </div>
      <div class="account__row">
        <span class="lab">Email Verified</span>
        <span class="content">
          <span v-if="user.email_verified_at != null">{{
            new Date(user.email_verified_at).toLocaleDateString()
          }}</span>
          <BaseButton @click="onVerify" v-else>Click here to verify</BaseButton>
        </span>
      </div>
    </div>
    <router-link :to="{ name: 'accountProfile' }">Edit Profile</router-link>
  </div>
</template>

<script lang="ts">
import type { user } from "@/model/userModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
export default defineComponent({
  data() {
    return {
      user: {} as user,
      isLoading: false,
      alertDialog: {
        title: "",
        message: "",
        show: false,
        mode: "announcement",
      },
    };
  },
  methods: {
    ...mapActions("auth", ["checkAuth", "emailVerification"]),
    onVerify() {
      this.isLoading = true;
      this.emailVerification(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status == "success") {
            this.alertDialog = {
              title: res.status,
              message: res.message,
              show: true,
              mode: "announcement",
            };
          } else {
            this.alertDialog = {
              title: res.status,
              message: res.message,
              show: true,
              mode: "danger",
            };
            this.onLoadUser();
          }
        });
    },
    onLoadUser() {
      this.isLoading = true;
      this.checkAuth()
        .then((res) => res.json())
        .then((res) => {
          this.user = res;
          if (this.user)
            this.user.created_at = new Date(res.created_at).toLocaleDateString(
              "en-US"
            );
          this.isLoading = false;
        });
    },
    onCloseDialog() {
      this.alertDialog.show = false;
    },
  },
  created() {
    this.onLoadUser();
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  components: { BaseFlatDialog, BaseCircleLoad, BaseButton },
});
</script>

<style lang="scss" scoped>
.container {
  max-height: 100%;
  display: flex;
  flex-direction: column;
  overflow: scroll;
  padding: 20px;
  h2 {
    margin-bottom: 20px;
    font-weight: 900;
    font-size: 2rem;
  }
  a {
    margin-top: 20px;
    font-size: 1.2rem;
    color: var(--text-primary-color);
    padding: 10px 20px;
    border: 1px solid var(--text-primary-color);
    width: max-content;
    border-radius: 35px;
    text-decoration: none;
    &:hover {
      transform: scale(1.05);
      border: 1px solid var(--color-primary);
    }
  }
  .accounts {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    .account__row {
      width: 100%;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      padding: 10px 0px;
      border-bottom: 1px solid #ccc;
      .lab {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-subdued);
        user-select: none;
      }
      .content {
        font-size: 1.2rem;
        font-weight: 400;
        color: var(--text-primary-color);
      }
    }
  }
}
</style>
