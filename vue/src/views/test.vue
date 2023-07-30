<template>
  <div class="root">
    <button @click="inc">Add 2</button>
    <h3>{{ finalCounter }} - {{ normalizedCounter }}</h3>
  </div>
  <div>
    <input type="file" @change="select" />
    <progress :value="uploaded / 100"></progress>
    <button @click="upload()">Upload</button>
  </div>
</template>

<script lang="ts">
import { mapActions, mapGetters } from "vuex";
import { defineComponent } from "vue";
export default defineComponent({
  data() {
    return {
      file: null as File | null,
      chunks: [] as Blob[],
      chunk_size: 2 * 1024 * 1024,
      chunk_count: 0,
      uploaded: 0,
    };
  },
  watch: {},
  methods: {
    ...mapActions("test", {
      inc: "increment",
      increase: "increase",
    }),
    select(payload: Event) {
      let target = payload.target as HTMLInputElement;
      if (target.files) this.file = target.files.item(0);

      this.createChunks();
    },
    upload() {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", `https://swallowtail-music.com/api/test`, true);
      // xhr.setRequestHeader("Authorization", `Bearer ${this.userToken}`);
      let formData = new FormData();
      formData.append("is_last", JSON.stringify(this.chunks.length === 1));
      if (this.file)
        formData.set("file", this.chunks[0], `${this.file.name}.part`);
      xhr.setRequestHeader("Accept", "multipart/form-data");
      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          const percentComplete = (e.loaded / e.total / this.chunk_count) * 100;
          this.uploaded += percentComplete;
          console.log(this.uploaded);
        }
      };
      xhr.onload = () => {
        if (xhr.status === 200) {
          this.chunks.shift();
          if (this.chunks.length > 0) this.upload();
        } else {
          console.log("error");
        }
      };
      xhr.send(formData);
    },
    createChunks() {
      if (this.file) {
        let chunks = Math.ceil(this.file.size / this.chunk_size);
        for (let i = 0; i < chunks; i++) {
          this.chunks.push(
            this.file.slice(
              i * this.chunk_size,
              Math.min(i * this.chunk_size + this.chunk_size, this.file.size),
              this.file.type
            )
          );
        }
        this.chunk_count = this.chunks.length;
      }
    },
  },
  computed: {
    ...mapGetters("test", {
      finalCounter: "finalCounter",
      normalizedCounter: "normalizedCounter",
    }),
    progress(): number {
      return this.file ? Math.floor((this.uploaded * 100) / this.file.size) : 0;
    },
  },
});
</script>

<style lang="scss" scoped>
// .root {
//   width: 100vw;
//   height: 100vh;
//   background-color: #000;
//   color: #fff;
// }
</style>
