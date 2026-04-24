<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Email\Services\EmailService;

class ComposeEmailApiController extends Controller
{
    use ApiResponseEnvelope;

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|array',
            'to.*' => 'email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'template_id' => 'nullable|integer',
            'cc' => 'nullable|array',
            'bcc' => 'nullable|array',
        ]);

        try {
            $result = $this->emailService->send($validated);
            return $this->apiSuccess($result, 'Email sent successfully');
        } catch (\Exception $e) {
            return $this->apiError('Failed to send email: ' . $e->getMessage(), 500);
        }
    }
}
