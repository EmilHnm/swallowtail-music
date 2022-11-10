<template>
  <div>
    <BaseAlbum
      v-if="Object.keys(album).length > 0"
      :data="album"
      @playAlbum="playAlbum"
      @addAlbumToQueue="addAlbumToQueue"
      @playSongInAlbum="playSongInAlbum"
    />
    <BaseCircleLoad v-else />
  </div>
</template>

<script lang="ts">
import BaseAlbum from "@/components/UI/BaseAlbum.vue";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import type { album } from "@/model/albumModel";
import { mapActions, mapGetters } from "vuex";
import { defineComponent } from "vue";
type albumData = album & {
  song_count: number;
};
export default defineComponent({
  emits: ["playAlbum", "addAlbumToQueue", "playSongInAlbum"],
  data() {
    return {
      album: {} as albumData,
    };
  },
  methods: {
    ...mapActions("album", ["getAlbum"]),
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
    this.getAlbum({
      token: this.token,
      album_id: this.$route.params.id,
    })
      .then((res) => {
        return res.json();
      })
      .then((res) => {
        this.album = res.album;
      });
  },
  components: { BaseAlbum, BaseCircleLoad },
});
</script>

<style scoped></style>
