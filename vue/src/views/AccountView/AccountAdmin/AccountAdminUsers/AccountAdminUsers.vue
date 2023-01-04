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
    <BaseFlatDialog
      :open="dialogUserDelete.show"
      :title="dialogUserDelete.title"
      :mode="dialogUserDelete.mode"
      @close="closeDialogUserDelete"
    >
      <template #default>
        <p>{{ dialogUserDelete.content }}</p>
      </template>
      <template #action>
        <button class="danger" @click="onDeleteUser">Delete</button>
        <button class="" @click="closeDialogUserDelete">Close</button>
      </template>
    </BaseFlatDialog>
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
  <div>
    <div class="main" ref="main">
      <h3>Users Management</h3>
      <div class="data">
        <div class="filter">
          <label for="filterText"><IconSearch /></label>
          <input
            type="text"
            name="filterText"
            id="filterText"
            v-model="filterText"
          />
        </div>
        <table>
          <thead>
            <tr>
              <td>#</td>
              <td>User Id</td>
              <td>User Name</td>
              <td v-if="mainWidth > 600">Type</td>
              <td v-if="mainWidth > 500">Join Date</td>
              <td>Control</td>
            </tr>
          </thead>
          <tbody v-if="!filterText">
            <tr v-for="(userData, index) in users" :key="userData.user_id">
              <td>{{ index + 1 }}</td>
              <td>{{ userData.user_id }}</td>
              <td>{{ userData.name }}</td>
              <td v-if="mainWidth > 600">
                {{ userData.role ? userData.role : "User" }}
              </td>
              <td v-if="mainWidth > 500">
                {{ new Date(userData.created_at).toLocaleDateString() }}
              </td>
              <td>
                <button class="show" @click="showUser(userData.user_id)">
                  Show
                </button>
                <button
                  class="delete"
                  @click="deleteUserConfirm(userData.user_id)"
                  v-if="userData.user_id !== user.user_id"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr
              v-for="(userData, index) in filteredUser"
              :key="userData.user_id"
            >
              <td>{{ index + 1 }}</td>
              <td>{{ userData.user_id }}</td>
              <td>{{ userData.name }}</td>
              <td v-if="mainWidth > 600">
                {{ userData.role ? userData.role : "User" }}
              </td>
              <td v-if="mainWidth > 500">
                {{ new Date(userData.created_at).toLocaleDateString() }}
              </td>
              <td>
                <button class="show" @click="showUser(userData.user_id)">
                  Show
                </button>
                <button
                  class="delete"
                  @click="deleteUserConfirm(userData.user_id)"
                  v-if="userData.user_id !== user.user_id"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import IconSearch from "@/components/icons/IconSearch.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import type { user } from "@/model/userModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    filteredUser: user[];
    user: user;
  }
}

export default defineComponent({
  data() {
    return {
      users: [] as user[],
      filterText: "",
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      dialogUserDelete: {
        title: "Warning",
        mode: "warning",
        user_id: "",
        content: `Are you sure you want to delete this user?`,
        show: false,
      },
      isLoading: false,
      observer: null as ResizeObserver | null,
      mainWidth: 0,
    };
  },
  methods: {
    ...mapActions("admin", ["getAllUsers", "deleteUser"]),
    showUser(id: string) {
      this.$router.push({ name: "accountAdminUserEdit", params: { id: id } });
    },
    deleteUserConfirm(user_id: string) {
      this.dialogUserDelete.user_id = user_id;
      this.dialogUserDelete.show = true;
    },
    onDeleteUser() {
      this.dialogUserDelete.show = false;
      this.isLoading = true;
      this.deleteUser({
        token: this.token,
        user_id: this.dialogUserDelete.user_id,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.dialogWaring.title = "Success";
            this.dialogWaring.mode = "announcement";
            this.dialogWaring.content = `Delete ${this.dialogUserDelete.user_id} success`;
            this.dialogWaring.show = true;
            this.dialogUserDelete.user_id = "";
            this.loadUsers();
          } else {
            this.dialogWaring.title = "Error";
            this.dialogWaring.mode = "error";
            this.dialogWaring.content = res.message;
            this.dialogWaring.show = true;
          }
        });
    },
    loadUsers() {
      this.isLoading = true;
      this.getAllUsers(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.users = res.users;
          }
        });
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    closeDialogUserDelete() {
      this.dialogUserDelete.show = false;
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      user: "auth/userData",
    }),
    filteredUser(): user[] {
      return this.users.filter((user) => {
        console.log(user.user_id);
        return (
          user.name.toLowerCase().includes(this.filterText.toLowerCase()) ||
          user.user_id
            .toString()
            .toLowerCase()
            .includes(this.filterText.toLowerCase())
        );
      });
    },
  },
  created() {
    this.loadUsers();
  },
  mounted() {
    const main = this.$refs.main as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.mainWidth = entry.contentRect.width;
      }
    });
    if (this.observer && main) this.observer.observe(main);
  },
  unmounted() {
    if (this.observer) this.observer.disconnect();
  },
  components: { IconSearch, BaseLineLoad, BaseFlatDialog },
});
</script>

<style lang="scss" scoped>
.main {
  width: 100%;
  display: flex;
  flex-direction: column;
  h3 {
    text-align: center;
  }
  .data {
    & .filter {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      align-self: center;
      padding: 20px;
      label {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% 0% 0% 50%;
        padding: 5px;
        background-color: var(--color-primary);
        svg {
          width: 20px;
          height: 20px;
          fill: #fff;
        }
      }
      input {
        height: 22px;
        border-radius: 0% 20px 20px 0%;
        padding: 5px;
        border: 1px solid var(--color-primary);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        font-size: 1.2rem;
        font-weight: 500;
        &:focus {
          outline: none;
        }
      }
    }
    table {
      width: 90%;
      margin: 0 auto;
      thead {
        tr {
          td {
            padding: 10px;
            font-weight: 900;
            font-size: 1rem;
            background: var(--background-glass-color-primary);
            text-align: center;
            cursor: pointer;
            user-select: none;
          }
        }
      }
      tbody {
        tr {
          td {
            padding: 10px;
            font-size: 1rem;
            background: var(--background-glass-color-secondary);
            text-align: center;
            border-bottom: 1px solid var(--background-glass-color-primary);
            img {
              width: 50px;
              height: 50px;
              object-fit: cover;
              user-select: none;
            }
            button {
              padding: 5px 10px;
              background: transparent;
              font-size: 1rem;
              cursor: pointer;
              transition: all 0.3s ease-in-out;
              margin: 5px 2px;
              &.show {
                border: 1px solid var(--color-primary);
                color: var(--color-primary);
                &:focus {
                  outline: none;
                }
                &:hover {
                  background: var(--color-primary);
                  color: var(--background-color-secondary);
                }
              }
              &.delete {
                border: 1px solid var(--color-danger);
                color: var(--color-danger);
                &:focus {
                  outline: none;
                }
                &:hover {
                  background: var(--color-danger);
                  color: var(--background-color-secondary);
                }
              }
            }
          }
        }
      }
    }
  }
}
button {
  padding: 5px 10px;
  background: transparent;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
  margin: 5px 2px;
  border: 1px solid var(--color-primary);
  color: var(--color-primary);
  &:focus {
    outline: none;
  }
  &:hover {
    background: var(--color-primary);
    color: var(--background-color-secondary);
  }

  &.danger {
    border: 1px solid var(--color-danger);
    color: var(--color-danger);
    &:focus {
      outline: none;
    }
    &:hover {
      background: var(--color-danger);
      color: var(--background-color-secondary);
    }
  }
}
</style>
