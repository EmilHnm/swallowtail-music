<template>
  <teleport to="body">
    <!-- Group Genre Dialog -->
    <BaseFlatDialog
      :open="dialogConfirmGroup.show"
      :title="dialogConfirmGroup.title"
      :mode="dialogConfirmGroup.mode"
      @close="onCloseGroupConrfirm"
    >
      <template #default>
        <p>
          {{
            `This action will move all songs of ${genre_id} to ${dialogConfirmGroup.to} and delete old genre, are you sure you want to continue?`
          }}
        </p>
      </template>
      <template #action>
        <BaseButton mode="danger" @click="onGroupGenre">Group</BaseButton>
        <BaseButton @click="onCloseGroupConrfirm">Cancel</BaseButton>
      </template>
    </BaseFlatDialog>
    <!-- Groupable Genre Dialog -->
    <BaseFlatDialog
      :open="dialogGroupalbeGenreList.show"
      :title="dialogGroupalbeGenreList.title"
      :mode="dialogGroupalbeGenreList.mode"
      @close="onCloseGroupDialog"
    >
      <template #default>
        <div v-if="!dialogGroupalbeGenreList.isGroupableLoading">
          <div
            style="margin: 10px 0px"
            v-for="genre in dialogGroupalbeGenreList.groupableGenre"
            :key="genre.genre_id"
          >
            <BaseListItem @click="onSelectGenre(genre.genre_id)">{{
              genre.name
            }}</BaseListItem>
          </div>
          <BaseTableBodyPagination
            :totalPages="dialogGroupalbeGenreList.lastPage"
            :currentPage="dialogGroupalbeGenreList.currentPage"
            @onClickPage="onGroupGenreClickPage"
            @onGoToFirstPage="onGroupGenreGoToFirstPage"
            @onGoToPreviousPage="onGroupGenreGoToPreviousPage"
            @onGoToNextPage="onGroupGenreGoToNextPage"
            @onGoToLastPage="onGroupGenreGoToLastPage"
          />
        </div>
        <div>
          <BaseCircleLoad v-if="dialogGroupalbeGenreList.isGroupableLoading" />
        </div>
      </template>
      <template #action>
        <div></div>
      </template>
    </BaseFlatDialog>
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
  <div class="container">
    <h2>Edit genre</h2>
    <form class="genre" @submit.prevent="onSubmit">
      <button class="genre__group" @click.prevent="onOpenGroupDialog">
        Group
      </button>
      <div class="genre__row">
        <label for="">Name</label>
        <input type="text" placeholder="Name" v-model="genre.name" />
      </div>
      <div class="genre__row">
        <label for="">Description</label>
        <textarea
          type="text"
          placeholder="Description"
          v-model="genre.description"
        ></textarea>
      </div>
      <div class="genre__row">
        <button type="submit">Save</button>
      </div>
    </form>
    <h3>Song</h3>
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
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import type { genre } from "@/model/genreModel";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { user } from "@/model/userModel";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import BaseListItem from "@/components/UI/BaseListItem.vue";
import BaseButton from "@/components/UI/BaseButton.vue";

type songData = song & {
  album: album;
  user: user;
};

export default defineComponent({
  data() {
    return {
      genre: {} as genre,
      genre_id: "" as string,
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
      dialogGroupalbeGenreList: {
        title: "Groupable Genre List",
        mode: "announcement",
        show: false,
        groupableGenre: [] as genre[],
        isGroupableLoading: false,
        currentPage: 0,
        lastPage: 0,
      },
      dialogConfirmGroup: {
        title: "Warning",
        mode: "warning",
        to: "",
        show: false,
      },
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      isLoading: false,
      mainWidth: 0,
      observer: null as ResizeObserver | null,
    };
  },
  methods: {
    ...mapActions("admin", [
      "getGenres",
      "getGenre",
      "updateGenre",
      "getGenreSong",
      "groupGenre",
    ]),
    onSubmit() {
      if (this.genre.name === "" || this.genre.description === "") {
        this.dialogWaring = {
          title: "Warning",
          mode: "warning",
          content: "Please fill in all the fields",
          show: true,
        };
        return;
      }
      this.isLoading = true;
      this.updateGenre({
        userToken: this.token,
        genre: this.genre,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success")
            this.dialogWaring = {
              title: "Success",
              mode: "annoucment",
              content: `Successfully updated ${this.genre.name} genre`,
              show: true,
            };
          else
            this.dialogWaring = {
              title: "Warning",
              mode: "warning",
              content: res.errors,
              show: true,
            };
        });
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    onLoadGenre() {
      this.isLoading = true;
      this.getGenre({ genre_id: this.genre_id, userToken: this.token })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.genre = res.genre;
          } else {
            this.dialogWaring.content = res.message;
            this.dialogWaring.show = true;
          }
        });
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
    navigateToSongEdit(id: string) {
      this.$router.push({ name: "accountAdminDiscSongEdit", params: { id } });
    },
    onLoadGenreSong() {
      this.songs.isLoading = true;
      if (!this.songs.controller) {
        this.songs.controller = new AbortController();
      } else {
        this.songs.controller.abort();
        this.songs.controller = new AbortController();
      }
      this.songs.signal = this.songs.controller.signal;
      this.getGenreSong({
        userToken: this.token,
        genre_id: this.genre_id,
        signal: this.songs.signal,
        query: this.songs.filterText,
        itemPerPage: this.songs.itemPerPage,
        currentPage: this.songs.currentPage,
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
    onOpenGroupDialog() {
      this.onLoadGroupableGenre();
      this.dialogGroupalbeGenreList.show = true;
    },
    onCloseGroupDialog() {
      this.dialogGroupalbeGenreList.show = false;
      this.dialogGroupalbeGenreList.currentPage = 0;
    },
    onGroupGenreClickPage(page: number) {
      this.dialogGroupalbeGenreList.currentPage = page;
    },
    onGroupGenreGoToFirstPage() {
      this.dialogGroupalbeGenreList.currentPage = 0;
    },
    onGroupGenreGoToLastPage() {
      this.dialogGroupalbeGenreList.currentPage =
        this.dialogGroupalbeGenreList.lastPage - 1;
    },
    onGroupGenreGoToNextPage() {
      this.dialogGroupalbeGenreList.currentPage--;
    },
    onGroupGenreGoToPreviousPage() {
      this.dialogGroupalbeGenreList.currentPage--;
    },
    onSelectGenre(id: string) {
      this.dialogConfirmGroup.to = id;
      this.onCloseGroupDialog();
      this.onOpenGroupConrfirm();
    },
    onLoadGroupableGenre() {
      this.dialogGroupalbeGenreList.isGroupableLoading = true;
      this.getGenres({
        userToken: this.token,
        query: "",
        itemPerPage: 5,
        currentPage: this.dialogGroupalbeGenreList.currentPage + 1,
      })
        .then((res) => res.json())
        .then((res) => {
          this.dialogGroupalbeGenreList.isGroupableLoading = false;
          if (res.status === "success") {
            this.dialogGroupalbeGenreList.groupableGenre =
              res.genres.data.filter(
                (genre: genre) => genre.id !== this.genre.id
              );
            this.dialogGroupalbeGenreList.currentPage =
              res.genres.current_page - 1;
            this.dialogGroupalbeGenreList.lastPage = res.genres.last_page;
          }
        });
    },
    onCloseGroupConrfirm() {
      this.dialogConfirmGroup.show = false;
    },
    onOpenGroupConrfirm() {
      this.dialogConfirmGroup.show = true;
    },
    onGroupGenre() {
      this.isLoading = true;
      this.groupGenre({
        userToken: this.token,
        from: this.genre_id,
        to: this.dialogConfirmGroup.to,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.$router.push({
              name: "accountAdminDiscGenreEdit",
              params: { id: this.dialogConfirmGroup.to },
            });
          } else {
            this.dialogWaring.content = res.message;
            this.dialogWaring.show = true;
          }
        });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  watch: {
    "dialogGroupalbeGenreList.currentPage": function (newVal, oldVal) {
      if (newVal !== oldVal) {
        this.onLoadGroupableGenre();
      }
    },
    "songs.filterText": function () {
      this.onLoadGenreSong();
    },
    "songs.itemPerPage": function () {
      this.onLoadGenreSong();
    },
    "songs.currentPage": function () {
      this.onLoadGenreSong();
    },
  },
  created() {
    this.genre_id = this.$route.params.id as string;
    this.onLoadGenre();
    this.onLoadGenreSong();
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
  components: {
    BaseFlatDialog,
    BaseCircleLoad,
    BaseTableBodyPagination,
    BaseListItem,
    BaseButton,
  },
});
</script>

<style lang="scss" scoped>
.container {
  width: 90%;
  max-height: calc(100% - 130px);
  display: flex;
  flex-direction: column;
  overflow: scroll;
  padding: 20px;
  position: relative;
  h2 {
    margin-bottom: 20px;
    font-weight: 900;
    font-size: 2rem;
  }
  .genre {
    display: flex;
    flex-direction: column;
    & button {
      margin: auto;
      height: 40px;
      background: var(--color-primary);
      color: #fff;
      padding: 0 20px;
      font-size: 1rem;
      border-radius: 10px;
      border: none;
      width: max-content;
      float: right;
      cursor: pointer;
      transition: all 0.5s;
      &:focus {
        outline: none;
      }
      &:hover {
        transform: scale(1.05);
        font-weight: 500;
      }
    }
    &__group {
      position: absolute;
      right: 10px;
      top: 50px;
    }
    &__row {
      width: 100%;
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
      label {
        font-size: 1rem;
        font-weight: 900;
        margin-bottom: 5px;
      }
      input {
        height: 40px;
        border: 1px solid var(--text-primary-color);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        padding: 0 10px;
        font-size: 1rem;
        &:focus {
          outline: none;
          border-color: var(--color-primary);
        }
      }
      textarea {
        height: 40px;
        border: 1px solid var(--text-primary-color);
        background: var(--background-color-secondary);
        color: var(--text-primary-color);
        padding: 10px;
        font-size: 1rem;
        min-height: 150px;
        max-height: 300px;
        resize: vertical;
        &:focus {
          outline: none;
          border-color: var(--color-primary);
        }
      }
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
}
</style>
