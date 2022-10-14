import store from "@/store";
import { createRouter, createWebHistory } from "vue-router";

import HomeView from "../views/HomeView/HomeView.vue";
import AuthView from "../views/AuthView/AuthView.vue";
import LogInView from "../views/AuthView/LogInView.vue";
import SignUpView from "../views/AuthView/SignUpView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to: number, from: number, savedPosittion) {
    if (savedPosittion) {
      return savedPosittion;
    }
    return { left: 0, top: 0 };
  },
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
      redirect: "home",
      meta: { requiresAuth: true },
      children: [
        {
          path: "/home",
          name: "mainPage",
          component: () => import("../views/HomeView/MainPageView.vue"),
        },
        {
          path: "/search",
          name: "searchPage",
          component: () => import("../views/HomeView/SearchPageView.vue"),
        },
        {
          path: "/library",
          name: "libraryPage",
          component: () => import("../views/HomeView/LibraryView.vue"),
        },
        {
          path: "/playlist/:id",
          name: "playlistViewPage",
          component: () => import("../views/HomeView/PlaylistDetails.vue"),
        },
        {
          path: "/playlist/create",
          name: "playlistCreatePage",
          component: () => import("../views/HomeView/PlaylistCreateView.vue"),
        },
        {
          path: "/collection",
          name: "collectionPage",
          component: () => import("../views/HomeView/CollectionView.vue"),
        },
        {
          path: "upload",
          name: "uploadPage",
          component: () => import("../views/HomeView/UploadView.vue"),
        },
        {
          path: "playing",
          name: "playingPage",
          component: () => import("../views/HomeView/PlayingSongView.vue"),
        },
        {
          path: "user/:id",
          name: "profilePage",
          component: () => import("../views/HomeView/ProfileView.vue"),
        },
        {
          path: "artist/:id",
          name: "artistPage",
          redirect: "artist/:id/overview",
          component: () =>
            import("../views/HomeView/ArtistDetailsView/ArtistDetailsView.vue"),
          children: [
            {
              path: "overview",
              name: "artistOverviewPage",
              component: () =>
                import(
                  "../views/HomeView/ArtistDetailsView/ArtistDetailsMain.vue"
                ),
            },
            {
              path: "albums",
              name: "artistAlbumsPage",
              component: () =>
                import(
                  "../views/HomeView/ArtistDetailsView/ArtistDetailsAlbum.vue"
                ),
            },
          ],
        },
      ],
    },

    {
      path: "/auth",
      redirect: "/login",
      name: "auth",
      component: AuthView,
      meta: { isGuest: true },
      children: [
        {
          path: "/login",
          name: "login",
          component: LogInView,
        },
        {
          path: "/signup",
          name: "signup",
          component: SignUpView,
        },
      ],
    },
  ],
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.user.token) {
    next({ name: "auth" });
  } else if (store.state.user.token && to.meta.isGuest) {
    next({ name: "home" });
  } else {
    next();
  }
});
export default router;
