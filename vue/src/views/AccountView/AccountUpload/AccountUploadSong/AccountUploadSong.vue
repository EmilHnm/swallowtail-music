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
      <table v-if="!filterText">
        <thead>
          <tr>
            <td class="index">#</td>
            <td class="title" @click="sortList('title')">Title</td>
            <td
              class="artist"
              @click="sortList('artist')"
              v-if="mainWidth > 600"
            >
              Artist
            </td>
            <td class="uploadDate" @click="sortList('uploadDate')">Upload</td>
            <td class="control">Control</td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(song, index, i) in uploadedSongList" :key="index">
            <td>{{ i + 1 }}</td>
            <td>{{ song[0].title }}</td>
            <td v-if="song[0].artist_name && mainWidth > 600">
              <router-link
                v-for="(artist, index) in song"
                :key="artist.artist_id"
                :to="{
                  name: 'artistOverviewPage',
                  params: { id: artist.artist_id },
                }"
              >
                {{ artist.artist_name
                }}<span v-if="index !== song.length - 1">,</span>
              </router-link>
            </td>
            <td v-if="!song[0].artist_name && mainWidth > 600">Unset</td>
            <td>{{ new Date(song[0].created_at).toLocaleDateString() }}</td>
            <td>
              <button class="edit" @click="navigateToEdit(song[0].song_id)">
                Edit
              </button>
              <button class="delete" @click="deleteItem(song[0].song_id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <table v-else>
        <thead>
          <tr>
            <td class="index">#</td>
            <td class="title" @click="sortList('title')">Title</td>
            <td
              class="artist"
              @click="sortList('artist')"
              v-if="mainWidth > 600"
            >
              Artist
            </td>
            <td class="uploadDate" @click="sortList('uploadDate')">Upload</td>
            <td class="control">Control</td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(song, index, i) in filteredUploadedSongList" :key="index">
            <td>{{ i + 1 }}</td>
            <td>{{ song[0].title }}</td>
            <td v-if="song[0].artist_name && mainWidth > 600">
              <router-link
                v-for="artist in song"
                :key="artist.artist_id"
                :to="{
                  name: 'artistOverviewPage',
                  params: { id: artist.artist_id },
                }"
              >
                {{ artist.artist_name }}
              </router-link>
            </td>
            <td v-if="!song[0].artist_name && mainWidth > 600">Unset</td>
            <td>{{ new Date(song[0].created_at).toLocaleDateString() }}</td>
            <td>
              <button class="edit" @click="navigateToEdit(song[0].song_id)">
                Edit
              </button>
              <button class="delete" @click="deleteItem(song[0].song_id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="no-data" v-else>
      <h3>No Song Uploaded</h3>
    </div>
  </div>
</template>

<script lang="ts">
import IconSearch from "@/components/icons/IconSearch.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";

type songListData = {
  [song_id: string]: songData;
};

type songData = {
  song_id: string;
  title: string;
  artist_name: string;
  artist_id: string;
  created_at: string;
}[];

export default defineComponent({
  data() {
    return {
      uploadedSongList: {} as songListData,
      filteredUploadedSongList: {} as songListData,
      observer: null as ResizeObserver | null,
      mainWidth: 0,
      filterText: "",
      isLoading: false,
      sortMode: {
        title: "asc",
        artist: "asc",
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
    ...mapActions("song", ["getUploadedSongs", "deleteSong"]),
    sortList(colname: string) {
      if (colname === "title") {
        if (this.sortMode.title == "asc") {
          this.sortMode.title = "desc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: [string, songData], b: [string, songData]) =>
                -a[1][0].title.localeCompare(b[1][0].title)
            )
          );
        } else {
          this.sortMode.title = "asc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: [string, songData], b: [string, songData]) =>
                a[1][0].title.localeCompare(b[1][0].title)
            )
          );
        }
        return;
      }
      if (colname === "artist") {
        if (this.sortMode.artist === "asc") {
          this.sortMode.artist = "desc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: [string, songData], b: [string, songData]) => {
                if (a[1][0].artist_name === null) {
                  return -1;
                }
                if (b[1][0].artist_name === null) {
                  return 1;
                }
                return -a[1][0].artist_name
                  .toString()
                  .localeCompare(b[1][0].artist_name.toString());
              }
            )
          );
        } else {
          this.sortMode.artist = "asc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: [string, songData], b: [string, songData]) => {
                if (a[1][0].artist_name === null) {
                  return 1;
                }
                if (b[1][0].artist_name === null) {
                  return -1;
                }
                return a[1][0].artist_name
                  .toString()
                  .localeCompare(b[1][0].artist_name.toString());
              }
            )
          );
        }
        return;
      }
      if (colname === "uploadDate") {
        if (this.sortMode.uploadDate === "asc") {
          this.sortMode.uploadDate = "desc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: [string, songData], b: [string, songData]) =>
                -a[1][0].created_at.localeCompare(b[1][0].created_at)
            )
          );
        } else {
          this.sortMode.uploadDate = "asc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: [string, songData], b: [string, songData]) =>
                a[1][0].created_at.localeCompare(b[1][0].created_at)
            )
          );
        }
        return;
      }
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
            this.getUploadedSongs(this.token)
              .then((res) => res.json())
              .then((res) => {
                this.uploadedSongList = res.songs.reduce((r: any, a: any) => {
                  r[a.song_id] = r[a.song_id] || [];
                  r[a.song_id].push(a);
                  return r;
                }, Object.create(null));
              });
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
      if (this.filterText === "") {
        this.filteredUploadedSongList = {};
        return;
      }
      this.filteredUploadedSongList = Object.fromEntries(
        Object.entries(this.uploadedSongList).filter(
          (song: [string, songData]) => {
            return (
              song[1][0].title
                .toLowerCase()
                .includes(this.filterText.toLowerCase()) ||
              song[1][0].artist_name
                ?.toLowerCase()
                .includes(this.filterText.toLowerCase())
            );
          }
        )
      );
    },
  },
  mounted() {
    this.isLoading = true;
    this.getUploadedSongs(this.token)
      .then((res) => res.json())
      .then((res) => {
        this.uploadedSongList = res.songs.reduce((r: any, a: any) => {
          r[a.song_id] = r[a.song_id] || [];
          r[a.song_id].push(a);
          return r;
        }, Object.create(null));
        this.isLoading = false;
      });
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
  components: { IconSearch, BaseFlatDialog, BaseCircleLoad },
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
