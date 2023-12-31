import { createApp } from "vue";
import { createMetaManager } from "vue-meta";

import App from "./App.vue";
import router from "./router";
import store from "./store";
import "./assets/main.css";

import BaseCard from "./components/UI/BaseCard.vue";
import BaseButton from "./components/UI/BaseButton.vue";
import DarkModeButton from "./components/UI/DarkModeButton.vue";
import BaseInput from "./components/UI/BaseInput.vue";
import BaseListItem from "./components/UI/BaseListItem.vue";
import BaseDialog from "./components/UI/BaseDialog.vue";
import IconLogo from "./components/icons/IconLogo.vue";
import LazyLoadDirective from "./shared/LazyLoadDirective";
import ClickOutsideDirective from "./shared/ClickOutsideDirective";
import type Echo from "laravel-echo";
import SwiperCore from "swiper";
import { Navigation } from "swiper/modules";

declare global {
  interface ObjectConstructor {
    groupBy<T>(
      items: Iterable<T>,
      callbackfn: (value: T, index: number) => string
    ): Record<string, T[]>;
  }
  interface Window {
    io: typeof io; // 👈️ turn off type checking
    Echo: Echo; // 👈️ turn off type checking
  }
}

const app = createApp(App);

const metaManager = createMetaManager();

app.component("base-card", BaseCard);
app.component("base-button", BaseButton);
app.component("darkmode-button", DarkModeButton);
app.component("base-input", BaseInput);
app.component("base-list-item", BaseListItem);
app.component("base-dialog", BaseDialog);
app.component("icon-logo", IconLogo);
app.use(router);
app.use(store as any);
app.use(metaManager);

app.directive("lazyload", LazyLoadDirective);
app.directive("click-outside", ClickOutsideDirective);

SwiperCore.use([Navigation]);

app.mount("#app");
