<template>
  <div class="main">
    <BaseFlatDialog
      :open="dialog.show"
      :title="dialog.title"
      :mode="dialog.mode"
      @close="closeDialog"
    >
      <template #default v-if="!isRequesting">
        {{ dialog.message }}
      </template>
      <template #default v-else>
        <BaseLineLoad />
      </template>
      <template #action> <div v-if="isRequesting"></div> </template>
    </BaseFlatDialog>
    <div class="title">
      <h1>Create new request</h1>
    </div>
    <form class="form" @submit.prevent="onSubmit">
      <div class="form-group">
        <label for="type">Type</label>
        <select name="type" id="type" v-model="form.type">
          <option value="" disabled selected>Select type</option>
          <option value="genre">Genre</option>
          <option value="artist">Author</option>
          <option value="song">Song</option>
          <option value="album">Album</option>
        </select>
      </div>
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" v-model="form.name" />
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea
          type="text"
          id="description"
          cols="30"
          rows="10"
          v-model="form.description"
        ></textarea>
      </div>
      <div class="form-row">
        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
        <button type="button" @click="onCancel">Cancel</button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

export default defineComponent({
  data() {
    return {
      form: {
        type: "",
        name: "",
        description: "",
      },
      dialog: {
        show: false,
        title: "",
        mode: "warning", // "warning", "error", "success", "info
        message: "",
      },
      isRequesting: false,
    };
  },
  methods: {
    ...mapActions("request", ["create"]),
    onSubmit() {
      if (this.form.type === "") {
        this.dialog.show = true;
        this.dialog.title = "Error";
        this.dialog.mode = "error";
        this.dialog.message = "Please select a type";
        return;
      }
      if (this.form.name === "") {
        this.dialog.show = true;
        this.dialog.title = "Error";
        this.dialog.mode = "error";
        this.dialog.message = "Please enter a name";
        return;
      }
      if (this.form.description === "") {
        this.dialog.show = true;
        this.dialog.title = "Error";
        this.dialog.mode = "error";
        this.dialog.message = "Please enter a description";
        return;
      }
      this.showLoadingDialog();
      this.create({ ...this.form, token: this.userToken })
        .then((res: Response) => {
          if (res.status >= 400) {
            this.showErrorMessage(res.statusText);
            return;
          }
          this.showSuccessDialog();
        })
        .catch((err) => {
          this.showErrorMessage(err.message);
        });
    },
    showSuccessDialog() {
      this.dialog.show = true;
      this.dialog.title = "Success";
      this.dialog.mode = "success";
      this.dialog.message = "Request created successfully";
      this.isRequesting = false;
    },
    showLoadingDialog() {
      this.dialog.show = true;
      this.dialog.title = "Loading";
      this.dialog.mode = "info";
      this.dialog.message = "Creating request...";
      this.isRequesting = true;
    },
    showErrorMessage(message: string) {
      this.isRequesting = false;
      this.dialog.show = true;
      this.dialog.title = "Error";
      this.dialog.mode = "error";
      this.dialog.message = message;
    },
    closeDialog() {
      this.dialog.show = false;
      this.dialog.title = "";
      this.dialog.mode = "";
      this.dialog.message = "";
      this.isRequesting = false;
    },
    onCancel() {
      this.$router.push({ name: "accountRequestManagement" });
    },
  },
  components: { BaseFlatDialog, BaseLineLoad },
  computed: {
    ...mapGetters({
      userToken: "auth/userToken",
    }),
  },
});
</script>

<style lang="scss" scoped>
.main {
  width: 100%;
  display: flex;
  flex-direction: column;
  padding: 0 20px;
  .form {
    display: flex;
    flex-direction: column;
    &-row {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      gap: 10px;
      button {
        padding: 5px 10px;
        background: transparent;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        margin: 5px 2px;
        outline: none;
        &[type="submit"] {
          border: 1px solid var(--color-primary);
          color: var(--color-primary);
          &:hover {
            background: var(--color-primary);
            color: var(--background-color-secondary);
          }
        }
        &[type="button"] {
          border: 1px solid var(--color-secondary);
          color: var(--color-secondary);
          &:hover {
            background: var(--color-secondary);
            color: var(--background-color-secondary);
          }
        }
        &[type="reset"] {
          border: 1px solid var(--color-danger);
          color: var(--color-danger);
          &:hover {
            background: var(--color-danger);
            color: var(--background-color-secondary);
          }
        }
      }
    }
    &-group {
      width: 100%;
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
      position: relative;
      label {
        font-size: 1rem;
        font-weight: 900;
        margin-bottom: 5px;
      }
      input,
      select,
      textarea {
        flex: 1;
        padding: 10px;
        outline: none;
        border: 1px solid var(--color-primary);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        font-size: 0.9rem;
        font-weight: 500;
      }
      textarea {
        resize: none;
        height: 100px;
      }
    }
  }
}
</style>
