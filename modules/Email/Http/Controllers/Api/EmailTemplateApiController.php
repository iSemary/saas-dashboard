<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Email\Entities\EmailTemplate;
use Modules\Email\Services\EmailService;

class EmailTemplateApiController extends ApiController
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');

            $query = EmailTemplate::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%");
                });
            }

            $templates = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'data' => [
                    'data' => $templates->items(),
                    'current_page' => $templates->currentPage(),
                    'last_page' => $templates->lastPage(),
                    'per_page' => $templates->perPage(),
                    'total' => $templates->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve email templates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'body' => 'required|string',
                'variables' => 'nullable|array',
                'status' => 'nullable|in:active,inactive',
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            $template = EmailTemplate::create($validated);

            return response()->json([
                'data' => $template,
                'message' => 'Email template created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create email template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $template = EmailTemplate::findOrFail($id);
            return response()->json(['data' => $template]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Email template not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $template = EmailTemplate::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'subject' => 'sometimes|required|string|max:255',
                'body' => 'sometimes|required|string',
                'variables' => 'nullable|array',
                'status' => 'nullable|in:active,inactive',
            ]);

            $template->update($validated);

            return response()->json([
                'data' => $template,
                'message' => 'Email template updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update email template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $template = EmailTemplate::findOrFail($id);
            $template->delete();

            return response()->json([
                'message' => 'Email template deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete email template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendTest(Request $request, $id)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $template = EmailTemplate::findOrFail($id);
            
            // Send test email using the email service
            $this->emailService->sendTemplate($template->id, $request->email, []);

            return response()->json([
                'message' => 'Test email sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send test email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
