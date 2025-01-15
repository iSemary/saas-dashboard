<?php

namespace Modules\FileManager\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\FileManager\Repositories\FileInterface;
use Modules\FileManager\Repositories\FileRepository;

class FileManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(FileInterface::class, FileRepository::class);
    }

    public function boot() {}
}
