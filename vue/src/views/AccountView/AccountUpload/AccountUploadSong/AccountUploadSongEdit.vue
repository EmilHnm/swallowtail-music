<template>
  <div class="main">
    <h3>Song Edit</h3>
    <form @submit.prevent="onSubmit">
      <div class="form__row">
        <label for="">Song Name</label>
        <input type="text" />
      </div>
      <div class="form__row">
        <label for="">Artist Name</label>
        <input type="text" />
      </div>
      <div class="form__row">
        <label for="">Genre</label>
        <input type="text" />
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
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import type { artist } from "@/model/artistModel";
import type { genre } from "@/model/genreModel";
import type { LocationQueryValue } from "vue-router";
import { functionModule } from "@/store/module/functionModule";
import { mapActions, mapGetters } from "vuex";
import BaseRadio from "@/components/UI/BaseRadio.vue";

export default {
  data() {
    return {
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
      displayMode: "",
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  methods: {
    ...mapActions("song", ["getSong", "getGenreList", "getArtistList"]),
    onSubmit() {
      if (functionModule.validateSongFileType(this.songFile) === false) {
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
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  components: {
    BaseRadio,
  },
  created() {
    this.song_id = this.$route.query.id;
    if (this.song_id) {
      console.log(this.song_id);
      this.getSong({ token: this.token, song_id: this.song_id })
        .then((res) => res.json())
        .then((res) => {
          console.log(res);
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

<style lang="scss" scoped></style>
