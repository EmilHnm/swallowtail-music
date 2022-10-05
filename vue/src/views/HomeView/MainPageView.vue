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
              <div class="latest-song__song--title">
                <h3>Future Parade</h3>
                <h4>虹ヶ咲学園スクールアイドル同好会</h4>
              </div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">
                <h3>Future Parade</h3>
                <h4>虹ヶ咲学園スクールアイドル同好会</h4>
              </div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">
                <h3>Future Parade</h3>
                <h4>虹ヶ咲学園スクールアイドル同好会</h4>
              </div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">
                <h3>Future Parade</h3>
                <h4>虹ヶ咲学園スクールアイドル同好会</h4>
              </div>
              <div class="latest-song__song--duration">3:00</div>
              <div class="latest-song__song--add-queue">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="latest-song__song">
              <div class="latest-song__song--title">
                <h3>Future Parade</h3>
                <h4>虹ヶ咲学園スクールアイドル同好会</h4>
              </div>
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
        <BaseHorizontalScroll>
          <BaseCardAlbum
            v-for="item in testArr"
            :key="item"
            :title="'Future Parade'"
            :uploader="'Emil'"
            :songCount="6"
        /></BaseHorizontalScroll>
      </div>
    </div>
    <div class="top">
      <div class="top__album">
        <h2>Top Album All Time</h2>
        <BaseHorizontalScroll>
          <BaseCardAlbum
            v-for="item in testArr"
            :key="item"
            :title="'Future Parade'"
            :uploader="'Emil'"
            :songCount="6"
          />
        </BaseHorizontalScroll>
      </div>
      <div class="top__artist">
        <h2>Top Artist All Time</h2>
        <BaseHorizontalScroll>
          <BaseCardArtist
            v-for="item in testArr"
            :key="item"
            :title="'虹ヶ咲学園スクールアイドル同好会'"
          />
        </BaseHorizontalScroll>
      </div>
    </div>
    <div class="favorite">
      <div class="favorite__playlist">
        <h2>Your Favorite List</h2>
        <div
          class="favorite__playlist--container"
          :class="{ min: min, medium: medium }"
        >
          <base-list-item>
            <div class="favorite__playlist__item">
              <div class="favorite__playlist__item--title">Future Parade</div>
              <div class="favorite__playlist__item--songCount">116 songs</div>
              <div class="favorite__playlist__item--play">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="favorite__playlist__item">
              <div class="favorite__playlist__item--title">Future Parade</div>
              <div class="favorite__playlist__item--songCount">116 songs</div>
              <div class="favorite__playlist__item--play">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="favorite__playlist__item">
              <div class="favorite__playlist__item--title">Future Parade</div>
              <div class="favorite__playlist__item--songCount">116 songs</div>
              <div class="favorite__playlist__item--play">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="favorite__playlist__item">
              <div class="favorite__playlist__item--title">Future Parade</div>
              <div class="favorite__playlist__item--songCount">116 songs</div>
              <div class="favorite__playlist__item--play">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="favorite__playlist__item">
              <div class="favorite__playlist__item--title">Future Parade</div>
              <div class="favorite__playlist__item--songCount">116 songs</div>
              <div class="favorite__playlist__item--play">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
          <base-list-item>
            <div class="favorite__playlist__item">
              <div class="favorite__playlist__item--title">Future Parade</div>
              <div class="favorite__playlist__item--songCount">116 songs</div>
              <div class="favorite__playlist__item--play">
                <IconPlay />
              </div>
            </div>
          </base-list-item>
        </div>
      </div>
      <div class="favorite__artist">
        <h2>Top Artist All Time</h2>
        <BaseHorizontalScroll>
          <BaseCardArtist
            v-for="item in testArr"
            :key="item"
            :title="'虹ヶ咲学園スクールアイドル同好会'"
          />
        </BaseHorizontalScroll>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "../../components/icons/IconPlay.vue";
import BaseCard from "../../components/UI/BaseCard.vue";
import BaseHorizontalScroll from "../../components/UI/BaseHorizontalScroll.vue";
import BaseCardArtist from "../../components/UI/BaseCardArtist.vue";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";
export default defineComponent({
  components: {
    IconPlay,
    BaseCard,
    BaseHorizontalScroll,
    BaseCardArtist,
    BaseCardAlbum,
  },
  setup() {},
  data() {
    return {
      news: 0,
      observer: null,
      min: false,
      medium: false,
      testArr: [1, 2, 3, 4, 5, 6, 7, 8],
    };
  },
  mounted() {
    const newsEle = this.$refs.news;
    this.observer = new ResizeObserver((entries) => {
      for (let entry of entries) {
        this.news = entry.contentRect.width;
      }
    });
    this.observer.observe(newsEle);
  },
  beforeUnmount() {
    this.observer.disconnect();
  },
  watch: {
    news(o: number, n) {
      if (550 < o && o <= 900) {
        this.medium = true;
        this.min = false;
      } else if (o <= 550) {
        this.min = true;
        this.medium = false;
      } else {
        this.min = false;
        this.medium = false;
      }
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
        width: 25%;
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
        width: 35%;
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
        overflow: hidden;
        & > h3 {
          width: 100%;
          font-size: 1.2rem;
          white-space: nowrap;
          text-overflow: ellipsis;
          margin: 2px 0;
          user-select: none;
        }
        & > h4 {
          width: 100%;
          font-size: 0.8rem;
          white-space: nowrap;
          text-overflow: ellipsis;
          color: var(--text-subdued);
          user-select: none;
          margin: 0;
          &:hover {
            text-decoration: underline;
          }
        }
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
          fill: #fff;
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
.favorite {
  padding: 0 10px;
  width: 100%;
  & .favorite__playlist {
    width: 100%;
    & .favorite__playlist--container {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      & > * {
        cursor: pointer;
        width: 30%;
        &:hover .favorite__playlist__item--play {
          opacity: 1;
        }
        &:hover .favorite__playlist__item--songCount {
          opacity: 0;
        }
      }
    }
    & .favorite__playlist--container.min {
      & > * {
        width: 90%;
      }
    }
    & .favorite__playlist--container.medium {
      & > * {
        width: 40%;
      }
    }
    & .favorite__playlist__item {
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
      &--songCount {
        font-size: 1rem;
        font-weight: 500;
      }

      &--play {
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
          fill: #fff;
        }
      }
    }
  }
}
</style>
