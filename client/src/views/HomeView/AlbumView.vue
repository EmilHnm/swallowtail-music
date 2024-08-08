<template>
  <div>
    <BaseAlbum
      v-if="Object.keys(album).length > 0"
      :data="album"
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
import { useMeta, createMetaManager } from "vue-meta";
import globalEmitListener from "@/shared/constants/globalEmitListener";
type albumData = album & {
  song_count: number;
};
export default defineComponent({
  emits: [...globalEmitListener],
  data() {
    return {
      album: {} as albumData,
    };
  },
  methods: {
    ...mapActions("album", ["getAlbum"]),
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
        useMeta(
          {
            title: "Album",
          },
          createMetaManager()
        );
      });
  },
  components: { BaseAlbum, BaseCircleLoad },
});
</script>

<style scoped></style>
