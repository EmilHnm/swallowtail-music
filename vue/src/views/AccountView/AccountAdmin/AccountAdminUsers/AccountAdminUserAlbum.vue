<template>
  <div class="user-album-container">
    <div class="tool">
      <div class="tool__albumCount">
        Uploaded Albums: {{ albumList.length }}
      </div>
      <div class="tool__filter">
        <label for="filterText"><IconSearch /></label>
        <input
          type="text"
          name="filterText"
          id="filterText"
          v-model="filterText"
        />
      </div>
    </div>
    <div class="data" ref="main">
      <BaseCircleLoad v-if="isLoading" />
      <table v-else>
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
          <tr
            v-for="(album, index) in albumList"
            :key="album.album_id"
            :id="album.album_id"
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
              <button class="delete" @click="onDeleteAlbum(album.album_id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr v-for="(album, index) in filteredAlbumList" :key="album.album_id">
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
            <td v-if="mainWidth > 600">{{ album.song_count }}</td>
            <td>{{ new Date(album.created_at).toLocaleDateString() }}</td>
            <td>
              <button class="delete" @click="onDeleteAlbum(album.album_id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import type { album } from "@/model/albumModel";
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import { mapActions, mapGetters } from "vuex";
import IconSearch from "@/components/icons/IconSearch.vue";

type albumData = album & { songCount: number };

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    filteredAlbumList: albumData[];
  }
}

export default defineComponent({
  data() {
    return {
      filterText: "",
      isLoading: false,
      observer: null as ResizeObserver | null,
      environment: environment,
      albumList: [] as albumData[],
      mainWidth: 0,
      sortMode: {
        title: "asc",
        release: "asc",
        type: "asc",
        songCount: "asc",
        uploadDate: "asc",
      },
    };
  },
  methods: {
    ...mapActions("admin", ["getUserUploadAlbum", "deleteAlbum"]),
    onDeleteAlbum(id: string) {
      this.deleteAlbum({
        userToken: this.token,
        albumId: id,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.loadAlbum();
          }
        });
    },
    sortList(colname: string) {
      if (colname === "title") {
        if (this.sortMode.title == "asc") {
          this.sortMode.title = "desc";
          this.albumList = this.albumList.sort((a, b) =>
            a.name > b.name ? -1 : 1
          );
        } else {
          this.sortMode.title = "asc";
          this.albumList = this.albumList.sort((a, b) =>
            a.name < b.name ? -1 : 1
          );
        }
      }
      if (colname === "release") {
        if (this.sortMode.release == "asc") {
          this.sortMode.release = "desc";
          this.albumList = this.albumList.sort((a, b) =>
            a.release_year > b.release_year ? -1 : 1
          );
        } else {
          this.sortMode.release = "asc";
          this.albumList = this.albumList.sort((a, b) =>
            a.release_year < b.release_year ? -1 : 1
          );
        }
      }
      if (colname === "type") {
        if (this.sortMode.type == "asc") {
          this.sortMode.type = "desc";
          this.albumList = this.albumList.sort((a, b) =>
            a.type > b.type ? -1 : 1
          );
        } else {
          this.sortMode.type = "asc";
          this.albumList = this.albumList.sort((a, b) =>
            a.type < b.type ? -1 : 1
          );
        }
      }
      if (colname === "songCount") {
        if (this.sortMode.songCount == "asc") {
          this.sortMode.songCount = "desc";
          this.albumList = this.albumList.sort((a, b) =>
            a.songCount > b.songCount ? -1 : 1
          );
        } else {
          this.sortMode.type = "asc";
          this.albumList = this.albumList.sort((a, b) =>
            a.songCount < b.songCount ? -1 : 1
          );
        }
      }
      if (colname === "uploadDate") {
        if (this.sortMode.uploadDate == "asc") {
          this.sortMode.uploadDate = "desc";
          this.albumList = this.albumList.sort((a, b) =>
            a.created_at > b.created_at ? -1 : 1
          );
        } else {
          this.sortMode.uploadDate = "asc";
          this.albumList = this.albumList.sort((a, b) =>
            a.created_at < b.created_at ? -1 : 1
          );
        }
      }
    },
    loadAlbum() {
      this.isLoading = true;
      this.getUserUploadAlbum({
        token: this.token,
        user_id: this.$route.params.id,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.albumList = res.album;
            this.isLoading = false;
          }
        });
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
    filteredAlbumList(): albumData[] {
      return this.albumList.filter(
        (album: albumData) =>
          album.name.toLowerCase().includes(this.filterText.toLowerCase()) ||
          album.type.toLowerCase().includes(this.filterText.toLowerCase())
      );
    },
  },
  created() {
    this.loadAlbum();
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
  components: { BaseCircleLoad, IconSearch },
});
</script>

<style lang="scss" scoped>
.user-album-container {
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
    &__filter {
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
