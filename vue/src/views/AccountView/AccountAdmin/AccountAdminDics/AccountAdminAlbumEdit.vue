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
              :src="`${environment.album_cover}/${album.image_path}`"
              alt="album image"
              v-if="!imgPath"
            />
            <img :src="imgPath" alt="album image" v-else-if="imgPath" />
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
          v-for="(song, index) in songList"
          :key="song.song_id"
        >
          <div class="song-detail__title">
            {{ songList[index].title }}
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
              v-for="song in filteredSongList"
              :key="song[0].song_id"
            >
              <div class="song-add__result--item--title">
                <span>{{ song[0].title }}</span>
              </div>
              <div
                class="song-add__result--item--artist"
                v-if="song[0].artist_name"
              >
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
              </div>
              <div class="song-add__result--item--artist" v-else>Unset</div>
              <div class="song-add__result--item--action">
                <button @click="addSongs(song[0].song_id)">Add</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import type { album } from "@/model/albumModel";
import type { song } from "@/model/songModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import { _function } from "@/mixins";
export default defineComponent({
  data() {
    return {
      isLoading: false,
      environment: environment,
      imgPath: "",
      album: {} as album,
      songList: [] as song[],
      file: null as File | null,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      filterText: "",
    };
  },
  methods: {
    ...mapActions("admin", ["getAlbum", "updateAlbumInfo", "removeAlbumSongs"]),
    closeDialog() {
      this.dialogWaring.show = false;
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
    onLoadAlbum() {
      this, (this.isLoading = true);
      this.getAlbum({
        album_id: this.$route.params.id,
        token: this.token,
      })
        .then((res: any) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.album = res.album;
            this.songList = res.songList;
          }
        });
    },
    removeSongs(id: string) {
      this.isLoading = true;
      this.removeAlbumSongs({ userToken: this.token, songId: id })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.onLoadAlbum();
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
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.onLoadAlbum();
  },
  components: { BaseFlatDialog, BaseCircleLoad },
});
</script>

<style scoped></style>
