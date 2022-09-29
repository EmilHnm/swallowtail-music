<template>
  <div class="playing-list" :class="{ active: isActive }">
    <div class="playing-list__playing">
      <div class="playing-list--title"><h3>Playing</h3></div>
      <base-list-item :selected="true">
        <div class="playing-list__song">
          <IconPlay />
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div>
      </base-list-item>
    </div>
    <div class="playing-list__queue">
      <div class="playing-list--title"><h3>In Queue</h3></div>
      <base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">1</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div> </base-list-item
      ><base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">2</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div> </base-list-item
      ><base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">3</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div> </base-list-item
      ><base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">4</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div> </base-list-item
      ><base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">5</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div> </base-list-item
      ><base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">6</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div> </base-list-item
      ><base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">7</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div> </base-list-item
      ><base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">8</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div>
      </base-list-item>
      <base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">9</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div>
      </base-list-item>
      <base-list-item>
        <div class="playing-list__song">
          <div class="playing-list__song--order">10</div>
          <div class="playing-list__song--title">Future Parade</div>
          <div class="playing-list__song--duration">3:00</div>
        </div>
      </base-list-item>
    </div>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";
import IconPlay from "../icons/IconPlay.vue";

export default defineComponent({
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
  },
  components: { IconPlay },
});
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.playing-list {
  width: 0px;
  max-width: 350px;
  transition: 0.5s;
  background: var(--background-color-secondary);
  height: 100%;
  overflow: auto;
  border-right: 0.5px solid var(--text-subdued);
  .playing-list--title {
    width: 100%;
    h3 {
      width: 100%;
      text-align: center;
      font-size: 1.2rem;
      font-weight: bolder;
      color: var(--text-subdued);
    }
  }
  .playing-list__song {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    user-select: none;
    cursor: pointer;
    .playing-list__song--order {
      font-size: 16px;
      color: var(--text-subdued);
    }
    .playing-list__song--title {
      font-size: 16px;
      color: var(--text-primary-color);
      width: 100%;
      text-overflow: ellipsis;
      overflow: hidden;
      white-space: nowrap;
      margin: 0 5px;
    }
    .playing-list__song--duration {
      font-size: 16px;
      color: var(--text-subdued);
    }
    & svg {
      width: 30px;
      height: 30px;
      fill: var(--text-primary-color);
    }
  }
  .playing-list__queue {
    width: 100%;
    & > * {
      margin-bottom: 10px;
    }
  }
}
.playing-list.active {
  width: 30%;
  max-width: 350px;
}

@media (max-width: $mobile-width) {
  .playing-list.active {
    width: 100%;
    max-width: 500px;
    position: absolute;
    top: 0;
    right: 0;
    z-index: 50;
    .playing-list__song {
      .playing-list__song--order {
        display: none;
      }
    }
  }
}
</style>
