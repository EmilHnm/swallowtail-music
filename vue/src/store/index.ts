import { userModule } from "./module/userModule";
import { createStore } from "vuex";
import { accountModule } from "./module/accountModule";
import { albumModule } from "./module/albumModule";
import { authModule } from "./module/authModule";
import { playlistModule } from "./module/playlistModule";
import { songModule } from "./module/songModule";
import { artistModule } from "./module/artistModule";
import { testModule } from "./module/testModule";
import { notificationModule } from "./module/notificationModule";
import { cacheModule } from "./module/cacheModule";
import { genreModule } from "./module/genreModule";
import { uploadQueueModule } from "./module/uploadQueueModule";
import requestModule from "./module/requestModule";
import { queueModule } from "@/store/module/queueModule";
export default createStore({
  modules: {
    auth: authModule,
    song: songModule,
    album: albumModule,
    account: accountModule,
    artist: artistModule,
    user: userModule,
    test: testModule,
    notification: notificationModule,
    cache: cacheModule,
    playlist: playlistModule as any,
    genre: genreModule as any,
    uploadQueue: uploadQueueModule as any,
    request: requestModule as any,
    queue: queueModule as any,
  },
});
