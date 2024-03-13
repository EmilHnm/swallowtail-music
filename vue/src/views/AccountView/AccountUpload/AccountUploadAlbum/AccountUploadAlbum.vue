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
            <td class="index" @click="sortList('id')">#</td>
            <td class="cover">Cover</td>
            <td class="title" @click="sortList('name')">Title</td>
            <td class="release" @click="sortList('release_year')">Release</td>
            <td class="type" @click="sortList('type')">Type</td>
            <td class="count">Song Count</td>
            <td class="uploaded" @click="sortList('created_at')">
              Upload Date
            </td>
            <td class="control">Control</td>
          </tr>
        </thead>
        <template v-if="isLoading">
          <tr>
            <td class="index" colspan="8">
              <BaseCircleLoad />
            </td>
          </tr>
          ">
        </template>
        <template v-else>
          <tbody>
            <tr
              v-for="(album, index) in uploadedAlbumList"
              :key="album.album_id"
            >
              <td class="index">{{ index + 1 }}</td>
              <td class="cover">
                <img
                  v-lazyload
                  :data-url="`${environment.album_cover}/${album.image_path}`"
                  alt=""
                  srcset=""
                />
              </td>
              <td class="title">
                <BaseTooltip :tooltip-text="album.name" :position="'bottom'">
                  {{
                    album.name.slice(0, 15) +
                    (album.name.length > 15 ? "..." : "")
                  }}
                </BaseTooltip>
              </td>
              <td class="release">
                {{ album.release_year }}
              </td>
              <td class="type">{{ album.type }}</td>
              <td class="count">{{ album.song_count }}</td>
              <td class="uploaded">
                {{ new Date(album.created_at).toLocaleDateString() }}
              </td>
              <td>
                <div class="control">
                  <button class="edit" @click="redirectEdit(album.album_id)">
                    Edit
                  </button>
                  <ConfirmFlatDialog
                    :passing-data="album.album_id"
                    @confirm="deleteItem"
                  >
                    <template #message>
                      <p>
                        Are you sure you want to delete album "{{
                          album.name
                        }}"?
                      </p>
                    </template>
                    <button class="delete">Delete</button>
                  </ConfirmFlatDialog>
                </div>
              </td>
            </tr>
          </tbody>
        </template>
      </table>
      <div class="pagination" v-if="!isLoading && uploadedAlbumList.length > 0">
        <BaseTableBodyPagination
          :totalPages="paginate.totalPages"
          :currentPage="paginate.currentPage"
          @onClickPage="onClickPage"
          @onGoToFirstPage="onGoToFirstPage"
          @onGoToPreviousPage="onGoToPreviousPage"
          @onGoToNextPage="onGoToNextPage"
          @onGoToLastPage="onGoToLastPage"
        />
      </div>
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
import BaseTooltip from "@/components/UI/BaseTooltip.vue";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import type { Pagination } from "@/model/mixin/PaginateDataModel";
import ConfirmFlatDialog from "@/components/AccountView/partials/Dialog/ConfirmFlatDialog.vue";

type albumData = album & { song_count: number };

type ResponseData = Pagination & {
  data: albumData[];
};

export default defineComponent({
  data() {
    return {
      environment: environment,
      uploadedAlbumList: [] as albumData[],
      filterText: "",
      isLoading: false,
      sortMode: {
        column: "id",
        type: "asc",
      },
      abortController: new AbortController(),
      abortSignal: null as AbortSignal | null,
      paginate: {
        currentPage: 0,
        totalPages: 0,
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
      if (this.sortMode.column === colname)
        if (this.sortMode.type === "asc") {
          this.sortMode.type = "desc";
        } else {
          this.sortMode.type = "asc";
        }
      else this.sortMode.type = "asc";
      this.sortMode.column = colname;
      this.loadData();
    },
    loadData() {
      this.isLoading = true;
      this.abortController.abort();
      this.abortController = new AbortController();
      this.abortSignal = this.abortController.signal;
      this.getUploadedAlbums({
        userToken: this.token,
        page: this.paginate.currentPage + 1,
        query: this.filterText,
        sort: this.sortMode,
        signal: this.abortSignal,
      })
        .then((res) => res.json())
        .then((res: { status: string; albums: ResponseData }) => {
          if (res.status === "success") {
            this.uploadedAlbumList = res.albums.data;
            this.paginate.currentPage = res.albums.current_page - 1;
            this.paginate.totalPages = res.albums.last_page;
            this.isLoading = false;
          }
        })
        .catch((err) => {
          if (err instanceof DOMException && err.name === "AbortError") {
            return;
          }
          this.dialogWaring.mode = "warning";
          this.dialogWaring.content = err.message;
          this.dialogWaring.title = "Warning";
          this.dialogWaring.show = true;
          this.isLoading = false;
        });
    },
    deleteItem(album_id: string) {
      this.isLoading = true;
      this.deleteAlbum({ userToken: this.token, albumId: album_id })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.loadData();
            this.dialogWaring.mode = "announcement";
            this.dialogWaring.content = res.message;
            this.dialogWaring.title = "Success";
            this.dialogWaring.show = true;
          } else {
            this.dialogWaring.mode = "warning";
            this.dialogWaring.content = res.message;
            this.dialogWaring.title = "Warning";
            this.dialogWaring.show = true;
          }
          this.isLoading = false;
        });
    },
    onClickPage(index: number) {
      this.paginate.currentPage = index;
    },
    onGoToFirstPage() {
      this.paginate.currentPage = 0;
    },
    onGoToPreviousPage() {
      this.paginate.currentPage = this.paginate.currentPage - 1;
    },
    onGoToNextPage() {
      this.paginate.currentPage = this.paginate.currentPage + 1;
    },
    onGoToLastPage() {
      this.paginate.currentPage = this.paginate.totalPages - 1;
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
  components: {
    IconSearch,
    BaseFlatDialog,
    BaseCircleLoad,
    BaseTooltip,
    BaseTableBodyPagination,
    ConfirmFlatDialog,
  },
  watch: {
    filterText() {
      this.paginate.currentPage = 0;
      this.loadData();
    },
    paginate: {
      handler() {
        this.loadData();
      },
      deep: true,
    },
  },
  mounted() {
    this.loadData();
  },
});
</script>

<style lang="scss" scoped>
.main {
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
            user-select: none;
            &.id,
            &.title,
            &.release,
            &.type,
            &.uploaded {
              cursor: pointer;
            }
            @media screen and (max-width: 500px) {
              &.cover {
                display: none;
              }
            }
            @media screen and (max-width: 600px) {
              &.release {
                display: none;
              }
            }
            @media screen and (max-width: 700px) {
              &.type {
                display: none;
              }
              &.count {
                display: none;
              }
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
            & .control {
              display: flex;
              flex-direction: row;
              justify-content: center;
              align-items: center;
              @media screen and (max-width: 500px) {
                flex-direction: column;
              }
            }
            @media screen and (max-width: 500px) {
              &.cover {
                display: none;
              }
            }
            @media screen and (max-width: 600px) {
              &.release {
                display: none;
              }
            }
            @media screen and (max-width: 700px) {
              &.type {
                display: none;
              }
              &.count {
                display: none;
              }
            }
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
