<template>
  <div class="admin-albums-container" ref="main">
    <h3>Albums List</h3>
    <div class="tool">
      <div class="item-count">
        <label for="itemPerPage">Item Per Page</label>
        <select name="itemPerPage" id="itemPerPage" v-model="itemPerPage">
          <option value="10" :selected="itemPerPage === 10">10</option>
          <option value="30" :selected="itemPerPage === 30">30</option>
          <option value="50" :selected="itemPerPage === 50">50</option>
        </select>
      </div>
      <div class="filter">
        <select name="filterType" id="filterType" v-model="filterType">
          <option value="title" :selected="filterType === 'title'">
            Album title
          </option>
          <option value="uploader" :selected="filterType === 'uploader'">
            Uploader
          </option>
          <option value="year" :selected="filterType === 'year'">
            Release Year
          </option>
        </select>
        <input type="text" name="filterText" v-model="filterText" />
      </div>
    </div>
    <div class="data">
      <table v-if="albumsList.length !== 0 && !isLoading">
        <thead>
          <tr>
            <td>#</td>
            <td>Title</td>
            <td>Uploader</td>
            <td>Song</td>
            <td>Upload date</td>
            <td>Control</td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in albumsList" :key="item.album_id">
            <td>
              {{ index + 1 + currentPage * itemPerPage }}
            </td>
            <td>
              {{ item.name }}
            </td>
            <!-- <td v-if="mainWidth > 550"> -->
            <td>
              {{ item.user?.name ?? "Unknow" }}
            </td>
            <!-- <td v-if="mainWidth > 600"> -->
            <td>
              {{ item.song_count }}
            </td>
            <td>
              {{ new Date(item.created_at).toLocaleDateString() }}
            </td>
            <td>
              <button
                class="btn btn-danger"
                @click="navigateToEdit(item.album_id)"
              >
                Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <BaseCircleLoad v-if="isLoading" />
    </div>
    <div class="pagination" v-if="!isLoading && albumsList.length !== 0">
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
    <div class="no-results" v-if="!isLoading && albumsList.length <= 0">
      <h3>No results</h3>
    </div>
  </div>
</template>

<script lang="ts">
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import type { album } from "@/model/albumModel";
import type { user } from "@/model/userModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

type albumData = album & {
  song_count: number;
  user: user | null;
};

export default defineComponent({
  data() {
    return {
      observer: null as ResizeObserver | null,
      mainWidth: 0,
      albumsList: [] as albumData[],
      currentPage: 0,
      totalPages: 0,
      itemPerPage: 10,
      filterType: "title",
      filterText: "",
      isLoading: false,
      filterController: null as AbortController | null,
      filterSignal: null as AbortSignal | null,
    };
  },
  methods: {
    ...mapActions("admin", ["getAllAlbums"]),
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
    loadAlbumList() {
      this.isLoading = true;
      if (!this.filterController) {
        this.filterController = new AbortController();
      } else {
        this.filterController.abort();
        this.filterController = new AbortController();
      }
      this.filterSignal = this.filterController.signal;
      this.getAllAlbums({
        token: this.token,
        page: this.currentPage,
        filterText: this.filterText,
        filterType: this.filterType,
        itemPerPage: this.itemPerPage,
        currentPage: this.currentPage,
      })
        .then((res: any) => res.json())
        .then((res: any) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.albumsList = res.albums.data;
            this.currentPage = res.albums.current_page - 1;
            this.totalPages = res.albums.last_page;
          }
        });
    },
    navigateToEdit(id: string) {
      this.$router.push({ name: "accountAdminDiscAlbumEdit", params: { id } });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.loadAlbumList();
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
      this.loadAlbumList();
    },
    filterText() {
      this.loadAlbumList();
    },
    currentPage() {
      this.loadAlbumList();
    },
  },
  components: {
    BaseTableBodyPagination,
    BaseCircleLoad,
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.admin-albums-container {
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
