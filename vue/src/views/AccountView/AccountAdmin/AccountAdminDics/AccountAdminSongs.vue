<template>
  <teleport to="body">
    <BaseFlatDialog
      :open="isLoading"
      :title="'Loading ...'"
      :mode="'announcement'"
    >
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseFlatDialog>
  </teleport>
  <div class="admin-songs-container" ref="main">
    <h3>Songs List</h3>
    <div class="tool">
      <div class="item-count">
        <label for="itemPerPage">Item Per Page</label>
        <select name="filterType" id="itemPerPage" v-model="itemPerPage">
          <option value="10" :selected="itemPerPage === 10">10</option>
          <option value="30" :selected="itemPerPage === 30">30</option>
          <option value="50" :selected="itemPerPage === 50">50</option>
        </select>
      </div>
      <div class="filter">
        <select name="filterType" id="filterType" v-model="filterType">
          <option value="title" :selected="filterType === 'title'">
            Song title
          </option>
          <option value="uploader" :selected="filterType === 'uploader'">
            Uploader
          </option>
          <option value="album" :selected="filterType === 'album'">
            Album Name
          </option>
        </select>
        <input type="text" name="filterText" v-model="filterText" />
      </div>
    </div>
    <div class="data">
      <table v-if="songsList.length !== 0">
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
        <tbody v-if="!filterText && songsList.length > 0">
          <tr
            v-for="index in countItemInPage"
            :key="index + currentPage * itemPerPage"
          >
            <td>
              {{ index + currentPage * itemPerPage }}
            </td>
            <td>
              {{ songsList[index - 1 + currentPage * itemPerPage].title }}
            </td>
            <td v-if="mainWidth > 550">
              {{ songsList[index - 1 + currentPage * itemPerPage].user.name }}
            </td>
            <td v-if="mainWidth > 600">
              {{ songsList[index - 1 + currentPage * itemPerPage].album.name }}
            </td>
            <td>
              {{
                new Date(
                  songsList[index - 1 + currentPage * itemPerPage].created_at
                ).toLocaleDateString()
              }}
            </td>
            <td>
              <button
                class="btn btn-danger"
                @click="
                  navigateToEdit(
                    songsList[index - 1 + currentPage * itemPerPage].song_id
                  )
                "
              >
                Edit
              </button>
            </td>
          </tr>
        </tbody>

        <tbody v-if="filterText && filteredSongList.length > 0">
          <tr
            v-for="index in countItemInPage"
            :key="index + currentPage * itemPerPage"
          >
            <td>
              {{ index + currentPage * itemPerPage }}
            </td>
            <td>
              {{
                filteredSongList[index - 1 + currentPage * itemPerPage].title
              }}
            </td>
            <td v-if="mainWidth > 550">
              {{
                filteredSongList[index - 1 + currentPage * itemPerPage].user
                  .name
              }}
            </td>
            <td v-if="mainWidth > 600">
              {{
                filteredSongList[index - 1 + currentPage * itemPerPage].album
                  .name
              }}
            </td>
            <td>
              {{
                new Date(
                  filteredSongList[
                    index - 1 + currentPage * itemPerPage
                  ].created_at
                ).toLocaleDateString()
              }}
            </td>
            <td>
              <button
                class="btn btn-danger"
                @click="
                  navigateToEdit(
                    songsList[index - 1 + currentPage * itemPerPage].song_id
                  )
                "
              >
                Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="pagination">
      <BaseTableBodyPagination
        :totalPages="totalPages"
        :currentPage="currentPage"
        @onClickPage="onClickPage"
        @onGoToFirstPage="onGoToFirstPage"
        @onGoToPreviousPage="onGoToPreviousPage"
        @onGoToNextPage="onGoToNextPage"
        @onGoToLastPage="onGoToLastPage"
      />
    </div>
  </div>
</template>

<script lang="ts">
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import type { album } from "@/model/albumModel";
import type { song } from "@/model/songModel";
import type { user } from "@/model/userModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

type songData = song & {
  album: album;
  user: user;
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    filteredSongList: songData[];
    totalPages: number;
    countItemInPage: number;
  }
}

export default defineComponent({
  data() {
    return {
      observer: null as ResizeObserver | null,
      mainWidth: 0,
      songsList: [] as songData[],
      currentPage: 0,
      itemPerPage: 10,
      filterType: "title",
      filterText: "",
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("admin", ["getAllSongs"]),
    onClickPage(index: number) {
      this.currentPage = index;
    },
    onGoToFirstPage() {
      this.currentPage = 0;
    },
    onGoToPreviousPage() {
      this.currentPage = this.currentPage - 1;
    },
    onGoToNextPage() {
      this.currentPage = this.currentPage + 1;
    },
    onGoToLastPage() {
      this.currentPage = this.totalPages - 1;
    },
    loadSongList() {
      this.isLoading = true;
      this.getAllSongs(this.token)
        .then((res: any) => res.json())
        .then((res: any) => {
          this.isLoading = false;
          if (res.status === "success") this.songsList = res.songs;
        });
    },
    navigateToEdit(id: string) {
      this.$router.push({ name: "accountAdminDiscSongEdit", params: { id } });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
    totalPages() {
      if (this.songsList.length === 0) return 0;
      if (this.filterText !== "")
        return Math.ceil(this.filteredSongList.length / this.itemPerPage);
      return Math.ceil(this.songsList.length / this.itemPerPage);
    },
    countItemInPage() {
      if (this.filterText !== "")
        return +this.currentPage + 1 === this.totalPages
          ? this.filteredSongList.length - this.currentPage * +this.itemPerPage
          : +this.itemPerPage;
      return +this.currentPage + 1 === this.totalPages
        ? this.songsList.length - this.currentPage * +this.itemPerPage
        : +this.itemPerPage;
    },
    filteredSongList(): songData[] {
      if (this.filterText !== "") {
        if (this.filterType === "title") {
          return this.songsList.filter(
            (song) =>
              song.title
                .toLowerCase()
                .includes(this.filterText.toLowerCase()) ||
              song.sub_title
                ?.toLowerCase()
                .includes(this.filterText.toLowerCase())
          );
        }
        if (this.filterType === "uploader") {
          return this.songsList.filter((song) =>
            song.user.name.toLowerCase().includes(this.filterText.toLowerCase())
          );
        }
        if (this.filterType === "album") {
          return this.songsList.filter((song) =>
            song.album.name
              .toLowerCase()
              .includes(this.filterText.toLowerCase())
          );
        }
      }
      return [] as songData[];
    },
  },
  created() {
    this.loadSongList();
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
    itemPerPage() {
      this.currentPage = 0;
    },
    filterText() {
      this.currentPage = 0;
    },
  },
  components: { BaseTableBodyPagination, BaseFlatDialog, BaseLineLoad },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.admin-songs-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  h3 {
    text-align: center;
  }
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
@media (max-width: $tablet-width) {
  .admin-songs-container {
    .tool {
      display: block;
      margin-bottom: 20px;
      .item-count {
        justify-content: flex-start;
      }
      .filter {
        width: 90%;
        margin: 0 auto;
        select {
          transform: translateY(0);
        }
      }
    }
  }
}
</style>
