<template>
  <teleport to="body">
    <!-- Warning Dialog -->
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
    <!-- Delete Confirm Dialog -->
    <BaseFlatDialog
      :open="dialogConfirmDelete.show"
      :title="dialogConfirmDelete.title"
      :mode="dialogConfirmDelete.mode"
      @close="onCloseDeleteConrfirm"
    >
      <template #default>
        <p>{{ dialogConfirmDelete.content }}</p>
      </template>
      <template #action>
        <BaseButton mode="danger" @click="onDeleteArtist">Delete</BaseButton>
        <BaseButton @click="onCloseDeleteConrfirm">Cancel</BaseButton>
      </template>
    </BaseFlatDialog>
    <!-- Group Artist Dialog -->
    <BaseFlatDialog
      :open="dialogConfirmGroup.show"
      :title="dialogConfirmGroup.title"
      :mode="dialogConfirmGroup.mode"
      @close="onCloseGroupConrfirm"
    >
      <template #default>
        <p>
          {{
            `This action will move all songs of ${artist_id} to ${dialogConfirmGroup.to} and delete old artist, are you sure you want to continue?`
          }}
        </p>
      </template>
      <template #action>
        <BaseButton mode="danger" @click="onGroupArtist">Group</BaseButton>
        <BaseButton @click="onCloseGroupConrfirm">Cancel</BaseButton>
      </template>
    </BaseFlatDialog>
    <!-- Groupable Artist Dialog -->
    <BaseFlatDialog
      :open="dialogGroupalbeArtistList.show"
      :title="dialogGroupalbeArtistList.title"
      :mode="dialogGroupalbeArtistList.mode"
      @close="onCloseGroupArtist"
    >
      <template #default>
        <div v-if="!dialogGroupalbeArtistList.isGroupableLoading">
          <BaseListItem
            v-for="artist in dialogGroupalbeArtistList.groupableArtist"
            :key="artist.artist_id"
            @click="onSelectArtist(artist.artist_id)"
            >{{ artist.name }}</BaseListItem
          >
        </div>
        <div>
          <BaseCircleLoad v-if="dialogGroupalbeArtistList.isGroupableLoading" />
        </div>
      </template>
      <template #action>
        <div></div>
      </template>
    </BaseFlatDialog>
    <!-- Loading Dialog -->
    <BaseFlatDialog
      :open="isLoading"
      :title="'Loading ...'"
      :mode="'announcement'"
    >
      <template #default>
        <BaseCircleLoad />
      </template>
      <template #action><div></div></template>
    </BaseFlatDialog>
  </teleport>
  <div class="main" ref="main">
    <h3>Edit Artist Details</h3>
    <div class="details">
      <div class="details__image">
        <img
          :src="
            artist.image_path
              ? `${environment.artist_image}/${artist.image_path}`
              : `${environment.default}/no_image.jpg`
          "
          alt=""
          srcset=""
          v-if="!tempImg"
        />
        <img :src="tempImg" alt="" srcset="" v-else />
        <input type="file" name="" id="" @change="onImageChange" />
      </div>
      <div class="details__statistic">
        <div class="row">
          <span class="lab">Name</span>
          <input type="text" name="" v-model="artist.name" />
        </div>
        <div class="row">
          <button @click="onUpdateArtist">Update</button>
        </div>
        <div class="row">
          <button @click="onOpenGroupArtist">Group to other Artist</button>
        </div>
        <div class="row">
          <button class="danger" @click="onDeleteConfirm">Delete</button>
        </div>
      </div>
    </div>
    <div class="song"></div>
  </div>
</template>

<script lang="ts">
import type { artist } from "@/model/artistModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import { _function } from "@/mixins";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import BaseListItem from "@/components/UI/BaseListItem.vue";

export default defineComponent({
  data() {
    return {
      artist_id: "" as string | string[],
      artist: {} as artist,
      tempImg: "",
      file: null as File | null,
      environment: environment,
      isLoading: false,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      dialogConfirmDelete: {
        title: "Danger",
        mode: "danger",
        content:
          "This action will permanently delete this artist, are you sure you want to continue?",
        show: false,
      },
      dialogConfirmGroup: {
        title: "Warning",
        mode: "warning",
        to: "",
        show: false,
      },
      dialogGroupalbeArtistList: {
        title: "Groupable Artist List",
        mode: "announcement",
        show: false,
        groupableArtist: [] as artist[],
        isGroupableLoading: false,
        currentPage: 0,
        lastPage: 0,
      },
    };
  },
  methods: {
    ...mapActions("admin", [
      "getArtist",
      "updateArtist",
      "deleteArtist",
      "getArtists",
    ]),
    onLoadArtist() {
      this.isLoading = true;
      this.getArtist({ userToken: this.token, artistId: this.artist_id })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.artist = res.artist;
          } else {
            this.$router.push({ name: "accountAdminArtists" });
          }
        });
    },
    onImageChange(e: Event) {
      const target = e.target as HTMLInputElement;
      if (target.files) {
        if (!_function.validateImageFileType(target.files[0])) {
          this.dialogWaring.title = "Warning";
          this.dialogWaring.content = "Please upload image file";
          this.dialogWaring.show = true;
          return;
        }
        let reader = new FileReader();
        reader.readAsDataURL(target.files[0]);
        reader.onload = (e) => {
          this.tempImg = reader.result as string;
        };
        this.file = target.files[0];
      }
    },
    onUpdateArtist() {
      let artistData = new FormData();
      if (!this.artist.name) {
        this.dialogWaring.title = "Warning";
        this.dialogWaring.content = "Please fill in all the fields";
        this.dialogWaring.show = true;
        return;
      }
      artistData.append("artist", JSON.stringify(this.artist));
      if (this.file) {
        artistData.append("image", this.file);
      }
      this.isLoading = true;
      this.updateArtist({ userToken: this.token, artistData: artistData })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.$router.push({ name: "accountAdminArtists" });
          } else {
            this.dialogWaring.title = "Warning";
            this.dialogWaring.content = res.message;
            this.dialogWaring.show = true;
          }
        });
    },
    onDeleteConfirm() {
      this.dialogConfirmDelete.show = true;
    },
    onCloseDeleteConrfirm() {
      this.dialogConfirmDelete.show = false;
    },
    onDeleteArtist() {
      this.dialogConfirmDelete.show = false;
      this.isLoading = true;
      this.deleteArtist({ userToken: this.token, artistId: this.artist_id })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.$router.push({ name: "accountAdminArtists" });
          } else {
            this.dialogWaring.title = "Warning";
            this.dialogWaring.content = res.message;
            this.dialogWaring.show = true;
          }
        });
    },
    onLoadGroupableArtist() {
      this.dialogGroupalbeArtistList.isGroupableLoading = true;
      this.getArtists({
        userToken: this.token,
        query: "",
        itemPerPage: 5,
        currentPage: this.dialogGroupalbeArtistList.currentPage + 1,
      })
        .then((res) => res.json())
        .then((res) => {
          this.dialogGroupalbeArtistList.isGroupableLoading = false;
          if (res.status === "success") {
            this.dialogGroupalbeArtistList.groupableArtist =
              res.artists.data.filter(
                (artist: artist) => artist.artist_id !== this.artist_id
              );
          }
          this.dialogGroupalbeArtistList.currentPage = res.artists.current_page;
          this.dialogGroupalbeArtistList.lastPage = res.artists.last_page;
          console.log(this.dialogGroupalbeArtistList.groupableArtist);
        });
    },
    onOpenGroupArtist() {
      this.onLoadGroupableArtist();
      this.dialogGroupalbeArtistList.show = true;
    },
    onCloseGroupArtist() {
      this.dialogGroupalbeArtistList.show = false;
    },
    onSelectArtist(id: string) {
      this.dialogConfirmGroup.to = id;
      this.onCloseGroupArtist();
      this.onGroupArtistConfirm();
    },
    onGroupArtistConfirm() {
      this.dialogConfirmGroup.show = true;
    },
    onGroupArtist() {
      this.dialogConfirmGroup.show = false;
    },
    onCloseGroupConrfirm() {
      this.dialogConfirmGroup.show = false;
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.artist_id = this.$route.params.id;
    this.onLoadArtist();
  },
  components: {
    BaseFlatDialog,
    BaseCircleLoad,
    BaseButton,
    BaseListItem,
  },
});
</script>

<style lang="scss" scoped>
.main {
  width: 90%;
  max-height: 100%;
  display: flex;
  flex-direction: column;
  padding: 20px;
  margin: auto;
  h3 {
    margin-bottom: 20px;
    font-weight: 900;
    font-size: 1.5rem;
    text-align: center;
  }
  .details {
    display: flex;
    flex-direction: row;
    width: 100%;
    &__image {
      aspect-ratio: 1 / 1;
      width: 40%;
      height: 40%;
      position: relative;
      flex: 0 0 auto;
      img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
      }
      input {
        width: 100%;
        height: 100%;
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
      }
    }
    &__statistic {
      width: 100%;
      display: flex;
      flex-direction: column;
      padding: 0 20px;
    }
  }
}
.row {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  margin-bottom: 10px;
  width: 100%;
  .lab {
    font-size: 1.2rem;
    font-weight: 900;
    margin-bottom: 5px;
    display: block;
  }
  input {
    height: 40px;
    width: calc(100% - 20px);
    border: 1px solid var(--text-primary-color);
    background: var(--background-color-secondary);
    color: var(--text-primary-color);
    padding: 0 10px;
    font-size: 1rem;
    &:focus {
      outline: none;
    }
  }
  button {
    margin-top: 1.2rem;
    width: 100%;
    height: 40px;
    border: 1px solid var(--color-primary);
    background: var(--background-color-secondary);
    color: var(--color-primary);
    padding: 0 10px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: 600;
    &:hover {
      background: var(--color-primary);
      color: var(--background-color-secondary);
    }
    &.danger {
      border-color: var(--color-danger);
      color: var(--color-danger);
      &:hover {
        background: var(--color-danger);
        color: var(--background-color-secondary);
      }
    }
  }
}
</style>
