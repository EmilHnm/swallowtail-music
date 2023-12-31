<template>
  <div class="upload-container">
    <div class="upload-container__header">
      <h1 class="upload-container__title">Upload</h1>
      <div class="upload-container__type" v-if="userdata.email_verified_at">
        <BaseButton @click="toggleComponent('SongUpload')">Songs</BaseButton>
        <BaseButton @click="toggleComponent('AlbumUpload')">Album</BaseButton>
      </div>
      <keep-alive v-if="userdata.email_verified_at">
        <component :is="componentName" @uploadSong="uploadSong"></component>
      </keep-alive>
      <div v-else class="upload-container__noti">
        <h3>You need to verify your email to upload songs or albums.</h3>
        <p>Please go to Account > Profile to verify your email</p>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import SongUpload from "@/components/UploadPage/SongUpload.vue";
import AlbumUpload from "@/components/UploadPage/AlbumUpload.vue";
import { mapGetters } from "vuex";
import { useMeta } from "vue-meta";
import globalEmitListener from "@/shared/globalEmitListener";
export default defineComponent({
  data() {
    return { componentName: "SongUpload" };
  },
  methods: {
    toggleComponent(component: string) {
      this.componentName = component;
    },
    uploadSong(args: []) {
      this.$emit("uploadSong", args);
    },
  },
  computed: {
    ...mapGetters({
      userdata: "auth/userData",
    }),
  },
  components: { BaseButton, SongUpload, AlbumUpload },
  emits: [...globalEmitListener],
  mounted() {
    useMeta({
      title: "Upload",
    });
  },
});
</script>

<style lang="scss" scoped>
.upload-container {
  width: 80%;
  margin: auto;
  &__title {
    font-size: 2rem;
    font-weight: 700;
    text-align: center;
  }
  &__type {
    display: flex;
    justify-content: center;
    margin: 1rem 0;
    & > * {
      margin: 0 0.5rem;
      flex: 1;
    }
  }
  &__noti {
    text-align: center;
    & > * {
      margin: 0.5rem 0;
    }
  }
}
</style>
