<template>
  <teleport to="body">
    <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action><div></div></template>
    </BaseDialog>
  </teleport>
  <div class="song-item" ref="songItem">
    <BaseListItem :selected="selected">
      <div class="song-item__left">
        <div class="song-item__left--image" @click="selectSong">
          <img
            v-lazyload
            :data-url="
              data.album
                ? `${environment.album_cover}/${data.album.image_path}`
                : `${environment.default}/no_image.jpg`
            "
            :alt="data.title"
            :title="data.title"
            srcset=""
          />
        </div>
        <div class="song-item__left--title">
          <BaseTooltip :tooltipText="data.title" :position="'bottom'">
            <span @click="selectSong">{{ data.title }}</span>
          </BaseTooltip>
          <span v-if="data.artist.length > 0">
            <BaseTooltip
              :tooltipText="data.artist.map((item) => item.name).join(', ')"
              :position="'bottom'"
            >
              <router-link
                :to="{
                  name: 'artistPage',
                  params: { id: artistitem.artist_id },
                }"
                v-for="(artistitem, index) in data.artist"
                :key="artistitem.artist_id"
                >{{ artistitem.name
                }}<span v-if="index !== data.artist.length - 1">,</span>
              </router-link></BaseTooltip
            >
          </span>
          <span v-else><BaseLineLoad /></span>
        </div>
      </div>
      <div class="song-item__right">
        <div class="song-item__right--album">
          <BaseTooltip
            v-if="data.album"
            :tooltipText="data.album.name"
            :position="'bottom'"
          >
            <span @click="redirectToAlbum(data.album_id)">{{
              data.album.name
            }}</span>
          </BaseTooltip>
          <span v-else>Unknown</span>
        </div>
        <div
          class="song-item__right--addDate"
          v-if="data.playlist != null && data.playlist.length > 0"
        >
          <span>{{
            new Date(data.playlist[0].pivot.created_at).toLocaleDateString()
          }}</span>
        </div>
        <div class="song-item__right--hears" v-else>
          <span>{{ data.listens }}</span>
        </div>

        <div class="song-item__right--duration">
          <span>{{
            new Date(data.file.duration * 1000).toISOString().substring(14, 19)
          }}</span>
        </div>
        <div class="song-item__right--control" v-if="control">
          <div class="song-item__right--control--button" @click="toggleMenu">
            <IconThreeDots />
          </div>
        </div>
      </div>
    </BaseListItem>
  </div>
  <teleport to="body">
    <div class="bg" @click="toggleMenu" v-if="isMenuOpen"></div>
    <div class="menu" v-if="isMenuOpen && control">
      <div class="menu__song">
        <div class="menu__song--img">
          <img
            v-lazyload
            :data-url="
              data.album
                ? `${environment.album_cover}/${data.album.image_path}`
                : `${environment.default}/no_image.jpg`
            "
            :alt="data.title"
            srcset=""
          />
        </div>
        <div class="menu__song--title">
          <span class="title">{{ data.title }}</span>
          <span class="artist">
            <router-link
              :to="{ name: 'artistPage', params: { id: artistitem.artist_id } }"
              v-for="(artistitem, index) in data.artist"
              :key="artistitem.artist_id"
              >{{ artistitem.name
              }}<span v-if="index !== data.artist.length - 1">,</span>
            </router-link>
          </span>
        </div>
        <div class="menu__song--album">
          <span v-if="data.album">{{ data.album.name }}</span>
          <span v-else><BaseLineLoad /></span>
        </div>
      </div>
      <div class="menu__btn">
        <div class="" v-if="menuMode === 'default'">
          <BaseListItem @click="onLikeSong">
            <span v-if="liked !== null">
              {{ liked ? "Unlike" : "Like" }}
            </span>
            <span v-else>
              <BaseLineLoad />
            </span>
          </BaseListItem>
          <BaseListItem @click="changeMenuMode('playlist')"
            >Add to Playlist</BaseListItem
          >
          <BaseListItem v-if="inQueue" @click="deleteFromQueue"
            >Delete from Queue</BaseListItem
          >
          <BaseListItem v-else @click="addToQueue">Add to Queue</BaseListItem>
          <BaseListItem v-if="inPlaylist" @click="deleteFromPlaylist"
            >Delete from Playlist</BaseListItem
          >
          <BaseListItem @click="changeMenuMode('artist')"
            >View Artist</BaseListItem
          >
        </div>
        <div class="" v-if="menuMode === 'artist'">
          <router-link
            :to="{ name: 'artistPage', params: { id: artistitem.artist_id } }"
            v-for="artistitem in data.artist"
            :key="artistitem.artist_id"
          >
            <BaseListItem>{{ artistitem.name }}</BaseListItem>
          </router-link>
        </div>
        <div class="" v-if="menuMode === 'playlist'">
          <BaseListItem
            v-for="playlist in playlists"
            :key="playlist.playlist_id"
            @click="onAddSongToPlaylistlist(playlist.playlist_id)"
            >{{ playlist.title }}</BaseListItem
          >
        </div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { environment } from "@/environment/environment";
import BaseListItem from "./BaseListItem.vue";
import IconThreeDots from "@/components/icons/IconThreeDots.vue";
import BaseTooltip from "./BaseTooltip.vue";
import BaseLineLoad from "./BaseLineLoad.vue";
import { mapActions, mapGetters, mapMutations } from "vuex";
import BaseDialog from "./BaseDialog.vue";
import type { playlist } from "@/model/playlistModel";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { like } from "@/model/likeModel";
import type { artist } from "@/model/artistModel";

type songData = song & {
  album?: album;
  artist: artist[];
  like: like[];
  playlist?: playlist &
    {
      pivot: {
        song_id: string;
        playlist_id: string;
        created_at: string;
        updated_at: string;
      };
    }[];
};

export default defineComponent({
  emits: [
    "deleteFromQueue",
    "deleteFromPlaylist",
    "selectSong",
    "likeSong",
    "addToQueue",
  ],
  data() {
    return {
      environment: environment,
      isMenuOpen: false,
      menuMode: "default",
      isLoading: false,
      liked: 0,
    };
  },
  props: {
    data: {
      type: Object as () => songData,
      required: true,
    },
    selected: {
      type: Boolean,
      default: false,
    },
    inQueue: {
      type: Boolean,
      default: false,
    },
    inPlaylist: {
      type: Boolean,
      default: false,
    },
    control: {
      type: Boolean,
      default: true,
    },
  },
  methods: {
    ...mapActions("playlist", ["addSongToPlaylist"]),
    ...mapActions("song", ["likeSong", "likedSong"]),
    ...mapMutations("queue", ["addSong", "deleteSong"]),
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
      this.changeMenuMode("default");
    },
    changeMenuMode(mode: string) {
      this.menuMode = mode;
    },
    deleteFromQueue() {
      this.isMenuOpen = false;
      this.deleteSong(this.data);
    },
    deleteFromPlaylist() {
      this.isMenuOpen = false;
      this.$emit("deleteFromPlaylist");
    },
    addToQueue() {
      this.isMenuOpen = false;
      this.addSong(this.data);
    },
    selectSong() {
      this.$emit("selectSong", this.data.song_id);
    },
    onAddSongToPlaylistlist(id: string) {
      this.isLoading = true;
      this.addSongToPlaylist({
        token: this.token,
        song_id: this.data.song_id,
        playlist_id: id,
      }).then(() => {
        this.isLoading = false;
        this.toggleMenu();
      });
    },
    redirectToAlbum(id: string) {
      this.$router.push({ name: "albumViewPage", params: { id: id } });
    },
    loadLiked() {
      this.likedSong({
        userToken: this.token,
        songId: this.data.song_id,
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res: any) => {
          this.liked = res.liked;
          document.dispatchEvent(
            new CustomEvent("like-song", {
              detail: {
                song_id: this.data.song_id,
                liked: res.liked,
              },
            })
          );
          this.isLoading = false;
        });
    },
    onLikeSong() {
      if (this.liked !== null) {
        this.isLoading = true;
        this.isMenuOpen = false;
        this.likeSong({
          userToken: this.token,
          songId: this.data.song_id,
        }).then(() => {
          this.loadLiked();
        });
      }
    },
  },
  created() {
    this.liked = this.data.like.length > 0 ? 1 : 0;
  },
  mounted() {
    document.addEventListener("like-song", (data: any) => {
      if (data.detail.song_id === this.data.song_id) {
        this.liked = data.detail.liked;
      }
    });
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      playlists: "playlist/getPlaylists",
    }),
  },
  components: {
    BaseListItem,
    IconThreeDots,
    BaseLineLoad,
    BaseDialog,
    BaseTooltip,
  },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: var(--background-glass-color-primary);
  z-index: 10;
}
.menu {
  position: absolute;
  display: block;
  left: 50%;
  top: 50%;
  max-width: 320px;
  border-radius: 10px;
  z-index: 20;
  padding: 30px 20px;
  transform: translate(-50%, -50%);
  background: var(--background-color-primary);
  user-select: none;
  max-height: 70%;
  overflow: scroll;
  &::-webkit-scrollbar {
    width: 0;
  }
  &__song {
    width: 100%;
    text-align: center;
    &--img {
      aspect-ratio: 1/1;
      width: calc(100% - 40px);
      height: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: auto;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
      }
    }
    &--title {
      margin: 10px auto;
      width: calc(100% - 40px);
      span {
        font-size: 1rem;

        &.artist {
          display: flex;
          flex-wrap: wrap;
          a {
            color: var(--text-color-primary);
            text-decoration: none;
            margin-right: 2px;
            &:hover {
              text-decoration: underline;
            }
          }
        }
        &.title {
          font-weight: 600;
          width: 100%;
          overflow-x: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
          display: block;
        }
      }
    }
    &--album {
      margin: 10px auto;
      width: calc(100% - 40px);
      span {
        display: block;
        width: 100%;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-color-secondary);
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
  }
  &__btn {
    margin-top: 10px;
    width: 100%;
    & > div {
      max-height: 300px;
      overflow: auto;
      & > * {
        margin-bottom: 10px;
        cursor: pointer;
      }
    }
  }
}
@media (max-width: $mobile-width) {
  .menu {
    max-width: 100%;
    padding: 10px 0;
    height: 70%;
    overflow: auto;
    z-index: 300;
    bottom: 0;
    top: auto;
    left: 0;
    transform: none;
    animation: 0.3s;
    border-radius: 0;
    &__song {
      width: 100%;
      &--img {
        width: 50%;
      }
      &--title {
        & > span {
          text-align: center;
          justify-content: center;
        }
      }
      &--album {
        span {
          text-align: center;
        }
      }
    }
  }
}

.song-item {
  width: 100%;
  height: max-content;
  display: flex;
  margin: 10px auto;
  cursor: pointer;
  gap: 10px;
  container-type: inline-size;
  container-name: song-item;
  &__left {
    width: 40%;
    display: flex;
    gap: 10px;
    overflow: hidden;
    flex: 0 0 auto;
    @container song-item  (max-width: #{$mobile-width}) {
      & {
        width: 70%;
      }
    }
    &--image {
      width: 50px;
      height: 50px;
      flex: 0 0 auto;
      margin-right: 10px;
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }
    &--title {
      display: flex;
      flex-direction: column;
      justify-content: center;
      overflow: hidden;
      width: 100%;
      user-select: none;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      span {
        &:first-child {
          font-size: 1rem;
          font-weight: 600;
          white-space: nowrap;
          text-overflow: ellipsis;
          overflow: hidden;
        }
        a {
          font-size: 0.8rem;
          color: var(--text-subdued);
          white-space: nowrap;
          text-overflow: ellipsis;
          overflow: hidden;
          &:hover {
            text-decoration: underline;
          }
        }
      }
    }
  }
  &__right {
    width: 60%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
    @container song-item  (max-width: #{$mobile-width}) {
      & {
        width: 100%;
      }
    }
    &--album {
      width: 50%;
      overflow: hidden;
      text-align: center;
      text-overflow: ellipsis;
      &:hover {
        text-decoration: underline;
      }
      @container song-item  (max-width: #{$mobile-width}) {
        & {
          display: none;
        }
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--hears {
      width: 20%;
      overflow: hidden;
      text-overflow: ellipsis;
      text-align: center;
      @container song-item  (max-width: #{$tablet-width}) {
        & {
          display: none;
        }
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--addDate {
      width: 20%;
      overflow: hidden;
      text-overflow: ellipsis;
      text-align: center;
      @container song-item  (max-width: #{$tablet-width}) {
        & {
          display: none;
        }
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--duration {
      width: 20%;
      overflow: hidden;
      text-align: center;
      @container song-item  (max-width: #{$tablet-width}) {
        & {
          width: 20%;
        }
      }
      @container song-item  (max-width: #{$mobile-width}) {
        & {
          width: 80%;
        }
      }
      span {
        font-size: 1rem;
        color: var(--text-subdued);
        white-space: nowrap;
        text-overflow: ellipsis;
      }
    }
    &--control {
      height: 30px;
      width: 30px;
      border-radius: 20%;
      position: relative;
      flex: 0 0 auto;
      &:hover {
        background: var(--background-glass-color-primary);
        box-shadow: 0 0px 15px rgb(0 0 0 / 50%);
      }
      &--button {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        & svg {
          width: 20px;
          height: 20px;
          fill: var(--text-primary-color);
        }
      }
    }
  }
}
</style>
