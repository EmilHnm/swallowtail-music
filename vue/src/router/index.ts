import store from "@/store";
import { createRouter, createWebHistory } from "vue-router";
import { mainPageRoute } from "./route-define/mainPageRoute";
import AccountView from "../views/AccountView/AccountView.vue";
import HomeView from "../views/HomeView/HomeView.vue";
import AuthView from "../views/AuthView/AuthView.vue";

import NotFoundView from "../views/NotFoundView.vue";
import { authPageRoute } from "./route-define/authPageRoute";
import { accountPageRoute } from "./route-define/accountPageRoute";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
      redirect: { name: "mainPage" },
      meta: {
        requiresAuth: true,
      },
      children: [...mainPageRoute],
    },

    {
      path: "/auth",
      redirect: { name: "login" },
      name: "auth",
      component: AuthView,
      meta: { isGuest: true },
      children: [...authPageRoute],
    },

    {
      path: "/account",
      name: "account",
      component: AccountView,
      redirect: { name: "accountOverview" },
      meta: {
        requiresAuth: true,
      },
      children: [...accountPageRoute],
    },
    {
      path: "/verify-email",
      name: "verifyEmail",
      meta: {
        requiresAuth: true,
      },
      component: () => import("../views/VerifyEmailView.vue"),
    },
    {
      path: "/test",
      meta: {
        requiresAuth: true,
      },
      component: () => import("../views/test.vue"),
    },
    {
      path: "/*",
      component: NotFoundView,
      name: "notFound",
    },
  ],
});

router.beforeEach((to, from, next) => {
  if (localStorage.getItem("TOKEN")) {
    if (!store.getters["auth/userToken"]) {
      store.commit("auth/checkToken");
      store
        .dispatch("auth/checkAuth")
        .then((res) => {
          if (res) {
            return res.json();
          }
        })
        .then((res) => {
          if (res.message === "Unauthenticated.") {
            store.commit("auth/clearUser");
            next({ name: "auth" });
          } else {
            const data = {
              user: {
                user_id: res.user_id,
                name: res.name,
                email: res.email,
                profile_photo_url: res.profile_photo_url,
                role: res.role,
                email_verified_at: res.email_verified_at,
              },
            };
            store.commit("auth/setUser", data);
            if (to.meta.requiresAuth) {
              next();
            } else {
              next({ name: "home" });
            }
          }
        });
    } else {
      if (to.meta.requiresAuth) {
        next();
      } else {
        //next();
        next({ name: "home" });
      }
    }
  } else {
    if (to.meta.isGuest) next();
    else next({ name: "auth" });
  }
});
export default router;
