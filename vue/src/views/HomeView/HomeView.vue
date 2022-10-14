<script lang="ts">
import { computed } from "vue";
import HomeViewLeftSideBar from "../../components/HomeView/HomeViewLeftSideBar.vue";
import HomeViewRightSideBar from "../../components/HomeView/HomeViewRightSideBar.vue";
import HomeViewPlayer from "../../components/HomeView/HomeViewPlayer.vue";
import HomeViewHeader from "../../components/HomeView/HomeViewHeader.vue";
export default {
  name: "HomeView",
  components: {
    HomeViewLeftSideBar,
    HomeViewRightSideBar,
    HomeViewPlayer,
    HomeViewHeader,
  },
  data() {
    return {
      isLeftSideBarActive: false,
      isRightSideBarActive: false,
      audio: null as HTMLAudioElement | null,
      audioList: [
        {
          title: "Future Parade",
          artist: " 虹ヶ咲学園スクールアイドル同好会 ",
          src: "somestring.mp3",
        },
        {
          title: "Level Oops! Adventures",
          artist: " 虹ヶ咲学園スクールアイドル同好会 ",
          src: "02. Level Oops! Adventures.mp3",
        },
        {
          title: "Future Parade (Off Vocal)",
          artist: " 虹ヶ咲学園スクールアイドル同好会 ",
          src: "03. Future Parade (Off Vocal).mp3",
        },
        {
          title: "Level Oops! Adventures (Off Vocal)",
          artist: " 虹ヶ咲学園スクールアイドル同好会 ",
          src: "04. Level Oops! Adventures (Off Vocal).mp3",
        },
      ],
      playingAudio: {
        title: "",
        artist: "",
        album: "",
        duration: 0,
        hears: 0,
        src: "",
      },
      //play song property
      isPlaying: false,
      progress: 0,
      audioIndex: 0,
      volume: 100,
      repeat: "off",
      isOnShuffle: false,
      shuffledList: [] as Array<any>,
      // visualizer
      ctx: null as AudioContext | null,
      audioSource: null as MediaElementAudioSourceNode | null,
      analayzer: null as AnalyserNode | null,
      frequencyData: null as Uint8Array | null,
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
    toggleLeftSideBar() {
      this.isLeftSideBarActive = !this.isLeftSideBarActive;
    },
    toggleRightSideBar(value: boolean) {
      this.isRightSideBarActive = value;
    },
    // Song event control
    playSong() {
      this.audio.play();
      this.isPlaying = true;
    },
    pauseSong() {
      this.audio.pause();
      this.isPlaying = false;
    },
    nextSong() {
      if (this.audioIndex === this.audioList.length - 1) {
        if (this.repeat === "off") return;
        this.audioIndex = 0;
      } else {
        this.audioIndex++;
      }
    },
    loadSong() {},
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
      if (this.isPlaying) this.playSong();
    },
    setPlaySong(index: number) {
      if (this.audioIndex === index) return;
      this.audioIndex = index;
    },
    //playlist
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
    // visualizer
    renderFrame() {
      if (this.frequencyData) {
        if (this.analayzer)
          this.analayzer.getByteFrequencyData(this.frequencyData);
      }
    },
    // Other Method
    shuffleArr(array: Array<any>) {
      let currentIndex = array.length,
        randomIndex;
      // While there remain elements to shuffle.
      while (currentIndex != 0) {
        // Pick a remaining element.
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex--;
        // And swap it with the current element.
        [array[currentIndex], array[randomIndex]] = [
          array[randomIndex],
          array[currentIndex],
        ];
      }
      return array;
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
        let playing = this.audioList[this.audioIndex];
        let listNotContainPlaying = this.audioList.filter(
          (item) => item.title !== playing.title
        );
        this.shuffledList = [
          playing,
          ...this.shuffleArr(listNotContainPlaying),
        ];
        this.audioIndex = 0;
        console.log("shuffle", this.audioIndex);
      } else {
        let playing = this.shuffledList[this.audioIndex];
        let audioIndex = this.audioList.findIndex(
          (item) => item.title === playing.title
        );
        this.audioIndex = audioIndex;
      }
    },
    audioIndex(n) {
      if (this.isOnShuffle) {
        this.playingAudio = this.shuffledList[n];
      } else {
        this.playingAudio = this.audioList[n];
      }
    },
    playingAudio() {
      this.audio.src =
        "http://127.0.0.1:5173/src/assets/music/" + this.playingAudio.src;
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
  },
  mounted() {
    (this.audio as HTMLAudioElement) = this.$refs["audio"];
    this.audio.volume = this.volume / 100;
    this.playingAudio = this.audioList[this.audioIndex];
    document.addEventListener("keyup", (e) => {
      if (e.code === "Space") {
        if (this.isPlaying) {
          this.pauseSong();
        } else {
          this.playSong();
        }
      }
      if (e.code === "ArrowRight") {
        this.nextSong();
      }
      if (e.code === "ArrowLeft") {
        this.prevSong();
      }
    });
  },
};
</script>

<template>
  <HomeViewHeader @toggleLeftSideBar="toggleLeftSideBar" />
  <div class="main-body">
    <HomeViewLeftSideBar :isActive="isLeftSideBarActive" />
    <main><RouterView /></main>
    <HomeViewRightSideBar
      :isActive="isRightSideBarActive"
      :playlist="audioList"
      :playingAudio="playingAudio"
      :audioIndex="audioIndex"
      :shuffle="isOnShuffle"
      :shuffledPlaylist="shuffledList"
      @onDrop="onDrop"
      @setPlaySong="setPlaySong"
    />
  </div>
  <HomeViewPlayer
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
  ></audio>
</template>
<style lang="scss" scoped>
.main-body {
  display: flex;
  position: relative;
  height: calc(100vh - 60px - 80px);
  main {
    flex: 1;
    overflow-y: auto;
  }
}
</style>
