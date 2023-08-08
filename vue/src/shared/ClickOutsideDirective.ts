export default {
  mounted(
    el: HTMLElement & { clickOutsideEvent: (event: Event) => void },
    binding: { value: (event: Event, el: HTMLElement) => void }
  ) {
    el.clickOutsideEvent = function (event: Event) {
      if (!(el === event.target || el.contains(event.target as Node))) {
        event.preventDefault();
        binding.value(event, el);
      }
    };
    document.body.addEventListener("click", el.clickOutsideEvent);
  },
  unmounted(el: HTMLElement & { clickOutsideEvent: (event: Event) => void }) {
    document.body.removeEventListener("click", el.clickOutsideEvent);
  },
};
