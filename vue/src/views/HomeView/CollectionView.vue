<template>
  <div class="collection-header">
    <div class="header__background"></div>
    <div class="header__icon">
      <IconHeartFilled />
    </div>
    <div class="info">
      <div class="info__type">Public</div>
      <div class="info__title">Liked Song</div>
      <div class="info__other">
        <div class="info__other--owner">{{ user.username }}</div>
        <div class="info__other--songCount">- {{ songCount }} Songs -</div>
        <div class="info__other--totalDuration">{{ totalDuration }}</div>
      </div>
    </div>
  </div>
  <div class="control">
    <div class="control__left">
      <div class="control__left--play" v-if="Object.keys(likedList).length > 0">
        <IconPlay @click="playLikedSong" />
      </div>
      <div
        class="control__left--menu"
        v-click-outside="() => (isMenuOpen = false)"
      >
        <IconHorizontalThreeDot @click="toggleMenu" />
        <div class="collection-menu" :class="{ active: isMenuOpen }">
          <BaseListItem @click="addLikedSongToQueue">Add to Queue</BaseListItem>
        </div>
      </div>
    </div>
  </div>
  <div class="songList" v-if="!isLoading" ref="songList">
    <div class="songList__header" v-if="Object.keys(likedList).length > 0">
      <div class="songList__header--left">
        <div class="songList__header--left--img"></div>
        <div class="songList__header--left--title">Title</div>
      </div>
      <div class="songList__header--right">
        <div class="songList__header--right--album">Album</div>
        <div class="songList__header--right--hears">Hears</div>
        <div class="songList__header--right--duration"><IconClock /></div>
        <div class="songList__header--right--control"></div>
      </div>
    </div>
    <div class="songList__empty" v-else>
      <h3>Just like some songs to fill the list!</h3>
    </div>
    <div class="songList__content">
      <BaseSongItem
        v-for="song in likedList"
        :key="song.song_id"
        :data="song"
        @select-song="onPlaySongInLikedList(song)"
      />
    </div>
  </div>
  <div class="" v-else>
    <BaseCircleLoad />
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "@/components/icons/IconPlay.vue";
import IconHorizontalThreeDot from "@/components/icons/IconHorizontalThreeDot.vue";
import BaseListItem from "@/components/UI/BaseListItem.vue";
import BaseSongItem from "@/components/UI/BaseSongItem.vue";
import globalEmitListener from "@/shared/constants/globalEmitListener";
import IconHeartFilled from "@/components/icons/IconHeartFilled.vue";
import { mapActions, mapGetters, mapMutations } from "vuex";
import { useMeta } from "vue-meta";
import BaseDialog from "@/components/UI/BaseDialog.vue";
import BaseLineLoad from "@/components/UI/BaseLineLoad.vue";
import type { song } from "@/model/songModel";
import type { album } from "@/model/albumModel";
import type { like } from "@/model/likeModel";
import type { artist } from "@/model/artistModel";
import BaseCircleLoad from "@/components/UI/BaseCircleLoad.vue";
import IconClock from "@/components/icons/IconClock.vue";

type songData = song & {
  album: album;
  artist: artist[];
  like: like[];
};

export default defineComponent({
  emits: [...globalEmitListener],
  components: {
    IconClock,
    IconPlay,
    IconHorizontalThreeDot,
    BaseListItem,
    BaseSongItem,
    IconHeartFilled,
    BaseDialog,
    BaseLineLoad,
    BaseCircleLoad,
  },
  data() {
    return {
      isMenuOpen: false,
      isSearchBarOpen: false,
      filterText: "",
      likedList: [] as songData[],
      songCount: 0,
      totalDuration: "",
      isLoading: false,
    };
  },
  methods: {
    ...mapActions("playlist", ["getLikedSongList"]),
    ...mapMutations("queue", [
      "clearQueue",
      "addSongs",
      "setCurrentIndex",
      "setQueue",
      "setPlaying",
    ]),
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    toggleSearchBar() {
      this.isSearchBarOpen = !this.isSearchBarOpen;
    },
    loadLikedList() {
      this.isLoading = true;
      this.getLikedSongList(this.token)
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            this.likedList = res.likedList;
            this.songCount = this.likedList.length;
            this.likedList = this.likedList.sort(
              (a: songData, b: songData) =>
                -a.like[0].created_at.localeCompare(b.like[0].created_at)
            );
            const tempDuration = this.likedList.reduce(
              (acc, cur) => acc + cur.file.duration,
              0
            );
            this.totalDuration = new Date(tempDuration * 1000)
              .toISOString()
              .substring(11, 19);
            this.isLoading = false;
          }
        });
    },
    likeSong() {
      this.loadLikedList();
    },
    playLikedSong() {
      if (this.likedList.length > 0) {
        this.clearQueue();
        this.setQueue([...this.likedList]);
        this.setCurrentIndex(0);
        this.setPlaying(true);
      }
    },
    addLikedSongToQueue() {
      this.isMenuOpen = false;
      if (this.likedList.length > 0) {
        this.addSongs(this.likedList);
      }
    },
    onPlaySongInLikedList(song: songData) {
      if (this.likedList.length > 0) {
        this.clearQueue();
        this.setQueue([...this.likedList]);
        const index = this.likedList.findIndex(
          (songLiked) => songLiked.song_id === song.song_id
        );
        this.setCurrentIndex(index);
        this.setPlaying(true);
      }
    },
  },
  mounted() {
    useMeta({
      title: `${this.user.username}'s Collection`,
    });
  },
  created() {
    this.loadLikedList();
    document.addEventListener("like-song", (data: any) => {
      this.loadLikedList();
    });
  },
  computed: {
    ...mapGetters({
      token: "auth/userToken",
      user: "auth/userData",
    }),
  },
  watch: {},
});
</script>
<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.collection-header {
  display: flex;
  align-items: center;
  padding: 40px 20px;
  position: relative;
  @container main  (max-width: #{$tablet-width}) {
    & {
      flex-direction: column;
      justify-content: center;
    }
  }
  & .header__background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(60deg, var(--color-primary), transparent);
    @container main  (max-width: #{$tablet-width}) {
      & {
        background: linear-gradient(180deg, var(--color-primary), transparent);
      }
    }

    z-index: -1;
    filter: blur(6px);
  }
  & .header__icon {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    position: relative;
    @container main  (max-width: #{$tablet-width}) {
      & {
        width: 150px;
        height: 150px;
      }
    }
    & svg {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 50%;
      height: 50%;
      fill: #fff;
    }
  }
  .info {
    width: 100%;
    height: 100%;
    padding: 0 20px;
    display: flex;
    flex-direction: column;
    @container main  (max-width: #{$tablet-width}) {
      & {
        text-align: center;
      }
    }

    &__type {
      font-size: 14px;
      color: #fff;
      font-weight: 700;
    }
    &__title {
      font-size: 32px;
      color: #fff;
      font-weight: 900;
    }
    &__other {
      display: flex;
      color: #fff;
      font-size: 16px;
      @container main  (max-width: #{$tablet-width}) {
        & {
          justify-content: center;
        }
      }
      @container main  (max-width: #{$mobile-width}) {
        & {
          flex-direction: column;
          justify-content: center;
        }
      }
      &--owner {
        font-weight: 700;
      }
      &--songCount {
        margin: 0 10px;
        font-weight: 400;
      }
    }
  }
}
.control {
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  &__left {
    display: flex;
    height: 100%;
    align-items: center;
    justify-content: center;
    &--play {
      aspect-ratio: 1/1;
      height: 50px;
      width: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      cursor: pointer;
      background: var(--color-primary);
      & svg {
        width: 25px;
        height: 25px;
        fill: #fff;
      }
      &:hover {
        transform: scale(1.05);
      }
    }
    &--menu {
      aspect-ratio: 1/1;
      height: 50px;
      width: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      margin: 0 10px;
      & svg {
        width: 25px;
        height: 25px;
        fill: var(--text-subdued);
      }
      &:hover svg {
        fill: var(--text-primary-color);
      }
      .collection-menu {
        position: absolute;
        display: block;
        top: 105%;
        left: 0px;
        width: 250px;
        border-radius: 10px;
        z-index: 20;
        padding: 10px 0px;
        display: none;
        background: var(--background-glass-color-primary);
        & > * {
          margin: 5px auto;
        }
        &.active {
          display: block;
        }
      }
    }
  }
}
.songList {
  width: 100%;
  &__header {
    width: 80%;
    height: 50px;
    color: var(--text-subdued);
    margin: 0 auto;
    display: flex;
    align-items: center;
    border-bottom: 1px solid var(--text-subdued);
    padding: 0px 17px;
    &--left {
      width: 70%;
      display: flex;
      &--img {
        width: 50px;
        flex: 1 0 50px;
      }
      &--title {
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        width: 100%;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
        text-align: center;
      }
    }
    &--right {
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      user-select: none;
      &--album {
        width: 60%;
        overflow: hidden;
        text-align: center;

        @container main  (max-width: #{$mobile-width}) {
          & {
            display: none;
          }
        }
      }
      &--hears {
        width: 20%;
        overflow: hidden;

        @container main  (max-width: #{$tablet-width}) {
          & {
            display: none;
          }
        }
      }
      &--duration {
        width: 20%;
        overflow: hidden;
        @container main  (max-width: #{$tablet-width}) {
          & {
            width: 20%;
          }
        }
        @container main  (max-width: #{$mobile-width}) {
          & {
            width: 80%;
          }
        }
      }
      &--control {
        width: 30px;
      }
    }
  }

  &__empty {
    h3 {
      width: 100%;
      text-align: center;
      user-select: none;
    }
  }
}

.bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: transparent;
  z-index: 10;
  display: none;
  &.active {
    display: block;
  }
}
</style>
