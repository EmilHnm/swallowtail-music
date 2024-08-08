<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use spresnac\databasehelper\BackupDatabase;
use spresnac\databasehelper\DropTables;
use spresnac\databasehelper\RestoreDatabase;

class PublishBackupServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        $this->commands([
            BackupDatabase::class,
            RestoreDatabase::class,
            DropTables::class,
        ]);
    }
}
