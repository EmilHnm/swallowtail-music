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
    <div class="playing-details__meta">
      <div class="playing-details__meta--title">
        <BaseTooltipVue :tooltipText="playingAudio.title" :position="'bottom'">
          <router-link
            :to="
              playingAudio.album_id
                ? {
                    name: 'albumViewPage',
                    params: { id: playingAudio.album_id },
                  }
                : { name: 'playingPage' }
            "
            >{{ playingAudio.title }}</router-link
          >
        </BaseTooltipVue>
        <div class="playing-details__meta--artists">
          <BaseTooltipVue
            :tooltipText="
              playingAudio.artist.map((artist) => artist.name).join(',')
            "
            :position="'bottom'"
          >
            <router-link
              v-for="artist in playingAudio.artist"
              :key="artist.id"
              :to="{ name: 'artistPage', params: { id: artist.artist_id } }"
              >{{ artist.name }}
            </router-link>
          </BaseTooltipVue>
        </div>
      </div>
      <div class="playing-details__meta--options">
        <button @click="menu.open = !menu.open">
          <IconThreeDots />
        </button>
        <div class="menu" v-if="menu.open">
          <template v-if="menu.menuMode == 'default'">
            <BaseListItem v-if="!menu.isLikeLoading" @click="onLikeSong">
              <span v-if="playingAudio.like !== null">
                {{ playingAudio.like?.length ? "Unlike" : "Like" }}
              </span>
            </BaseListItem>
            <BaseListItem v-else>
              <BaseLineLoad />
            </BaseListItem>
            <BaseListItem
              v-if="!menu.isPlaylistLoading"
              @click="menu.menuMode = 'playlists'"
            >
              <span>Add to playlist</span>
            </BaseListItem>
            <BaseListItem v-else>
              <BaseLineLoad />
            </BaseListItem>
            <BaseListItem @click="menu.menuMode = 'artists'">
              <span>View Artists</span>
            </BaseListItem>
            <BaseListItem @click="navigateToAlbum(playingAudio.album_id)">
              <span>View Album</span>
            </BaseListItem>
            <BaseListItem>
              <span>Report</span>
            </BaseListItem>
          </template>

          <template v-if="menu.menuMode == 'playlists'">
            <BaseListItem
              v-for="playlist in playlists"
              :key="playlist.playlist_id"
              @click="addToPlaylist(playlist.playlist_id)"
            >
              <span>
                {{ playlist.title }}
              </span>
            </BaseListItem>
            <BaseListItem @click="menu.menuMode = 'default'">
              <span>Back</span>
            </BaseListItem>
          </template>

          <template v-if="menu.menuMode == 'artists'">
            <BaseListItem
              @click="navigateToArtist(artist.artist_id)"
              v-for="artist in playingAudio.artist"
              :key="artist.artist_id"
            >
              <span>{{ artist.name }}</span>
            </BaseListItem>
            <BaseListItem @click="menu.menuMode = 'default'">
              <span>Back</span>
            </BaseListItem>
          </template>
        </div>
      </div>
    </div>
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
import { mapActions, mapGetters, mapMutations } from "vuex";
import { environment } from "@/environment/environment";
import IconThreeDots from "@/components/icons/IconThreeDots.vue";
import BaseDotLoading from "@/components/UI/BaseDotLoading.vue";
import { ImageColor } from "@/mixins/ImageColor";
import BaseTooltipVue from "@/components/UI/BaseTooltip.vue";
import BaseListItem from "@/components/UI/BaseListItem.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import type { songData } from "@/model/songModel";
export default defineComponent({
  props: {
    isActive: {
      type: Boolean,
      default: false,
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
      menu: {
        open: false,
        menuMode: "default" as "default" | "artists" | "playlists",
        isLikeLoading: false,
        isPlaylistLoading: false,
      },
    };
  },
  methods: {
    ...mapActions("song", ["getSongLyrics", "likeSong", "likedSong"]),
    ...mapActions("playlist", ["addSongToPlaylist"]),
    ...mapMutations("queue", ["setCurrentSongLike"]),
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
          this.lyrics = [];
          this.lyrics_loading = false;
        });
    },
    onLikeSong() {
      this.menu.isLikeLoading = true;
      this.likeSong({
        songId: this.playingAudio.song_id,
        userToken: this.token,
      }).then(() => {
        this.loadLiked();
        this.menu.isLikeLoading = false;
      });
    },
    loadLiked() {
      this.menu.isLikeLoading = true;
      this.likedSong({
        userToken: this.token,
        songId: this.playingAudio.song_id,
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res: any) => {
          document.dispatchEvent(
            new CustomEvent("like-song", {
              detail: {
                song_id: this.playingAudio.song_id,
                liked: res.liked,
              },
            })
          );
          if (res.liked) {
            this.setCurrentSongLike({
              data: [
                {
                  id: 0,
                  created_at: "",
                  updated_at: "",
                  user_id: this.user.user_id,
                  song_id: this.playingAudio.song_id,
                },
              ],
              id: this.playingAudio.song_id,
            });
          } else {
            this.setCurrentSongLike({
              data: [],
              id: this.playingAudio.song_id,
            });
          }
          console.log(res);
          this.menu.isLikeLoading = false;
        });
    },
    navigateToArtist(artist_id: string) {
      this.menu.open = false;
      this.menu.menuMode = "default";
      this.$router.push({
        name: "artistPage",
        params: {
          id: artist_id,
        },
      });
    },
    navigateToAlbum(album_id: string) {
      this.menu.open = false;
      this.$router.push({
        name: "albumViewPage",
        params: {
          id: album_id,
        },
      });
    },
    addToPlaylist(playlist_id: string) {
      this.menu.open = false;
      this.menu.menuMode = "default";
      this.addSongToPlaylist({
        token: this.token,
        song_id: this.playingAudio.song_id,
        playlist_id: playlist_id,
      }).then(() => {});
    },
  },
  watch: {
    playingAudio: {
      handler(n, o) {
        if (n.song_id != o?.song_id) {
          this.songChanged();
        }
      },
      deep: true,
      immediate: true,
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      user: "auth/userData",
      playlists: "playlist/getPlaylists",
      getCurrentSong: "queue/getCurrentSong",
    }),
    playingAudio(): songData {
      return this.getCurrentSong;
    },
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
    document.addEventListener("like-song", (data: any) => {
      if (data.detail.song_id === this.playingAudio.song_id) {
        if (data.detail.liked) {
          this.playingAudio.like = [
            {
              id: 0,
              created_at: "",
              updated_at: "",
              user_id: this.user.user_id,
              song_id: this.playingAudio.song_id,
            },
          ];
        } else {
          this.playingAudio.like = [];
        }
      }
    });
  },
  components: {
    IconThreeDots,
    BaseDotLoading,
    BaseTooltipVue,
    BaseListItem,
    BaseLineLoad,
  },
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
    max-width: 300px;
  }
  &__meta {
    display: flex;
    justify-content: space-between;
    width: 90%;
    gap: 10px;
    &--title {
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
        white-space: nowrap;
        text-overflow: ellipsis;
        &:hover {
          text-decoration: underline;
        }
      }
    }
    &--artists {
      display: flex;
      width: 100%;
      flex-wrap: nowrap;
      a {
        font-size: 1rem;
        width: max-content;
        white-space: nowrap;
        color: var(--text-subdued);
        margin-right: 8px;
        &:hover {
          color: var(--text-primary-color);
        }
      }
    }
    &--options {
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
        aspect-ratio: 1/1;
        width: 36px;
        height: auto;
        &:hover {
          background-color: var(--background-glass-color-primary);
        }
        svg {
          width: 20px;
          height: 20px;
          fill: var(--text-primary-color);
        }
      }
      .menu {
        position: absolute;
        right: 0;
        top: 100%;
        background-color: var(--background-blur-color-primary);
        border-radius: 10px;
        padding: 20px 0px;
        min-width: 250px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        backdrop-filter: blur(10px);
        overflow: hidden;
        & > * {
          cursor: pointer;
        }
      }
    }
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
