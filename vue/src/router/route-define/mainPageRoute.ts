import type { RouteLocationNormalized } from "vue-router";

export const mainPageRoute = [
  {
    path: "/home",
    name: "mainPage",
    component: () => import("@/views/HomeView/MainPageView.vue"),
  },
  {
    path: "/notification",
    name: "notificationPage",
    component: () => import("@/views/HomeView/NotificationView.vue"),
  },
  {
    path: "/search",
    name: "searchPage",
    component: () => import("@/views/HomeView/SearchPageView.vue"),
  },
  {
    path: "/library",
    name: "libraryPage",
    component: () => import("@/views/HomeView/LibraryView.vue"),
  },
  {
    path: "/album/:id",
    name: "albumViewPage",
    component: () => import("@/views/HomeView/AlbumView.vue"),
  },
  {
    path: "/playlist/:id",
    name: "playlistViewPage",
    component: () => import("@/views/HomeView/PlaylistDetails.vue"),
  },
  {
    path: "/playlist/create",
    name: "playlistCreatePage",
    component: () => import("@/views/HomeView/PlaylistCreateView.vue"),
  },
  {
    path: "/collection",
    name: "collectionPage",
    component: () => import("@/views/HomeView/CollectionView.vue"),
  },
  {
    path: "upload",
    name: "uploadPage",
    component: () => import("@/views/HomeView/UploadView.vue"),
  },
  {
    path: "playing",
    name: "playingPage",
    component: () => import("@/views/HomeView/PlayingSongView.vue"),
  },
  {
    path: "user/:id",
    name: "profilePage",
    component: () => import("@/views/HomeView/ProfileView.vue"),
  },
  {
    path: "genres/:id",
    name: "genrePage",
    component: () => import("@/views/HomeView/GenreView.vue"),
  },
  {
    path: "artist/:id",
    name: "artistPage",
    redirect: (to: RouteLocationNormalized) => ({
      name: "artistOverviewPage",
      params: { id: to.params.id },
    }),
    component: () =>
      import("@/views/HomeView/ArtistDetailsView/ArtistDetailsView.vue"),
    children: [
      {
        path: "overview",
        name: "artistOverviewPage",
        component: () =>
          import("@/views/HomeView/ArtistDetailsView/ArtistDetailsMain.vue"),
      },
      {
        path: "albums",
        name: "artistAlbumsPage",
        component: () =>
          import("@/views/HomeView/ArtistDetailsView/ArtistDetailsAlbum.vue"),
      },
    ],
  },
];
