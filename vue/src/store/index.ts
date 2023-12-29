import { userModule } from "./module/userModule";
import { createStore, type GetterTree } from "vuex";
import { accountModule } from "./module/accountModule";
import { albumModule } from "./module/albumModule";
import { authModule } from "./module/authModule";
import { playlistModule } from "./module/playlistModule";
import { songModule } from "./module/songModule";
import { artistModule } from "./module/artistModule";
import { adminModule } from "./module/adminModule";
import { testModule } from "./module/testModule";
import { notificationModule } from "./module/notificationModule";
import { cacheModule } from "./module/cacheModule";
import { genreModule } from "./module/genreModule";
import uploadQueueModule from "./module/uploadQueueModule";
export default createStore({
  modules: {
    auth: authModule,
    song: songModule,
    album: albumModule,
    playlist: playlistModule as any,
    account: accountModule,
    artist: artistModule,
    user: userModule,
    admin: adminModule,
    test: testModule,
    notification: notificationModule,
    cache: cacheModule,
    genre: genreModule as any,
    uploadQueue: uploadQueueModule as any,
  },
});
