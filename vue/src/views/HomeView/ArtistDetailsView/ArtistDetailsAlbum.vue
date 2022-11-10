<template>
  <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
    <template #default>
      <BaseLineLoad />
    </template>
    <template #action><div></div></template>
  </BaseDialog>
  <div v-for="album in albums" :key="album.album_id" :id="album.album_id">
    <BaseAlbum
      :data="album"
      @playAlbum="playAlbum"
      @addAlbumToQueue="addAlbumToQueue"
      @playSongInAlbum="playSongInAlbum"
    />
  </div>
</template>

<script lang="ts">
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import type { album } from "@/model/albumModel";
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import BaseAlbum from "../../../components/UI/BaseAlbum.vue";

type albumData = album & {
  song_count: number;
};

export default defineComponent({
  emits: ["playAlbum", "addAlbumToQueue", "playSongInAlbum"],
  data() {
    return {
      isMenuOpen: false,
      albums: [] as albumData[],
      isLoading: true,
    };
  },
  methods: {
    ...mapActions("artist", ["getArtistAlbumById"]),
    playAlbum(id: string) {
      this.$emit("playAlbum", id);
    },
    addAlbumToQueue(id: string) {
      this.$emit("addAlbumToQueue", id);
    },
    playSongInAlbum(id: string) {
      this.$emit("playSongInAlbum", id);
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  created() {
    this.getArtistAlbumById({
      token: this.token,
      artist_id: this.$route.params.id,
    })
      .then((res) => {
        return res.json();
      })
      .then((res) => {
        this.isLoading = false;
        if (res.status === "success") {
          this.albums = res.albums.sort(
            (a: albumData, b: albumData) => -(a.release_year - b.release_year)
          );
        }
      });
  },
  components: { BaseAlbum, BaseDialog, BaseLineLoad },
});
</script>

<style lang="scss" scoped></style>
