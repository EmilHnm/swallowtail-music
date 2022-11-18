export class Timer {
  timerId: number | null = null;
  start: number;
  remaining: number;
  callback: TimerHandler;
  constructor(callback: TimerHandler, delay: number) {
    this.start = Date.now();
    this.remaining = delay;
    this.callback = callback;
  }

  pause() {
    window.clearTimeout(this.timerId as number);
    this.timerId = null;
    this.remaining -= Date.now() - this.start;
  }

  resume() {
    if (!this.timerId) {
      this.timerId = window.setTimeout(this.callback, this.remaining);
    } else {
      return;
    }
  }
}
