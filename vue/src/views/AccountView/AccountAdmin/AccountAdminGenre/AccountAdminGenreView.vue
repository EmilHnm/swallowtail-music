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
      :open="dialogGenreDelete.show"
      :title="dialogGenreDelete.title"
      :mode="dialogGenreDelete.mode"
      @close="closedialogGenreDelete"
    >
      <template #default>
        <p>{{ dialogGenreDelete.content }}</p>
      </template>
      <template #action>
        <button class="danger" @click="onDeleteGenre">Delete</button>
        <button class="" @click="closedialogGenreDelete">Close</button>
      </template>
    </BaseFlatDialog>
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
  <div class="main" ref="main">
    <h3>Artists Management</h3>
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
        <input type="text" name="filterText" v-model="filterText" />
      </div>
    </div>
    <div class="data">
      <table>
        <thead>
          <tr>
            <td>#</td>
            <td>Id</td>
            <td>Name</td>
            <td v-if="mainWidth > 500">Song Count</td>
            <td>Control</td>
          </tr>
        </thead>
        <tbody v-if="!isListLoading">
          <tr v-for="(genre, index) in gernes" :key="genre.genre_id">
            <td>{{ index + 1 + currentPage * itemPerPage }}</td>
            <td>{{ genre.genre_id }}</td>
            <td>{{ genre.name }}</td>
            <td v-if="mainWidth > 500">
              {{ genre.song_count }}
            </td>
            <td>
              <button class="show" @click="editGenre(genre.genre_id)">
                Edit
              </button>
              <button
                class="delete"
                @click="deleteGenreConfirm(genre.genre_id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <BaseCircleLoad v-if="isListLoading" />
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
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseTableBodyPagination from "@/components/UI/BaseTableBodyPagination.vue";
import type { genre } from "@/model/genreModel";

type genreData = genre & {
  song_count: number;
};

export default defineComponent({
  data() {
    return {
      gernes: [] as genreData[],
      filterText: "",
      itemPerPage: 10,
      currentPage: 0,
      totalPages: 0,
      filterController: null as AbortController | null,
      filterSignal: null as AbortSignal | null,
      environment: environment,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      dialogGenreDelete: {
        title: "Warning",
        mode: "warning",
        genre_id: "",
        content: `Are you sure you want to delete this genre?`,
        show: false,
      },
      isLoading: false,
      isListLoading: false,
      observer: null as ResizeObserver | null,
      mainWidth: 0,
    };
  },
  methods: {
    ...mapActions("admin", ["getGenres", "deleteGenre"]),
    editGenre(id: string) {
      this.$router.push({ name: "accountAdminGenreEdit", params: { id: id } });
    },
    deleteGenreConfirm(genre_id: string) {
      this.dialogGenreDelete.genre_id = genre_id;
      this.dialogGenreDelete.show = true;
    },
    onDeleteGenre() {
      this.dialogGenreDelete.show = false;
      this.isLoading = true;
      this.deleteGenre({
        userToken: this.token,
        genre_id: this.dialogGenreDelete.genre_id,
      })
        .then(() => {
          this.isLoading = false;
          this.onLoadGenres();
        })
        .catch(() => {
          this.isLoading = false;
        });
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    closedialogGenreDelete() {
      this.dialogGenreDelete.show = false;
    },
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
    onLoadGenres() {
      this.isListLoading = true;
      if (!this.filterController) {
        this.filterController = new AbortController();
      } else {
        this.filterController.abort();
        this.filterController = new AbortController();
      }
      this.filterSignal = this.filterController.signal;
      this.getGenres({
        userToken: this.token,
        signal: this.filterSignal,
        query: this.filterText,
        itemPerPage: this.itemPerPage,
        currentPage: this.currentPage + 1,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isListLoading = false;
          this.gernes = res.genres.data;
          this.currentPage = res.genres.current_page - 1;
          this.totalPages = res.genres.last_page;
        });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  watch: {
    itemPerPage() {
      this.onLoadGenres();
    },
    filterText() {
      this.onLoadGenres();
    },
    currentPage() {
      this.onLoadGenres();
    },
  },
  created() {
    this.onLoadGenres();
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
  components: {
    BaseLineLoad,
    BaseFlatDialog,
    BaseCircleLoad,
    BaseTableBodyPagination,
  },
});
</script>

<style lang="scss" scoped>
.main {
  width: 100%;
  display: flex;
  flex-direction: column;
  height: calc(100% - 95px);
  overflow-y: scroll;
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
      width: 90%;
      margin: 0 auto;
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
              &.show {
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
button {
  padding: 5px 10px;
  background: transparent;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
  margin: 5px 2px;
  border: 1px solid var(--color-primary);
  color: var(--color-primary);
  &:focus {
    outline: none;
  }
  &:hover {
    background: var(--color-primary);
    color: var(--background-color-secondary);
  }

  &.danger {
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
</style>
