<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        's3_gdriver' => [
            'driver' => 's3',
            'key' => env('GDRIVER_ACCESS_KEY_ID'),
            'secret' => env('GDRIVER_SECRET_ACCESS_KEY'),
            'region' => env('GDRIVER_DEFAULT_REGION'),
            'bucket' => env('GDRIVER_BUCKET'),
            'url' => env('GDRIVER_URL'),
            'endpoint' => env('GDRIVER_ENDPOINT'),
            'use_path_style_endpoint' => env('GDRIVER_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        's3_dc' => [
            'driver' => 's3',
            'key' => env('DC_ACCESS_KEY_ID'),
            'secret' => env('DC_SECRET_ACCESS_KEY'),
            'region' => env('DC_DEFAULT_REGION'),
            'bucket' => env('DC_BUCKET'),
            'url' => env('DC_URL'),
            'endpoint' => env('DC_ENDPOINT'),
            'use_path_style_endpoint' => env('DC_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'chunk_file' => [
            'driver' => 'local',
            'root' => storage_path('app/chunks'),
            'url' => env('APP_URL') . '/storage/chunks',
            'visibility' => 'private',
            'throw' => false,
        ],

        'raws_audio' => [
            'driver' => 'local',
            'root' => storage_path('app/raws'),
            'url' => env('APP_URL') . '/storage/raws',
            'visibility' => 'private',
            'throw' => false,
        ],

        'final_audio' => [
            'driver' => 'local',
            'root' => public_path('storage/upload/song_src'),
            'url' => env('APP_URL') . '/storage/upload/song_src',
            'visibility' => 'public',
            'throw' => false,
        ],

        'final_cover' => [
            'driver' => 'local',
            'root' => public_path('storage/upload/album_cover'),
            'url' => env('APP_URL') . '/storage/upload/album_cover',
            'visibility' => 'public',
            'throw' => false,
        ],

        'final_artist_image' => [
            'driver' => 'local',
            'root' => public_path('storage/upload/artist_image'),
            'url' => env('APP_URL') . '/storage/upload/artist_image',
            'visibility' => 'public',
            'throw' => false,
        ],

        'profile_image' => [
            'driver' => 'local',
            'root' => public_path('storage/upload/profile_image'),
            'url' => env('APP_URL') . '/storage/upload/profile_image',
            'visibility' => 'public',
            'throw' => false,
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
