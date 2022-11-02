import { createStore } from "vuex";
import { accountModule } from "./module/accountModule";
import { albumModule } from "./module/albumModule";
import { authModule } from "./module/authModule";
import { playlistModule } from "./module/playlistModule";
import { songModule } from "./module/songModule";
import { artistModule } from "./module/artistModule";
import { testModule } from "./module/testModule";
export default createStore({
  modules: {
    auth: authModule,
    song: songModule,
    album: albumModule,
    playlist: playlistModule,
    account: accountModule,
    artist: artistModule,
    test: testModule,
  },
});
