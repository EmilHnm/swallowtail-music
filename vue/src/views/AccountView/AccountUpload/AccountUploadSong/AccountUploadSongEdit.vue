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
    <GenreRequestFlatDialog
      :isOpen="isOpenRequestGenre"
      @genre-request-close="isOpenRequestGenre = false"
    />
    <ArtistRequestFlatDialog
      :isOpen="isOpenRequestArtist"
      @artist-request-close="isOpenRequestArtist = false"
    />
  </teleport>
  <div class="main">
    <h3>Song Edit</h3>
    <form class="form" @submit.prevent="onSubmit">
      <div class="form__row">
        <label for="">Song Name</label>
        <input type="text" placeholder="Song Name" v-model="songName" />
      </div>
      <div class="form__row">
        <div class="tagList">
          <BaseTag
            v-for="(artist, index) in artistArray"
            @click="func.removeItemFormArr(index, artistArray)"
            :key="artist.artist_id"
            >{{ artist.name }}</BaseTag
          >
        </div>
        <label for="">Artist Name</label>
        <input type="text" placeholder="Artist Name" v-model="artistName" />
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
        <div class="form__row--new">
          <span>Don't find your artists?</span>
          <button @click.prevent="isOpenRequestArtist = true">
            Request artists
          </button>
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
        </div>
        <label for="">Genre</label>
        <input type="text" placeholder="Genre" v-model="genreName" />
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
        <div class="form__row--new">
          <span>Don't find your genre?</span>
          <button @click.prevent="isOpenRequestGenre = true">
            Request Genre
          </button>
        </div>
      </div>
      <div class="form__row">
        <label for="">Display Mode</label>
        <BaseRadio
          :name="'displayMode'"
          v-model="displayMode"
          :disable="artistArray.length === 0 || genreArray.length === 0"
          :value="'public'"
          :checked="displayMode === 'public'"
          >Public</BaseRadio
        >
        <BaseRadio
          :name="'displayMode'"
          v-model="displayMode"
          :value="'private'"
          :checked="displayMode === 'private'"
          >Private</BaseRadio
        >
      </div>
      <div class="form__row">
        <button type="submit">Submit</button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type { artist } from "@/model/artistModel";
import type { genre } from "@/model/genreModel";
import type { LocationQueryValue } from "vue-router";
import { _function } from "@/mixins";
import { mapActions, mapGetters } from "vuex";
import BaseRadio from "@/components/UI/BaseRadio.vue";
import BaseTag from "@/components/UI/BaseTag.vue";
import BaseFlatDialog from "@/components/UI/BaseFlatDialog.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import GenreRequestFlatDialog from "@/components/AccountView/partials/Request/GenreRequestFlatDialog.vue";
import ArtistRequestFlatDialog from "@/components/AccountView/partials/Request/ArtistRequestFlatDialog.vue";
export default defineComponent({
  data() {
    return {
      func: _function,
      song_id: null as LocationQueryValue | LocationQueryValue[],
      templateArtistArray: [] as artist[],
      templateGenreArray: [] as genre[],
      artistArray: [] as artist[],
      genreArray: [] as genre[],
      artistName: "",
      genreName: "",
      songName: "",
      displayMode: "",
      isLoading: false,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      isOpenRequestArtist: false,
      isOpenRequestGenre: false,
    };
  },
  methods: {
    ...mapActions("song", ["getSong", "getGenreList", "updateSong"]),
    ...mapActions("artist", ["getArtistList"]),
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
    onSubmit() {
      if (this.songName == "") {
        this.dialogWaring.content = "Please fill in the song name";
        this.dialogWaring.show = true;
        return;
      }
      if (this.displayMode === "public") {
        if (this.artistArray.length === 0 || this.genreArray.length === 0) {
          this.dialogWaring.content =
            "If you want to public, please fill in the artist and genre";
          this.dialogWaring.show = true;
          return;
        }
      }
      const songForm = new FormData();
      songForm.append("song_id", this.song_id as string);
      songForm.append("songName", this.songName);
      songForm.append("displayMode", this.displayMode);
      let artistArrUpload: string[] = [];
      this.artistArray.forEach((artist: artist) => {
        if (artist.artist_id) artistArrUpload.push(artist.artist_id);
      });
      songForm.append("artist", JSON.stringify(artistArrUpload));
      let genreArrUpload: string[] = [];
      this.genreArray.forEach((genre: genre) => {
        if (genre.genre_id) genreArrUpload.push(genre.genre_id);
      });
      songForm.append("genre", JSON.stringify(genreArrUpload));
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
    GenreRequestFlatDialog,
    ArtistRequestFlatDialog,
  },
  watch: {
    artistArray: {
      handler(n: artist[]) {
        if (n.length === 0 || this.genreArray.length === 0) {
          this.displayMode = "private";
        }
      },
      deep: true,
    },
    genreArray: {
      handler(n: genre[]) {
        if (n.length === 0 || this.artistArray.length === 0) {
          this.displayMode = "private";
        }
      },
      deep: true,
    },
  },
  created() {
    this.song_id = this.$route.query.id;
    if (this.song_id) {
      this.isLoading = true;
      this.getSong({ token: this.token, song_id: this.song_id })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            this.songName = res.song.title;
            this.displayMode = res.song.display;
            for (const artist of res.artist) {
              this.artistArray.push({
                id: artist.id,
                artist_id: artist.artist_id,
                name: artist.name,
                image_path: artist.image_path,
                listens: artist.listens,
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
});
</script>

<style lang="scss" scoped>
.main {
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
        background: var(--background-blur-color-primary);
        backdrop-filter: blur(20px);
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
      &--new {
        display: flex;
        align-items: center;
        margin-top: 10px;
        gap: 10px;
        button {
          padding: 0px 10px !important;
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
        padding: 0 20px;
        font-size: 1rem;
        border: 1px solid var(--color-primary);
        width: max-content;
        float: right;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
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
