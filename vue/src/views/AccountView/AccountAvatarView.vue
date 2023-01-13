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
      <template #action>
        <div></div>
      </template>
    </BaseFlatDialog>
    <BaseFlatDialog
      :open="isUploading"
      :title="'Uploading'"
      :mode="'announcement'"
      @close="closeDialog"
    >
      <template #default>
        <BaseLineLoad />
      </template>
    </BaseFlatDialog>
  </teleport>

  <div class="container" ref="container">
    <h2>Change Avatar</h2>
    <div class="currentAvatar">
      <img
        :src="
          user.profile_photo_url
            ? `${environment.profile_image}/${user.profile_photo_url}`
            : 'http://127.0.0.1:5173/src/assets/default/default-avatar.jpg'
        "
      />
    </div>
    <div class="upload" v-if="!options.img">
      <input class="uploadArea" type="file" @change="photoChange" />
      <span class="uploadTitle">Drag your image file here to upload</span>
    </div>
    <div class="crop" v-else :class="{ tab: tab }">
      <h3 class="cropTilte">Crop and Preview</h3>
      <div class="cropArea" :class="{ tab: tab }">
        <VueCropper
          ref="cropper"
          :src="options.img"
          :cropBoxResizable="options.cropBoxResizable"
          :aspectRatio="options.aspectRatio"
          :background="options.background"
          :zoomable="options.zoomable"
          :dragMode="options.dragMode"
          :movable="options.movable"
          @crop="cropImage"
        ></VueCropper>
      </div>
      <div class="preview" :class="{ tab: tab }">
        <img class="previewSquare" :src="imgDataUrl" />
        <img class="previewCircle" :src="imgDataUrl" />
      </div>
    </div>
    <div class="control" v-if="options.img">
      <button @click="reset">Back</button>
      <button @click="upload">Upload</button>
    </div>
  </div>
</template>

<script lang="ts">
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import BaseFlatDialog from "../../components/UI/BaseFlatDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";

export default defineComponent({
  data() {
    return {
      observer: null as ResizeObserver | null,
      environment: environment,
      show: false,
      imgDataUrl: "",
      tab: false,
      isUploading: false,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      headers: {
        Accept: "multipart/form-data",
        Authorization: "",
      },
      options: {
        img: "",
        cropBoxResizable: true,
        aspectRatio: 1 / 1,
        background: false,
        zoomable: false,
        dragMode: "move",
        movable: false,
      },
    };
  },
  methods: {
    ...mapActions("account", ["updateProfilePhoto"]),
    upload() {
      this.isUploading = true;
      const formData = new FormData();
      const file: Blob = this.dataURItoBlob(this.imgDataUrl);
      formData.append("profile_image", file);
      this.updateProfilePhoto({
        form: formData,
        token: this.token,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isUploading = false;
          if (res.status === "success") {
            this.$router.go(0);
          } else {
            this.dialogWaring.content = res.message;
            this.dialogWaring.show = true;
          }
        });
    },
    reset() {
      this.options.img = "";
      this.imgDataUrl = "";
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    cropImage() {
      this.imgDataUrl = (<VueCropper>this.$refs.cropper)
        .getCroppedCanvas({
          width: 180,
          height: 180,
          imageSmoothingEnabled: true,
        })
        .toDataURL("image/jpeg", 1.0);
    },
    photoChange(e: any) {
      if (e.target.files.length > 0) {
        if (!this.validateImageFileType(e.target.files[0])) {
          this.dialogWaring.content = "Please upload valid image file";
          this.dialogWaring.show = true;
          return;
        }
        let reader = new FileReader();
        reader.readAsDataURL(e.target.files[0]);
        reader.onload = (e) => {
          this.options.img = reader.result as string;
        };
      }
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
    dataURItoBlob(dataURI: string) {
      // convert base64 to raw binary data held in a string
      // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
      var byteString = atob(dataURI.split(",")[1]);

      // separate out the mime component
      var mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0];

      // write the bytes of the string to an ArrayBuffer
      var ab = new ArrayBuffer(byteString.length);
      var ia = new Uint8Array(ab);
      for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }
      return new Blob([ab], { type: mimeString });
    },
  },
  computed: {
    ...mapGetters({
      user: "auth/userData",
      token: "auth/userToken",
    }),
  },
  components: {
    BaseFlatDialog,
    VueCropper,
    BaseLineLoad,
  },
  mounted() {
    const container = this.$refs.container as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        if (entry.contentRect.width < 700) {
          this.tab = true;
        } else {
          this.tab = false;
        }
      }
    });
    if (container) this.observer.observe(container);
  },
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
  .currentAvatar {
    display: flex;
    flex-direction: column;
    align-items: center;
    img {
      width: 175px;
      height: 175px;
      border-radius: 50%;
      margin: 20px auto;
    }
  }
  .upload {
    position: relative;
    width: 100%;
    margin: 20px auto;
    border: 2px solid var(--color-primary);
    .uploadArea {
      width: 100%;
      height: 300px;
      opacity: 0;
      cursor: pointer;
    }
    .uploadTitle {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--color-primary);
      z-index: -1;
    }
  }
  .crop {
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    justify-content: space-between;
    &.tab {
      display: block;
    }
    &Tilte {
      width: 100%;
      font-weight: 900;
      font-size: 1.5rem;
      margin-bottom: 20px;
      flex: 0 0 auto;
    }
    &Area {
      aspect-ratio: 1 / 1;
      width: 50%;
      height: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      &.tab {
        width: 80%;
        margin: 0 auto;
      }
    }
    .preview {
      width: 50%;
      height: auto;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      .previewSquare {
        width: 180px;
        height: 180px;
        border-radius: 0;
        margin: 20px auto;
        object-fit: cover;
      }
      .previewCircle {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        margin: 20px auto;
        object-fit: cover;
      }
      &.tab {
        flex-direction: row;
        justify-content: space-between;
        width: 100%;
        .previewSquare {
          aspect-ratio: 1 / 1;
          width: 40%;
          height: auto;
        }
        .previewCircle {
          aspect-ratio: 1 / 1;
          width: 40%;
          height: auto;
        }
      }
    }
  }
  .control {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    width: 100%;
    margin: 20px auto;
    button {
      height: 40px;
      background: var(--color-primary);
      color: #fff;
      padding: 0 20px;
      font-size: 1rem;
      border-radius: 20px;
      border: none;
      width: 30%;
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
}
</style>
