<template>
  <div class="main" ref="main">
    <h3>Song Uploaded Management</h3>
    <div class="data" v-if="Object.keys(uploadedSongList).length">
      <div class="filter">
        <label for=""><IconSearch /></label>
        <input type="text" name="" id="" v-model="filterText" />
      </div>
      <table v-if="!filterText">
        <thead>
          <tr>
            <td class="index">#</td>
            <td class="title" @click="sortList('title')">Title</td>
            <td class="artist" @click="sortList('artist')">Artist</td>
            <td class="uploadDate" @click="sortList('uploadDate')">Upload</td>
            <td class="control">Control</td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(song, index, i) in uploadedSongList" :key="index">
            <td>{{ i + 1 }}</td>
            <td>{{ song[0].title }}</td>
            <td>
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
            <td>{{ new Date(song[0].created_at).toLocaleDateString() }}</td>
            <td>
              <button class="edit" @click="navigateToEdit(song[0].song_id)">
                Edit
              </button>
              <button class="delete">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <table v-else>
        <thead>
          <tr>
            <td class="index">#</td>
            <td class="title" @click="sortList('title')">Title</td>
            <td class="artist" @click="sortList('artist')">Artist</td>
            <td class="uploadDate" @click="sortList('uploadDate')">Upload</td>
            <td class="control">Control</td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(song, index, i) in filteredUploadedSongList" :key="index">
            <td>{{ i + 1 }}</td>
            <td>{{ song[0].title }}</td>
            <td>
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
            <td>{{ new Date(song[0].created_at).toLocaleDateString() }}</td>
            <td>
              <button class="edit" @click="navigateToEdit(song[0].song_id)">
                Edit
              </button>
              <button class="delete">Delete</button>
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
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";

type songData = {
  [song_id: string]: {
    song_id: string;
    title: string;
    artist_name: string;
    artist_id: string;
    created_at: string;
  }[];
};

export default defineComponent({
  data() {
    return {
      uploadedSongList: {} as songData,
      filteredUploadedSongList: {} as songData,
      observer: null as ResizeObserver | null,
      mainWidth: 0,
      filterText: "",
      sortMode: {
        title: "asc",
        artist: "asc",
        uploadDate: "asc",
      },
    };
  },
  methods: {
    ...mapActions("song", ["getUploadedSongs"]),
    sortList(colname: string) {
      if (colname === "title") {
        if (this.sortMode.title == "asc") {
          this.sortMode.title = "desc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: songData, b: songData) =>
                -a[1][0].title.localeCompare(b[1][0].title)
            )
          );
        } else {
          this.sortMode.title = "asc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: songData, b: songData) =>
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
              (a: songData, b: songData) =>
                -a[1][0].artist_name.localeCompare(b[1][0].artist_name)
            )
          );
        } else {
          this.sortMode.artist = "asc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: songData, b: songData) =>
                a[1][0].artist_name.localeCompare(b[1][0].artist_name)
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
              (a: songData, b: songData) =>
                -a[1][0].created_at.localeCompare(b[1][0].created_at)
            )
          );
        } else {
          this.sortMode.uploadDate = "asc";
          this.uploadedSongList = Object.fromEntries(
            Object.entries(this.uploadedSongList).sort(
              (a: songData, b: songData) =>
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
          (song: songData) =>
            song[1][0].title
              .toLowerCase()
              .includes(this.filterText.toLowerCase()) ||
            song[1][0].artist_name
              .toLowerCase()
              .includes(this.filterText.toLowerCase())
        )
      );
    },
  },
  mounted() {
    this.getUploadedSongs(this.token)
      .then((res) => res.json())
      .then((res) => {
        this.uploadedSongList = res.songs.reduce((r: any, a: any) => {
          r[a.song_id] = r[a.song_id] || [];
          r[a.song_id].push(a);
          return r;
        }, Object.create(null));
      });
    const songItem = this.$refs.songItem as HTMLElement;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.mainWidth = entry.contentRect.width;
      }
    });
    if (this.observer && songItem) this.observer.observe(songItem);
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
  components: { IconSearch },
});
</script>

<style lang="scss" scoped>
.main {
  width: 100%;
  display: flex;
  flex-direction: column;
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
              margin: 5px 0;
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
