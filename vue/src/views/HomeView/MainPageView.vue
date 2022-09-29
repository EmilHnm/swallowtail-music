<template>
  <div class="wrapper">
    <div class="news" ref="news">
      <div class="latest-songs">
        <h2>Latest Songs Update</h2>
        <div
          class="latest-song__container"
          :class="{ min: min, medium: medium }"
        >
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">Future Parade</div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">Future Parade</div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">Future Parade</div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">Future Parade</div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">Future Parade</div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">Future Parade</div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
        </div>
      </div>
      <div class="latest-album">
        <h2>Latest Album Update</h2>
        <div class="album__container" ref="album__container">
          <button class="album__container--left-arrow" @click="moveRight">
            <IconLeftArrow />
          </button>
          <button class="album__container--right-arrow" @click="moveLeft">
            <IconRightArrow />
          </button>
          <div
            class="album__wrapper"
            ref="album__wrapper"
            :style="{ transform: 'translateX(' + album__wrapperX + 'px)' }"
          >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
          </div>
        </div>
      </div>
    </div>
    <div class="top">
      <div class="top__album">
        <h2>Top Album All Time</h2>
        <div class="album__container" ref="album__container">
          <button class="album__container--left-arrow" @click="moveRight">
            <IconLeftArrow />
          </button>
          <button class="album__container--right-arrow" @click="moveLeft">
            <IconRightArrow />
          </button>
          <div
            class="album__wrapper"
            ref="album__wrapper"
            :style="{ transform: 'translateX(' + album__wrapperX + 'px)' }"
          >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
            <BaseCard>
              <div class="album">
                <div class="album__image">
                  <img class="img" src="../../assets/music/cover.jpg" />
                  <div class="album__image--play">
                    <IconPlay />
                  </div>
                </div>
                <div class="album__title">Future Parade</div>
                <div class="album__uploader">Emil</div>
                <div class="album__songCount">6 songs</div>
              </div></BaseCard
            >
          </div>
        </div>
      </div>
      <div class="top__artist"></div>
    </div>
    <div class="favorite">
      <div class="favorite__playlist"></div>
      <div class="favorite__artist"></div>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "../../components/icons/IconPlay.vue";
import BaseCard from "../../components/UI/BaseCard.vue";
import IconLeftArrow from "../../components/icons/IconLeftArrow.vue";
import IconRightArrow from "../../components/icons/IconRightArrow.vue";

export default defineComponent({
  components: { IconPlay, BaseCard, IconLeftArrow, IconRightArrow },
  setup() {},
  data() {
    return {
      album__wrapper: 0,
      album__wrapperX: 0,
      news: 0,
      observer: null,
      min: false,
      medium: false,
    };
  },
  methods: {
    moveLeft() {
      if (this.album__wrapperX < -this.album__wrapper - 200) {
        this.album__wrapperX = 0;
        return;
      }
      this.album__wrapperX -= 230;
    },
    moveRight() {
      if (this.album__wrapperX > 200) {
        this.album__wrapperX = -this.album__wrapper;
        return;
      }
      this.album__wrapperX += 230;
    },
  },
  mounted() {
    const newsEle = this.$refs.news;
    const resizeObserver = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.news = entry.contentRect.width;
      }
    });
    resizeObserver.observe(newsEle);
    ///==============================================
    this.album__wrapper = this.$refs.album__wrapper.clientWidth - this.news;
  },
  watch: {
    news(o: number, n) {
      if (550 < o && o <= 780) {
        this.medium = true;
        this.min = false;
      } else if (o <= 550) {
        this.min = true;
        this.medium = false;
      } else {
        this.min = false;
        this.medium = false;
      }
      //this.album__wrapperX = 0;
      this.album__wrapper = this.$refs.album__wrapper.clientWidth - this.news;
    },
  },
});
</script>
<style lang="scss" scoped>
.wrapper {
  width: 100%;
  height: 100%;
  overflow: auto;
  overflow-x: hidden;
  h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--color-primary);
    user-select: none;
  }
  .album__wrapper {
    display: flex;
    width: max-content;
    padding: 60px;
    transition: 0.3s;
    & > * {
      cursor: pointer;
      &:hover .album__image--play {
        opacity: 1;
      }
    }
  }
  .album {
    width: 180px;
    height: 100%;
    margin: 10px auto;
    padding: 10px;
    &__image {
      aspect-ratio: 1/1;
      width: 100%;
      height: auto;
      position: relative;
      border-radius: 10px;
      overflow: hidden;

      & img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      &--play {
        position: absolute;
        bottom: -10px;
        right: 10px;
        transform: translateY(-50%);
        width: 25px;
        height: 25px;
        background: var(--color-primary);
        border-radius: 50%;
        padding: 10px;
        color: var(--text-subdued);
        transition: 0.3s;
        opacity: 0;
        & svg {
          width: 100%;
          height: 100%;
        }
      }
    }
    &__title {
      font-size: 1.2rem;
      font-weight: 600;
      text-overflow: ellipsis;
      margin-top: 0.5rem;
      user-select: none;
    }
    &__uploader {
      font-size: 1rem;
      font-weight: 500;
      text-overflow: ellipsis;
      user-select: none;
    }
    &__songCount {
      font-size: 1rem;
      font-weight: 500;
      text-overflow: ellipsis;
      user-select: none;
    }
  }
  .album__container {
    width: 100%;
    height: 350px;
    overflow: hidden;
    display: flex;
    position: relative;
    & > .box {
      cursor: pointer;
      width: 200px;
      &:hover .album__image--play {
        opacity: 1;
      }
    }
    &--right-arrow,
    &--left-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 50px;
      height: 50px;
      background: var(--color-primary);
      border-radius: 50%;
      padding: 3px;
      color: var(--text-subdued);
      opacity: 1;
      transition: 0.3s;
      z-index: 20;
      border: none;
      & svg {
        width: 100%;
        height: 100%;
      }
    }
    &--right-arrow {
      right: 15px;
    }
    &--left-arrow {
      left: 15px;
    }
  }
}
.news {
  width: 100%;
  padding: 0 10px;
  & .latest-songs {
    width: 100%;
    & .latest-song__container {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      & > * {
        cursor: pointer;
        width: 30%;
        &:hover .latest-song__song--add-queue {
          opacity: 1;
        }
        &:hover .latest-song__song--duration {
          opacity: 0;
        }
      }
    }
    & .latest-song__container.min {
      & > * {
        width: 90%;
      }
    }
    & .latest-song__container.medium {
      & > * {
        width: 40%;
      }
    }
    & .latest-song__song {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      width: 100%;
      &--title {
        font-size: 1.2rem;
        font-weight: 600;
        text-overflow: ellipsis;
      }
      &--duration {
        font-size: 1rem;
        font-weight: 500;
      }

      &--add-queue {
        position: absolute;
        top: 50%;
        right: 0;
        transform: translateY(-50%);
        width: 25px;
        height: 25px;
        background: var(--color-primary);
        border-radius: 50%;
        padding: 3px;
        color: var(--text-subdued);
        opacity: 0;
        transition: 0.3s;
        & svg {
          width: 100%;
          height: 100%;
        }
      }
    }
  }
  & .latest-album {
    width: 100%;
  }
}

.top {
  padding: 0 10px;
  width: 100%;
}
</style>
