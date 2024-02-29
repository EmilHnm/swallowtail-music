<script lang="ts">
import io from "socket.io-client";
import Echo from "laravel-echo";
import { computed, defineComponent } from "vue";
import { Timer } from "@/mixins/Timer";
import { mapActions, mapGetters, mapMutations } from "vuex";
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
      songLoadController: null as AbortController | null,
      songLoadSignal: null as AbortSignal | null,
      //play song property
      audioIndex: 0,
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
    ...mapActions("playlist", ["getAccountPlaylist"]),
    ...mapMutations("queue", ["setProgress", "setPlaying", "setCurrentIndex"]),
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
      this.setPlaying(true);
    },
    pauseAudio() {
      this.audio.pause();
      this.setPlaying(false);
    },
    onSetProgress(progress: number) {
      this.audio.currentTime =
        (progress * this.playingSong.file.duration) / 100;
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
    ...mapActions("song", ["increaseSongListens"]),
    // NOTE: dialog
    closeDialog() {
      this.dialogWaring.show = false;
    },
  },
  watch: {
    repeat(n) {
      this.audio.loop = n === "one";
    },
    volume() {
      this.audio.volume = this.volume / 100;
    },
    // visualizer
    isPlaying() {
      if (this.isPlaying) {
        this.audio?.play();
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
        this.audio?.pause();
      }
    },
    progress() {
      if (this.isPlaying) {
        this.renderFrame();
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
    playingSong: {
      handler(n: songData | null, o: songData | null) {
        if (n && n.song_id !== o?.song_id) {
          this.timeOut = null;
          this.timeOut = new Timer(() => {
            this.increaseSongListens({
              token: this.token,
              song_id: this.playingSong.song_id,
            })
              .then((res) => res.json())
              .then((res) => {
                if (res.status === "success") {
                  this.timeOut = null;
                }
              });
          }, 45000);
          this.timeOut.resume();
          this.audio.src = "";
          this.audio.pause();
          this.audio.load();
          this.waiting();
          if (!this.songLoadController) {
            this.songLoadController = new AbortController();
          } else {
            this.songLoadController.abort();
            this.songLoadController = new AbortController();
          }
          this.songLoadSignal = this.songLoadController.signal;
          fetch(`${environment.api}/song/${this.playingSong.song_id}/stream`, {
            method: "GET",
            headers: {
              Authorization: `Bearer ${this.token}`,
            },
            signal: this.songLoadSignal,
          })
            .then((res) => res.blob())
            .then((blob) => {
              const objectUrl = URL.createObjectURL(blob);
              this.audio.src = objectUrl;
              this.audio.load();
              if (this.isPlaying) this.playAudio();
            });
        }
      },
      deep: true,
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      currentSong: "queue/getCurrentSong",
      getVolume: "queue/getVolume",
      getRepeat: "queue/getRepeat",
      getCurrentProgress: "queue/getCurrentProgress",
      getCurrentSong: "queue/getCurrentSong",
      getPlaying: "queue/getPlaying",
      getCurrentIndex: "queue/getCurrentIndex",
      getQueue: "queue/getQueue",
    }),
    playingSong(): songData {
      return this.currentSong;
    },
    volume(): number {
      return this.getVolume;
    },
    repeat(): string {
      return this.getRepeat;
    },
    progress(): number {
      return this.getCurrentProgress;
    },
    isPlaying(): boolean {
      return this.getPlaying;
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
      this.setProgress(this.audio.currentTime);
    };
    // this.audio.ondurationchange = () => {
    //   this.getDuration();
    // };
    this.audio.onended = () => {
      this.setCurrentIndex(this.getCurrentIndex + 1);
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
      if (e.key === " " && this.playingSong) {
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

    document.addEventListener("play", () => {
      this.playAudio();
    });

    document.addEventListener("pause", () => {
      this.pauseAudio();
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
      <router-view @updatePlaylist="loadPlaylist" v-slot="{ Component }">
        <keep-alive include="mainPage">
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </main>

    <HomeViewRightSideBar
      v-if="getQueue.length > 0"
      :isActive="isRightSideBarActive"
    />
    <HomeUploadBox :isPlaying="!!getCurrentSong"></HomeUploadBox>
  </div>
  <HomeViewPlayer
    v-if="getQueue.length > 0"
    :isPlaying="isPlaying"
    :isWating="isAudioWaitting"
    @toggleRightSideBar="toggleRightSideBar"
    @playSong="playAudio"
    @pauseSong="pauseAudio"
    @onSetProgress="onSetProgress"
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
