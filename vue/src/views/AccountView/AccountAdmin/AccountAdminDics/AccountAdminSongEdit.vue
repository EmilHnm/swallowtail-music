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
      :open="dialogDelete.show"
      :title="dialogDelete.title"
      :mode="dialogDelete.mode"
      @close="closeDeleteDialog"
    >
      <template #default>
        <p>{{ dialogDelete.content }}</p>
      </template>
      <template #action>
        <BaseButton :mode="'danger'" @click="onDeleteSong">Delete</BaseButton>
        <BaseButton @click="closeDeleteDialog">Cancel</BaseButton>
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
    <h3>Song Edit</h3>
    <form class="form" @submit.prevent="onSubmit">
      <div class="form__row">
        <label for="">Song Name</label>
        <input type="text" placeholder="Song Name" v-model="songName" />
      </div>
      <div class="form__row">
        <label for="">Song Sub Name</label>
        <input type="text" placeholder="Song Name" v-model="songSubName" />
      </div>
      <div class="form__row">
        <div class="tagList">
          <BaseTag
            v-for="(artist, index) in artistArray"
            @click="func.removeItemFormArr(index, artistArray)"
            :key="artist.artist_id"
            >{{ artist.name }}</BaseTag
          >
          <BaseTag
            v-for="(artist, index) in newArtistArray"
            @click="func.removeItemFormArr(index, newArtistArray)"
            :key="index"
            >{{ artist }}</BaseTag
          >
        </div>
        <label for="">Artist Name</label>
        <input
          type="text"
          placeholder="Artist Name"
          v-model="artistName"
          @keydown.enter.prevent="
            pushItemtoNewArray(artistName, newArtistArray, templateArtistArray)
          "
        />
        <div class="form__row--searchBox" v-if="artistName">
          <div
            class="form__row--searchBox--item"
            v-for="artist in func.checkIncludeString(
              artistName,
              templateArtistArray,
              artistArray
            )"
            :key="artist"
            @click="pushItemtoArray(artist, artistArray)"
          >
            {{ artist.name }}
          </div>
        </div>
      </div>
      <div class="form__row">
        <div class="tagList">
          <BaseTag
            v-for="(genre, index) in genreArray"
            :key="genre.genre_id"
            @click="func.removeItemFormArr(index, genreArray)"
            >{{ genre.name }}</BaseTag
          >
          <BaseTag
            v-for="(genre, index) in newGenreArray"
            @click="func.removeItemFormArr(index, newGenreArray)"
            :key="genre"
            >{{ genre }}</BaseTag
          >
        </div>
        <label for="">Genre</label>
        <input
          type="text"
          placeholder="Genre"
          v-model="genreName"
          @keydown.enter.prevent="
            pushItemtoNewArray(genreName, newGenreArray, templateGenreArray)
          "
        />
        <div class="form__row--searchBox" v-if="genreName">
          <div
            class="form__row--searchBox--item"
            v-for="genre in func.checkIncludeString(
              genreName,
              templateGenreArray,
              genreArray
            )"
            :key="genre.genre_id"
            @click="pushItemtoArray(genre, genreArray)"
          >
            {{ genre.name }}
          </div>
        </div>
      </div>
      <div class="form__row">
        <label for="">Display Mode</label>
        <BaseRadio
          :name="'displayMode'"
          v-model="displayMode"
          :disable="
            artistArray.length != 0 && genreArray.length != 0 ? false : true
          "
          :value="'public'"
          >Public</BaseRadio
        >
        <BaseRadio
          :name="'displayMode'"
          v-model="displayMode"
          :value="'private'"
          :checked="true"
          >Private</BaseRadio
        >
      </div>
      <div class="form__row">
        <button type="submit">Submit</button>
        <button type="button" class="danger" @click="onDeleteSongConfirm">
          Delete
        </button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import type { artist } from "@/model/artistModel";
import type { genre } from "@/model/genreModel";
import type { LocationQueryValue } from "vue-router";
import { _function } from "@/mixins";
import { mapActions, mapGetters } from "vuex";
import BaseRadio from "@/components/UI/BaseRadio.vue";
import BaseTag from "@/components/UI/BaseTag.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
export default {
  data() {
    return {
      func: _function,
      song_id: null as LocationQueryValue | LocationQueryValue[],
      templateArtistArray: [] as artist[],
      templateGenreArray: [] as genre[],
      artistArray: [] as artist[],
      genreArray: [] as genre[],
      newArtistArray: [] as string[],
      newGenreArray: [] as string[],
      artistName: "",
      genreName: "",
      songName: "",
      songSubName: "",
      displayMode: "",
      isLoading: false,
      dialogWaring: {
        mode: "warning",
        title: "Warning",
        content: "Please fill in all the fields",
        show: false,
      },
      dialogDelete: {
        mode: "danger",
        title: "Delete song",
        content:
          "Are You Sure You Want To Delete This Song?\n After Deleting, You Cannot Recover It.",
        show: false,
      },
    };
  },
  methods: {
    ...mapActions("song", ["getGenreList"]),
    ...mapActions("artist", ["getArtistList"]),
    ...mapActions("admin", ["getSong", "updateSong", "deleteSong"]),
    pushItemtoArray(item: genre | artist, array: genre[] | artist[]): void {
      _function.pushItemtoArray(item, array);
      this.artistName = "";
      this.genreName = "";
    },
    pushItemtoNewArray(
      item: string,
      array: string[],
      tempArr: genre[] | artist[]
    ): void {
      _function.pushItemNotIncludedtoNewArray(item, array, tempArr);
      this.artistName = "";
      this.genreName = "";
    },
    closeDialog() {
      this.dialogWaring.show = false;
    },
    closeDeleteDialog() {
      this.dialogDelete.show = false;
    },
    onDeleteSongConfirm() {
      this.dialogDelete.show = true;
    },
    onDeleteSong() {
      this.dialogDelete.show = false;
      this.isLoading = true;
      this.deleteSong({ song_id: this.song_id, token: this.token })
        .then((res) => {
          return res.json();
        })
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success")
            this.$router.push({ name: "accountAdminDiscSong" });
        });
    },
    onSubmit() {
      if (this.songName == "") {
        this.dialogWaring.content = "Please fill in the song name";
        this.dialogWaring.show = true;
        return;
      }
      if (this.displayMode === "public") {
        if (
          (this.artistArray.length === 0 && this.newArtistArray.length === 0) ||
          (this.genreArray.length === 0 && this.newGenreArray.length) === 0
        ) {
          this.dialogWaring.content =
            "If you want to public, please fill in the artist and genre";
          this.dialogWaring.show = true;
          return;
        }
      }
      const songForm = new FormData();
      songForm.append("song_id", this.song_id as string);
      songForm.append("songName", this.songName);
      songForm.append("songSubName", this.songSubName);
      songForm.append("displayMode", this.displayMode);
      let artistArrUpload: string[] = [];
      this.artistArray.forEach((artist: artist) => {
        if (artist.artist_id) artistArrUpload.push(artist.artist_id);
      });
      songForm.append("artist", JSON.stringify(artistArrUpload));
      songForm.append("newArtist", JSON.stringify(this.newArtistArray));
      let genreArrUpload: string[] = [];
      this.genreArray.forEach((genre: genre) => {
        if (genre.genre_id) genreArrUpload.push(genre.genre_id);
      });
      songForm.append("genre", JSON.stringify(genreArrUpload));
      songForm.append("newGenre", JSON.stringify(this.newGenreArray));
      this.isLoading = true;
      this.updateSong({ token: this.token, songForm: songForm })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.dialogWaring.content = res.message;
            this.dialogWaring.mode = "announcement";
            this.dialogWaring.title = "Success";
            this.dialogWaring.show = true;
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
  components: {
    BaseRadio,
    BaseTag,
    BaseFlatDialog,
    BaseCircleLoad,
    BaseButton,
  },
  created() {
    this.song_id = this.$route.params.id;
    if (this.song_id) {
      this.isLoading = true;
      this.getSong({ token: this.token, song_id: this.song_id })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.songName = res.song.title;
            this.songSubName = res.song.sub_title;
            this.displayMode = res.song.display;
            for (const artist of res.artist) {
              this.artistArray.push({
                id: artist.id,
                artist_id: artist.artist_id,
                name: artist.name,
                image_path: artist.image_path,
                created_at: artist.created_at,
                updated_at: artist.updated_at,
              });
            }
            for (const genre of res.genre as genre[]) {
              this.genreArray.push({
                id: genre.id,
                genre_id: genre.genre_id,
                name: genre.name,
                description: genre.description,
                created_at: genre.created_at,
                updated_at: genre.updated_at,
              });
            }
          } else {
            this.$router.push({ name: "accountUploadSong" });
          }
        });
    }
    this.getGenreList(this.token)
      .then((res) => res.json())
      .then((res) => {
        this.templateGenreArray = res;
      });
    this.getArtistList(this.token)
      .then((res) => res.json())
      .then((res) => {
        this.templateArtistArray = res;
      });
  },
};
</script>

<style lang="scss" scoped>
.main {
  width: 90%;
  max-height: 100%;
  display: flex;
  flex-direction: column;
  padding: 20px;
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
      &--searchBox {
        position: absolute;
        max-height: 200px;
        width: 100%;
        overflow: scroll;
        z-index: 20;
        background: var(--background-glass-color-primary);
        backdrop-filter: blur(10px);
        top: 100%;
        &--item {
          width: 100%;
          font-size: 1.2rem;
          padding: 3px 10px;
          cursor: pointer;
          &:hover {
            background: var(--background-color-secondary);
          }
        }
      }
      .tagList {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
      }
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
        }
      }
      & button {
        height: 40px;
        background: transparent;
        color: var(--color-primary);
        margin: 0.5rem auto;
        padding: 0 20px;
        font-size: 1rem;
        border: 1px solid var(--color-primary);
        width: 80%;
        float: right;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        &.danger {
          background: transparent;
          color: var(--color-danger);
          border: 1px solid var(--color-danger);
          &:focus {
            outline: none;
          }
          &:hover {
            transform: scale(1.05);
            background: var(--color-danger);
            color: var(--background-color-secondary);
          }
        }
        &:focus {
          outline: none;
        }
        &:hover {
          transform: scale(1.05);
          background: var(--color-primary);
          color: var(--background-color-secondary);
        }
      }
    }
  }
}
</style>
