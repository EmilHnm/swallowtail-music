export const environment = {
  site_name: import.meta.env.VITE_APP_NAME,
  site_url: import.meta.env.VITE_APP_URL,
  api: import.meta.env.VITE_API_URL,

  default: import.meta.env.VITE_DEFAULT_FILE,
  album_cover: import.meta.env.VITE_ALBUM_COVER,
  playlist_cover: import.meta.env.VITE_PLAYLIST_COVER,
  profile_image: import.meta.env.VITE_PROFILE_IMAGE,
  artist_image: import.meta.env.VITE_ARTIST_IMAGE,

  // broadcast
  socket_url: import.meta.env.VITE_SOCKET_URL,
  socket_port: import.meta.env.VITE_SOCKET_PORT,

  //cache
  cache_timeout: (import.meta.env.VITE_CACHE_TIMEOUT ?? 10) * 60 * 1000,
};
