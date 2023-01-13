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
    </BaseFlatDialog>
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
    <h3>Album Upload Management</h3>
    <div class="data" v-if="uploadedAlbumList.length > 0">
      <div class="filter">
        <label for="filterText"><IconSearch /></label>
        <input
          type="text"
          name="filterText"
          id="filterText"
          v-model="filterText"
        />
      </div>
      <table>
        <thead>
          <tr>
            <td>#</td>
            <td v-if="mainWidth > 450">Cover</td>
            <td @click="sortList('title')">Title</td>
            <td v-if="mainWidth > 500" @click="sortList('release')">Release</td>
            <td v-if="mainWidth > 700" @click="sortList('type')">Type</td>
            <td v-if="mainWidth > 600" @click="sortList('songCount')">
              Song Count
            </td>
            <td @click="sortList('uploadDate')">Upload Date</td>
            <td>Control</td>
          </tr>
        </thead>
        <tbody v-if="!filterText">
          <tr v-for="(album, index) in uploadedAlbumList" :key="album.album_id">
            <td>{{ index + 1 }}</td>
            <td v-if="mainWidth > 450">
              <img
                :src="`${environment.album_cover}/${album.image_path}`"
                alt=""
                srcset=""
              />
            </td>
            <td>{{ album.name }}</td>
            <td v-if="mainWidth > 500">{{ album.release_year }}</td>
            <td v-if="mainWidth > 700">{{ album.type }}</td>
            <td v-if="mainWidth > 600">{{ album.songCount }}</td>
            <td>{{ new Date(album.created_at).toLocaleDateString() }}</td>
            <td>
              <button class="edit" @click="redirectEdit(album.album_id)">
                Edit
              </button>
              <button class="delete" @click="deleteItem(album.album_id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr
            v-for="(album, index) in filteredUploadedAlbumList"
            :key="album.album_id"
          >
            <td>{{ index + 1 }}</td>
            <td v-if="mainWidth > 450">
              <img
                :src="`${environment.album_cover}/${album.image_path}`"
                alt=""
                srcset=""
              />
            </td>
            <td>{{ album.name }}</td>
            <td v-if="mainWidth > 500">{{ album.release_year }}</td>
            <td v-if="mainWidth > 700">{{ album.type }}</td>
            <td v-if="mainWidth > 600">{{ album.songCount }}</td>
            <td>{{ new Date(album.created_at).toLocaleDateString() }}</td>
            <td>
              <button class="edit" @click="redirectEdit(album.album_id)">
                Edit
              </button>
              <button class="delete" @click="deleteItem(album.album_id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="no-data" v-else>
      <h3>No Album Uploaded</h3>
    </div>
  </div>
</template>

<script lang="ts">
import IconSearch from "@/components/icons/IconSearch.vue";
import type { album } from "@/model/albumModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";

type albumData = album & { songCount: number };

export default defineComponent({
  data() {
    return {
      environment: environment,
      uploadedAlbumList: [] as albumData[],
      filteredUploadedAlbumList: [] as albumData[],
      observer: null as ResizeObserver | null,
      mainWidth: 0,
      filterText: "",
      isLoading: false,
      sortMode: {
        title: "asc",
        release: "asc",
        type: "asc",
        songCount: "asc",
        uploadDate: "asc",
      },
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  methods: {
    ...mapActions("album", ["getUploadedAlbums", "deleteAlbum"]),
    sortList(colname: string) {
      if (colname === "title") {
        if (this.sortMode.title == "asc") {
          this.sortMode.title = "desc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.name > b.name ? -1 : 1
          );
        } else {
          this.sortMode.title = "asc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.name < b.name ? -1 : 1
          );
        }
      }
      if (colname === "release") {
        if (this.sortMode.release == "asc") {
          this.sortMode.release = "desc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.release_year > b.release_year ? -1 : 1
          );
        } else {
          this.sortMode.release = "asc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.release_year < b.release_year ? -1 : 1
          );
        }
      }
      if (colname === "type") {
        if (this.sortMode.type == "asc") {
          this.sortMode.type = "desc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.type > b.type ? -1 : 1
          );
        } else {
          this.sortMode.type = "asc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.type < b.type ? -1 : 1
          );
        }
      }
      if (colname === "songCount") {
        if (this.sortMode.songCount == "asc") {
          this.sortMode.songCount = "desc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.songCount > b.songCount ? -1 : 1
          );
        } else {
          this.sortMode.type = "asc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.songCount < b.songCount ? -1 : 1
          );
        }
      }
      if (colname === "uploadDate") {
        if (this.sortMode.uploadDate == "asc") {
          this.sortMode.uploadDate = "desc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.created_at > b.created_at ? -1 : 1
          );
        } else {
          this.sortMode.uploadDate = "asc";
          this.uploadedAlbumList = this.uploadedAlbumList.sort((a, b) =>
            a.created_at < b.created_at ? -1 : 1
          );
        }
      }
    },
    deleteItem(album_id: string) {
      this.isLoading = true;
      this.deleteAlbum({ userToken: this.token, albumId: album_id })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.uploadedAlbumList.splice(
              this.uploadedAlbumList.findIndex(
                (album) => album.album_id === album_id
              ),
              1
            );
            this.isLoading = false;
            this.dialogWaring.mode = "announcement";
            this.dialogWaring.content = res.message;
            this.dialogWaring.title = "Success";
            this.dialogWaring.show = true;
          }
        });
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    redirectEdit(id: string) {
      this.$router.push({
        name: "accountUploadAlbumEdit",
        query: { id },
      });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  components: { IconSearch, BaseFlatDialog, BaseCircleLoad },
  watch: {
    filterText() {
      if (this.filterText === "") {
        this.filteredUploadedAlbumList.length = 0;
        return;
      }
      this.filteredUploadedAlbumList = this.uploadedAlbumList.filter(
        (album: albumData) =>
          album.name.toLowerCase().includes(this.filterText.toLowerCase()) ||
          album.type.toLowerCase().includes(this.filterText.toLowerCase())
      );
    },
  },
  created() {
    this.isLoading = true;
    this.getUploadedAlbums(this.token)
      .then((res) => res.json())
      .then((res) => {
        if (res.status === "success") {
          this.uploadedAlbumList = res.album;
          this.isLoading = false;
        }
      });
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
  unmounted() {
    if (this.observer) this.observer.disconnect();
  },
});
</script>

<style lang="scss" scoped>
.main {
  width: 100%;
  display: flex;
  flex-direction: column;
  h3 {
    text-align: center;
  }
  .data {
    & .filter {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      align-self: center;
      padding: 20px;
      label {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% 0% 0% 50%;
        padding: 5px;
        background-color: var(--color-primary);
        svg {
          width: 20px;
          height: 20px;
          fill: #fff;
        }
      }
      input {
        height: 22px;
        border-radius: 0% 20px 20px 0%;
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
    }
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
            user-select: none;
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
            img {
              width: 50px;
              height: 50px;
              object-fit: cover;
              user-select: none;
            }
            button {
              padding: 5px 10px;
              background: transparent;
              font-size: 1rem;
              cursor: pointer;
              transition: all 0.3s ease-in-out;
              margin: 5px 2px;
              &.edit {
                border: 1px solid var(--color-primary);
                color: var(--color-primary);
                &:focus {
                  outline: none;
                }
                &:hover {
                  background: var(--color-primary);
                  color: var(--background-color-secondary);
                }
              }
              &.delete {
                border: 1px solid var(--color-danger);
                color: var(--color-danger);
                &:focus {
                  outline: none;
                }
                &:hover {
                  background: var(--color-danger);
                  color: var(--background-color-secondary);
                }
              }
            }
          }
        }
      }
    }
  }
}
</style>
