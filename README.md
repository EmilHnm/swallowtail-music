## Swallowtail Music Player

Just a simple music player for the terminal. It's not very good, but it's mine. I build it for only learning purposes.

### Dependencies

-   [FFmpeg](https://ffmpeg.org/)
-   [vorbis](https://xiph.org/vorbis/)
-   [Minio](https://min.io/)
-   [Redis](https://redis.io/)
-   [Laravel](https://laravel.com/)
-   [Vue](https://vuejs.org/)
-   [SCSS](https://sass-lang.com/)
-   [Typescript](https://www.typescriptlang.org/)

### Installation

```bash
git clone https://github.com/EmilRailgun/swallowtail-music.git
```

-   For first setup

    ```bash
    cd swallowtail-music
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
    ```

-   To run server with default port 8000

    ```bash
    cd swallowtail-music
    php artisan serve
    ```

-   To run client

    ```bash
    cd swallowtail-music
    cd vue
    npm install
    npm run dev
    ```

## Feature

**Admin**

-   [x] Admin Theme
-   [x] Login, Logout
-   [x] User management and authentication
-   [x] CRUD Song/Album/Gernes/Artist
-   [x] Filter Database
-   [x] Display Logs
-   [ ] Display Report
-   [x] Dashboard

**Client**

-   Authentication

    -   [x] Login, Logout
    -   [x] Register
    -   [x] Forgot Password
    -   [x] Reset Password
    -   [x] Verify Email

-   Song

    -   [x] Play Song
    -   [x] Add to Playlist
    -   [x] Add to Favorite
    -   [x] Search Song
    -   [x] Filter Song
    -   [x] Display Song
    -   [x] Upload Song
    -   [x] Edit Song

-   Album

    -   [x] Display Album
    -   [x] Filter Album
    -   [x] Search Album
    -   [x] Upload Album
    -   [x] Edit Album
    -   [x] Delete Album
    -   [x] Play Album

-   Playlist

    -   [x] Display Playlist
    -   [x] Filter Playlist
    -   [x] Search Playlist
    -   [x] Create Playlist
    -   [x] Edit Playlist
    -   [x] Delete Playlist
    -   [x] Play Playlist

-   Other

    -   [x] Notification
    -   [x] Change Profile Picture
    -   [x] Edit Profile

### V1.5 (Storage)

-   [x] Create crawler
-   [x] Change to chunk upload
-   [x] Change music storage from public storage to minio storage
-   [x] Stream file instead of download

### V2

-   [x] Change theme design
-   [x] Add Lazyload for Image
-   [ ] Change admin dashboard
-   [ ] Cross Authenticate
-   [x] Create API for backup and sync
