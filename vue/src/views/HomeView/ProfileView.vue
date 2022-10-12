<template>
  <div class="user-header">
    <div class="header__background"></div>
    <div class="header__image">
      <img src="../../assets/music/cover.jpg" alt="" srcset="" />
    </div>
    <div class="info">
      <div class="info__type">Profile</div>
      <div class="info__title">Hoa Ngominh</div>
      <div class="info__other">
        <div class="info__other--playlistCount">4 public playlists</div>
        <div class="info__other--songUploaded">- 4 Songs -</div>
        <div class="info__other--albumUploaded">1 Album</div>
      </div>
    </div>
  </div>
  <div class="control">
    <BaseButton>Edit Profile</BaseButton>
  </div>
  <div class="detail" ref="detail">
    <div class="topArtist">
      <h2>Top artist this month</h2>
      <span>This only visible to you</span>
      <BaseHorizontalScroll>
        <BaseCardArtist
          v-for="album in albums"
          :key="album"
          :title="' 虹ヶ咲学園スクールアイドル同好会 '"
        />
      </BaseHorizontalScroll>
    </div>
    <div class="topTracks">
      <h2>Top artist this month</h2>
      <span>This only visible to you</span>
      <BaseSongItem
        v-for="song in songs"
        :key="song"
        :title="' Future Parade '"
        :artist="' 虹ヶ咲学園スクールアイドル同好会 '"
        :album="' Future Parade '"
        :duration="' 3:21 '"
        :hears="1000"
      />
    </div>
    <div class="publicPlaylist">
      <h2>Public Playlist</h2>
      <BaseHorizontalScroll>
        <BaseCardAlbum
          v-for="playlist in playlists"
          :key="playlist"
          :songCount="4"
          :title="' 虹ヶ咲学園スクールアイドル同好会 '"
        />
      </BaseHorizontalScroll>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import BaseButton from "../../components/UI/BaseButton.vue";
import BaseHorizontalScroll from "../../components/UI/BaseHorizontalScroll.vue";
import BaseCardArtist from "../../components/UI/BaseCardArtist.vue";
import BaseSongItem from "../../components/UI/BaseSongItem.vue";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";

export default defineComponent({
  setup() {},
  components: {
    BaseButton,
    BaseHorizontalScroll,
    BaseCardArtist,
    BaseSongItem,
    BaseCardAlbum,
  },
  data() {
    return {
      isMenuOpen: false,
      isSearchBarOpen: false,
      filterText: "",
      detailWidth: 0,
      observer: null as ResizeObserver | null,
      small: false,
      medium: false,
      albums: [1, 2, 3, 4, 5],
      songs: [1, 2, 3, 4, 5],
      playlists: [1, 2, 3, 4],
    };
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    toggleSearchBar() {
      this.isSearchBarOpen = !this.isSearchBarOpen;
    },
  },
  mounted() {
    const detail = this.$refs.detail;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.detailWidth = entry.contentRect.width;
      }
    });
    if (this.observer && detail) this.observer.observe(detail);
  },
  watch: {
    detailWidth(o) {
      if (o < 600) {
        this.small = true;
        this.medium = true;
      } else if (o < 700 && o > 600) {
        this.small = false;
        this.medium = true;
      } else {
        this.small = false;
        this.medium = false;
      }
    },
  },
});
</script>
<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.user-header {
  display: flex;
  align-items: center;
  padding: 40px 20px;
  position: relative;
  & .header__background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(60deg, var(--color-primary), transparent);
    z-index: -1;
    filter: blur(6px);
  }
  & .header__image {
    width: 190px;
    height: 190px;
    overflow: hidden;
    flex: 0 0 auto;
    border-radius: 50%;
    & img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
  .info {
    width: 100%;
    height: 100%;
    padding: 0 20px;
    display: flex;
    flex-direction: column;

    &__type {
      font-size: 14px;
      color: #fff;
      font-weight: 700;
    }
    &__title {
      font-size: 42px;
      color: #fff;
      font-weight: 900;
    }
    &__other {
      display: flex;
      color: #fff;
      font-size: 16px;
      &--playlistCount {
        font-weight: 700;
      }
      &--songUploaded {
        margin: 0 10px;
        font-weight: 400;
      }
      &--albumUploaded {
        margin: 0 10px;
        font-weight: 400;
      }
    }
  }
}
.control {
  height: 100px;
  width: 20%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}
.detail {
  width: 100%;
}

@media (max-width: $mobile-width) {
  .user-header {
    & .header__image {
      display: none;
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
