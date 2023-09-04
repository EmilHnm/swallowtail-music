<template>
  <div class="playing-details">
    <img
      v-lazyload
      :data-url="
        playingAudio.album
          ? `${environment.album_cover}/${playingAudio.album.image_path}`
          : `${environment.default}/no_image.jpg`
      "
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
    <div class="playing-details__lyric">
      <h3 class="playing-details__lyric--title">Lyrics</h3>
      <p>
        見上げた空の向こうへと<br />
        鮮やかに伸びてくrainbow ときめきから紡いでく our stories 膨らんでく
        どこまでだって 未来は果てしない 誰かの夢の鼓動が高鳴る
        あふれる想いのバトン 繋いでいこう 新しい（勇気と）出会いと (harmonies)
        生まれてく 一緒に行こう さぁ きみも！ はじまりの風 光る 瞬く明日へと
        きみの（きみの）想い（想い） 咲くって信じてるの ひとりじゃないからね
        どんなときも 描こうよ with you forever 夢の虹は いつも 胸の中
        僕らを繋いでるから ふと振り返れば続く 刻んだ軌跡 夢色 どんな瞬間もきらり
        true stories 笑顔 ナミダ 迷いも彩り 未来は無限大 僕らで創ろう
        虹の咲く世界を ためらう心に今 送るエール
        踏み出した（勇気の）一歩が（力に） 変わるんだ 一緒にいこう さぁ 僕ら
        はじまりの風 そよぐ ほらね すぐそばで きみの（きみの）想い（想い）
        羽ばたくの待ってるの 僕らがいるからね どんなときも 大丈夫 We're all
        together きみの虹は いつも 胸の中 明日へと駆けていくんだ 出会いと 夢が
        今 あぁ 輝くよ はじまりの風 光る 瞬く明日へと
        きみの（きみの）想い（想い） 咲くって信じてるの ひとりじゃないからね
        どんなときも 描こうよ with you forever 夢の虹は いつも 胸の中
        僕らを繋いでるから 光 たどって きみと 僕らで進もう Go for dream 何度でも
        夢を 夢を 追いかけていこう！
      </p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type { album } from "@/model/albumModel";
import type { artist } from "@/model/artistModel";
import type { like } from "@/model/likeModel";
import type { song } from "@/model/songModel";
import { environment } from "@/environment/environment";
import IconThreeDots from "@/components/icons/IconThreeDots.vue";
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
    };
  },
  components: { IconThreeDots },
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
      width: 100%;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      gap: 8px;
      a {
        font-size: 1rem;
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
    margin: 0 auto;
    padding: 10px;
    border-radius: 10px;
    background-color: var(--background-glass-color-primary);
    &--title {
      font-size: 1.4rem;
      font-weight: bold;
      margin-bottom: 10px;
    }
    p {
      font-size: 1rem;
      line-height: 1.5;
      white-space: pre-wrap;
      word-break: break-all;
    }
  }
}
</style>
