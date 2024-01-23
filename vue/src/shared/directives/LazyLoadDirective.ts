import { environment } from "@/environment/environment";
export default {
  mounted: (el: HTMLImageElement) => {
    function loadImage() {
      if (el) {
        el.addEventListener("load", () => {
          el.classList.add("loaded");
        });

        el.addEventListener("error", () => {
          console.log("Image failed to load:", el.dataset.url);
          el.src = `${environment.default}/no_image.jpg`;
        });

        el.src = el.dataset.url as string;
      }
    }

    function handleIntersect(
      entries: IntersectionObserverEntry[],
      observer: IntersectionObserver
    ) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          loadImage();
          observer.unobserve(el);
        }
      });
    }

    function createObserver() {
      const options: IntersectionObserverInit = {
        root: null,
        threshold: 0,
      };
      const observer = new IntersectionObserver(handleIntersect, options);
      observer.observe(el);
    }

    if (window.IntersectionObserver) {
      createObserver();
    } else {
      loadImage();
    }
  },
};
