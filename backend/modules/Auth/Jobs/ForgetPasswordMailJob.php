<?php

namespace Modules\Auth\Jobs;

use Modules\Auth\Mail\ForgetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Tenant\Entities\Tenant;

class ForgetPasswordMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $user;
    private $token;
    protected $tenantId;
    protected $url;
    /**
     * Create a new job instance.
     */
    public function __construct($user, $token, $tenantId)
    {
        $this->user = $user;
        $this->token = $token;
        $this->tenantId = $tenantId;
        $tenant = Tenant::find($this->tenantId);
        $tenant->makeCurrent();

        $this->url = route("password.reset.show", ['token' => $token]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'token' => $this->token,
            'url' => $this->url
        ];
        Mail::to($this->user['email'])->send(new ForgetPasswordMail($data));
    }
}
