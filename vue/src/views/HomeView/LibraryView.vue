<template>
  <div class="container">
    <!-- TODO: move redirect to BaseCardAlbum -->
    <BaseCardAlbum
      v-for="item in playlist"
      :key="item.playlist_id"
      :id="item.playlist_id"
      :title="item.title"
      :uploader="'Emil'"
      :songCount="6"
      :type="'playlist'"
      @redirect="redirect"
    />
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import { mapGetters } from "vuex";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";

export default defineComponent({
  components: { BaseCardAlbum },
  computed: {
    ...mapGetters({
      playlist: "playlist/getPlaylist",
    }),
  },
  methods: {
    redirect({ id, type }: { id: string; type: string }) {
      this.$router.push({ name: "playlistViewPage", params: { id: id } });
    },
  },
});
</script>
<style lang="scss" scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  flex-wrap: wrap;
}
</style>
