<template>
  <teleport to="body">
    <BaseDialog
      :open="dialogWaring.show"
      :title="dialogWaring.title"
      :mode="dialogWaring.mode"
      @close="closeDialog"
    >
      <template #default>
        <p>{{ dialogWaring.content }}</p>
      </template>
    </BaseDialog>
    <BaseDialog
      :open="dialogProgress.progress > 0"
      :title="dialogProgress.title"
      :mode="dialogProgress.mode"
    >
      <template #default>
        <div class="progress">
          <div
            class="progress-bar"
            :style="{ width: dialogProgress.progress + '%' }"
          ></div>
        </div>
      </template>
      <template #action>
        <div></div>
      </template>
    </BaseDialog>
  </teleport>
  <form class="uploadform" @submit.prevent="onFormSubmit">
    <div class="uploadform__control" ref="wrapper">
      <div class="uploadform__control--album" :class="{ mobile: mobile }">
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
            :data-url="albumImageTempPath"
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
          <div class="albumType">
            <label for="albumType">Album Type</label>
            <select name="albumType" id="albumType" v-model="albumType">
              <option value="" disable>Select album type</option>
              <option
                v-for="(value, key) in availableAlbumTypes"
                :key="key"
                :value="key"
              >
                {{ value }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="uploadform__control">
      <div
        class="uploadform__control--song"
        :class="{ mobile: mobile }"
        v-for="(item, index) in songForm"
        :key="index"
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
            @click="removeSong(index)"
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
import { mapGetters, mapActions } from "vuex";
import { environment } from "@/environment/environment";
import { _function } from "@/mixins/index";
import BaseInput from "@/components/UI/BaseInput.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";

export default defineComponent({
  data() {
    return {
      albumImage: null as File | null,
      albumImageTempPath: "",
      albumTitle: "",
      albumReleaseYear: "",
      availableAlbumTypes: {} as { [key: string]: string },
      albumType: "",
      observableData: null as any,
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
      dialogProgress: {
        title: "Uploading! Do not close this tab",
        mode: "anouncement",
        progress: 0,
        show: true,
      },
      observer: null as ResizeObserver | null,
      mobile: false,
    };
  },
  mounted() {
    const wrapper = this.$refs.wrapper as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        if (entry.contentRect.width < 700) {
          this.mobile = true;
        } else {
          this.mobile = false;
        }
      }
    });
    if (wrapper) this.observer.observe(wrapper);

    this.getAlbumAvailableTypes(this.userToken)
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          this.availableAlbumTypes = data.types;
        }
      });
  },
  methods: {
    ...mapActions("uploadQueue", ["addFileToQueue"]),
    ...mapActions("album", ["getAlbumAvailableTypes"]),
    albumImageChange(e: any) {
      if (e.target.files.length > 0) {
        if (!_function.validateImageFileType(e.target.files[0])) {
          this.dialogWaring.content = "Please upload valid image file";
          this.dialogWaring.show = true;
          return;
        }
        let reader = new FileReader();
        reader.readAsDataURL(e.target.files[0]);
        reader.onload = () => {
          this.albumImageTempPath = reader.result as string;
        };
        this.albumImage = e.target.files[0];
      }
    },
    songFileChange(e: any, index: number) {
      if (e.target.files.length > 0) {
        if (_function.validateSongFileType(e.target.files[0].type)) {
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
    },
    removeSong(id: number) {
      this.songForm.splice(id, 1);
    },

    async onFormSubmit() {
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
      if (this.albumType === "") {
        this.dialogWaring.content = "Please select a album type";
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
      let albumForm = new FormData();
      albumForm.append("albumImage", this.albumImage);
      albumForm.append("albumTitle", this.albumTitle);
      albumForm.append("albumReleaseYear", this.albumReleaseYear);
      albumForm.append("albumType", this.albumType);
      i = 0;
      this.songForm.forEach((item) => {
        albumForm.append("songName_" + i, item.songName);
        if (item.songFile) albumForm.append("songFile_" + i, item.songFile);
        i++;
      });
      albumForm.append("songCount", this.songForm.length.toString());

      const xhr = new XMLHttpRequest();
      xhr.open("POST", `${environment.api}/album/upload`, true);
      xhr.setRequestHeader("Authorization", `Bearer ${this.userToken}`);
      xhr.setRequestHeader("Accept", "multipart/form-data");
      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          const percentComplete = (e.loaded / e.total) * 100;
          this.dialogProgress.progress = percentComplete;
        }
      };
      xhr.onload = () => {
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.response);
          if (response.status == "success") {
            this.dialogWaring.content = response.message;
            this.dialogWaring.show = true;
            this.dialogWaring.mode = "anouncement";
            response.songs.forEach(
              (element: { song_id: string; index: number }) => {
                // this.$emit("uploadSong", [
                //   this.songForm[element.index].songFile,
                //   element.song_id,
                // ]);
                this.addFileToQueue({
                  file: this.songForm[element.index].songFile,
                  song_id: element.song_id,
                  token: this.userToken,
                });
              }
            );
            this.albumImage = null;
            this.albumImageTempPath = "";
            this.albumTitle = "";
            this.albumReleaseYear = "";
            this.albumType = "";
            this.songForm = [
              {
                songName: "",
                songFile: null,
              },
            ];
          } else {
            this.dialogWaring.content = response.message;
            this.dialogWaring.show = true;
            this.dialogWaring.mode = "warning";
          }
          this.dialogProgress.progress = 0;
        } else {
          this.dialogWaring.content = "Something went wrong";
          this.dialogWaring.show = true;
          this.dialogWaring.mode = "warning";
          this.dialogProgress.progress = 0;
        }
      };
      xhr.send(albumForm);
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
  },
  computed: {
    ...mapGetters({
      userToken: "auth/userToken",
    }),
  },
  components: { BaseInput, BaseButton, BaseDialog },
  emits: ["uploadSong"],
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
      &.mobile {
        flex-direction: column;
        height: auto;
        .uploadform__control--albumImage {
          height: 300px;
        }
        .uploadform__control--albumDetail {
          margin: 5px auto;
          & > * {
            margin: 5px auto;
            width: 100%;
          }
        }
      }
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
        & .albumType {
          & label {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--color-primary);
            display: block;
          }
          & select {
            height: 40px;
            width: 100%;
            border-radius: 10px;
            border: none;
            outline: none;
            line-height: 50px;
            padding: 0 10px;
            margin-right: 10px;
            background: var(--background-glass-color-primary);
            color: var(--text-primary-color);
            font-size: 16px;
            font-weight: 400;
            &::placeholder {
              color: var(--text-primary-color);
            }
            & > * {
              color: var(--text-primary-color);
              background: var(--background-color-primary);
            }
          }
        }
      }
    }
    &--song {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 100px;
      margin: 20px auto;

      &.mobile {
        display: block;
        height: auto;
        & .uploadform__control--songFile {
          height: 100px;
          width: 100%;
        }
        & .uploadform__control--songName {
          margin: 5px auto;
        }
      }
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
.progress {
  width: 100%;
  height: 10px;
  background: var(--background-color-primary);
  border-radius: 10px;
  position: relative;
  & > div {
    height: 100%;
    background: var(--color-primary);
    border-radius: 10px;
    position: absolute;
    top: 0;
    left: 0;
  }
}
</style>
