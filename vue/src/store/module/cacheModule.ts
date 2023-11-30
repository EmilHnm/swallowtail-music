import { environment } from "@/environment/environment";
import type { CacheEntry } from "@/model/mixin/CacheEntry";

export const cacheModule = {
  namespaced: true,
  state() {
    return {
      cache: new Map<string, CacheEntry>(),
    };
  },
  getters: {
    data: (state: any): any => {
      return (key: string) => {
        const cached = state.cache.get(key);
        if (!cached) {
          return undefined;
        }

        const hasExpired = new Date().getTime() >= cached.expiresOn;
        if (hasExpired) {
          state.cache.delete(key);
          return undefined;
        }

        return cached.value;
      };
    },
  },
  mutations: {
    set(state: any, data: { key: string; value: any }): void {
      state.cache.set(data.key, {
        value: data.value,
        expiresOn: new Date().getTime() + environment.cache_timeout,
      });
    },
  },
};
