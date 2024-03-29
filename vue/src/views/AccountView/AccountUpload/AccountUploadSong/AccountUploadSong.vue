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
    <h3>Song Uploaded Management</h3>
    <div class="data" v-if="Object.keys(uploadedSongList).length">
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
            <td class="title" @click="sortList('title')">Title</td>
            <td class="artist">Artist</td>
            <td class="uploadDate" @click="sortList('created_at')">Upload</td>
            <td class="control">Control</td>
          </tr>
        </thead>
        <tbody v-if="isLoading">
          <tr>
            <td colspan="5" style="min-height: 60vh">
              <BaseCircleLoad />
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr v-for="(song, i) in uploadedSongList" :key="i">
            <td>{{ i + 1 + paginate.currentPage * 15 }}</td>
            <td>
              <BaseTooltip :tooltipText="song.title" :position="'bottom'">
                {{
                  song.title.slice(0, 15) +
                  (song.title.length > 15 ? "..." : "")
                }}
              </BaseTooltip>
            </td>
            <td class="artist" v-if="song.artist.length">
              <template v-for="artist in song.artist" :key="artist.artist_id">
                <router-link
                  :to="{
                    name: 'artistOverviewPage',
                    params: { id: artist.artist_id },
                  }"
                >
                  <BaseTooltip :tooltipText="artist.name" :position="'bottom'">
                    {{
                      artist.name.slice(0, 15) +
                      (artist.name.length > 15 ? "..." : "")
                    }}
                  </BaseTooltip>
                  <br />
                </router-link>
              </template>
            </td>
            <td v-else>Unset</td>
            <td>{{ new Date(song.created_at).toLocaleDateString() }}</td>
            <td>
              <div class="control">
                <button class="edit" @click="navigateToEdit(song.song_id)">
                  Edit
                </button>
                <ConfirmFlatDialogVue
                  :passingData="song.song_id"
                  @confirm="deleteItem"
                >
                  <template #message>
                    <p>
                      Are you sure you want to delete song "{{ song.title }}"?
                    </p>
                  </template>
                  <button class="delete">Delete</button>
                </ConfirmFlatDialogVue>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="pagination" v-if="!isLoading && uploadedSongList.length > 0">
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
      <h3>No Song Uploaded</h3>
    </div>
  </div>
</template>

<script lang="ts">
import IconSearch from "@/components/icons/IconSearch.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import type { song } from "@/model/songModel";
import type { artist } from "@/model/artistModel";
import type { Pagination } from "@/model/mixin/PaginateDataModel";
import BaseTooltip from "@/components/UI/BaseTooltip.vue";
import ConfirmFlatDialogVue from "@/components/AccountView/partials/Dialog/ConfirmFlatDialog.vue";

type songData = song & {
  artist: artist[];
};

type SongResData = Pagination & {
  data: songData[];
};

export default defineComponent({
  data() {
    return {
      uploadedSongList: [] as songData[],
      filteredUploadedSongList: [] as songData[],
      filterText: "",
      isLoading: true,
      paginate: {
        currentPage: 0,
        totalPages: 0,
      },
      sortMode: {
        column: "id",
        type: "asc",
      },
      abortController: new AbortController(),
      abortSignal: null as AbortSignal | null,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  methods: {
    ...mapActions("song", ["getUploadedSongs", "deleteSong"]),
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
    navigateToEdit(id: string | number) {
      this.$router.push({
        name: "accountUploadSongEdit",
        query: { id },
      });
    },
    deleteItem(id: string) {
      this.isLoading = true;
      this.deleteSong({ userToken: this.token, songId: id })
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
    loadData() {
      this.isLoading = true;
      this.abortController.abort();
      this.abortController = new AbortController();
      this.abortSignal = this.abortController.signal;
      this.getUploadedSongs({
        userToken: this.token,
        page: this.paginate.currentPage + 1,
        query: this.filterText,
        sort: this.sortMode,
        signal: this.abortSignal,
      })
        .then((res) => res.json())
        .then((res: { songs: SongResData; status: string }) => {
          this.uploadedSongList = res.songs.data;
          this.paginate.currentPage = res.songs.current_page - 1;
          this.paginate.totalPages = res.songs.last_page;
          this.isLoading = false;
        })
        .catch((err) => {
          if (err instanceof DOMException && err.name === "AbortError") {
            return;
          }
        });;
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
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
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
  components: {
    IconSearch,
    BaseFlatDialog,
    BaseCircleLoad,
    BaseTableBodyPagination,
    BaseTooltip,
    ConfirmFlatDialogVue,
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
            &.artist {
              @media screen and (max-width: 600px) {
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
            &.artist {
              @media screen and (max-width: 600px) {
                display: none;
              }
            }
            & .control {
              display: flex;
              flex-direction: row;
              justify-content: center;
              align-items: center;
              @media screen and (max-width: 500px) {
                flex-direction: column;
              }
            }
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
