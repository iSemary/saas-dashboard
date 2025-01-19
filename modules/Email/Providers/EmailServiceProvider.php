<?php

namespace Modules\Email\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Email\Repositories\EmailTemplateInterface;
use Modules\Email\Repositories\EmailTemplateRepository;

class EmailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(EmailTemplateInterface::class, EmailTemplateRepository::class);
    }

    public function boot() {}
}
