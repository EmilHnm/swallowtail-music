<template>
  <teleport to="body">
    <BaseFlatDialog
      :open="isLoading"
      :title="'Loading ...'"
      :mode="'announcement'"
    >
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseFlatDialog>
  </teleport>
  <div class="user-container">
    <div class="details">
      <h2>User Overview</h2>
      <div class="user">
        <div class="user__row">
          <span class="lab">User Id</span>
          <span class="content">{{ user ? user.user_id : "" }}</span>
        </div>
        <div class="user__row">
          <span class="lab">User Name</span>
          <span class="content">{{ user ? user.name : "" }}</span>
        </div>
        <div class="user__row">
          <span class="lab">Email</span>
          <span class="content">{{ user ? user.email : "" }}</span>
        </div>
        <div class="user__row">
          <span class="lab">Gender</span>
          <span class="content">{{
            user ? (user.gender ? user.gender : "Unset") : ""
          }}</span>
        </div>
        <div class="user__row">
          <span class="lab">Date of Birth</span>
          <span class="content">{{
            user ? (user.dob ? user.dob : "Unset") : ""
          }}</span>
        </div>
        <div class="user__row">
          <span class="lab">Region</span>
          <span class="content">{{
            user ? (user.dob ? user.region : "Unset") : ""
          }}</span>
        </div>
        <div class="user__row">
          <span class="lab">Join Date</span>
          <span class="content">{{
            user ? new Date(user.created_at).toLocaleDateString() : ""
          }}</span>
        </div>
      </div>
    </div>
    <div class="upload">
      <div class="upload__nav">
        <button @click="changeComponent('AccountAdminUserSong')">Songs</button>
        <button @click="changeComponent('AccountAdminUserAlbum')">
          Albums
        </button>
      </div>
      <keep-alive>
        <component :is="componentName"></component>
      </keep-alive>
    </div>
  </div>
</template>

<script lang="ts">
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import AccountAdminUserSong from "./AccountAdminUserSong.vue";
import AccountAdminUserAlbum from "./AccountAdminUserAlbum.vue";
import type { user } from "@/model/userModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

export default defineComponent({
  data() {
    return {
      componentName: "AccountAdminUserSong",
      isLoading: false,
      user: {} as user,
    };
  },
  methods: {
    ...mapActions("admin", ["getUser", "getUserUploadSong"]),
    loadUser() {
      this.isLoading = true;
      this.getUser({
        token: this.token,
        user_id: this.$route.params.id,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") this.user = res.user;
        });
    },

    changeComponent(componentName: string) {
      this.componentName = componentName;
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.loadUser();
  },
  components: {
    BaseFlatDialog,
    BaseLineLoad,
    AccountAdminUserSong,
    AccountAdminUserAlbum,
  },
});
</script>

<style lang="scss" scoped>
.user-container {
  height: calc(100% - 90px);
  width: 100%;
  overflow-y: scroll;
  .details {
    width: 90%;
    max-height: 100%;
    display: flex;
    flex-direction: column;
    padding: 20px;
    margin: auto;
    h2 {
      margin-bottom: 20px;
      font-weight: 900;
      font-size: 2rem;
    }
    .user {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      .user__row {
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
  .upload {
    width: 95%;
    display: flex;
    flex-direction: column;
    padding: 20px;
    &__nav {
      width: 100%;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      button {
        width: 100%;
        height: 50px;
        border: none;
        outline: none;
        background: var(--color-primary-blur);
        color: var(--text-primary-color);
        font-size: 1.2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        &:hover {
          background: var(--color-primary);
        }
      }
    }
  }
}
</style>
