import { createStore } from "vuex";
import { albumModule } from "./module/albumModule";
import { authModule } from "./module/authModule";
import { playlistModule } from "./module/playlistModule";
import { songModule } from "./module/songModule";
import { testModule } from "./module/testModule";
export default createStore({
  modules: {
    auth: authModule,
    song: songModule,
    album: albumModule,
    playlist: playlistModule,
    test: testModule,
  },
});
