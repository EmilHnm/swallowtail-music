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
          <div
            style="margin: 10px 0px"
            v-for="artist in dialogGroupalbeArtistList.groupableArtist"
            :key="artist.artist_id"
          >
            <BaseListItem @click="onSelectArtist(artist.artist_id)">{{
              artist.name
            }}</BaseListItem>
          </div>
          <BaseTableBodyPagination
            :totalPages="dialogGroupalbeArtistList.lastPage"
            :currentPage="dialogGroupalbeArtistList.currentPage"
            @onClickPage="onGroupArtistClickPage"
            @onGoToFirstPage="onGroupArtistGoToFirstPage"
            @onGoToPreviousPage="onGroupArtistGoToPreviousPage"
            @onGoToNextPage="onGroupArtistGoToNextPage"
            @onGoToLastPage="onGroupArtistGoToLastPage"
          />
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
    <div class="songs">
      <h3>Songs List</h3>
      <div class="tool">
        <div class="item-count">
          <label for="itemPerPage">Item Per Page</label>
          <select
            name="itemPerPage"
            id="itemPerPage"
            v-model="songs.itemPerPage"
          >
            <option value="10" :selected="songs.itemPerPage === 10">10</option>
            <option value="30" :selected="songs.itemPerPage === 30">30</option>
            <option value="50" :selected="songs.itemPerPage === 50">50</option>
          </select>
        </div>
        <div class="filter">
          <select name="filterType" id="filterType" v-model="songs.filterType">
            <option value="title" :selected="songs.filterType === 'title'">
              Song title
            </option>
            <option
              value="uploader"
              :selected="songs.filterType === 'uploader'"
            >
              Uploader
            </option>
            <option value="album" :selected="songs.filterType === 'album'">
              Album Name
            </option>
          </select>
          <input type="text" name="filterText" v-model="songs.filterText" />
        </div>
      </div>
      <div class="data">
        <table v-if="songs.songsList.length !== 0 && !songs.isLoading">
          <thead>
            <tr>
              <td>#</td>
              <td>Title</td>
              <td v-if="mainWidth > 550">Uploader</td>
              <td v-if="mainWidth > 600">Album</td>
              <td>Upload date</td>
              <td>Control</td>
            </tr>
          </thead>
          <tbody v-if="songs.songsList.length > 0">
            <tr v-for="(data, index) in songs.songsList" :key="data.song_id">
              <td>
                {{ index + 1 + songs.currentPage * songs.itemPerPage }}
              </td>
              <td>
                {{ data.title }}
              </td>
              <td v-if="mainWidth > 550">
                {{ data.user.name }}
              </td>
              <td v-if="mainWidth > 600">
                {{ data.album ? data.album.name : "No album" }}
              </td>
              <td>
                {{ new Date(data.created_at).toLocaleDateString() }}
              </td>
              <td>
                <button
                  class="btn btn-danger"
                  @click="navigateToSongEdit(data.song_id)"
                >
                  Edit
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <BaseCircleLoad v-if="songs.isLoading" />
      </div>
      <div
        class="pagination"
        v-if="!songs.isLoading && songs.songsList.length > 0"
      >
        <BaseTableBodyPagination
          :totalPages="songs.lastPage"
          :currentPage="songs.currentPage"
          @onClickPage="onClickSongListPage"
          @onGoToFirstPage="onGoToFirstSongListPage"
          @onGoToPreviousPage="onGoToPreviousSongListPage"
          @onGoToNextPage="onGoToNextSongListPage"
          @onGoToLastPage="onGoToLastSongListPage"
        />
      </div>
      <div
        class="no-results"
        v-if="!songs.isLoading && songs.songsList.length <= 0"
      >
        <h3>No results</h3>
      </div>
    </div>
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
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { user } from "@/model/userModel";

type songData = song & {
  album: album;
  user: user;
};

export default defineComponent({
  data() {
    return {
      artist_id: "" as string | string[],
      artist: {} as artist,
      songs: {
        songsList: [] as songData[],
        filterType: "title",
        filterText: "",
        itemPerPage: 10,
        currentPage: 0,
        lastPage: 0,
        controller: null as AbortController | null,
        signal: null as AbortSignal | null,
        isLoading: false,
      },
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
      mainWidth: 0,
      observer: null as ResizeObserver | null,
    };
  },
  methods: {
    ...mapActions("admin", [
      "getArtist",
      "updateArtist",
      "deleteArtist",
      "getArtists",
      "groupArtist",
      "getArtistSongs",
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
          this.dialogGroupalbeArtistList.currentPage =
            res.artists.current_page - 1;
          this.dialogGroupalbeArtistList.lastPage = res.artists.last_page;
        });
    },
    onOpenGroupArtist() {
      this.onLoadGroupableArtist();
      this.dialogGroupalbeArtistList.show = true;
    },
    onGroupArtistClickPage(page: number) {
      this.dialogGroupalbeArtistList.currentPage = page;
    },
    onGroupArtistGoToFirstPage() {
      this.dialogGroupalbeArtistList.currentPage = 0;
    },
    onGroupArtistGoToLastPage() {
      this.dialogGroupalbeArtistList.currentPage =
        this.dialogGroupalbeArtistList.lastPage - 1;
    },
    onGroupArtistGoToNextPage() {
      this.dialogGroupalbeArtistList.currentPage--;
    },
    onGroupArtistGoToPreviousPage() {
      this.dialogGroupalbeArtistList.currentPage--;
    },
    onCloseGroupArtist() {
      this.dialogGroupalbeArtistList.show = false;
      this.dialogGroupalbeArtistList.currentPage = 0;
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
      this.isLoading = true;
      this.groupArtist({
        userToken: this.token,
        from: this.artist_id,
        to: this.dialogConfirmGroup.to,
      })
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
    onCloseGroupConrfirm() {
      this.dialogConfirmGroup.show = false;
    },
    navigateToSongEdit(id: string) {
      this.$router.push({ name: "accountAdminDiscSongEdit", params: { id } });
    },
    onClickSongListPage(page: number) {
      this.songs.currentPage = page;
    },
    onGoToFirstSongListPage() {
      this.songs.currentPage = 0;
    },
    onGoToPreviousSongListPage() {
      this.songs.currentPage--;
    },
    onGoToNextSongListPage() {
      this.songs.currentPage++;
    },
    onGoToLastSongListPage() {
      this.songs.currentPage = this.songs.lastPage - 1;
    },
    onLoadArtistSongs() {
      this.songs.isLoading = true;
      if (!this.songs.controller) {
        this.songs.controller = new AbortController();
      } else {
        this.songs.controller.abort();
        this.songs.controller = new AbortController();
      }
      this.songs.signal = this.songs.controller.signal;
      this.getArtistSongs({
        token: this.token,
        page: this.songs.currentPage + 1,
        filterText: this.songs.filterText,
        filterType: this.songs.filterType,
        signal: this.songs.signal,
        itemPerPage: this.songs.itemPerPage,
        currentPage: this.songs.currentPage,
        artistId: this.artist_id,
      })
        .then((res) => res.json())
        .then((res) => {
          this.songs.isLoading = false;
          if (res.status === "success") {
            this.songs.songsList = res.songs.data;
            this.songs.currentPage = res.songs.current_page - 1;
            this.songs.lastPage = res.songs.last_page;
          }
        });
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
    this.onLoadArtistSongs();
  },
  mounted() {
    const main = this.$refs.main as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.mainWidth = entry.contentRect.width;
      }
    });
    if (this.observer && main) this.observer.observe(main);
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
  watch: {
    "dialogGroupalbeArtistList.currentPage": function (newVal, oldVal) {
      if (newVal !== oldVal) {
        this.onLoadGroupableArtist();
      }
    },
    "songs.filterText": function () {
      this.onLoadArtistSongs();
    },
    "songs.itemPerPage": function () {
      this.onLoadArtistSongs();
    },
    "songs.currentPage": function () {
      this.onLoadArtistSongs();
    },
  },
  components: {
    BaseFlatDialog,
    BaseCircleLoad,
    BaseButton,
    BaseListItem,
    BaseTableBodyPagination,
  },
});
</script>

<style lang="scss" scoped>
.main {
  width: 90%;
  max-height: calc(100% - 130px);
  display: flex;
  flex-direction: column;
  padding: 20px;
  margin: auto;
  overflow: scroll;
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
  .songs {
    width: 100%;
    display: flex;
    flex-direction: column;
    .tool {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      .filter {
        justify-content: flex-start;
        input {
          height: 22px;
          width: 60%;
          padding: 5px;
          border: 1px solid var(--color-primary);
          background: var(--background-color-secondary);
          color: var(--text-primary-color);
          font-size: 1.2rem;
          font-weight: 500;
          &:focus {
            outline: none;
          }
        }
        select {
          height: 34px;
          width: 30%;
          padding: 5px;
          border: 1px solid var(--color-primary);
          border-right: none;
          background: var(--background-color-secondary);
          color: var(--text-primary-color);
          font-size: 1rem;
          font-weight: 500;
          transform: translateY(-1px);
          &:focus {
            outline: none;
          }
        }
      }
      .item-count {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-self: center;
        padding: 20px;
        label {
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 5px;
        }
        select {
          height: 34px;
          padding: 3px;
          border: 1px solid var(--color-primary);
          background: var(--background-color-secondary);
          color: var(--text-primary-color);
          font-size: 1rem;
          font-weight: 500;
          transform: translateY(-1px);
          &:focus {
            outline: none;
          }
        }
      }
    }
    .data {
      table {
        width: 100%;
        thead {
          tr {
            td {
              padding: 10px;
              font-weight: 900;
              font-size: 1rem;
              background: var(--background-glass-color-primary);
              text-align: center;
              cursor: pointer;
              &.index {
                max-width: 10px;
              }
            }
          }
        }
        tbody {
          tr {
            td {
              padding: 10px;
              font-size: 1rem;
              background: var(--background-glass-color-secondary);
              text-align: center;
              border-bottom: 1px solid var(--background-glass-color-primary);
              overflow: hidden;
              line-clamp: 1;
              text-overflow: ellipsis;
              a {
                color: var(--color-primary);
                text-decoration: none;
                cursor: pointer;
              }
              button {
                padding: 5px 10px;
                background: transparent;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
                margin: 5px 2px;
                border: 1px solid var(--color-primary);
                color: var(--color-primary);
              }
            }
          }
        }
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
}
</style>
