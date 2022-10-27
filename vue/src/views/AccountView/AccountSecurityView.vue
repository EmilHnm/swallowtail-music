<template>
  <teleport to="body">
    <BaseFlatDialog
      :open="dialogWaring.show"
      :title="dialogWaring.title"
      :mode="dialogWaring.mode"
      @close="closeDialog"
    >
      <template #default>
        <p>{{ dialogWaring.content }}</p>
      </template>
    </BaseFlatDialog>
  </teleport>
  <div class="container">
    <h2>Security</h2>
    <div class="password">
      <h3>Change Password</h3>
      <form @submit.prevent="onSubmit">
        <div class="password__row">
          <label for="">Current Password</label>
          <input
            type="password"
            placeholder="Current Password"
            v-model="current_password"
          />
        </div>
        <div class="password__row">
          <label for="">New Password</label>
          <input
            type="password"
            placeholder="New Password"
            v-model="password"
          />
        </div>
        <div class="password__row">
          <label for="">Confirm New Password</label>
          <input
            type="password"
            placeholder="Confirm New Password"
            v-model="password_confirmation"
          />
        </div>
        <button type="submit">Change Password</button>
      </form>
    </div>
    <div class="authenticate">
      <h3>Logout On All Devices</h3>
      <p>This will sign you out on all devices you're signed in to.</p>
      <button @click="logOut">Log Out</button>
    </div>
  </div>
</template>

<script lang="ts">
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

export default defineComponent({
  data() {
    return {
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      current_password: "",
      password: "",
      password_confirmation: "",
    };
  },
  methods: {
    ...mapActions("account", ["updatePassword", "logOutAll"]),
    closeDialog() {
      this.dialogWaring.show = false;
    },
    onSubmit() {
      this.updatePassword({
        password: {
          current_password: this.current_password,
          password: this.password,
          password_confirmation: this.password_confirmation,
        },
        token: this.token,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            this.current_password = "";
            this.password = "";
            this.password_confirmation = "";
            this.dialogWaring = {
              title: "Success",
              mode: "success",
              content: "Password updated successfully",
              show: true,
            };
          } else {
            this.dialogWaring = {
              title: "Warning",
              mode: "warning",
              content: data.message,
              show: true,
            };
          }
        });
    },
    logOut() {
      this.logOutAll(this.token)
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            this.dialogWaring = {
              title: "Success",
              mode: "success",
              content: "Logged out on all devices",
              show: true,
            };
            let counter: number = 2;
            const interval = setInterval(() => {
              this.dialogWaring.content = `Logged out in ${counter} seconds`;
              counter--;
              if (counter < 0) {
                clearInterval(interval);
                this.$router.go(0);
              }
            }, 2000);
          } else {
            this.dialogWaring = {
              title: "Warning",
              mode: "warning",
              content: data.message,
              show: true,
            };
          }
        });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  components: { BaseFlatDialog },
});
</script>

<style lang="scss" scoped>
.container {
  width: 90%;
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
  .password {
    display: flex;
    flex-direction: column;
    &__row {
      width: 100%;
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
      label {
        font-size: 1rem;
        font-weight: 900;
        margin-bottom: 5px;
      }
      input {
        height: 40px;
        border: 1px solid var(--text-primary-color);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        padding: 0 10px;
        font-size: 1rem;
        &:focus {
          outline: none;
        }
      }
    }
  }
  .authenticate {
    display: flex;
    flex-direction: column;
    & p {
      padding: 20px 10px;
      background-color: var(--background-color-secondary);
    }
    & button {
      background: var(--color-danger);
    }
  }
  & button {
    height: 40px;
    background: var(--color-primary);
    color: #fff;
    padding: 0 20px;
    font-size: 1rem;
    border-radius: 20px;
    border: none;
    width: max-content;
    float: right;
    cursor: pointer;
    &:focus {
      outline: none;
    }
    &:hover {
      transform: scale(1.05);
      font-weight: 500;
    }
  }
}
</style>
