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
  <div class="main">
    <h3>Album Edit</h3>
    <div class="form">
      <div class="form__row">
        <div class="album-detail">
          <div class="album-detail__image">
            <input
              type="file"
              name="image"
              id="image"
              class="preview"
              @change="imageFileChange"
            />
            <img
              v-lazyload
              :data-url="`${environment.album_cover}/${album.image_path}`"
              alt="album image"
              v-if="!imgPath"
            />
            <img
              v-lazyload
              :data-url="imgPath"
              alt="album image"
              v-else-if="imgPath"
            />
            <label for="image" v-else>
              <span>Click or drag your album image here to upload </span>
            </label>
          </div>
          <div class="album-detail__info">
            <div class="album-detail__info--title">
              <label for="title">Title</label>
              <input
                type="text"
                id="title"
                placeholder="Album Title"
                v-model="album.name"
              />
            </div>
            <div class="album-detail__info--year">
              <label for="title">Release Year</label>
              <input
                type="number"
                id="title"
                placeholder="Album Release Year"
                v-model="album.release_year"
              />
            </div>
            <div class="album-detail__info--type">
              <label for="albumType">Album Type</label>
              <select name="albumType" id="albumType" v-model="album.type">
                <option value="" disable>Select album type</option>
                <option
                  :selected="album.type === 'single' ? true : false"
                  value="single"
                >
                  Single
                </option>
                <option
                  :selected="album.type === 'album' ? true : false"
                  value="album"
                >
                  Album
                </option>
                <option
                  :selected="album.type === 'mixtape' ? true : false"
                  value="mixtape"
                >
                  Mixtape
                </option>
                <option
                  :selected="album.type === 'DJ Mix' ? true : false"
                  value="DJ Mix"
                >
                  DJ Mix
                </option>
                <option
                  :selected="album.type === 'bootleg' ? true : false"
                  value="bootleg"
                >
                  Bootleg / Unauthorized
                </option>
                <option
                  :selected="album.type === 'soundtracks' ? true : false"
                  value="soundtracks"
                >
                  Soundtracks
                </option>
                <option
                  :selected="album.type === 'live albums' ? true : false"
                  value="live albums"
                >
                  Live albums
                </option>
              </select>
            </div>
            <div class="album-detail__info--submit">
              <button @click="submitAlbum">Submit</button>
            </div>
          </div>
        </div>
      </div>
      <div class="form__row">
        <h3>Song List</h3>
        <div
          class="song-detail"
          v-for="(song, index) in songData"
          :key="song.song_id"
        >
          <div class="song-detail__title">
            {{ songData[index].title }}
          </div>
          <div class="song-detail__action">
            <button @click="removeSongs(song.song_id)">Remove</button>
          </div>
        </div>
      </div>
      <div class="form__row">
        <h3>Add More Song</h3>
        <div class="song-add">
          <div class="song-add__title">
            <label for="song-title">Song Title</label>
            <input
              type="text"
              placeholder="Enter song title"
              v-model="filterText"
            />
          </div>
          <div class="song-add__result">
            <div
              class="song-add__result--item"
              v-for="song in addableSongList"
              :key="song.song_id"
            >
              <div class="song-add__result--item--title">
                <span>{{ song.title }}</span>
              </div>
              <div
                class="song-add__result--item--artist"
                v-if="song.artist.length > 0"
              >
                <router-link
                  v-for="(artist, index) in song.artist"
                  :key="artist.artist_id"
                  :to="{
                    name: 'artistOverviewPage',
                    params: { id: artist.artist_id },
                  }"
                >
                  {{ artist.name
                  }}<span v-if="index !== song.artist.length - 1">,</span>
                </router-link>
              </div>
              <div class="song-add__result--item--artist" v-else>Unset</div>
              <div class="song-add__result--item--action">
                <button @click="addSongs(song.song_id)">Add</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import type { album } from "@/model/albumModel";
import { defineComponent, reactive } from "vue";
import type { LocationQueryValue } from "vue-router";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import type { song } from "@/model/songModel";
import { _function } from "@/mixins";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import type { artist } from "@/model/artistModel";

type songItem = {
  song_id?: string;
  title?: string;
};

type songInfo = {
  song_id: string;
  title: string;
  artist_name: string;
  artist_id: string;
  created_at: string;
};

type songList = {
  [song_id: string]: songInfo[];
};

type addableSong = song & {
  artist: artist[];
};

// declare module "@vue/runtime-core" {
//   interface ComponentCustomProperties {
//     filteredSongList: songList;
//   }
// }

export default defineComponent({
  data() {
    return {
      environment: environment,
      album_id: null as LocationQueryValue | LocationQueryValue[] | null,
      uploadedSongList: {} as songList,
      songs: [] as songItem[],
      addableSongList: [] as addableSong[],
      album: {} as album,
      file: null as File | null,
      imgPath: "",
      songData: reactive([] as songItem[]),
      filterText: "",
      isLoading: false,
      searchController: null as AbortController | null,
      searchSignal: null as AbortSignal | null,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  methods: {
    ...mapActions("album", [
      "getAlbum",
      "getAddableSong",
      "getAlbumSongs",
      "removeAlbumSongs",
      "addAlbumSongs",
      "updateAlbumInfo",
    ]),
    removeSongs(id?: string) {
      this.isLoading = true;
      this.removeAlbumSongs({ userToken: this.token, songId: id })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.loadAlbumSongs();
          }
        });
    },
    addSongs(id?: string) {
      this.isLoading = true;
      this.addAlbumSongs({
        userToken: this.token,
        songId: id,
        albumId: this.album_id as string,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.loadAlbumSongs();
            this.filterText = "";
          }
        });
    },
    loadAlbumSongs() {
      this.getAlbumSongs({ userToken: this.token, album_id: this.album_id })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.songData.length = 0;
            res.songs.forEach((song: song) => {
              this.songData.push({
                song_id: song.song_id,
                title: song.title,
              });
            });
          }
        });
    },
    loadAddableSong() {
      if (!this.searchController) {
        this.searchController = new AbortController();
        this.searchSignal = this.searchController.signal;
      } else {
        this.searchController.abort();
        this.searchController = new AbortController();
        this.searchSignal = this.searchController.signal;
      }
      this.getAddableSong({
        userToken: this.token,
        signal: this.searchSignal,
        query: this.filterText,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.addableSongList = res.songs;
          }
        });
    },
    submitAlbum() {
      if (this.album.name && this.album.type && this.album.release_year) {
        this.dialogWaring.show = false;
      } else {
        this.dialogWaring.title = "Warning";
        this.dialogWaring.content = "Please fill in all the fields";
        this.dialogWaring.show = true;
      }
      this.isLoading = true;
      let formData = new FormData();
      if (this.file) formData.append("image", this.file);
      formData.append("albumData", JSON.stringify(this.album));
      this.updateAlbumInfo({
        userToken: this.token,
        albumInfo: formData,
      })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.dialogWaring.title = "Success";
            this.dialogWaring.content = "Album updated successfully";
            this.dialogWaring.mode = "announcement";
            this.dialogWaring.show = true;
          }
        });
    },
    imageFileChange(e: Event) {
      const target = e.target as HTMLInputElement;
      if (target.files) {
        if (!_function.validateImageFileType(target.files[0])) {
          this.dialogWaring.title = "Warning";
          this.dialogWaring.content = "Please upload image file";
          this.dialogWaring.show = true;
          return;
        }
        let reader = new FileReader();
        reader.readAsDataURL(target.files[0]);
        reader.onload = (e) => {
          this.imgPath = reader.result as string;
        };
        this.file = target.files[0];
      }
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
      if (this.filterText.length === 0) {
        this.addableSongList.length = 0;
        return;
      }
      this.loadAddableSong();
    },
  },
  created() {
    this.isLoading = true;
    this.album_id = this.$route.query.id;
    this.getAlbum({ token: this.token, album_id: this.album_id })
      .then((res) => res.json())
      .then((res) => {
        this.isLoading = false;
        if (res.status === "success") {
          this.album = res.album;
        }
      });
    this.loadAlbumSongs();
  },
  components: { BaseFlatDialog, BaseCircleLoad },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.main {
  width: 90%;
  max-height: 100%;
  display: flex;
  flex-direction: column;
  padding: 20px;
  margin: auto;
  h3 {
    margin-bottom: 20px;
    font-weight: 900;
    font-size: 1.5rem;
    text-align: center;
  }
  .form {
    display: flex;
    flex-direction: column;
    &__row {
      width: 100%;
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
      position: relative;
      .album-detail {
        display: flex;
        align-items: center;
        justify-content: space-between;
        &__image {
          width: 300px;
          height: 300px;
          display: flex;
          flex: 0 0 auto;
          align-items: center;
          justify-content: center;
          position: relative;
          cursor: pointer;
          img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
          label {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            border: 2px solid var(--color-primary);
            color: #fff;
            font-size: 1.2rem;
            font-weight: 900;
            cursor: pointer;
            span {
              display: block;
              text-align: center;
              color: var(--color-primary);
            }
          }
          input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
          }
        }
        &__info {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          width: 100%;
          padding: 20px;
          label {
            font-size: 1.2rem;
            font-weight: 900;
            margin-bottom: 5px;
            display: block;
          }
          input {
            height: 40px;
            width: calc(100% - 20px);
            border: 1px solid var(--text-primary-color);
            background: var(--background-color-secondary);
            color: var(--text-primary-color);
            padding: 0 10px;
            font-size: 1rem;
            &:focus {
              outline: none;
            }
          }
          select {
            height: 40px;
            border: 1px solid var(--text-primary-color);
            background: var(--background-color-secondary);
            color: var(--text-primary-color);
            padding: 0 10px;
            font-size: 1rem;
            width: 100%;
            &:focus {
              outline: none;
            }
          }
          button {
            margin-top: 1.2rem;
            width: 100%;
            height: 40px;
            border: 1px solid var(--color-primary);
            background: var(--background-color-secondary);
            color: var(--color-primary);
            padding: 0 10px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 600;
            &:hover {
              background: var(--color-primary);
              color: var(--background-color-secondary);
            }
          }
        }
      }
      .song-detail {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0px;
        &__title {
          height: 40px;
          width: calc(100% - 20px);
          border: 1px solid var(--text-primary-color);
          background: var(--background-color-secondary);
          color: var(--text-primary-color);
          padding: 0 10px;
          font-size: 1rem;
          text-align: center;
          line-height: 40px;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
        &__action {
          margin-left: 10px;
          width: 100px;
          height: 100%;
          display: flex;
          align-items: flex-end;
          justify-content: center;
          button {
            height: 40px;
            width: 100%;
            border: 1px solid var(--color-danger);
            background: var(--background-color-secondary);
            color: var(--color-danger);
            padding: 0 10px;
            font-size: 1rem;
            cursor: pointer;
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
      .song-add {
        display: flex;
        flex-direction: column;
        &__title {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          width: 100%;
          padding: 20px;
          label {
            font-size: 1.2rem;
            font-weight: 900;
            margin-bottom: 5px;
            display: block;
          }
          input {
            height: 40px;
            width: calc(100% - 20px);
            border: 1px solid var(--text-primary-color);
            background: var(--background-color-secondary);
            color: var(--text-primary-color);
            padding: 0 10px;
            font-size: 1rem;
            &:focus {
              outline: none;
            }
          }
        }
        &__result {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          width: 100%;
          padding: 20px;
          max-height: 300px;
          overflow-y: scroll;
          &--item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            &:hover {
              background: var(--background-color-secondary);
            }
            &--title {
              height: 40px;
              width: calc(60% - 20px);
              padding: 0 10px;
              font-size: 1rem;
              text-align: center;
              line-height: 40px;
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
            }
            &--artist {
              height: 40px;
              width: calc(40% - 20px);
              padding: 0 10px;
              font-size: 1rem;
              line-height: 40px;
              white-space: nowrap;
              text-overflow: ellipsis;
              overflow: hidden;
              & a {
                color: var(--text-primary-color);
              }
            }
            &--action {
              margin-left: 10px;
              width: 100px;
              height: 100%;
              display: flex;
              align-items: flex-end;
              justify-content: center;
              button {
                height: 40px;
                width: 100%;
                border: 1px solid var(--color-primary);
                background: var(--background-color-secondary);
                color: var(--color-primary);
                padding: 0 10px;
                font-size: 1rem;
                cursor: pointer;
                &:focus {
                  outline: none;
                }
                &:hover {
                  background: var(--color-primary);
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
@media (max-width: $tablet-width) {
  .main {
    .form {
      &__row {
        .album-detail {
          display: block;
          &__image {
            width: 200px;
            height: 200px;
            margin: auto;
          }
          &__info {
            padding: 20px 0;
          }
        }
        .song-add {
          &__title {
            padding: 20px 0;
          }
          &__result {
            padding: 20px 0;
            &--item {
              &--artist {
                display: none;
              }
            }
          }
        }
      }
    }
  }
}
</style>
