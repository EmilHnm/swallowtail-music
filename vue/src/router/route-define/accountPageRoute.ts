export const accountPageRoute = [
  {
    path: "overview",
    name: "accountOverview",
    component: () => import("@/views/AccountView/AccountOverviewView.vue"),
  },
  {
    path: "profile",
    name: "accountProfile",
    component: () => import("@/views/AccountView/AccountProfileView.vue"),
  },
  {
    path: "security",
    name: "accountSecurity",
    component: () => import("@/views/AccountView/AccountSecurityView.vue"),
  },
  {
    path: "avatar",
    name: "accountAvatar",
    component: () => import("@/views/AccountView/AccountAvatarView.vue"),
  },
  {
    path: "upload",
    name: "accountUpload",
    redirect: { name: "accountUploadSong" },
    component: () =>
      import(
        "@/views/AccountView/AccountUpload/AccountUploadManagementView.vue"
      ),
    children: [
      {
        path: "album",
        name: "accountUploadAlbum",
        component: () =>
          import(
            "@/views/AccountView/AccountUpload/AccountUploadAlbum/AccountUploadAlbum.vue"
          ),
      },
      {
        path: "album/edit",
        name: "accountUploadAlbumEdit",
        component: () =>
          import(
            "@/views/AccountView/AccountUpload/AccountUploadAlbum/AccountUploadAlbumEdit.vue"
          ),
      },
      {
        path: "song",
        name: "accountUploadSong",
        component: () =>
          import(
            "@/views/AccountView/AccountUpload/AccountUploadSong/AccountUploadSong.vue"
          ),
      },
      {
        path: "song/edit",
        name: "accountUploadSongEdit",
        component: () =>
          import(
            "@/views/AccountView/AccountUpload/AccountUploadSong/AccountUploadSongEdit.vue"
          ),
      },
    ],
  },
  {
    path: "request",
    name: "accountRequest",
    redirect: { name: "accountRequestManagement" },
    children: [
      {
        path: "",
        name: "accountRequestManagement",
        component: () =>
          import("@/views/AccountView/AccountRequest/AccountReqest.vue"),
      },
    ],
  },
];
