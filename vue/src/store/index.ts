import { userModule } from "./module/userModule";
import { createStore } from "vuex";
import { accountModule } from "./module/accountModule";
import { albumModule } from "./module/albumModule";
import { authModule } from "./module/authModule";
import { playlistModule } from "./module/playlistModule";
import { songModule } from "./module/songModule";
import { artistModule } from "./module/artistModule";
import { adminModule } from "./module/adminModule";
import { testModule } from "./module/testModule";
import { notificationModule } from "./module/notificationModule";
export default createStore({
  modules: {
    auth: authModule,
    song: songModule,
    album: albumModule,
    playlist: playlistModule,
    account: accountModule,
    artist: artistModule,
    user: userModule,
    admin: adminModule,
    test: testModule,
    notification: notificationModule,
  },
});
