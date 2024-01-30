<template>
  <BaseDialog
    :open="isOpen"
    :title="'Request new genre'"
    :mode="'announcement'"
  >
    <template v-if="!isRequseting" #default>
      <div class="genre-request">
        <BaseInput
          type="text"
          label="Name"
          placeholder="Name of genre"
          :required="true"
          labelColor="text"
          id="genre-name"
          name="genre-name"
          v-model="name"
        />
        <BaseInput
          type="text"
          label="Descriptions"
          placeholder="Description of genre"
          :required="true"
          id="genre-descriptions"
          name="genre-descriptions"
          labelColor="text"
          mode="textarea"
          :size="{ cols: 20, rows: 10 }"
          v-model="descriptions"
        />
        <span class="errors">
          {{ errors }}
        </span>
      </div>
    </template>
    <template v-else #default>
      <div class="genre-request">
        <span>Please wait...</span>
        <BaseLineLoad />
      </div>
    </template>
    <template v-if="!isRequseting" #action>
      <BaseButton @click="onRequest">Request</BaseButton>
      <BaseButton :mode="'warning'" @click="closeDialog">Cancel</BaseButton>
    </template>
    <template v-else #action>
      <div></div>
    </template>
  </BaseDialog>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseInput from "@/components/UI/BaseInput.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
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
    };
  },
  methods: {
    ...mapActions("request", ["create"]),
    closeDialog() {
      this.name = "";
      this.descriptions = "";
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
    BaseDialog,
    BaseInput,
    BaseButton,
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
}
</style>
