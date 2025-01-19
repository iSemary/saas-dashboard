<?php

namespace Modules\Email\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Email\Repositories\EmailTemplateInterface;
use Modules\Email\Repositories\EmailTemplateRepository;
use Modules\Email\Repositories\EmailRecipientInterface;
use Modules\Email\Repositories\EmailRecipientRepository;
use Modules\Email\Repositories\EmailSubscriberInterface;
use Modules\Email\Repositories\EmailSubscriberRepository;
use Modules\Email\Repositories\EmailCampaignInterface;
use Modules\Email\Repositories\EmailCampaignRepository;

class EmailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(EmailTemplateInterface::class, EmailTemplateRepository::class);
        $this->app->bind(EmailRecipientInterface::class, EmailRecipientRepository::class);
        $this->app->bind(EmailSubscriberInterface::class, EmailSubscriberRepository::class);
        $this->app->bind(EmailCampaignInterface::class, EmailCampaignRepository::class);
    }

    public function boot() {}
}
