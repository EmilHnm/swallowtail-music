<script lang="ts">
import io from "socket.io-client";
import Echo from "laravel-echo";
import { computed, defineComponent } from "vue";
import { _function } from "@/mixins";
import { Timer } from "@/mixins/Timer";
import { mapActions, mapGetters } from "vuex";
import { environment } from "@/environment/environment";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import HomeViewLeftSideBar from "@/components/HomeView/HomeViewLeftSideBar.vue";
import HomeViewRightSideBar from "@/components/HomeView/RightSideBar/HomeViewRightSideBar.vue";
import HomeViewPlayer from "@/components/HomeView/HomeViewPlayer.vue";
import HomeViewHeader from "@/components/HomeView/Header/HomeViewHeader.vue";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import HomeUploadBox from "@/components/HomeView/HomeUpload/HomeUploadBox.vue";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    playingAudioSrc: string;
    playingAudio: songData;
    token: string;
  }
}

export default defineComponent({
  name: "HomeView",
  components: {
    HomeViewLeftSideBar,
    HomeViewRightSideBar,
    HomeViewPlayer,
    HomeViewHeader,
    BaseDialog,
    BaseLineLoad,
    HomeUploadBox,
  },
  data() {
    return {
      isLeftSideBarActive: false,
      isRightSideBarActive: false,
      audio: null as HTMLAudioElement | null,
      audioList: [] as songData[],
      timeOut: null as null | Timer,
      //play song property
      isPlaying: false,
      progress: 0,
      audioIndex: 0,
      volume: 100,
      repeat: "off",
      isOnShuffle: false,
      shuffledList: [] as songData[],
      isAudioWaitting: false,
      // visualizer
      ctx: null as AudioContext | null,
      audioSource: null as MediaElementAudioSourceNode | null,
      analayzer: null as AnalyserNode | null,
      frequencyData: null as Uint8Array | null,
      //dialog
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
      isLoading: false,
    };
  },
  provide() {
    return {
      playingAudio: computed(() => this.playingAudio),
      progress: computed(() => this.progress),
      isPlaying: computed(() => this.isPlaying),
      audio: computed(() => this.audio),
      frequencyData: computed(() => this.frequencyData),
    };
  },
  methods: {
    ...mapActions("playlist", [
      "getAccountPlaylist",
      "getPlaylistSongs",
      "getLikedSongList",
    ]),
    ...mapActions("album", ["getAlbumSongs"]),
    // NOTE: Sidebar control
    toggleLeftSideBar() {
      this.isLeftSideBarActive = !this.isLeftSideBarActive;
    },
    toggleRightSideBar(value: boolean) {
      this.isRightSideBarActive = value;
    },
    // NOTE: Song event control
    playAudio() {
      this.audio.play();
      this.isPlaying = true;
    },
    pauseAudio() {
      this.audio.pause();
      this.isPlaying = false;
    },
    nextSong() {
      if (this.audioIndex === this.audioList.length - 1) {
        if (this.repeat === "off") {
          this.isPlaying = false;
          return;
        }
        this.audioIndex = 0;
      } else {
        this.audioIndex++;
      }
    },
    prevSong() {
      if (this.audioIndex === 0) {
        if (this.repeat === "off") return;
        this.audioIndex = this.audioList.length - 1;
      } else {
        this.audioIndex--;
      }
    },
    repeatToggle() {
      if (this.repeat === "off") {
        this.repeat = "one";
      } else if (this.repeat === "one") {
        this.repeat = "all";
      } else {
        this.repeat = "off";
      }
    },
    shuffleToggle() {
      this.isOnShuffle = !this.isOnShuffle;
    },
    getCurrentTime() {
      this.progress = this.audio.currentTime;
    },
    getDuration() {
      this.playingAudio.duration = this.audio.duration;
    },
    setProgress(progress: number) {
      this.audio.currentTime = (progress * this.playingAudio.duration) / 100;
    },
    setVolume(value: string) {
      this.volume = +value;
    },
    setMuted() {
      if (this.volume > 0) {
        this.volume = 0;
      } else {
        this.volume = 100;
      }
    },
    canplay() {
      this.isAudioWaitting = false;
      if (this.isPlaying) this.playAudio();
    },
    waiting() {
      this.isAudioWaitting = true;
      this.audio.pause();
    },
    loadeddata() {
      this.isAudioWaitting = false;
      if (this.isPlaying) this.playAudio();
    },
    setPlaySong(index: number) {
      if (this.audioIndex === index) return;
      this.audioIndex = index;
    },
    // NOTE:playinglist
    onDrop(start: number, end: number) {
      if (this.isOnShuffle) {
        const temp = this.shuffledList[start];
        this.shuffledList.splice(start, 1);
        this.shuffledList.splice(end, 0, temp);
      } else {
        const temp = this.audioList[start];
        this.audioList.splice(start, 1);
        this.audioList.splice(end, 0, temp);
      }
      if (start === this.audioIndex) {
        this.audioIndex = end;
      } else if (start < this.audioIndex && end >= this.audioIndex) {
        this.audioIndex--;
      } else if (start > this.audioIndex && end <= this.audioIndex) {
        this.audioIndex++;
      }
    },
    deleteFromQueue(index: number) {
      if (index === this.audioIndex) {
        return;
      }
      if (this.isOnShuffle) {
        this.shuffledList.splice(index, 1);
      } else {
        this.audioList.splice(index, 1);
      }
      if (index < this.audioIndex) {
        this.audioIndex--;
      }
    },
    // NOTE:visualizer
    renderFrame() {
      if (this.frequencyData && this.analayzer) {
        this.analayzer.getByteFrequencyData(this.frequencyData);
      }
    },
    // NOTE:playlist
    loadPlaylist() {
      this.getAccountPlaylist(this.token);
    },
    playPlaylist(id: string) {
      this.isLoading = true;
      this.getPlaylistSongs({ playlist_id: id, token: this.token })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.songList) {
            if (res.songList.length) {
              this.audioList = res.songList;
              this.audioIndex = 0;
              this.playAudio();
            } else {
              this.dialogWaring = {
                title: "Error",
                mode: "warning",
                content: "This playlist is empty, cannot be played",
                show: true,
              };
            }
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    addPlaylistToQueue(id: string) {
      this.isLoading = true;
      this.getPlaylistSongs({ playlist_id: id, token: this.token })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.songList) {
            const newSongList = res.songList;
            this.audioList = [...this.audioList, newSongList];
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    playSongInPlaylist(array: string[]) {
      this.isLoading = true;
      this.getPlaylistSongs({ playlist_id: array[0], token: this.token })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.songList) {
            this.audioList = res.songList;
            this.audioIndex = this.audioList.findIndex(
              (song: songData) => song.song_id == array[1]
            );
            this.playAudio();
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    // NOTE: Album
    playAlbum(id: string) {
      this.isLoading = true;
      this.getAlbumSongs({ album_id: id, userToken: this.token })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.status === "success") {
            this.audioList = res.songs;
            this.audioIndex = 0;
            this.playAudio();
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    addAlbumToQueue(id: string) {
      this.isLoading = true;
      this.getAlbumSongs({ album_id: id, userToken: this.token })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          if (res.status === "success") {
            const newSongList = res.songs;
            this.audioList = [...this.audioList, ...newSongList].filter(
              (song: songData, index: number, self: songData[]) =>
                index === self.findIndex((t) => t.song_id === song.song_id)
            );
            if (this.isOnShuffle)
              this.shuffledList = [...this.shuffledList, ...newSongList].filter(
                (song: songData, index: number, self: songData[]) =>
                  index === self.findIndex((t) => t.song_id === song.song_id)
              );
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    playSongInAlbum(array: string[]) {
      this.isLoading = true;
      this.getAlbumSongs({ album_id: array[0], userToken: this.token })
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.status === "success") {
            this.audioList = res.songs;
            this.audioList.forEach((key, index) => {
              if (key.song_id === array[1]) {
                this.audioIndex = index;
              }
            });
            this.playAudio();
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    //NOTE : Artist
    ...mapActions("artist", ["getArtistTopSongById"]),
    playArtistSong(id: string) {
      this.isLoading = true;
      this.getArtistTopSongById({
        token: this.token,
        artist_id: id,
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.status === "success") {
            this.audioList = res.songs;
            this.audioIndex = 0;
            this.playAudio();
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    addArtistSongToQueue(id: string) {
      this.isLoading = true;
      this.getArtistTopSongById({
        token: this.token,
        artist_id: id,
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.status === "success") {
            const newSongList: songData[] = res.songs;
            this.audioList = [...this.audioList, ...newSongList].filter(
              (song: songData, index: number, self: songData[]) =>
                index === self.findIndex((t) => t.song_id === song.song_id)
            );
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    playSongOfArtist(array: string[]) {
      this.isLoading = true;
      this.getArtistTopSongById({
        token: this.token,
        artist_id: array[0],
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.status === "success") {
            this.audioList = res.songs;
            this.audioList.forEach((key, index) => {
              if (key.song_id === array[1]) {
                this.audioIndex = index;
              }
            });
            this.playAudio();
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    //NOTE: Liked Song
    ...mapActions("song", ["getSongForPlay", "increaseSongListens"]),
    playLikedSong() {
      this.isLoading = true;
      this.getLikedSongList(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.status === "success") {
            this.audioList = res.likedList;
            this.audioIndex = 0;
            this.playAudio();
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    addLikedSongToQueue() {
      this.isLoading = true;
      this.getLikedSongList(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.isLoading = false;
          this.isOnShuffle = false;
          this.shuffledList = [];
          if (res.status === "success") {
            this.audioList = [...this.audioList, res.likedList].filter(
              (song: songData, index: number, self: songData[]) =>
                index === self.findIndex((t) => t.song_id === song.song_id)
            );
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    // NOTE: Song
    addToQueue(song: any) {
      this.audioList = [...this.audioList, song].filter(
        (song: songData, index: number, self: songData[]) =>
          index === self.findIndex((t) => t.song_id === song.song_id)
      );
      if (this.isOnShuffle)
        this.shuffledList = [...this.shuffledList, song].filter(
          (song: songData, index: number, self: songData[]) =>
            index === self.findIndex((t) => t.song_id === song.song_id)
        );
    },
    playSong(song_id: string) {
      this.getSongForPlay({
        token: this.token,
        song_id: song_id,
      })
        .then((res: any) => {
          return res.json();
        })
        .then((res) => {
          if (res.status === "success") {
            this.audioList.length = 0;
            this.audioList.push(res.song);
            this.audioIndex = 0;
            this.playAudio();
          } else {
            this.dialogWaring = {
              title: "Error",
              mode: "warning",
              content: res.message,
              show: true,
            };
          }
        });
    },
    // NOTE: dialog
    closeDialog() {
      this.dialogWaring.show = false;
    },
    // NOTE: audio
    getAudio() {
      this.audio = this.$refs.audio as HTMLAudioElement;
    },
  },
  watch: {
    repeat(n) {
      if (n === "one") {
        this.audio.loop = true;
      } else {
        this.audio.loop = false;
      }
    },
    isOnShuffle() {
      if (this.isOnShuffle) {
        let playingSongData = this.audioList[this.audioIndex];
        let listNotContainPlaying = this.audioList.filter(
          (item: songData) => item.song_id !== playingSongData.song_id
        );
        let temp: songData[] = _function.shuffleArr(listNotContainPlaying);
        this.shuffledList = [playingSongData, ...temp];
        this.audioIndex = 0;
      } else {
        let playingKey = this.shuffledList[this.audioIndex].song_id;
        let audioIndex = this.audioList.findIndex(
          (key: songData) => key.song_id === playingKey
        );
        this.audioIndex = audioIndex;
      }
    },
    volume() {
      this.audio.volume = this.volume / 100;
    },
    // visualizer
    isPlaying() {
      if (this.isPlaying) {
        if (this.timeOut) this.timeOut.resume();
        if (this.ctx === null) {
          this.ctx = new AudioContext();
          this.audioSource = this.ctx.createMediaElementSource(this.audio);
          this.analayzer = this.ctx.createAnalyser();
          if (this.analayzer) {
            this.audioSource.connect(this.analayzer);
            this.audioSource.connect(this.ctx.destination);
            this.frequencyData = new Uint8Array(
              this.analayzer.frequencyBinCount
            ) as any;
            if (this.frequencyData)
              this.analayzer.getByteFrequencyData(
                this.frequencyData as Uint8Array
              );
          }
        }
      } else {
        if (this.timeOut) this.timeOut.pause();
      }
    },
    progress() {
      if (this.isPlaying) {
        this.renderFrame();
      }
    },
    playingAudio() {
      if (this.playingAudio) {
        this.timeOut = null;
        this.timeOut = new Timer(() => {
          this.increaseSongListens({
            token: this.token,
            song_id: this.playingAudio.song_id,
          })
            .then((res) => res.json())
            .then((res) => {
              if (res.status === "success") {
                this.timeOut = null;
              }
            });
        }, 45000);
        this.timeOut.resume();
        fetch(`${environment.api}/song/${this.playingAudio.song_id}/stream`, {
          method: "GET",
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        })
          .then((res) => res.blob())
          .then((blob) => {
            const objectUrl = URL.createObjectURL(blob);
            this.audio.src = objectUrl;
            if (this.isPlaying) this.playAudio();
          });
      }
    },
    "$store.state.uploadQueue.getUploadingFile": {
      handler(n) {
        if (n) {
          window.onbeforeunload = () =>
            "Some files are uploading. Are you sure you want to leave?";
        } else {
          window.onbeforeunload = () => {};
        }
      },
      immediate: true,
      deep: true,
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
    playingAudio(): songData {
      // BUG : when isOnShuffle change, this run 2 time
      // Once isOnShuffle change, and once audioIndex change
      if (this.isOnShuffle) {
        return this.shuffledList[this.audioIndex];
      }
      return this.audioList[this.audioIndex];
    },
  },
  created() {
    window.io = io;

    window.Echo = new Echo({
      broadcaster: "socket.io",
      host: `${environment.socket_url}:${environment.socket_port}`,
      auth: {
        headers: {
          Authorization: `Bearer ${this.token}`,
        },
      },
    });
    this.loadPlaylist();
    this.audio = new Audio();
    this.audio.crossOrigin = "anonymous";
    this.audio.volume = this.volume / 100;
    this.audio.ontimeupdate = () => {
      this.getCurrentTime();
    };
    this.audio.ondurationchange = () => {
      this.getDuration();
    };
    this.audio.onended = () => {
      this.nextSong();
    };
    this.audio.oncanplay = () => {
      this.canplay();
    };
    this.audio.onwaiting = () => {
      this.waiting();
    };
    this.audio.onloadeddata = () => {
      this.loadeddata();
    };

    document.addEventListener("keydown", (e: KeyboardEvent) => {
      if (e.target instanceof HTMLInputElement) {
        return;
      }
      if (e.key === " " && this.playingAudio) {
        e.preventDefault();
        if (this.isPlaying) {
          this.pauseAudio();
          return;
        }
        this.playAudio();
      }
    });

    document.addEventListener("contextmenu", (e) => {
      e.preventDefault();
    });
  },
  unmounted() {
    if (this.audio.src) this.audio.src = "";
  },
});
</script>

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
    <BaseDialog :open="isLoading" :title="'Loading ...'" :mode="'announcement'">
      <template #default>
        <BaseLineLoad />
      </template>
      <template #action>
        <div></div>
      </template>
    </BaseDialog>
  </teleport>
  <HomeViewHeader @toggleLeftSideBar="toggleLeftSideBar" />
  <div class="main-body">
    <HomeViewLeftSideBar :isActive="isLeftSideBarActive" />
    <main>
      <router-view
        @updatePlaylist="loadPlaylist"
        @playPlaylist="playPlaylist"
        @playSongInPlaylist="playSongInPlaylist"
        @addPlaylistToQueue="addPlaylistToQueue"
        @playAlbum="playAlbum"
        @addAlbumToQueue="addAlbumToQueue"
        @playSongInAlbum="playSongInAlbum"
        @playArtistSong="playArtistSong"
        @playSongOfArtist="playSongOfArtist"
        @addArtistSongToQueue="addArtistSongToQueue"
        @playLikedSong="playLikedSong"
        @addLikedSongToQueue="addLikedSongToQueue"
        @addToQueue="addToQueue"
        @playSong="playSong"
        v-slot="{ Component }"
      >
        <keep-alive include="mainPage">
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </main>

    <HomeViewRightSideBar
      v-if="audioList.length > 0"
      :isActive="isRightSideBarActive"
      :playlist="audioList"
      :playingAudio="playingAudio"
      :audioIndex="audioIndex"
      :shuffle="isOnShuffle"
      :shuffledPlaylist="shuffledList"
      @onDrop="onDrop"
      @setPlaySong="setPlaySong"
      @deleteFromQueue="deleteFromQueue"
    />
    <HomeUploadBox :isPlaying="playingAudio ? true : false"></HomeUploadBox>
  </div>
  <HomeViewPlayer
    v-if="audioList.length > 0"
    :playingAudio="playingAudio"
    :progress="progress"
    :volume="volume"
    :repeat="repeat"
    :shuffle="isOnShuffle"
    :isPlaying="isPlaying"
    :isWating="isAudioWaitting"
    @toggleRightSideBar="toggleRightSideBar"
    @shuffleToggle="shuffleToggle"
    @repeatToggle="repeatToggle"
    @playSong="playAudio"
    @pauseSong="pauseAudio"
    @setProgress="setProgress"
    @prevSong="prevSong"
    @nextSong="nextSong"
    @setVolume="setVolume"
    @setMuted="setMuted"
  />
</template>
<style lang="scss" scoped>
.main-body {
  display: flex;
  position: relative;
  overflow: hidden;
  flex: 1;
  justify-content: center;
  width: calc(100% - 20px);
  gap: 10px;
  main {
    flex: 1;
    background: var(--background-glass-color-primary);
    border-radius: 10px;
    overflow-y: auto;
    user-select: none;
    container-name: main;
    container-type: inline-size;
  }
}
</style>
