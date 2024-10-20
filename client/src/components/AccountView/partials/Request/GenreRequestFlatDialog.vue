<template>
  <BaseFlatDialog
    :open="isOpen"
    :title="'Request new genre'"
    :mode="'announcement'"
  >
    <template v-if="!isRequseting" #default>
      <template v-if="!isSuccess">
        <div class="genre-request">
          <div class="form__row">
            <label for="genre_name">Name</label>
            <input
              type="text"
              placeholder="Genre Name"
              id="genre_name"
              v-model="name"
            />
          </div>
          <div class="form__row">
            <label for="genre_descriptions">Descriptions</label>
            <textarea
              placeholder="Genre Descriptions"
              id="genre_descriptions"
              v-model="descriptions"
              cols="30"
              rows="10"
            ></textarea>
          </div>
          <span class="errors">
            {{ errors }}
          </span>
        </div>
      </template>
      <template v-else>
        <div class="genre-request">
          <span>Request success</span>
        </div>
      </template>
    </template>
    <template v-else #default>
      <div class="genre-request">
        <span>Please wait...</span>
        <BaseLineLoad />
      </div>
    </template>
    <template v-if="isRequseting">
      <div class="genre-request">
        <span>Please wait...</span>
        <BaseLineLoad />
      </div>
    </template>
    <template v-if="!isRequseting" #action>
      <template v-if="!isSuccess">
        <button @click="onRequest">Request</button>
        <button @click="closeDialog">Cancel</button>
      </template>
      <template v-else>
        <button @click="closeDialog">Close</button>
      </template>
    </template>
  </BaseFlatDialog>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
export default defineComponent({
  name: "GenreRequestDialog",
  emits: ["genre-request-close"],
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      name: "",
      descriptions: "",
      errors: "",
      isRequseting: false,
      isSuccess: false,
    };
  },
  methods: {
    ...mapActions("request", ["create"]),
    closeDialog() {
      this.name = "";
      this.descriptions = "";
      this.errors = "";
      this.isRequseting = false;
      this.isSuccess = false;
      this.$emit("genre-request-close");
    },
    onRequest() {
      if (this.name === "") {
        this.errors = "Name is required";
        return;
      }
      if (this.descriptions === "") {
        this.errors = "Descriptions is required";
        return;
      }
      this.errors = "";
      this.isRequseting = true;
      this.create({
        name: this.name,
        description: this.descriptions,
        type: "genre",
        token: this.userToken,
      })
        .then((res: any) => res.json())
        .then((data: any) => {
          this.isRequseting = false;
          if (data.errors) {
            this.errors = data.message;
            return;
          }
          this.isSuccess = true;
        })
        .catch((err) => {
          this.isRequseting = false;
          this.errors = err.message;
        });
    },
  },
  computed: {
    ...mapGetters({
      userToken: "auth/userToken",
    }),
  },
  components: {
    BaseFlatDialog,
    BaseLineLoad,
  },
});
</script>

<style lang="scss" scoped>
.genre-request {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  & .errors {
    color: var(--color-danger);
    font-size: 1.2rem;
    font-weight: 500;
  }
  & .form__row {
    display: flex;
    flex-direction: column;
    gap: 5px;
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
    textarea {
      border: 1px solid var(--text-primary-color);
      background: var(--background-color-secondary);
      color: var(--text-primary-color);
      padding: 10px;
      font-size: 1rem;
      &:focus {
        outline: none;
      }
    }
  }
}
button {
  height: 40px;
  background: transparent;
  color: var(--color-primary);
  padding: 0 20px;
  font-size: 1rem;
  border: 1px solid var(--color-primary);
  width: max-content;
  float: right;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  &:focus {
    outline: none;
  }
  &:hover {
    transform: scale(1.05);
    background: var(--color-primary);
    color: var(--background-color-secondary);
  }
}
</style>
