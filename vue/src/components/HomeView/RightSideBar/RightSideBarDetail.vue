<template>
  <div class="playing-details">
    <img
      :src="
        playingAudio.album
          ? `${environment.album_cover}/${playingAudio.album.image_path}`
          : `${environment.default}/no_image.jpg`
      "
      crossorigin="anonymous"
      ref="cover"
    />
    <div class="playing-details__title">
      <div class="playing-details__title--text">
        <router-link
          :to="
            playingAudio.album_id
              ? { name: 'albumViewPage', params: { id: playingAudio.album_id } }
              : { name: 'playingPage' }
          "
          :title="playingAudio.title"
          >{{ playingAudio.title }}</router-link
        >
        <div class="playing-details__title--artists">
          <router-link
            v-for="artist in playingAudio.artist"
            :key="artist.id"
            :to="{ name: 'artistPage', params: { id: artist.id } }"
            >{{ artist.name }}</router-link
          >
        </div>
      </div>
      <div class="playing-details__title--menu">
        <button>
          <IconThreeDots />
        </button>
      </div>
    </div>
    <div class="playing-details__artist"></div>
    <div
      class="playing-details__lyric"
      :style="{
        backgroundColor: album_cover_color,
        color: lyricsColor,
      }"
    >
      <h3 class="playing-details__lyric--title">Lyrics</h3>
      <div
        class="playing-details__lyric--content"
        v-if="lyrics.length > 0 && !lyrics_loading"
      >
        <p v-for="(lyric, index) in lyrics" :key="index">{{ lyric }}</p>
      </div>
      <div class="playing-details__lyric--loading" v-if="lyrics_loading">
        <BaseDotLoading if="lyrics_loading" />
      </div>
      <div
        class="playing-details__lyrics--error"
        v-if="lyrics.length == 0 && !lyrics_loading"
      >
        No lyrics found for this song
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { mapActions, mapGetters } from "vuex";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import type { song } from "@/model/songModel";
import { environment } from "@/environment/environment";
import IconThreeDots from "@/components/icons/IconThreeDots.vue";
import BaseDotLoading from "@/components/UI/BaseDotLoading.vue";
import { ImageColor } from "@/mixins/ImageColor";
type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};
export default defineComponent({
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
    playlist: {
      type: Array as () => songData[],
      required: true,
    },
    shuffledPlaylist: {
      type: Array as () => songData[],
      required: true,
    },
    playingAudio: {
      type: Object as () => songData,
      required: true,
    },
    audioIndex: {
      type: Number,
      required: true,
    },
    shuffle: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      environment,
      lyrics: [] as string[],
      lyrics_loading: false,
      album_cover_color: "var(--background-glass-color-primary)",
      lyricsColor: "var(--text-primary-color)",
      imgCover: null as HTMLImageElement | null,
      imageColor: null as ImageColor | null,
    };
  },
  methods: {
    ...mapActions("song", ["getSongLyrics"]),
    songChanged() {
      this.lyrics_loading = true;
      this.getSongLyrics({
        song_id: this.playingAudio.song_id,
        token: this.token,
      })
        .then((res: any) => res.json())
        .then((data: any) => {
          if (data.status == "success") {
            this.lyrics = data.lyrics ?? [];
          } else {
            this.lyrics = [];
          }
          this.lyrics_loading = false;
        })
        .catch((err: any) => {
          console.log(err);
          this.lyrics = [];
          this.lyrics_loading = false;
        });
    },
  },
  watch: {
    playingAudio: {
      handler() {
        this.songChanged();
      },
      deep: true,
      immediate: true,
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
  },
  mounted() {
    this.imgCover = this.$refs.cover as HTMLImageElement;
    this.imageColor = new ImageColor(this.$refs.cover as HTMLImageElement);
    this.imgCover.onload = () => {
      if (this.imageColor) {
        this.imageColor.getAverageRGB().then((res) => {
          let Y = 0.2126 * res.r + 0.7152 * res.g + 0.0722 * res.b;
          this.lyricsColor = Y < 128 ? "white" : "black";
          this.album_cover_color = `rgb(${res.r},${res.g},${res.b})`;
        });
      } else {
        this.album_cover_color = "var(--background-glass-color-primary)";
      }
    };
  },
  components: { IconThreeDots, BaseDotLoading },
});
</script>

<style lang="scss" scoped>
.playing-details {
  width: 100%;
  padding-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  gap: 20px;
  img {
    width: 90%;
    border-radius: 10px;
  }
  &__title {
    display: flex;
    justify-content: space-between;
    width: 90%;
    row-gap: 10px;
    &--text {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      a {
        font-size: 1.6rem;
        font-weight: bold;
        color: var(--text-primary-color);
        text-decoration: none;
        overflow: hidden;
        text-overflow: ellipsis;
        &:hover {
          text-decoration: underline;
        }
      }
    }
    &--artists {
      display: flex;
      width: max-content;
      // white-space: nowrap;
      // text-overflow: ellipsis;
      // overflow: scroll;
      gap: 8px;
      a {
        font-size: 1rem;
        display: block;
        width: max-content;
        white-space: nowrap;
        color: var(--text-subdued);
        &:hover {
          color: var(--text-primary-color);
        }
      }
    }
    &--menu {
      display: flex;
      align-items: center;
      position: relative;
      button {
        border-radius: 50%;
        padding: 5px;
        background-color: transparent;
        outline: none;
        border: none;
        cursor: pointer;
        &:hover {
          background-color: var(--background-glass-color-primary);
        }
        svg {
          width: 20px;
          height: 20px;
          fill: var(--text-primary-color);
        }
      }
    }
  }
  &__artist {
  }
  &__lyric {
    width: 90%;
    margin: 20px auto;
    padding: 10px;
    border-radius: 10px;
    background-color: var(--background-glass-color-primary);
    &--title {
      font-size: 1.4rem;
      font-weight: bold;
      margin-bottom: 10px;
    }
    &--content {
      font-size: 1rem;
      line-height: 1.5;
      white-space: pre-wrap;
      overflow-wrap: break-word;
      p {
        margin: 0;
        margin-bottom: 5px;
        min-height: 1em;
      }
    }
    &--loading {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100px;
    }
    &--error {
      text-align: center;
    }
  }
}
</style>
