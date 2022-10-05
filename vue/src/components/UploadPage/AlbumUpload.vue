<template>
  <teleport to="body">
    <BaseDialog
      v-if="dialogWaring.show"
      :title="dialogWaring.title"
      :mode="dialogWaring.mode"
      @close="closeDialog"
    >
      <template #default>
        <p>{{ dialogWaring.content }}</p>
      </template>
    </BaseDialog>
  </teleport>
  <form class="uploadform" @submit.prevent="onFormSubmit">
    <div class="uploadform__control">
      <div class="uploadform__control--album">
        <div class="uploadform__control--albumImage">
          <input
            type="file"
            name="image"
            id="image"
            @change="albumImageChange"
          />
          <label for="image">
            <span>Click or drag your album image here to upload </span>
          </label>
          <img
            class="preview"
            :src="albumImageTempPath"
            v-if="albumImageTempPath"
          />
        </div>
        <div class="uploadform__control--albumDetail">
          <BaseInput
            :label="'Album Title'"
            :type="'text'"
            :required="true"
            :placeholder="'Enter Album Title'"
            :id="'albumTitle'"
            v-model.trim="albumTitle"
          />
          <BaseInput
            :label="'Album Release Year'"
            :type="'number'"
            :required="true"
            :placeholder="'Enter Album Release Year'"
            :id="'albumReleaseYear'"
            v-model="albumReleaseYear"
          />
        </div>
      </div>
    </div>

    <div class="uploadform__control">
      <div
        class="uploadform__control--song"
        v-for="(item, index) in songForm"
        :key="item"
      >
        <div class="uploadform__control--songFile">
          <input
            type="file"
            :name="'file-' + index"
            :id="'file-' + index"
            @change="songFileChange($event, index)"
          />
          <label :for="'file-' + index">
            <span v-if="item.songFile">{{ item.songFile.type }}</span>
            <span v-else>Audio file</span>
          </label>
        </div>
        <div class="uploadform__control--songName">
          <BaseInput
            :label="'Song Name'"
            :type="'text'"
            :required="true"
            :placeholder="'Enter Song Name'"
            :id="'songName-' + index"
            v-model.trim="item.songName"
          />
        </div>
        <div class="uploadform__control--songControl">
          <BaseButton @click="addSong">Add Song</BaseButton>
          <BaseButton
            v-if="index > 0"
            :mode="'danger'"
            @click="removeSong($event, index)"
            >Remove Song</BaseButton
          >
        </div>
      </div>
    </div>
    <div class="uploadform__control">
      <div class="uploadform__control--submit">
        <BaseButton :type="'submit'">Create</BaseButton>
      </div>
    </div>
  </form>
</template>
<script lang="ts">
import { defineComponent, reactive } from "vue";
import BaseInput from "../UI/BaseInput.vue";
import BaseButton from "../UI/BaseButton.vue";
import BaseDialog from "../UI/BaseDialog.vue";

export default defineComponent({
  data() {
    return {
      albumImage: null,
      albumImageTempPath: null,
      albumTitle: "",
      albumReleaseYear: "",
      songCount: 1,
      songForm: reactive([
        {
          songName: "",
          songFile: null as File | null,
        },
      ]),
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  mounted() {},
  methods: {
    albumImageChange(e: any) {
      if (e.target.files.length > 0) {
        if (!this.validateImageFileType(e.target.files[0])) {
          this.dialogWaring.content = "Please upload valid image file";
          this.dialogWaring.show = true;
          return;
        }
        let reader = new FileReader();
        reader.readAsDataURL(e.target.files[0]);
        reader.onload = (e) => {
          this.albumImageTempPath = reader.result as string;
        };
        this.albumImage = e.target.files[0];
      }
    },
    songFileChange(e: any, index: number) {
      if (e.target.files.length > 0) {
        if (!this.validateSongFileType(e.target.files[0].type)) {
          this.dialogWaring.content = "Please upload valid audio file";
          this.dialogWaring.show = true;
          return;
        }
        this.songForm[index].songFile = e.target.files[0];
      }
    },
    addSong() {
      this.songForm.push({
        songName: "",
        songFile: null,
      });
      this.songCount++;
    },
    removeSong(id: number) {
      this.songForm.splice(id, 1);
      this.songCount--;
    },

    onFormSubmit() {
      if (!this.albumImage) {
        this.dialogWaring.content = "Please upload album image";
        this.dialogWaring.show = true;
        return;
      }
      if (this.albumTitle === "") {
        this.dialogWaring.content = "Please enter album title";
        this.dialogWaring.show = true;
        return;
      }
      let i = 0;
      this.songForm.forEach((item) => {
        if (item.songName === "") {
          this.dialogWaring.content = "Please enter song name";
          this.dialogWaring.show = true;
          return;
        }
        if (!item.songFile) {
          this.dialogWaring.content = "Please upload valid song file";
          this.dialogWaring.show = true;
          return;
        }
        i++;
      });
      let formData = new FormData();
      formData.append("albumImage", this.albumImage);
      formData.append("albumTitle", this.albumTitle);
      formData.append("albumReleaseYear", this.albumReleaseYear);
      i = 0;
      this.songForm.forEach((item) => {
        formData.append("songName-" + i, item.songName);
        formData.append("songFile-" + i, item.songFile);
        i++;
      });
      console.log(formData.get("albumImage"));
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    validateSongFileType(file: File): boolean {
      const validTypes = [
        "audio/mpeg",
        "audio/mp3",
        "audio/aac",
        "audio/ogg",
        "audio/flac",
        "audio/alac",
        "audio/wav",
        "audio/aiff",
        "audio/dsd",
        "audio/pcm",
      ];
      if (validTypes.indexOf(file.type) === -1) {
        return false;
      }
      return true;
    },
    validateImageFileType(file: File): boolean {
      const validTypes = [
        "image/jpg",
        "image/jpeg",
        "image/bmp",
        "image/gif",
        "image/png",
      ];
      if (validTypes.indexOf(file.type) === -1) {
        return false;
      }
      return true;
    },
  },

  components: { BaseInput, BaseButton, BaseDialog },
});
</script>
<style lang="scss" scoped>
.uploadform {
  width: 100%;
  &__control {
    position: relative;
    margin: 30px 0;
    &--album {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 300px;
      &Image {
        aspect-ratio: 1/1;
        height: 100%;
        width: auto;
        flex: 0 0 auto;
        position: relative;
        border: 2px solid var(--color-primary);
        & > input {
          width: 100%;
          height: 100%;
          opacity: 0;
        }
        & > label {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          font-size: 1.3rem;
          font-weight: 700;
          color: var(--color-primary);
          z-index: -2;
        }
        & > .preview {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          width: 100%;
          height: 100%;
          object-fit: cover;
          z-index: -1;
        }
      }
      &Detail {
        width: 100%;
        height: 100%;
        margin-left: 5%;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
      }
    }
    &--song {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 100px;
      margin: 20px auto;
      &File {
        aspect-ratio: 4/3;
        height: 100%;
        width: auto;
        flex: 0 0 auto;
        position: relative;
        border: 2px solid var(--color-primary);
        & > input {
          width: 100%;
          height: 100%;
          opacity: 0;
        }
        & > label {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          font-size: 1.3rem;
          font-weight: 700;
          color: var(--color-primary);
          z-index: -2;
        }
      }
      &Name {
        width: 100%;
        height: 100%;
        margin: 3%;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
      }
      &Control {
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        & > button {
          margin: 5px 0;
        }
      }
    }
  }
}
</style>
