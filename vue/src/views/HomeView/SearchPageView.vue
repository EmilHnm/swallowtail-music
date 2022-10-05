<template>
  <div class="search-form">
    <form @submit.prevent="onSubmit">
      <BaseInput
        :id="'search'"
        :label="'Search'"
        :type="'text'"
        :placeholder="'Enter your key word!'"
        v-model="searchText"
      />
      <BaseButton :type="'submit'">Search</BaseButton>
    </form>
  </div>
  <div class="search-result">
    <div class="search-result__tag" ref="container" @wheel="searchFilterScroll">
      <div class="search-result__tag--all">
        <BaseButton @click="changeComponent('AllSearchPage')">All</BaseButton>
      </div>
      <div class="search-result__tag--song">
        <BaseButton @click="changeComponent('SongSearchPage')">Song</BaseButton>
      </div>
      <div class="search-result__tag--album">
        <BaseButton @click="changeComponent('AlbumSearchPage')"
          >Album</BaseButton
        >
      </div>
      <div class="search-result__tag--artist">
        <BaseButton @click="changeComponent('ArtistSearchPage')"
          >Artist</BaseButton
        >
      </div>
      <div class="search-result__tag--user">
        <BaseButton @click="changeComponent('UserSearchPage')">User</BaseButton>
      </div>
    </div>
    <keep-alive v-if="testArr.length !== 0">
      <component :is="selectedSearchFilter"></component>
    </keep-alive>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import BaseInput from "../../components/UI/BaseInput.vue";
import BaseButton from "../../components/UI/BaseButton.vue";
import AllSearchPage from "../../components/SearchPage/AllSearchPage.vue";
import ArtistSearchPage from "../../components/SearchPage/ArtistSearchPage.vue";
import AlbumSearchPage from "../../components/SearchPage/AlbumSearchPage.vue";
import UserSearchPage from "../../components/SearchPage/UserSearchPage.vue";
import SongSearchPage from "../../components/SearchPage/SongSearchPage.vue";
export default defineComponent({
  setup() {},
  data() {
    return {
      testArr: [1, 2, 3, 4],
      container: null,
      selectedSearchFilter: "AllSearchPage",
      pos: { top: 0, left: 0, x: 0, y: 0 },
      searchText: "",
    };
  },
  provide() {
    return {
      testArr: this.testArr,
    };
  },
  components: {
    BaseInput,
    BaseButton,
    AllSearchPage,
    ArtistSearchPage,
    AlbumSearchPage,
    UserSearchPage,
    SongSearchPage,
  },
  methods: {
    onSubmit() {
      console.log("searchText", this.searchText);
    },
    changeComponent(componentName: string) {
      this.selectedSearchFilter = componentName;
    },
    searchFilterScroll(e: WheelEvent) {
      e.preventDefault();
      if (e.deltaY > 0) {
        this.container.scrollLeft += 10;
      } else {
        this.container.scrollLeft -= 10;
      }
    },
    mouseDown(e: MouseEvent) {
      this.container.style.cursor = "grabbing";
      this.container.style.userSelect = "none";
      this.container.addEventListener("mousemove", this.mouseMove);
      this.pos = {
        left: this.container.offsetLeft,
        top: this.container.offsetTop,
        x: e.clientX,
        y: e.clientY,
      };
    },
    mouseUp() {
      this.container.style.cursor = "grab";
      this.container.style.removeProperty("user-select");
      this.container.removeEventListener("mousemove", this.mouseMove);
    },
    mouseMove(e: MouseEvent) {
      // How far the mouse has been moved
      const dx = e.clientX - this.pos.x;
      const dy = e.clientY - this.pos.y;
      // Scroll the element
      this.container.scrollTop = this.pos.top - dy;
      this.container.scrollLeft = this.pos.left - dx;
    },
  },
  mounted() {
    this.container = this.$refs.container;
  },
});
</script>

<style lang="scss" scoped>
.search-form {
  margin: 0 auto;
  width: 80%;
  padding: 0 20px;
  form > * {
    margin: 10px 0;
  }
}
.search-result {
  margin: 0 auto;
  width: 90%;
  &__tag {
    display: flex;
    justify-content: space-between;
    margin: 20px auto;
    width: 80%;
    overflow-y: auto;
    cursor: grab;
    & > * {
      width: 150px;
    }
  }
  &__container {
    margin: 20px 0;
  }
}
</style>
