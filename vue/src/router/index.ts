import store from "@/store";
import { createRouter, createWebHistory } from "vue-router";

import HomeView from "../views/HomeView/HomeView.vue";
import AuthView from "../views/AuthView/AuthView.vue";
import LogInView from "../views/AuthView/LogInView.vue";
import SignUpView from "../views/AuthView/SignUpView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
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
          name: "playlistPage",
          component: () => import("../views/HomeView/PlaylistDetails.vue"),
        },
        {
          path: "/playlist/create",
          name: "playlistCreatePage",
          component: () => import("../views/HomeView/PlaylistCreate.vue"),
        },
        {
          path: "/collection",
          name: "CollectionPage",
          component: () => import("../views/HomeView/CollectionView.vue"),
        },
        {
          path: "upload",
          name: "uploadPage",
          component: () => import("../views/HomeView/UploadView.vue"),
        },
        {
          path: "user/:id",
          name: "profilePage",
          component: () => import("../views/HomeView/ProfileView.vue"),
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
