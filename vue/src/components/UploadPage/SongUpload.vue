<template>
  <teleport to="body">
    <BaseDialog
      :open="dialogWaring.show"
      :title="dialogWaring.title"
      :mode="dialogWaring.mode"
      @close="closeDialog"
    >
      <template #default>
        <p>{{ dialogWaring.content }}</p>
      </template>
    </BaseDialog>
    <ArtistRequestDialog
      :is-open="isRequsetingArtist"
      @artist-request-close="isRequsetingArtist = false"
    />
    <GenreRequestDialog
      :is-open="isRequsetingGenre"
      @genre-request-close="isRequsetingGenre = false"
    />
  </teleport>
  <form class="uploadform">
    <div class="uploadform__control">
      <div
        class="uploadform__control--file"
        :class="{ dragging: inputDragging }"
        @dragover="() => (inputDragging = true)"
        @dragleave="() => (inputDragging = false)"
        @drop="() => (inputDragging = false)"
      >
        <input type="file" name="file" id="file" @change="previewFile" />
        <label for="file">
          <span v-if="!songFile">Click or drag your file here to upload </span>
          <span v-else>{{ songFile.name }}</span>
        </label>
      </div>
    </div>
    <div class="uploadform__control">
      <BaseInput
        :label="'Song Name'"
        :type="'text'"
        :required="true"
        :placeholder="'Enter Song Name'"
        :id="'songName'"
        v-model="songName"
      />
    </div>
    <div class="uploadform__control">
      <div class="uploadform__control--artist">
        <span>Artist List </span>
        <BaseTag
          v-for="(artist, index) in artistArray"
          :key="artist.artist_id"
          @click="func.removeItemFormArr(index, artistArray)"
          >{{ artist.name }}</BaseTag
        >
      </div>
      <BaseInput
        :label="'Artist Name'"
        :type="'text'"
        :placeholder="'Enter Artist Name'"
        :id="'artistName'"
        v-model="artistName"
      />
      <div
        class="uploadform__control--artistSearch"
        :class="{ active: artistName }"
      >
        <div
          class="artistSearch--item"
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
      <div class="uploadform__control--artistSearch--new">
        <span>Don't find your artist?</span>
        <BaseButton @click="isRequsetingArtist = true"
          >Request Artist</BaseButton
        >
      </div>
    </div>
    <div class="uploadform__control">
      <div class="uploadform__control--genre">
        <span>Genre List</span>
        <BaseTag
          v-for="(genre, index) in genreArray"
          :key="genre.genre_id"
          @click="func.removeItemFormArr(index, genreArray)"
          >{{ genre.name }}</BaseTag
        >
      </div>
      <BaseInput
        :label="'Genre'"
        :type="'text'"
        :id="'genre'"
        :placeholder="'Enter Genre'"
        v-model="genreName"
      />
      <div
        class="uploadform__control--genreSearch"
        :class="{ active: genreName }"
      >
        <div
          class="genreSearch--item"
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
      <div class="uploadform__control--genreSearch--new">
        <span>Don't find your genre?</span>
        <BaseButton @click="isRequsetingGenre = true">Request Genre</BaseButton>
      </div>
    </div>
    <div class="uploadform__control">
      <h3 class="uploadform__control--display">Display Mode</h3>
      <div class="uploadform__control--displaymode">
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
    </div>
    <div class="uploadform__control">
      <BaseButton @click="onSubmitForm">Submit</BaseButton>
    </div>
  </form>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import { _function } from "@/mixins";
import type { genre } from "@/model/genreModel";
import type { artist } from "@/model/artistModel";
import BaseInput from "@/components/UI/BaseInput.vue";
import BaseRadio from "@/components/UI/BaseRadio.vue";
import BaseButton from "@/components/UI/BaseButton.vue";
import BaseTag from "@/components/UI/BaseTag.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import ArtistRequestDialog from "@/components/HomeView/partials/Request/ArtistRequestDialog.vue";
import GenreRequestDialog from "@/components/HomeView/partials/Request/GenreRequestDialog.vue";
export default defineComponent({
  emits: ["uploadSong"],
  data() {
    return {
      songName: "",
      displayMode: "private",
      inputDragging: false,
      templateArtistArray: [] as artist[],
      templateGenreArray: [] as genre[],
      artistArray: [] as artist[],
      genreArray: [] as genre[],
      artistName: "",
      genreName: "",
      songFile: null as File | null,
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      isRequsetingArtist: false,
      isRequsetingGenre: false,
      isLoading: true,
      func: _function,
    };
  },
  methods: {
    ...mapActions("song", ["getGenreList", "uploadSong"]),
    ...mapActions("artist", ["getArtistList"]),
    ...mapActions("uploadQueue", ["addFileToQueue"]),
    previewFile(event: Event) {
      let target = event.target as HTMLInputElement;
      if (target) if (target.files) this.songFile = target.files[0];
    },
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
    closeDialog(): void {
      this.dialogWaring.show = false;
    },
    onSubmitForm() {
      if (!this.songFile) {
        this.dialogWaring.content = "Please upload a song file";
        this.dialogWaring.show = true;
        return;
      }
      if (_function.validateSongFileType(this.songFile) === false) {
        this.dialogWaring.content = "Please upload a valid song file";
        this.dialogWaring.show = true;
        return;
      }
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
      if (this.dialogWaring.show === true) {
        alert("Some thong went wrong! Please refresh the page and try again!");
        return;
      }
      const songForm = new FormData();
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
      this.dialogWaring.content =
        "Uploading Song Infomation! Please do not close tab when uploading";
      this.dialogWaring.show = true;
      this.uploadSong({ songForm: songForm, token: this.userToken })
        .then((res) => {
          this.isLoading = false;
          return res.json();
        })
        .then((res) => {
          if (res.status === "success") {
            this.dialogWaring.content = res.message;
            this.dialogWaring.mode = res.announcement;
            this.dialogWaring.title = res.status;
            this.dialogWaring.show = true;
            this.songName = "";
            this.artistName = "";
            this.genreName = "";
            this.addFileToQueue({
              file: this.songFile,
              song_id: res.song_id,
              token: this.userToken,
            });
            this.songFile = null;
            this.artistArray = [];
            this.genreArray = [];
          } else {
            this.dialogWaring.content = res.error;
            this.dialogWaring.show = true;
          }
        });
    },
  },
  components: {
    BaseInput,
    BaseRadio,
    BaseButton,
    BaseTag,
    BaseDialog,
    ArtistRequestDialog,
    GenreRequestDialog,
  },
  computed: {
    ...mapGetters({
      userToken: "auth/userToken",
    }),
  },
  created() {
    //get Genres list
    this.getGenreList(this.userToken)
      .then((res) => res.json())
      .then((res) => {
        this.templateGenreArray = res;
      });
    this.getArtistList(this.userToken)
      .then((res) => res.json())
      .then((res) => {
        this.templateArtistArray = res;
      });
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
});
</script>

<style lang="scss" scoped>
.uploadform {
  width: 100%;
  display: flex;
  flex-direction: column;
  &__control {
    position: relative;
    margin: 10px 0;
    &--file {
      height: 300px;
      border: 2px solid var(--color-primary);
      position: relative;
      border-radius: 10px;
      &.dragging {
        border: 2px solid transparent;
        background: var(--color-primary);
        & label {
          color: var(--background-color-primary);
        }
      }
      & input {
        width: 100%;
        height: 100%;
        opacity: 0;
      }
      & label {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem;
        font-weight: 700;
        z-index: -1;
        color: var(--color-primary);
      }
    }
    &--genre {
      display: flex;
      flex-wrap: wrap;
      font-weight: 700;
      align-items: center;
      &Search {
        display: none;
        width: 100%;
        max-height: 200px;
        background: var(--background-glass-color-primary);
        border-radius: 30px;
        overflow: auto;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 20;
        &.active {
          display: block;
        }
        .genreSearch--item {
          width: 100%;
          height: 50px;
          background: var(--background-color-secondary);
          border-radius: 30px;
          margin: 5px 0;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
        }
        &--new {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-top: 10px;
          & button {
            padding: 5px 10px;
          }
        }
      }
    }
    &--artist {
      display: flex;
      flex-wrap: wrap;
      font-weight: 700;
      align-items: center;
      &Search {
        display: none;
        width: 100%;
        max-height: 200px;
        background: var(--background-glass-color-primary);
        border-radius: 30px;
        overflow: auto;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 20;
        &.active {
          display: block;
        }
        .artistSearch--item {
          width: 100%;
          height: 50px;
          background: var(--background-color-secondary);
          border-radius: 30px;
          margin: 5px 0;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
        }
        &--new {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-top: 10px;
          & button {
            padding: 5px 10px;
          }
        }
      }
    }
  }
}
</style>
