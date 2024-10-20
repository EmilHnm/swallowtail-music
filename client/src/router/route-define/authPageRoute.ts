import LogInView from "@/views/AuthView/LogInView.vue";
import SignUpView from "@/views/AuthView/SignUpView.vue";

export const authPageRoute = [
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
  {
    path: "/forgot-password",
    name: "forgotPassword",
    component: () => import("@/views/AuthView/ForgotPasswordView.vue"),
  },
  {
    path: "/recover-password",
    name: "recoverPassword",
    component: () => import("@/views/AuthView/RecoverPasswordView.vue"),
  },
];
