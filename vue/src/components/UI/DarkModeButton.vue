<template>
  <div class="switch">
    <input
      @change="toggleTheme"
      id="checkbox"
      type="checkbox"
      class="switch-checkbox"
      :checked="userTheme === 'light-theme'"
    />
    <label for="checkbox" class="switch-label">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        width="24"
        height="24"
        preserveAspectRatio="xMidYMid meet"
        style="width: 100%; height: 100%; cursor: pointer"
        class="switch-icon"
      >
        <defs>
          <clipPath id="__lottie_element_128">
            <rect width="24" height="24" x="0" y="0"></rect>
          </clipPath>
        </defs>
        <g clip-path="url(#__lottie_element_128)">
          <g opacity="1" style="display: block" class="inner">
            <g opacity="1" transform="matrix(1,0,0,1,0,0)">
              <path
                style="cursor: pointer"
                fill-opacity="1"
                d=" M0,-4 C-2.2100000381469727,-4 -4,-2.2100000381469727 -4,0 C-4,2.2100000381469727 -2.2100000381469727,4 0,4 C2.2100000381469727,4 4,2.2100000381469727 4,0 C4,-2.2100000381469727 2.2100000381469727,-4 0,-4z"
              ></path>
            </g>
          </g>
          <g
            transform="matrix(1,0,0,1,12,12)"
            opacity="1"
            style="display: block"
            class="outer"
          >
            <g opacity="1" transform="matrix(1,0,0,1,0,0)">
              <path
                style="cursor: pointer"
                fill-opacity="1"
                d=" M0,6 C-3.309999942779541,6 -6,3.309999942779541 -6,0 C-6,-3.309999942779541 -3.309999942779541,-6 0,-6 C3.309999942779541,-6 6,-3.309999942779541 6,0 C6,3.309999942779541 3.309999942779541,6 0,6z M8,-3.309999942779541 C8,-3.309999942779541 8,-8 8,-8 C8,-8 3.309999942779541,-8 3.309999942779541,-8 C3.309999942779541,-8 0,-11.3100004196167 0,-11.3100004196167 C0,-11.3100004196167 -3.309999942779541,-8 -3.309999942779541,-8 C-3.309999942779541,-8 -8,-8 -8,-8 C-8,-8 -8,-3.309999942779541 -8,-3.309999942779541 C-8,-3.309999942779541 -11.3100004196167,0 -11.3100004196167,0 C-11.3100004196167,0 -8,3.309999942779541 -8,3.309999942779541 C-8,3.309999942779541 -8,8 -8,8 C-8,8 -3.309999942779541,8 -3.309999942779541,8 C-3.309999942779541,8 0,11.3100004196167 0,11.3100004196167 C0,11.3100004196167 3.309999942779541,8 3.309999942779541,8 C3.309999942779541,8 8,8 8,8 C8,8 8,3.309999942779541 8,3.309999942779541 C8,3.309999942779541 11.3100004196167,0 11.3100004196167,0 C11.3100004196167,0 8,-3.309999942779541 8,-3.309999942779541z"
              ></path>
            </g>
          </g>
        </g>
      </svg>
    </label>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "DarkModeButton",
  data() {
    return {
      userTheme: "light-theme",
    };
  },
  methods: {
    toggleTheme() {
      const activeTheme = localStorage.getItem("user-theme");
      if (activeTheme === "light-theme") {
        this.setTheme("dark-theme");
      } else {
        this.setTheme("light-theme");
      }
    },

    getTheme() {
      return localStorage.getItem("user-theme");
    },

    setTheme(theme: string) {
      localStorage.setItem("user-theme", theme);
      this.userTheme = theme;
      document.documentElement.className = theme;
    },

    getMediaPreference() {
      const hasDarkPreference = window.matchMedia(
        "(prefers-color-scheme: dark)"
      ).matches;
      if (hasDarkPreference) {
        return "dark-theme";
      } else {
        return "light-theme";
      }
    },
  },
  mounted() {
    const initUserTheme = this.getTheme() || this.getMediaPreference();
    this.setTheme(initUserTheme);
  },
});
</script>

<style scoped>
.switch {
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.switch-checkbox {
  display: none;
}
#checkbox:checked + .switch-label .switch-icon {
  filter: invert(30%) sepia(61%) saturate(590%) hue-rotate(233deg)
    brightness(90%) contrast(85%) drop-shadow(0px 0px 2px var(--color-primary));
}
.switch-icon {
  filter: invert(61%) sepia(92%) saturate(634%) hue-rotate(161deg)
    brightness(90%) contrast(91%) drop-shadow(0px 0px 2px var(--color-primary));
}
.inner {
  transition: 0.3s;
  transform: matrix(1, 0, 0, 1, 9, 12);
}
#checkbox:checked + .switch-label .inner {
  transform: matrix(1, 0, 0, 1, 12, 12);
}
.outer {
  transition: 0.3s;
  transform: matrix(1, 0, 0, 1, 12, 12);
}
#checkbox:checked + .switch-label .outer {
  transform: matrix(1, 0, 0, 1, 12, 12) rotate(180deg);
}
</style>
