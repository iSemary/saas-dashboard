<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\BaseMail;
use Modules\Email\Entities\EmailLog;

class SendBaseEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send the email
        {
            try {
                // TODO Setup the from email here 

                Mail::to($this->data['email'])->send(new BaseMail($this->data));

                EmailLog::where("id", $this->data['log_id'])->update(['status' => 'active']);
            } catch (\Exception $e) {
                EmailLog::where("id", $this->data['log_id'])->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                \Log::error('Email sending failed: ' . $e->getMessage());
            }
        }
    }
}
