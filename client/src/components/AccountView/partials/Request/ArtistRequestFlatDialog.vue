<template>
  <BaseFlatDialog
    :open="isOpen"
    :title="'Request new artist'"
    :mode="'announcement'"
  >
    <template v-if="!isRequseting" #default>
      <div class="artist-request">
        <div class="form__row">
          <label for="artist_name">Name</label>
          <input
            type="text"
            placeholder="Artist Name"
            id="artist_name"
            v-model="name"
          />
        </div>
        <div class="form__row">
          <label for="artist_descriptions">Descriptions</label>
          <textarea
            placeholder="Artist Descriptions"
            id="artist_descriptions"
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
    <template v-else #default>
      <div class="artist-request">
        <span>Please wait...</span>
        <BaseLineLoad />
      </div>
    </template>
    <template v-if="!isRequseting" #action>
      <button @click="onRequest">Request</button>
      <button :mode="'warning'" @click="closeDialog">Cancel</button>
    </template>
    <template v-else #action>
      <div></div>
    </template>
  </BaseFlatDialog>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import { mapActions, mapGetters } from "vuex";
export default defineComponent({
  name: "ArtistRequestDialog",
  emits: ["artist-request-close"],
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
    };
  },
  methods: {
    ...mapActions("request", ["create"]),
    closeDialog() {
      this.name = "";
      this.descriptions = "";
      this.errors = "";
      this.isRequseting = false;
      this.$emit("artist-request-close");
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
        type: "artist",
        token: this.userToken,
      })
        .then((res: any) => res.json())
        .then((data: any) => {
          this.isRequseting = false;
          if (data.errors) {
            this.errors = data.message;
            return;
          }
          this.closeDialog();
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
.artist-request {
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
      resize: none;
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
