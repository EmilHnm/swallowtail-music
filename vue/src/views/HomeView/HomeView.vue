<script lang="ts">
import { computed } from "vue";
import { _function } from "@/mixins";
import { mapActions, mapGetters } from "vuex";
import type { playlist } from "@/model/playlistModel";
import { environment } from "@/environment/environment";
import HomeViewLeftSideBar from "../../components/HomeView/HomeViewLeftSideBar.vue";
import HomeViewRightSideBar from "../../components/HomeView/HomeViewRightSideBar.vue";
import HomeViewPlayer from "../../components/HomeView/HomeViewPlayer.vue";
import HomeViewHeader from "../../components/HomeView/HomeViewHeader.vue";
import BaseDialog from "../../components/UI/BaseDialog.vue";

type playlistData = playlist & {
  songCount: number;
};

type songData = {
  song_id: string;
  title: string;
  artist_name: string;
  artist_id: string;
  added_date?: string;
  album_name: string;
  album_id: string;
  image_path: string;
  duration: number;
  listens?: number;
}[];

type songList = {
  [song_id: string]: songData;
};

export default {
  name: "HomeView",
  components: {
    HomeViewLeftSideBar,
    HomeViewRightSideBar,
    HomeViewPlayer,
    HomeViewHeader,
    BaseDialog,
  },
  data() {
    return {
      isLeftSideBarActive: false,
      isRightSideBarActive: false,
      audio: null as HTMLAudioElement | null,
      audioList: {} as songList,
      //play song property
      isPlaying: false,
      progress: 0,
      audioIndex: 0,
      volume: 100,
      repeat: "off",
      isOnShuffle: false,
      shuffledList: {} as songList,
      isAudioWaitting: false,
      // visualizer
      ctx: null as AudioContext | null,
      audioSource: null as MediaElementAudioSourceNode | null,
      analayzer: null as AnalyserNode | null,
      frequencyData: null as Uint8Array | null,
      // playlist
      userPlaylist: [] as playlistData[],
      //dialog
      dialogWaring: {
        title: "Warning",
        mode: "warning",
        content: "Please fill in all the fields",
        show: false,
      },
    };
  },
  provide() {
    return {
      playingAudio: computed(() => this.playingAudio),
      progress: computed(() => this.progress),
      isPlaying: computed(() => this.isPlaying),
      audio: computed(() => this.audio),
      frequencyData: computed(() => this.frequencyData),
      userPlaylist: computed(() => this.userPlaylist),
    };
  },
  methods: {
    ...mapActions("playlist", [
      "getAccountPlaylist",
      "deletePlaylist",
      "getPlaylistSongs",
    ]),
    // NOTE: Sidebar control
    toggleLeftSideBar() {
      this.isLeftSideBarActive = !this.isLeftSideBarActive;
    },
    toggleRightSideBar(value: boolean) {
      this.isRightSideBarActive = value;
    },
    // NOTE: Song event control
    playSong() {
      this.audio.play();
      this.isPlaying = true;
    },
    pauseSong() {
      this.audio.pause();
      this.isPlaying = false;
    },
    nextSong() {
      if (this.audioIndex === Object.keys(this.audioList).length - 1) {
        if (this.repeat === "off") return;
        this.audioIndex = 0;
      } else {
        this.audioIndex++;
      }
    },
    prevSong() {
      if (this.audioIndex === 0) {
        if (this.repeat === "off") return;
        this.audioIndex = Object.keys(this.audioList).length - 1;
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
      this.playingAudio[0].duration = this.audio.duration;
    },
    setProgress(progress: number) {
      this.audio.currentTime = (progress * this.playingAudio[0].duration) / 100;
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
      if (this.isPlaying) this.playSong();
    },
    waiting() {
      this.isAudioWaitting = true;
      this.audio.pause();
    },
    loadeddata() {
      this.isAudioWaitting = false;
      if (this.isPlaying) this.playSong();
    },
    setPlaySong(index: number) {
      if (this.audioIndex === index) return;
      this.audioIndex = index;
    },
    // NOTE:playinglist
    onDrop(start: number, end: number) {
      if (this.isOnShuffle) {
        const tempValue = Object.values(this.shuffledList)[start];
        const tempKey = Object.keys(this.shuffledList)[start];
        this.shuffledList = _function.spliceObject(this.shuffledList, start, 1);
        this.shuffledList = _function.spliceObject(this.shuffledList, end, 0, {
          [tempKey]: tempValue,
        });
      } else {
        const tempValue = Object.values(this.audioList)[start];
        const tempKey = Object.keys(this.audioList)[start];
        console.log(this.audioList);
        this.audioList = _function.spliceObject(this.audioList, start, 1);
        console.log("update");
        this.audioList = _function.spliceObject(this.audioList, end, 0, {
          [tempKey]: tempValue,
        });
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
        this.shuffledList = _function.spliceObject(this.shuffledList, index, 1);
      } else {
        this.audioList = _function.spliceObject(this.audioList, index, 1);
      }
      if (index < this.audioIndex) {
        this.audioIndex++;
      }
    },
    // NOTE:visualizer
    renderFrame() {
      if (this.frequencyData) {
        if (this.analayzer)
          this.analayzer.getByteFrequencyData(this.frequencyData);
      }
    },
    // NOTE:playlist
    loadPlaylist() {
      this.getAccountPlaylist(this.token)
        .then((res) => res.json())
        .then((res) => {
          this.userPlaylist = res.playlist;
        });
    },
    removePlaylist(id: string) {
      this.deletePlaylist({ playlist_id: id, token: this.token })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.loadPlaylist();
            this.dialogWaring = {
              title: "Success",
              mode: "announcement",
              content: res.message,
              show: true,
            };
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
    playPlaylist(id: string) {
      this.getPlaylistSongs({ playlist_id: id, token: this.token })
        .then((res) => res.json())
        .then((res) => {
          if (res.songList) {
            this.audioList = res.songList.reduce((r: any, a: any) => {
              r[a.song_id] = r[a.song_id] || [];
              r[a.song_id].push(a);
              return r;
            }, Object.create(null));
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
      console.log(id);
    },
    addAlbumToQueue(id: string) {
      console.log(id);
    },
    playSongInAlbum(id: string) {
      console.log(id);
    },
    //NOTE : Artist
    playArtistSong(id: string) {
      console.log(id);
    },
    // NOTE: dialog
    closeDialog() {
      this.dialogWaring.show = false;
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
    isOnShuffle(n) {
      if (n) {
        let playingData = Object.values(this.audioList)[this.audioIndex];
        let playingKey = Object.keys(this.audioList)[this.audioIndex];
        let listNotContainPlaying = _function.filterObject(
          this.audioList,
          playingKey
        );
        this.shuffledList = {
          [playingKey]: playingData,
          ..._function.shuffleObject(listNotContainPlaying),
        };
        this.audioIndex = 0;
        console.log(this.shuffledList, this.audioIndex);
      } else {
        let playingKey = Object.keys(this.shuffledList)[this.audioIndex];
        let audioIndex = Object.keys(this.audioList).findIndex(
          (key) => key === playingKey
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
        if (this.ctx === null) {
          this.ctx = new AudioContext();
          this.audioSource = this.ctx.createMediaElementSource(this.audio);
          this.analayzer = this.ctx.createAnalyser();
          if (this.analayzer) {
            this.audioSource.connect(this.analayzer);
            this.audioSource.connect(this.ctx.destination);
            this.frequencyData = new Uint8Array(
              this.analayzer.frequencyBinCount
            );
            if (this.frequencyData)
              this.analayzer.getByteFrequencyData(this.frequencyData);
          }
        }
      }
    },
    progress() {
      if (this.isPlaying) {
        this.renderFrame();
      }
    },
    playingAudio() {
      if (this.playingAudio) {
        this.audio.src = this.playingAudioSrc;
        this.playSong();
      }
    },
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
    }),
    playingAudio() {
      return Object.values(this.audioList)[this.audioIndex];
    },
    playingAudioSrc() {
      return this.playingAudio
        ? `${environment.song_src}/${this.playingAudio[0].song_id}.ogg`
        : "";
    },
  },
  created() {
    this.loadPlaylist();
  },
  mounted() {
    this.audio = this.$refs["audio"] as HTMLAudioElement;
    this.audio.crossOrigin = "anonymous";
    this.audio.volume = this.volume / 100;
  },
};
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
  </teleport>
  <HomeViewHeader @toggleLeftSideBar="toggleLeftSideBar" />
  <div class="main-body" :class="{ full: Object.keys(audioList).length <= 0 }">
    <HomeViewLeftSideBar :isActive="isLeftSideBarActive" />
    <main>
      <RouterView
        @updatePlaylist="loadPlaylist"
        @deletePlaylist="removePlaylist"
        @playPlaylist="playPlaylist"
        @playAlbum="playAlbum"
        @addAlbumToQueue="addAlbumToQueue"
        @playSongInAlbum="playSongInAlbum"
        @playArtistSong="playArtistSong"
      />
    </main>
    <HomeViewRightSideBar
      v-if="Object.keys(audioList).length > 0"
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
  </div>
  <HomeViewPlayer
    v-if="Object.keys(audioList).length > 0"
    :playingAudio="playingAudio"
    :progress="progress"
    :volume="volume"
    :repeat="repeat"
    :shuffle="isOnShuffle"
    :isPlaying="isPlaying"
    @toggleRightSideBar="toggleRightSideBar"
    @shuffleToggle="shuffleToggle"
    @repeatToggle="repeatToggle"
    @playSong="playSong"
    @pauseSong="pauseSong"
    @setProgress="setProgress"
    @prevSong="prevSong"
    @nextSong="nextSong"
    @setVolume="setVolume"
    @setMuted="setMuted"
  />
  <audio
    ref="audio"
    :volumn="volume"
    @timeupdate="getCurrentTime"
    @durationchange="getDuration"
    @ended="nextSong"
    @canplay="canplay"
    @waiting="waiting"
    @loadeddata="loadeddata"
  ></audio>
</template>
<style lang="scss" scoped>
.main-body {
  display: flex;
  position: relative;
  height: calc(100vh - 60px - 81px);
  &.full {
    height: calc(100vh - 81px);
  }
  main {
    flex: 1;
    overflow-y: auto;
  }
}
</style>
