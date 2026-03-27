<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Email\Entities\EmailCredential;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailCredentialApiController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $credentials = EmailCredential::orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $credentials]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve SMTP configurations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'from_address' => 'required|email',
                'from_name' => 'required|string|max:255',
                'mailer' => 'required|in:smtp,ses,mailgun,postmark',
                'host' => 'required|string|max:255',
                'port' => 'required|integer|min:1|max:65535',
                'username' => 'nullable|string|max:255',
                'password' => 'nullable|string',
                'encryption' => 'nullable|in:tls,ssl',
                'status' => 'nullable|in:active,inactive',
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            $credential = EmailCredential::create($validated);

            return response()->json([
                'data' => $credential,
                'message' => 'SMTP configuration created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create SMTP configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $credential = EmailCredential::findOrFail($id);
            // Don't expose password in response
            $credential->password = $credential->password ? '***' : null;
            return response()->json(['data' => $credential]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'SMTP configuration not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $credential = EmailCredential::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'from_address' => 'sometimes|required|email',
                'from_name' => 'sometimes|required|string|max:255',
                'mailer' => 'sometimes|required|in:smtp,ses,mailgun,postmark',
                'host' => 'sometimes|required|string|max:255',
                'port' => 'sometimes|required|integer|min:1|max:65535',
                'username' => 'nullable|string|max:255',
                'password' => 'nullable|string',
                'encryption' => 'nullable|in:tls,ssl',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Only update password if provided
            if (!isset($validated['password']) || $validated['password'] === '***') {
                unset($validated['password']);
            }

            $credential->update($validated);

            return response()->json([
                'data' => $credential,
                'message' => 'SMTP configuration updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update SMTP configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $credential = EmailCredential::findOrFail($id);
            $credential->delete();

            return response()->json([
                'message' => 'SMTP configuration deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete SMTP configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testConnection(Request $request, $id)
    {
        try {
            $request->validate([
                'test_email' => 'required|email',
            ]);

            $credential = EmailCredential::findOrFail($id);

            // Configure mail settings temporarily
            Config::set('mail.mailers.smtp.host', $credential->host);
            Config::set('mail.mailers.smtp.port', $credential->port);
            Config::set('mail.mailers.smtp.username', $credential->username);
            Config::set('mail.mailers.smtp.password', $credential->password);
            Config::set('mail.mailers.smtp.encryption', $credential->encryption);
            Config::set('mail.from.address', $credential->from_address);
            Config::set('mail.from.name', $credential->from_name);

            // Send test email
            Mail::raw('This is a test email from your SMTP configuration.', function ($message) use ($request, $credential) {
                $message->to($request->test_email)
                        ->subject('SMTP Configuration Test')
                        ->from($credential->from_address, $credential->from_name);
            });

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
